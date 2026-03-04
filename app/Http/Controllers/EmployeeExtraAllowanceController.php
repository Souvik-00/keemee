<?php

namespace App\Http\Controllers;

use App\Http\Requests\Common\ActionRequest;
use App\Http\Requests\EmployeeExtraAllowance\StoreEmployeeExtraAllowanceRequest;
use App\Http\Requests\EmployeeExtraAllowance\UpdateEmployeeExtraAllowanceRequest;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\EmployeeExtraAllowance;
use App\Models\Site;
use App\Models\Subscriber;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class EmployeeExtraAllowanceController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('checkPermission:allowances.view', only: ['index']),
            new Middleware('checkPermission:allowances.manage', except: ['index']),
        ];
    }

    public function index(Request $request): View
    {
        $year = (int) ($request->integer('year') ?: now()->year);
        $month = (int) ($request->integer('month') ?: now()->month);

        $baseQuery = EmployeeExtraAllowance::query()
            ->with(['employee', 'site', 'subscriber'])
            ->where('year', $year)
            ->where('month', $month)
            ->when($request->integer('employee_id'), fn ($q, $employeeId) => $q->where('employee_id', $employeeId))
            ->when($request->integer('site_id'), fn ($q, $siteId) => $q->where('site_id', $siteId))
            ->when($request->string('allowance_type')->toString(), fn ($q, $type) => $q->where('allowance_type', $type));

        if ($this->isCustomerUser($request)) {
            $baseQuery->whereIn('site_id', $this->ownedSiteIds($request));
        }

        $allowances = (clone $baseQuery)
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $totalsByEmployee = (clone $baseQuery)
            ->selectRaw('employee_id, SUM(amount) as total_amount')
            ->groupBy('employee_id')
            ->with('employee')
            ->get();

        return view('employee_extra_allowances.index', [
            'allowances' => $allowances,
            'totalsByEmployee' => $totalsByEmployee,
            'employees' => $this->accessibleEmployees($request),
            'sites' => $this->accessibleSites($request),
            'filters' => compact('year', 'month'),
        ]);
    }

    public function create(Request $request): View
    {
        return view('employee_extra_allowances.create', [
            'subscribers' => Subscriber::query()->orderBy('name')->get(),
            'employees' => $this->accessibleEmployees($request),
            'sites' => $this->accessibleSites($request),
        ]);
    }

    public function store(StoreEmployeeExtraAllowanceRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $subscriberId = $this->resolveSubscriberId($request);

        $employee = Employee::withoutGlobalScopes()->findOrFail((int) $data['employee_id']);
        $site = isset($data['site_id']) && $data['site_id'] ? Site::withoutGlobalScopes()->findOrFail((int) $data['site_id']) : null;

        $this->assertOwnership($request, $employee, $site, $subscriberId);

        EmployeeExtraAllowance::query()->create([
            'subscriber_id' => $subscriberId,
            'employee_id' => $employee->id,
            'site_id' => $site?->id,
            'year' => (int) $data['year'],
            'month' => (int) $data['month'],
            'allowance_type' => $data['allowance_type'],
            'amount' => (float) $data['amount'],
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()->route('employee-extra-allowances.index', [
            'year' => $data['year'],
            'month' => $data['month'],
        ])->with('success', 'Employee extra allowance added successfully.');
    }

    public function edit(EmployeeExtraAllowance $employeeExtraAllowance, Request $request): View
    {
        $this->assertExistingRecordAccess($request, $employeeExtraAllowance);

        return view('employee_extra_allowances.edit', [
            'extraAllowance' => $employeeExtraAllowance,
            'subscribers' => Subscriber::query()->orderBy('name')->get(),
            'employees' => $this->accessibleEmployees($request),
            'sites' => $this->accessibleSites($request),
        ]);
    }

    public function update(UpdateEmployeeExtraAllowanceRequest $request, EmployeeExtraAllowance $employeeExtraAllowance): RedirectResponse
    {
        $this->assertExistingRecordAccess($request, $employeeExtraAllowance);

        $data = $request->validated();
        $employee = Employee::withoutGlobalScopes()->findOrFail((int) $data['employee_id']);
        $site = isset($data['site_id']) && $data['site_id'] ? Site::withoutGlobalScopes()->findOrFail((int) $data['site_id']) : null;

        $this->assertOwnership($request, $employee, $site, (int) $employeeExtraAllowance->subscriber_id);

        $employeeExtraAllowance->update([
            'employee_id' => $employee->id,
            'site_id' => $site?->id,
            'year' => (int) $data['year'],
            'month' => (int) $data['month'],
            'allowance_type' => $data['allowance_type'],
            'amount' => (float) $data['amount'],
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()->route('employee-extra-allowances.index', [
            'year' => $data['year'],
            'month' => $data['month'],
        ])->with('success', 'Employee extra allowance updated successfully.');
    }

    public function destroy(EmployeeExtraAllowance $employeeExtraAllowance, ActionRequest $request): RedirectResponse
    {
        $this->assertExistingRecordAccess($request, $employeeExtraAllowance);

        $employeeExtraAllowance->delete();

        return redirect()->route('employee-extra-allowances.index')
            ->with('success', 'Employee extra allowance deleted successfully.');
    }

    protected function resolveSubscriberId(Request $request): int
    {
        if ($request->user() && $request->user()->hasRole('leadership')) {
            return (int) $request->integer('subscriber_id');
        }

        return tenant_id() ?? (int) $request->user()->subscriber_id;
    }

    protected function isCustomerUser(Request $request): bool
    {
        return (bool) ($request->user() && $request->user()->hasRole('customer'));
    }

    /**
     * @return array<int, int>
     */
    protected function ownedCustomerIds(Request $request): array
    {
        return Customer::query()
            ->where('user_id', $request->user()->id)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    /**
     * @return array<int, int>
     */
    protected function ownedSiteIds(Request $request): array
    {
        return Site::query()
            ->whereIn('customer_id', $this->ownedCustomerIds($request))
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    protected function accessibleSites(Request $request): Collection
    {
        $query = Site::query()->where('status', 'active')->orderBy('name');

        if ($this->isCustomerUser($request)) {
            $query->whereIn('id', $this->ownedSiteIds($request));
        }

        return $query->get();
    }

    protected function accessibleEmployees(Request $request): Collection
    {
        $query = Employee::query()->where('status', 'active')->orderBy('name');

        if ($this->isCustomerUser($request)) {
            $ownedSiteIds = $this->ownedSiteIds($request);

            $query->whereHas('assignments', function ($assignmentQuery) use ($ownedSiteIds): void {
                $assignmentQuery->whereIn('site_id', $ownedSiteIds);
            });
        }

        return $query->get();
    }

    protected function assertOwnership(Request $request, Employee $employee, ?Site $site, int $subscriberId): void
    {
        abort_unless($employee->subscriber_id === $subscriberId, 422, 'Employee does not belong to selected subscriber.');

        if ($site) {
            abort_unless($site->subscriber_id === $subscriberId, 422, 'Site does not belong to selected subscriber.');
        }

        if ($this->isCustomerUser($request)) {
            $ownedSiteIds = $this->ownedSiteIds($request);

            abort_unless($site && in_array((int) $site->id, $ownedSiteIds, true), 403, 'Customer can manage extra allowances only for own sites.');

            $hasAssignment = $employee->assignments()
                ->where('site_id', $site->id)
                ->exists();

            abort_unless($hasAssignment, 422, 'Employee is not mapped to this customer site.');
        }
    }

    protected function assertExistingRecordAccess(Request $request, EmployeeExtraAllowance $record): void
    {
        if ($this->isCustomerUser($request)) {
            $ownedSiteIds = $this->ownedSiteIds($request);

            abort_unless($record->site_id && in_array((int) $record->site_id, $ownedSiteIds, true), 403, 'Customer can manage extra allowances only for own sites.');
        }
    }
}
