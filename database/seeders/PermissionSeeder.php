<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'View Own Profile', 'slug' => 'profile.view_own', 'module' => 'profile'],
            ['name' => 'Update Own Profile', 'slug' => 'profile.update_own', 'module' => 'profile'],
            ['name' => 'View Dashboard', 'slug' => 'dashboard.view', 'module' => 'dashboard'],
            ['name' => 'View Roles', 'slug' => 'rbac.roles.view', 'module' => 'rbac'],
            ['name' => 'Manage Roles', 'slug' => 'rbac.roles.manage', 'module' => 'rbac'],
            ['name' => 'View Permissions', 'slug' => 'rbac.permissions.view', 'module' => 'rbac'],
            ['name' => 'View Roles Matrix', 'slug' => 'rbac.matrix.view', 'module' => 'rbac'],
            ['name' => 'View Subscribers', 'slug' => 'subscribers.view', 'module' => 'subscribers'],
            ['name' => 'Manage Subscribers', 'slug' => 'subscribers.manage', 'module' => 'subscribers'],
            ['name' => 'View Customers', 'slug' => 'customers.view', 'module' => 'customers'],
            ['name' => 'Create Customers', 'slug' => 'customers.create', 'module' => 'customers'],
            ['name' => 'Update Customers', 'slug' => 'customers.update', 'module' => 'customers'],
            ['name' => 'Delete Customers', 'slug' => 'customers.delete', 'module' => 'customers'],
            ['name' => 'View Client Contacts', 'slug' => 'client_contacts.view', 'module' => 'client_contacts'],
            ['name' => 'Create Client Contacts', 'slug' => 'client_contacts.create', 'module' => 'client_contacts'],
            ['name' => 'Update Client Contacts', 'slug' => 'client_contacts.update', 'module' => 'client_contacts'],
            ['name' => 'Delete Client Contacts', 'slug' => 'client_contacts.delete', 'module' => 'client_contacts'],
            ['name' => 'View Sites', 'slug' => 'sites.view', 'module' => 'sites'],
            ['name' => 'Create Sites', 'slug' => 'sites.create', 'module' => 'sites'],
            ['name' => 'Update Sites', 'slug' => 'sites.update', 'module' => 'sites'],
            ['name' => 'Delete Sites', 'slug' => 'sites.delete', 'module' => 'sites'],
            ['name' => 'View Active Sites', 'slug' => 'sites.view_active', 'module' => 'sites'],
            ['name' => 'View Employees', 'slug' => 'employees.view', 'module' => 'employees'],
            ['name' => 'Create Employees', 'slug' => 'employees.create', 'module' => 'employees'],
            ['name' => 'Update Employees', 'slug' => 'employees.update', 'module' => 'employees'],
            ['name' => 'Delete Employees', 'slug' => 'employees.delete', 'module' => 'employees'],
            ['name' => 'View Assignments', 'slug' => 'assignments.view', 'module' => 'assignments'],
            ['name' => 'Create Assignments', 'slug' => 'assignments.create', 'module' => 'assignments'],
            ['name' => 'Update Assignments', 'slug' => 'assignments.update', 'module' => 'assignments'],
            ['name' => 'Close Assignments', 'slug' => 'assignments.close', 'module' => 'assignments'],
            ['name' => 'View Bench', 'slug' => 'bench.view', 'module' => 'assignments'],
            ['name' => 'View Attendance', 'slug' => 'attendance.view', 'module' => 'attendance'],
            ['name' => 'Create Attendance', 'slug' => 'attendance.create', 'module' => 'attendance'],
            ['name' => 'Update Attendance', 'slug' => 'attendance.update', 'module' => 'attendance'],
            ['name' => 'Monthly Attendance Report', 'slug' => 'attendance.monthly_report', 'module' => 'attendance'],
            ['name' => 'View Allowances', 'slug' => 'allowances.view', 'module' => 'allowances'],
            ['name' => 'Manage Allowances', 'slug' => 'allowances.manage', 'module' => 'allowances'],
            ['name' => 'Site Allowance Report', 'slug' => 'allowances.site_report', 'module' => 'allowances'],
            ['name' => 'View Payroll', 'slug' => 'payroll.view', 'module' => 'payroll'],
            ['name' => 'Process Payroll', 'slug' => 'payroll.process', 'module' => 'payroll'],
            ['name' => 'Finalize Payroll', 'slug' => 'payroll.finalize', 'module' => 'payroll'],
            ['name' => 'View Salary Slips', 'slug' => 'salary_slips.view', 'module' => 'payroll'],
            ['name' => 'View All Salary Lists', 'slug' => 'salary_lists.view_all', 'module' => 'payroll'],
            ['name' => 'View Guard Salary List', 'slug' => 'salary_lists.view_guards', 'module' => 'payroll'],
            ['name' => 'View Site Manager Salary List', 'slug' => 'salary_lists.view_site_managers', 'module' => 'payroll'],
            ['name' => 'View Site Visits', 'slug' => 'site_visits.view', 'module' => 'operations'],
            ['name' => 'Create Site Visits', 'slug' => 'site_visits.create', 'module' => 'operations'],
            ['name' => 'Update Site Visits', 'slug' => 'site_visits.update', 'module' => 'operations'],
            ['name' => 'Manager Site Visit Report', 'slug' => 'site_visits.manager_report', 'module' => 'operations'],
            ['name' => 'View Expenses', 'slug' => 'expenses.view', 'module' => 'expenses'],
            ['name' => 'Create Expenses', 'slug' => 'expenses.create', 'module' => 'expenses'],
            ['name' => 'Update Expenses', 'slug' => 'expenses.update', 'module' => 'expenses'],
            ['name' => 'Delete Expenses', 'slug' => 'expenses.delete', 'module' => 'expenses'],
            ['name' => 'Expense Reports', 'slug' => 'expenses.reports', 'module' => 'expenses'],
            ['name' => 'Employee Profile Report', 'slug' => 'reports.employee_profile', 'module' => 'reports'],
            ['name' => 'Attendance Monthly Report', 'slug' => 'reports.attendance_monthly', 'module' => 'reports'],
            ['name' => 'Active Sites Report', 'slug' => 'reports.active_sites', 'module' => 'reports'],
            ['name' => 'Site Allowance Report (Reports)', 'slug' => 'reports.site_allowance', 'module' => 'reports'],
            ['name' => 'Periodic Expense Report', 'slug' => 'reports.expense_periodic', 'module' => 'reports'],
        ];

        foreach ($permissions as $permission) {
            Permission::query()->updateOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }
    }
}
