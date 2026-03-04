<?php

namespace App\Http\Controllers;

use App\Http\Requests\Attendance\StoreAttendanceMonthlySummaryRequest;
use App\Http\Requests\Attendance\UpdateAttendanceMonthlySummaryRequest;
use App\Models\AttendanceMonthlySummary;
use App\Models\Employee;
use App\Models\Site;
use App\Models\Subscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class AttendanceMonthlySummaryController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('checkPermission:attendance.view', only: ['index', 'create']),
            new Middleware('checkPermission:attendance.create', only: ['store']),
            new Middleware('checkPermission:attendance.update', only: ['edit', 'update']),
            new Middleware('checkPermission:attendance.monthly_report', only: ['report']),
        ];
    }

    public function index(Request $request): View
    {
        $year = (int) ($request->integer('year') ?: now()->year);
        $month = (int) ($request->integer('month') ?: now()->month);

        $summaries = AttendanceMonthlySummary::query()
            ->with(['employee', 'site', 'subscriber'])
            ->when($request->integer('employee_id'), fn ($query, $employeeId) => $query->where('employee_id', $employeeId))
            ->where('year', $year)
            ->where('month', $month)
            ->when($this->isGuardManager($request), fn ($query) => $query->whereHas('employee', fn ($employeeQuery) => $employeeQuery->where('employee_type', 'guard')))
            ->orderByDesc('attendance_percent')
            ->paginate(15)
            ->withQueryString();

        return view('attendance.index', [
            'summaries' => $summaries,
            'filters' => compact('year', 'month'),
            'employees' => $this->employeeOptions($request),
        ]);
    }

    public function create(Request $request): View
    {
        return view('attendance.create', [
            'employees' => $this->employeeOptions($request),
            'sites' => Site::query()->where('status', 'active')->orderBy('name')->get(),
            'subscribers' => Subscriber::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreAttendanceMonthlySummaryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $subscriberId = $this->resolveSubscriberId($request);

        $employee = Employee::withoutGlobalScopes()->findOrFail((int) $data['employee_id']);
        $site = isset($data['site_id']) && $data['site_id']
            ? Site::withoutGlobalScopes()->findOrFail((int) $data['site_id'])
            : null;

        $this->assertTenantConsistency($subscriberId, $employee, $site);

        $exists = AttendanceMonthlySummary::withoutGlobalScopes()
            ->where('subscriber_id', $subscriberId)
            ->where('employee_id', $employee->id)
            ->where('year', $data['year'])
            ->where('month', $data['month'])
            ->exists();

        abort_if($exists, 422, 'Attendance summary already exists for this employee and month. Please update it instead.');

        $attendancePercent = AttendanceMonthlySummary::calculatePercent(
            (int) $data['present_days'],
            (int) $data['absent_days']
        );

        AttendanceMonthlySummary::query()->create([
            'subscriber_id' => $subscriberId,
            'employee_id' => $employee->id,
            'site_id' => $site?->id,
            'year' => (int) $data['year'],
            'month' => (int) $data['month'],
            'present_days' => (int) $data['present_days'],
            'absent_days' => (int) $data['absent_days'],
            'attendance_percent' => $attendancePercent,
        ]);

        return redirect()
            ->route('attendance-monthly-summaries.index', [
                'year' => $data['year'],
                'month' => $data['month'],
            ])
            ->with('success', 'Attendance summary saved successfully.');
    }

    public function edit(AttendanceMonthlySummary $attendanceMonthlySummary, Request $request): View
    {
        return view('attendance.edit', [
            'summary' => $attendanceMonthlySummary,
            'employees' => $this->employeeOptions($request),
            'sites' => Site::query()->where('status', 'active')->orderBy('name')->get(),
            'subscribers' => Subscriber::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateAttendanceMonthlySummaryRequest $request, AttendanceMonthlySummary $attendanceMonthlySummary): RedirectResponse
    {
        $data = $request->validated();

        $employee = Employee::withoutGlobalScopes()->findOrFail((int) $data['employee_id']);
        $site = isset($data['site_id']) && $data['site_id']
            ? Site::withoutGlobalScopes()->findOrFail((int) $data['site_id'])
            : null;

        $this->assertTenantConsistency((int) $attendanceMonthlySummary->subscriber_id, $employee, $site);

        $duplicate = AttendanceMonthlySummary::withoutGlobalScopes()
            ->where('subscriber_id', $attendanceMonthlySummary->subscriber_id)
            ->where('employee_id', $employee->id)
            ->where('year', $data['year'])
            ->where('month', $data['month'])
            ->where('id', '!=', $attendanceMonthlySummary->id)
            ->exists();

        abort_if($duplicate, 422, 'Another attendance summary exists for this employee and month.');

        $attendanceMonthlySummary->update([
            'employee_id' => $employee->id,
            'site_id' => $site?->id,
            'year' => (int) $data['year'],
            'month' => (int) $data['month'],
            'present_days' => (int) $data['present_days'],
            'absent_days' => (int) $data['absent_days'],
            'attendance_percent' => AttendanceMonthlySummary::calculatePercent((int) $data['present_days'], (int) $data['absent_days']),
        ]);

        return redirect()
            ->route('attendance-monthly-summaries.index', [
                'year' => $data['year'],
                'month' => $data['month'],
            ])
            ->with('success', 'Attendance summary updated successfully.');
    }

    public function report(Request $request): View
    {
        $year = (int) ($request->integer('year') ?: now()->year);
        $month = (int) ($request->integer('month') ?: now()->month);

        $reportRows = AttendanceMonthlySummary::query()
            ->with(['employee', 'site', 'subscriber'])
            ->where('year', $year)
            ->where('month', $month)
            ->when($request->integer('employee_id'), fn ($query, $employeeId) => $query->where('employee_id', $employeeId))
            ->when($this->isGuardManager($request), fn ($query) => $query->whereHas('employee', fn ($employeeQuery) => $employeeQuery->where('employee_type', 'guard')))
            ->orderByDesc('attendance_percent')
            ->paginate(20)
            ->withQueryString();

        return view('attendance.report', [
            'reportRows' => $reportRows,
            'filters' => compact('year', 'month'),
            'employees' => $this->employeeOptions($request),
        ]);
    }

    protected function employeeOptions(Request $request)
    {
        return Employee::query()
            ->where('status', 'active')
            ->when($this->isGuardManager($request), fn ($query) => $query->where('employee_type', 'guard'))
            ->orderBy('name')
            ->get();
    }

    protected function isGuardManager(Request $request): bool
    {
        return (bool) ($request->user() && $request->user()->hasRole('security_guard_manager'));
    }

    protected function resolveSubscriberId(Request $request): int
    {
        if ($request->user() && $request->user()->hasRole('leadership')) {
            return (int) $request->integer('subscriber_id');
        }

        return tenant_id() ?? (int) $request->user()->subscriber_id;
    }

    protected function assertTenantConsistency(int $subscriberId, Employee $employee, ?Site $site): void
    {
        abort_unless($employee->subscriber_id === $subscriberId, 422, 'Employee does not belong to selected subscriber.');

        if ($site) {
            abort_unless($site->subscriber_id === $subscriberId, 422, 'Site does not belong to selected subscriber.');
        }
    }
}
