<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class ReportHubController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('checkPermission:dashboard.view', only: ['index']),
        ];
    }

    public function index(Request $request): View
    {
        $links = [
            [
                'label' => 'Employee Profile Report',
                'route' => 'reports.employee-profiles',
                'permission' => 'reports.employee_profile',
                'filters' => ['q'],
            ],
            [
                'label' => 'Monthly Attendance Report',
                'route' => 'attendance.report',
                'permission' => 'attendance.monthly_report',
                'filters' => ['year', 'month', 'employee_id', 'site_id'],
            ],
            [
                'label' => 'Bench Report',
                'route' => 'reports.bench',
                'permission' => 'bench.view',
                'filters' => ['q'],
            ],
            [
                'label' => 'Active Sites Report',
                'route' => 'reports.active-sites',
                'permission' => 'reports.active_sites',
                'filters' => ['q'],
            ],
            [
                'label' => 'Site Allowance Report',
                'route' => 'site-allowance-report',
                'permission' => 'reports.site_allowance',
                'filters' => ['year', 'month', 'site_id', 'allowance_type'],
            ],
            [
                'label' => 'Manager-wise Site Visit Report',
                'route' => 'site-visits.report',
                'permission' => 'site_visits.manager_report',
                'filters' => ['manager_employee_id', 'site_id', 'date_from', 'date_to'],
            ],
            [
                'label' => 'Site-wise Expense Report',
                'route' => 'expenses.site-report',
                'permission' => 'expenses.reports',
                'filters' => ['grouping', 'customer_id', 'site_id', 'date_from', 'date_to'],
            ],
            [
                'label' => 'Customer-wise Expense Report',
                'route' => 'expenses.customer-report',
                'permission' => 'expenses.reports',
                'filters' => ['grouping', 'customer_id', 'site_id', 'date_from', 'date_to'],
            ],
            [
                'label' => 'Salary List Report',
                'route' => 'salary-reports.list',
                'permission' => 'payroll.view',
                'filters' => ['year', 'month', 'type'],
            ],
            [
                'label' => 'Roles List',
                'route' => 'roles.index',
                'permission' => 'rbac.roles.view',
                'filters' => ['q'],
            ],
            [
                'label' => 'Role-Permission Matrix',
                'route' => 'roles.matrix',
                'permission' => 'rbac.matrix.view',
                'filters' => ['module'],
            ],
        ];

        $user = $request->user();

        $visibleLinks = collect($links)
            ->filter(fn (array $link): bool => $user->hasPermission($link['permission']))
            ->map(fn (array $link): array => [
                'label' => $link['label'],
                'url' => route($link['route']),
                'filters' => $link['filters'],
            ])
            ->values()
            ->all();

        return view('reports.hub', [
            'links' => $visibleLinks,
        ]);
    }
}
