<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'leadership' => [
                'name' => 'Leadership',
                'permissions' => ['*'],
            ],
            'manager' => [
                'name' => 'Manager',
                'permissions' => [
                    'dashboard.view',
                    'profile.view_own',
                    'profile.update_own',
                    'employees.view',
                    'sites.view',
                    'sites.view_active',
                    'assignments.view',
                    'bench.view',
                    'attendance.view',
                    'attendance.monthly_report',
                    'payroll.view',
                    'payroll.process',
                    'salary_slips.view',
                    'salary_lists.view_all',
                    'salary_lists.view_guards',
                    'salary_lists.view_site_managers',
                    'site_visits.view',
                    'site_visits.create',
                    'site_visits.update',
                    'site_visits.manager_report',
                    'reports.employee_profile',
                    'reports.attendance_monthly',
                    'reports.active_sites',
                ],
            ],
            'security_guard_manager' => [
                'name' => 'Security Guard Manager',
                'permissions' => [
                    'dashboard.view',
                    'profile.view_own',
                    'profile.update_own',
                    'employees.view',
                    'sites.view',
                    'sites.view_active',
                    'attendance.view',
                    'attendance.monthly_report',
                    'salary_lists.view_guards',
                    'reports.attendance_monthly',
                    'reports.active_sites',
                ],
            ],
            'security_guard' => [
                'name' => 'Security Guard',
                'permissions' => [
                    'dashboard.view',
                    'profile.view_own',
                    'profile.update_own',
                ],
            ],
            'customer' => [
                'name' => 'Customer',
                'permissions' => [
                    'dashboard.view',
                    'profile.view_own',
                    'profile.update_own',
                    'sites.view',
                    'sites.view_active',
                    'allowances.view',
                    'allowances.manage',
                    'reports.site_allowance',
                    'reports.active_sites',
                    'expenses.view',
                    'expenses.reports',
                    'reports.expense_periodic',
                ],
            ],
        ];

        $allPermissionIds = Permission::query()->pluck('id');

        foreach ($roles as $slug => $roleConfig) {
            $role = Role::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $roleConfig['name'],
                    'subscriber_id' => null,
                ]
            );

            $permissionIds = $roleConfig['permissions'] === ['*']
                ? $allPermissionIds
                : Permission::query()
                    ->whereIn('slug', $roleConfig['permissions'])
                    ->pluck('id');

            $role->permissions()->sync($permissionIds);
        }
    }
}
