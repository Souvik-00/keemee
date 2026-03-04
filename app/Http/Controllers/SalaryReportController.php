<?php

namespace App\Http\Controllers;

use App\Models\SalaryRecord;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class SalaryReportController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('checkPermission:payroll.view', only: ['list']),
            new Middleware('checkPermission:salary_slips.view', only: ['slip']),
        ];
    }

    public function list(Request $request): View
    {
        $year = (int) ($request->integer('year') ?: now()->year);
        $month = (int) ($request->integer('month') ?: now()->month);
        $type = $request->string('type')->toString();

        if ($type === '') {
            $type = $request->user()->hasPermission('salary_lists.view_all')
                ? 'all'
                : ($request->user()->hasPermission('salary_lists.view_guards')
                    ? 'guards'
                    : ($request->user()->hasPermission('salary_lists.view_site_managers') ? 'site_managers' : 'all'));
        }

        $recordsQuery = SalaryRecord::query()
            ->with(['employee', 'site', 'payrollRun'])
            ->where('year', $year)
            ->where('month', $month)
            ->whereHas('payrollRun', fn ($q) => $q->where('status', 'finalized'));

        if ($type === 'all') {
            abort_unless($request->user()->hasPermission('salary_lists.view_all'), 403);
        } elseif ($type === 'guards') {
            abort_unless($request->user()->hasPermission('salary_lists.view_guards'), 403);
            $recordsQuery->whereHas('employee', fn ($q) => $q->where('employee_type', 'guard'));
        } elseif ($type === 'site_managers') {
            abort_unless($request->user()->hasPermission('salary_lists.view_site_managers'), 403);
            $recordsQuery->whereHas('employee', fn ($q) => $q->where('employee_type', 'site_manager'));
        } else {
            abort(422, 'Invalid salary list type filter.');
        }

        $records = $recordsQuery
            ->orderByDesc('net_salary')
            ->paginate(20)
            ->withQueryString();

        $totals = (clone $recordsQuery)
            ->selectRaw('SUM(basic_amount) as basic_total, SUM(extra_allowance_total) as extra_total, SUM(deduction_total) as deduction_total, SUM(net_salary) as net_total')
            ->first();

        return view('salary_reports.list', [
            'records' => $records,
            'totals' => $totals,
            'filters' => compact('year', 'month', 'type'),
        ]);
    }

    public function slip(SalaryRecord $salaryRecord): View
    {
        return view('salary_reports.slip', [
            'salaryRecord' => $salaryRecord->load(['employee', 'site.customer', 'components', 'payrollRun.subscriber']),
        ]);
    }
}
