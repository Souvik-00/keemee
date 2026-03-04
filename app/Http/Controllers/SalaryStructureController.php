<?php

namespace App\Http\Controllers;

use App\Http\Requests\Common\ActionRequest;
use App\Http\Requests\SalaryStructure\StoreSalaryStructureRequest;
use App\Http\Requests\SalaryStructure\UpdateSalaryStructureRequest;
use App\Models\Employee;
use App\Models\SalaryStructure;
use App\Models\Subscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class SalaryStructureController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('checkPermission:payroll.view', only: ['index']),
            new Middleware('checkPermission:payroll.process', except: ['index']),
        ];
    }

    public function index(Request $request): View
    {
        $structures = SalaryStructure::query()
            ->with(['employee', 'subscriber'])
            ->when($request->integer('employee_id'), fn ($q, $employeeId) => $q->where('employee_id', $employeeId))
            ->orderByDesc('effective_from')
            ->paginate(15)
            ->withQueryString();

        return view('salary_structures.index', [
            'structures' => $structures,
            'employees' => Employee::query()->where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('salary_structures.create', [
            'subscribers' => Subscriber::query()->orderBy('name')->get(),
            'employees' => Employee::query()->where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function store(StoreSalaryStructureRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $subscriberId = $this->resolveSubscriberId($request);

        $employee = Employee::withoutGlobalScopes()->findOrFail((int) $data['employee_id']);
        abort_unless($employee->subscriber_id === $subscriberId, 422, 'Employee does not belong to selected subscriber.');

        SalaryStructure::query()->create([
            'subscriber_id' => $subscriberId,
            'employee_id' => $employee->id,
            'basic_salary' => $data['basic_salary'],
            'pf_percent' => $data['pf_percent'],
            'esi_percent' => $data['esi_percent'],
            'other_deduction_fixed' => $data['other_deduction_fixed'],
            'effective_from' => $data['effective_from'],
            'effective_to' => $data['effective_to'] ?? null,
        ]);

        return redirect()->route('salary-structures.index')
            ->with('success', 'Salary structure created successfully.');
    }

    public function edit(SalaryStructure $salaryStructure): View
    {
        return view('salary_structures.edit', [
            'salaryStructure' => $salaryStructure,
            'subscribers' => Subscriber::query()->orderBy('name')->get(),
            'employees' => Employee::query()->where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateSalaryStructureRequest $request, SalaryStructure $salaryStructure): RedirectResponse
    {
        $data = $request->validated();

        $employee = Employee::withoutGlobalScopes()->findOrFail((int) $data['employee_id']);
        abort_unless($employee->subscriber_id === $salaryStructure->subscriber_id, 422, 'Employee does not belong to salary structure subscriber.');

        $salaryStructure->update([
            'employee_id' => $employee->id,
            'basic_salary' => $data['basic_salary'],
            'pf_percent' => $data['pf_percent'],
            'esi_percent' => $data['esi_percent'],
            'other_deduction_fixed' => $data['other_deduction_fixed'],
            'effective_from' => $data['effective_from'],
            'effective_to' => $data['effective_to'] ?? null,
        ]);

        return redirect()->route('salary-structures.index')
            ->with('success', 'Salary structure updated successfully.');
    }

    public function destroy(ActionRequest $request, SalaryStructure $salaryStructure): RedirectResponse
    {
        $salaryStructure->delete();

        return redirect()->route('salary-structures.index')
            ->with('success', 'Salary structure deleted successfully.');
    }

    protected function resolveSubscriberId(Request $request): int
    {
        if ($request->user() && $request->user()->hasRole('leadership')) {
            return (int) $request->integer('subscriber_id');
        }

        return tenant_id() ?? (int) $request->user()->subscriber_id;
    }
}
