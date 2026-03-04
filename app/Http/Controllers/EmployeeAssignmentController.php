<?php

namespace App\Http\Controllers;

use App\Http\Requests\Assignment\CloseEmployeeSiteAssignmentRequest;
use App\Http\Requests\Assignment\StoreEmployeeSiteAssignmentRequest;
use App\Models\Employee;
use App\Models\EmployeeSiteAssignment;
use App\Models\Site;
use App\Models\Subscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class EmployeeAssignmentController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('checkPermission:assignments.view', only: ['index']),
            new Middleware('checkPermission:assignments.create', only: ['create', 'store']),
            new Middleware('checkPermission:assignments.close', only: ['edit', 'update']),
        ];
    }

    public function index(Request $request): View
    {
        $assignments = EmployeeSiteAssignment::query()
            ->with(['employee', 'site.customer'])
            ->when($request->string('status')->toString() === 'active', fn ($query) => $query->active())
            ->orderByDesc('assigned_from')
            ->paginate(12)
            ->withQueryString();

        return view('assignments.index', compact('assignments'));
    }

    public function create(): View
    {
        return view('assignments.create', [
            'employees' => Employee::query()->where('status', 'active')->orderBy('name')->get(),
            'sites' => Site::query()->where('status', 'active')->orderBy('name')->get(),
            'subscribers' => Subscriber::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreEmployeeSiteAssignmentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $subscriberId = $this->resolveSubscriberId($request);

        $employee = Employee::withoutGlobalScopes()->findOrFail($data['employee_id']);
        $site = Site::withoutGlobalScopes()->findOrFail($data['site_id']);

        abort_unless(
            $employee->subscriber_id === $subscriberId && $site->subscriber_id === $subscriberId,
            422,
            'Employee and site must belong to the same subscriber.'
        );

        $alreadyAssigned = EmployeeSiteAssignment::withoutGlobalScopes()
            ->where('subscriber_id', $subscriberId)
            ->where('employee_id', $employee->id)
            ->active()
            ->exists();

        abort_if($alreadyAssigned, 422, 'Employee already has an active assignment. Close it before creating a new one.');

        EmployeeSiteAssignment::query()->create([
            'subscriber_id' => $subscriberId,
            'employee_id' => $employee->id,
            'site_id' => $site->id,
            'assigned_from' => $data['assigned_from'],
            'assigned_to' => null,
            'is_active' => true,
        ]);

        return redirect()
            ->route('employee-assignments.index')
            ->with('success', 'Employee assigned to site successfully.');
    }

    public function edit(EmployeeSiteAssignment $employeeAssignment): View
    {
        return view('assignments.edit', [
            'assignment' => $employeeAssignment->load(['employee', 'site.customer']),
        ]);
    }

    public function update(CloseEmployeeSiteAssignmentRequest $request, EmployeeSiteAssignment $employeeAssignment): RedirectResponse
    {
        abort_if(! $employeeAssignment->is_active, 422, 'Assignment is already closed.');

        $employeeAssignment->update([
            'assigned_to' => $request->date('assigned_to')->toDateString(),
            'is_active' => false,
        ]);

        return redirect()
            ->route('employee-assignments.index')
            ->with('success', 'Assignment closed successfully.');
    }

    protected function resolveSubscriberId(Request $request): int
    {
        if ($request->user() && $request->user()->hasRole('leadership')) {
            return (int) $request->integer('subscriber_id');
        }

        return tenant_id() ?? (int) $request->user()->subscriber_id;
    }
}
