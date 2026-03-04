<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\AttendanceMonthlySummaryController;
use App\Http\Controllers\ClientContactController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeAssignmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeExtraAllowanceController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ModulePlaceholderController;
use App\Http\Controllers\PayrollRunController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReportHubController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SalaryReportController;
use App\Http\Controllers\SalaryStructureController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\SiteVisitController;
use App\Http\Controllers\SiteAllowanceConfigController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\TenantContextController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard.index')
        : redirect()->route('login');
})->name('home');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

Route::middleware(['auth', 'tenant.context'])->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/tenant/switch', [TenantContextController::class, 'switch'])
        ->name('tenant.switch')
        ->middleware('checkPermission:subscribers.view');

    Route::prefix('admin')
        ->name('admin.')
        ->middleware('leadership.only')
        ->group(function (): void {
            Route::resource('users', UserManagementController::class)->except(['destroy']);
            Route::get('users/{user}/reset-password', [UserManagementController::class, 'resetPasswordForm'])
                ->name('users.reset-password.form');
            Route::post('users/{user}/reset-password', [UserManagementController::class, 'resetPassword'])
                ->name('users.reset-password');
            Route::post('users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])
                ->name('users.toggle-status');
        });

    Route::resource('dashboard', DashboardController::class)
        ->only(['index'])
        ->middleware('checkPermission:dashboard.view');

    Route::singleton('profile', ProfileController::class)
        ->only(['show', 'edit', 'update']);

    Route::get('/modules/auth-rbac', [ModulePlaceholderController::class, 'show'])
        ->defaults('module', 'auth-rbac')
        ->name('modules.auth-rbac')
        ->middleware('checkPermission:rbac.roles.view');

    Route::get('/modules/masters', [ModulePlaceholderController::class, 'show'])
        ->defaults('module', 'masters')
        ->name('modules.masters')
        ->middleware('checkPermission:customers.view');

    Route::get('/modules/attendance', [ModulePlaceholderController::class, 'show'])
        ->defaults('module', 'attendance')
        ->name('modules.attendance')
        ->middleware('checkPermission:attendance.view');

    Route::get('/modules/payroll', [ModulePlaceholderController::class, 'show'])
        ->defaults('module', 'payroll')
        ->name('modules.payroll')
        ->middleware('checkPermission:payroll.view');

    Route::get('/modules/allowances', [ModulePlaceholderController::class, 'show'])
        ->defaults('module', 'allowances')
        ->name('modules.allowances')
        ->middleware('checkPermission:allowances.view');

    Route::get('/modules/operations', [ModulePlaceholderController::class, 'show'])
        ->defaults('module', 'operations')
        ->name('modules.operations')
        ->middleware('checkPermission:site_visits.view');

    Route::get('/modules/expenses', [ModulePlaceholderController::class, 'show'])
        ->defaults('module', 'expenses')
        ->name('modules.expenses')
        ->middleware('checkPermission:expenses.view');

    Route::get('/modules/reports', [ModulePlaceholderController::class, 'show'])
        ->defaults('module', 'reports')
        ->name('modules.reports')
        ->middleware('checkPermission:reports.active_sites');

    Route::resource('subscribers', SubscriberController::class)
        ->except(['show']);

    Route::get('reports', [ReportHubController::class, 'index'])
        ->name('reports.hub');

    Route::resource('roles', RoleController::class)
        ->only(['index', 'edit', 'update']);

    Route::get('roles-matrix', [RoleController::class, 'matrix'])
        ->name('roles.matrix');

    Route::resource('customers', CustomerController::class)
        ->except(['show']);

    Route::resource('client-contacts', ClientContactController::class)
        ->except(['show']);

    Route::resource('sites', SiteController::class)
        ->except(['show']);

    Route::resource('employees', EmployeeController::class)
        ->except(['show']);

    Route::resource('employee-assignments', EmployeeAssignmentController::class)
        ->only(['index', 'create', 'store', 'edit', 'update']);

    Route::resource('attendance-monthly-summaries', AttendanceMonthlySummaryController::class)
        ->only(['index', 'create', 'store', 'edit', 'update']);

    Route::resource('site-allowance-configs', SiteAllowanceConfigController::class)
        ->except(['show']);

    Route::resource('employee-extra-allowances', EmployeeExtraAllowanceController::class)
        ->except(['show']);

    Route::resource('site-visits', SiteVisitController::class)
        ->only(['index', 'create', 'store', 'edit', 'update']);

    Route::resource('expenses', ExpenseController::class)
        ->except(['show']);

    Route::resource('salary-structures', SalaryStructureController::class)
        ->except(['show']);

    Route::resource('payroll-runs', PayrollRunController::class)
        ->only(['index', 'create', 'store', 'show']);

    Route::post('payroll-runs/{payroll_run}/finalize', [PayrollRunController::class, 'finalize'])
        ->name('payroll-runs.finalize');

    Route::get('reports/attendance-monthly', [AttendanceMonthlySummaryController::class, 'report'])
        ->name('attendance.report');

    Route::get('reports/site-allowance', [SiteAllowanceConfigController::class, 'report'])
        ->name('site-allowance-report');

    Route::get('reports/site-visits-manager', [SiteVisitController::class, 'managerReport'])
        ->name('site-visits.report');

    Route::get('reports/expenses/site-wise', [ExpenseController::class, 'siteReport'])
        ->name('expenses.site-report');

    Route::get('reports/expenses/customer-wise', [ExpenseController::class, 'customerReport'])
        ->name('expenses.customer-report');

    Route::get('reports/salary-list', [SalaryReportController::class, 'list'])
        ->name('salary-reports.list');

    Route::get('reports/salary-slip/{salary_record}', [SalaryReportController::class, 'slip'])
        ->name('salary-reports.slip');

    Route::get('reports/bench', [ReportController::class, 'bench'])
        ->name('reports.bench');

    Route::get('reports/active-sites', [ReportController::class, 'activeSites'])
        ->name('reports.active-sites');

    Route::get('reports/employee-profiles', [ReportController::class, 'employeeProfiles'])
        ->name('reports.employee-profiles');
});
