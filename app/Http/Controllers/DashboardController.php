<?php

namespace App\Http\Controllers;

use App\Models\AttendanceMonthlySummary;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\PayrollRun;
use App\Models\Site;
use App\Models\SiteAllowanceConfig;
use App\Models\Subscriber;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $role = $this->resolvePrimaryRole($user);

        return view('dashboard.index', [
            'role' => $role,
            'cards' => $this->cardsForRole($role, $user->id),
            'quickLinks' => $this->quickLinksForRole($role, $user),
            'customerSites' => $role === 'customer' ? $this->customerSites($user->id) : collect(),
            'customerAllowances' => $role === 'customer' ? $this->customerAllowances($user->id) : collect(),
        ]);
    }

    protected function resolvePrimaryRole($user): string
    {
        $priority = ['leadership', 'manager', 'security_guard_manager', 'security_guard', 'customer'];

        foreach ($priority as $slug) {
            if ($user->hasRole($slug)) {
                return $slug;
            }
        }

        return 'unknown';
    }

    protected function cardsForRole(string $role, int $userId): array
    {
        return match ($role) {
            'leadership' => [
                ['label' => 'Subscribers', 'value' => Subscriber::query()->count()],
                ['label' => 'Customers', 'value' => Customer::query()->count()],
                ['label' => 'Active Sites', 'value' => Site::query()->where('status', 'active')->count()],
                ['label' => 'Active Employees', 'value' => Employee::query()->where('status', 'active')->count()],
                ['label' => 'Finalized Payroll Runs', 'value' => PayrollRun::query()->where('status', 'finalized')->count()],
            ],
            'manager' => [
                ['label' => 'Active Employees', 'value' => Employee::query()->where('status', 'active')->count()],
                ['label' => 'Active Sites', 'value' => Site::query()->where('status', 'active')->count()],
                ['label' => 'Bench Employees', 'value' => Employee::query()->where('status', 'active')->whereDoesntHave('activeAssignments')->count()],
                ['label' => 'Draft Payroll Runs', 'value' => PayrollRun::query()->where('status', 'draft')->count()],
            ],
            'security_guard_manager' => [
                ['label' => 'Active Guards', 'value' => Employee::query()->where('status', 'active')->where('employee_type', 'guard')->count()],
                ['label' => 'Active Sites', 'value' => Site::query()->where('status', 'active')->count()],
                ['label' => 'Attendance (This Month)', 'value' => AttendanceMonthlySummary::query()->where('year', now()->year)->where('month', now()->month)->count()],
            ],
            'security_guard' => [
                ['label' => 'Profile', 'value' => 'Own profile access enabled'],
                ['label' => 'Attendance', 'value' => 'Own attendance toggle planned'],
                ['label' => 'Salary Slip', 'value' => 'Own slip toggle planned'],
            ],
            'customer' => [
                ['label' => 'Owned Customers', 'value' => Customer::query()->where('user_id', $userId)->count()],
                ['label' => 'Owned Active Sites', 'value' => Site::query()->whereIn('customer_id', $this->ownedCustomerIds($userId))->where('status', 'active')->count()],
                ['label' => 'Active Allowance Configs', 'value' => SiteAllowanceConfig::query()->whereIn('customer_id', $this->ownedCustomerIds($userId))->where('is_active', true)->count()],
            ],
            default => [
                ['label' => 'Dashboard', 'value' => 'No mapped role found'],
            ],
        };
    }

    protected function quickLinksForRole(string $role, $user): array
    {
        $links = match ($role) {
            'leadership' => [
                ['label' => 'Subscribers', 'route' => 'subscribers.index', 'permission' => 'subscribers.view'],
                ['label' => 'Customers', 'route' => 'customers.index', 'permission' => 'customers.view'],
                ['label' => 'Employees', 'route' => 'employees.index', 'permission' => 'employees.view'],
                ['label' => 'Site Visits Report', 'route' => 'site-visits.report', 'permission' => 'site_visits.manager_report'],
                ['label' => 'Expense Reports', 'route' => 'expenses.site-report', 'permission' => 'expenses.reports'],
            ],
            'manager' => [
                ['label' => 'Employees', 'route' => 'employees.index', 'permission' => 'employees.view'],
                ['label' => 'Assignments', 'route' => 'employee-assignments.index', 'permission' => 'assignments.view'],
                ['label' => 'Attendance Report', 'route' => 'attendance.report', 'permission' => 'attendance.monthly_report'],
                ['label' => 'Payroll Runs', 'route' => 'payroll-runs.index', 'permission' => 'payroll.view'],
                ['label' => 'Site Visits', 'route' => 'site-visits.index', 'permission' => 'site_visits.view'],
            ],
            'security_guard_manager' => [
                ['label' => 'Attendance Report', 'route' => 'attendance.report', 'permission' => 'attendance.monthly_report'],
                ['label' => 'Active Sites', 'route' => 'reports.active-sites', 'permission' => 'reports.active_sites'],
            ],
            'security_guard' => [
                ['label' => 'My Profile', 'route' => 'profile.show', 'permission' => 'profile.view_own'],
            ],
            'customer' => [
                ['label' => 'My Profile', 'route' => 'profile.show', 'permission' => 'profile.view_own'],
                ['label' => 'My Sites', 'route' => 'sites.index', 'permission' => 'sites.view'],
                ['label' => 'Site Allowances', 'route' => 'site-allowance-configs.index', 'permission' => 'allowances.view'],
                ['label' => 'Site Allowance Report', 'route' => 'site-allowance-report', 'permission' => 'reports.site_allowance'],
            ],
            default => [],
        };

        return collect($links)
            ->filter(function (array $link) use ($user): bool {
                return Route::has($link['route']) && $user->hasPermission($link['permission']);
            })
            ->map(fn (array $link): array => [
                'label' => $link['label'],
                'url' => route($link['route']),
            ])
            ->values()
            ->all();
    }

    protected function customerSites(int $userId)
    {
        return Site::query()
            ->with('customer')
            ->whereIn('customer_id', $this->ownedCustomerIds($userId))
            ->orderBy('name')
            ->get();
    }

    protected function customerAllowances(int $userId)
    {
        return SiteAllowanceConfig::query()
            ->with(['site', 'customer'])
            ->whereIn('customer_id', $this->ownedCustomerIds($userId))
            ->where('is_active', true)
            ->orderByDesc('effective_from')
            ->limit(8)
            ->get();
    }

    /**
     * @return array<int, int>
     */
    protected function ownedCustomerIds(int $userId): array
    {
        return Customer::query()
            ->where('user_id', $userId)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }
}
