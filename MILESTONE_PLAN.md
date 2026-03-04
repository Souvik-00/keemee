## Milestone Plan (Small + Shippable)

- [ ] **Milestone 1: Project Foundation + Auth + RBAC**
  - Deliverables:
  - Session login (email or username + password), logout, password hashing
  - Custom RBAC tables, middleware, seed roles/permissions
  - Default Leadership/admin seed user
  - Base layout (Blade + Bootstrap 5), flash messages, protected dashboard
  - Shippable outcome: leadership can log in and access role-protected pages
  - Gate: run `php artisan migrate:fresh --seed` and fix all failures

- [ ] **Milestone 2: Tenant/Subscriber Scope + Core Master Data**
  - Deliverables:
  - `subscribers` (tenants), tenant-scoped data model pattern (`subscriber_id`)
  - CRUD: customers, client contacts, sites (with active/inactive status)
  - Tenant scoping in queries/controllers
  - Shippable outcome: tenant-safe customer/site management working end-to-end
  - Gate: run `php artisan migrate:fresh --seed` and fix all failures

- [ ] **Milestone 3: Employees + Assignments + Bench Logic**
  - Deliverables:
  - CRUD: employees
  - Employee↔site assignment lifecycle (start/end, active flag)
  - Bench report query: employees with no active assignment
  - Active sites list with customer relation
  - Shippable outcome: staffing + bench reporting usable
  - Gate: run `php artisan migrate:fresh --seed` and fix all failures

- [ ] **Milestone 4: Attendance (Monthly Summary)**
  - Deliverables:
  - Monthly attendance summary storage per employee (`present_days`, `absent_days`, `%`)
  - Attendance % rule: `present / (present + absent) * 100`
  - Monthly attendance report screens/filters
  - Shippable outcome: monthly attendance reports ready
  - Gate: run `php artisan migrate:fresh --seed` and fix all failures

- [ ] **Milestone 5: Allowances**
  - Deliverables:
  - Site-wise allowance configuration
  - Employee extra allowances (food/night shift/other)
  - Site allowance report
  - Shippable outcome: allowance setup feeds payroll inputs
  - Gate: run `php artisan migrate:fresh --seed` and fix all failures

- [ ] **Milestone 6: Payroll + Salary Slips**
  - Deliverables:
  - Monthly payroll run
  - Salary computation rule: `Basic + Extra Allowances - Deductions`
  - Employee salary records + slip view
  - Salary lists (all / guards / site managers)
  - Shippable outcome: monthly payroll and slips generated
  - Gate: run `php artisan migrate:fresh --seed` and fix all failures

- [ ] **Milestone 7: Operations (Site Visits)**
  - Deliverables:
  - Site visit capture (manager, site, date, in/out time, remarks)
  - Manager-wise site visit report
  - Shippable outcome: operations visit logs and reporting
  - Gate: run `php artisan migrate:fresh --seed` and fix all failures

- [ ] **Milestone 8: Expenses**
  - Deliverables:
  - Expense capture for site/customer
  - Monthly/quarterly/yearly aggregation reports
  - Shippable outcome: financial ops reporting complete
  - Gate: run `php artisan migrate:fresh --seed` and fix all failures

- [ ] **Milestone 9: Role-Specific Portals + Profile Flows**
  - Deliverables:
  - Role dashboards (Leadership, Manager, Security Guard Manager, Guard, Customer)
  - Profile pages/edit flows by role
  - Customer portal: site list + site allowance config
  - Shippable outcome: role-specific UX aligned with scope
  - Gate: run `php artisan migrate:fresh --seed` and fix all failures

- [ ] **Milestone 10: Final Report Pack + Hardening**
  - Deliverables:
  - Roles list + role-permission matrix page
  - Validation pass (`FormRequest` on all write endpoints)
  - Authorization pass (middleware and policy checks)
  - Regression checks on all required reports
  - Shippable outcome: production-ready MVP scope
  - Gate: run `php artisan migrate:fresh --seed` and fix all failures

---

## Database Schema Proposal (Table List, Key Fields, Relationships)

- [ ] **`subscribers`**
  - Key fields: `id`, `name`, `code`, `status`, timestamps
  - Purpose: tenant root

- [ ] **`users`**
  - Key fields: `id`, `subscriber_id`, `name`, `username`, `email`, `password`, `status`, `last_login_at`, timestamps
  - Relationships: belongs to `subscribers`

- [ ] **`roles`**
  - Key fields: `id`, `subscriber_id`, `name`, `slug`, timestamps
  - Relationships: belongs to `subscribers` (tenant-specific role sets)

- [ ] **`permissions`**
  - Key fields: `id`, `name`, `slug`, `module`, timestamps
  - Relationships: global permission catalog

- [ ] **`role_permission`** (pivot)
  - Key fields: `role_id`, `permission_id`
  - Relationships: roles ↔ permissions (many-to-many)

- [ ] **`user_role`** (pivot)
  - Key fields: `user_id`, `role_id`
  - Relationships: users ↔ roles (many-to-many)

- [ ] **`customers`**
  - Key fields: `id`, `subscriber_id`, `user_id` (nullable for portal login mapping), `name`, `code`, `billing_address`, `status`, timestamps
  - Relationships: belongs to `subscribers`; optional belongs to `users`

- [ ] **`client_contacts`**
  - Key fields: `id`, `subscriber_id`, `customer_id`, `name`, `phone`, `email`, `designation`, `is_primary`, timestamps
  - Relationships: belongs to `customers`

- [ ] **`employees`**
  - Key fields: `id`, `subscriber_id`, `employee_code`, `name`, `phone`, `email`, `designation`, `employee_type` (guard/site_manager/manager/other), `join_date`, `basic_salary`, `status`, timestamps
  - Relationships: belongs to `subscribers`

- [ ] **`sites`**
  - Key fields: `id`, `subscriber_id`, `customer_id`, `site_code`, `name`, `address`, `status` (active/inactive), timestamps
  - Relationships: belongs to `customers`

- [ ] **`employee_site_assignments`**
  - Key fields: `id`, `subscriber_id`, `employee_id`, `site_id`, `assigned_from`, `assigned_to` (nullable), `is_active`, timestamps
  - Relationships: belongs to `employees`; belongs to `sites`
  - Bench support: employee is bench when no row exists with `is_active=1` and valid date range

- [ ] **`attendance_monthly_summaries`**
  - Key fields: `id`, `subscriber_id`, `employee_id`, `site_id` (nullable), `year`, `month`, `present_days`, `absent_days`, `attendance_percent`, timestamps
  - Relationships: belongs to `employees`; optional belongs to `sites`
  - Unique: (`subscriber_id`,`employee_id`,`year`,`month`)

- [ ] **`site_allowance_configs`**
  - Key fields: `id`, `subscriber_id`, `site_id`, `customer_id`, `allowance_type` (food/night_shift/other), `amount`, `effective_from`, `effective_to` (nullable), `is_active`, timestamps
  - Relationships: belongs to `sites`; belongs to `customers`

- [ ] **`employee_extra_allowances`**
  - Key fields: `id`, `subscriber_id`, `employee_id`, `site_id` (nullable), `year`, `month`, `allowance_type`, `amount`, `notes`, timestamps
  - Relationships: belongs to `employees`; optional belongs to `sites`

- [ ] **`salary_structures`**
  - Key fields: `id`, `subscriber_id`, `employee_id`, `basic_salary`, `pf_percent`, `esi_percent`, `other_deduction_fixed`, `effective_from`, `effective_to` (nullable), timestamps
  - Relationships: belongs to `employees`

- [ ] **`payroll_runs`**
  - Key fields: `id`, `subscriber_id`, `year`, `month`, `status` (draft/finalized), `processed_by`, `processed_at`, timestamps
  - Relationships: belongs to `users` (processed_by)

- [ ] **`salary_records`**
  - Key fields: `id`, `subscriber_id`, `payroll_run_id`, `employee_id`, `site_id` (nullable), `year`, `month`, `basic_amount`, `extra_allowance_total`, `deduction_total`, `net_salary`, `slip_no`, timestamps
  - Relationships: belongs to `payroll_runs`; belongs to `employees`; optional belongs to `sites`
  - Supports salary list + slip

- [ ] **`salary_record_components`**
  - Key fields: `id`, `subscriber_id`, `salary_record_id`, `component_type` (earning/deduction), `component_name`, `amount`, timestamps
  - Relationships: belongs to `salary_records`

- [ ] **`site_visits`**
  - Key fields: `id`, `subscriber_id`, `site_id`, `manager_employee_id`, `visit_date`, `in_time`, `out_time`, `remarks`, timestamps
  - Relationships: belongs to `sites`; belongs to `employees` (manager)

- [ ] **`expenses`**
  - Key fields: `id`, `subscriber_id`, `customer_id`, `site_id` (nullable), `expense_date`, `category`, `amount`, `description`, timestamps
  - Relationships: belongs to `customers`; optional belongs to `sites`
  - Reports via date grouping (monthly/quarterly/yearly)

### Relationship Notes (Critical)
- [ ] Every business table is tenant-scoped via `subscriber_id`.
- [ ] Foreign keys always include tenant-consistency checks at app layer (and DB constraints where feasible).
- [ ] Bench report derives from `employees` minus active `employee_site_assignments`.
- [ ] Active site list uses `sites.status = active` joined with `customers`.
- [ ] Attendance/payroll/expense reports are period-grouped from summary/run tables.

---

## Role → Permission Matrix

### Permission Catalog (suggested slugs)
- [ ] `profile.view_own`, `profile.update_own`
- [ ] `dashboard.view`
- [ ] `rbac.roles.view`, `rbac.roles.manage`, `rbac.permissions.view`, `rbac.matrix.view`
- [ ] `subscribers.view`, `subscribers.manage`
- [ ] `customers.view`, `customers.create`, `customers.update`, `customers.delete`
- [ ] `client_contacts.view`, `client_contacts.create`, `client_contacts.update`, `client_contacts.delete`
- [ ] `sites.view`, `sites.create`, `sites.update`, `sites.delete`, `sites.view_active`
- [ ] `employees.view`, `employees.create`, `employees.update`, `employees.delete`
- [ ] `assignments.view`, `assignments.create`, `assignments.update`, `assignments.close`, `bench.view`
- [ ] `attendance.view`, `attendance.create`, `attendance.update`, `attendance.monthly_report`
- [ ] `allowances.view`, `allowances.manage`, `allowances.site_report`
- [ ] `payroll.view`, `payroll.process`, `payroll.finalize`, `salary_slips.view`
- [ ] `salary_lists.view_all`, `salary_lists.view_guards`, `salary_lists.view_site_managers`
- [ ] `site_visits.view`, `site_visits.create`, `site_visits.update`, `site_visits.manager_report`
- [ ] `expenses.view`, `expenses.create`, `expenses.update`, `expenses.delete`, `expenses.reports`
- [ ] `reports.employee_profile`, `reports.attendance_monthly`, `reports.active_sites`, `reports.site_allowance`, `reports.expense_periodic`

### Role Mapping
- [ ] **Leadership**
  - All permissions (`*`)

- [ ] **Manager**
  - `dashboard.view`
  - `profile.view_own`, `profile.update_own`
  - `employees.view`
  - `sites.view`, `sites.view_active`
  - `assignments.view`, `bench.view`
  - `attendance.view`, `attendance.monthly_report`
  - `payroll.view`, `payroll.process`, `salary_slips.view`
  - `salary_lists.view_all`, `salary_lists.view_guards`, `salary_lists.view_site_managers`
  - `site_visits.view`, `site_visits.create`, `site_visits.update`, `site_visits.manager_report`
  - `reports.employee_profile`, `reports.attendance_monthly`, `reports.active_sites`

- [ ] **Security Guard Manager**
  - `dashboard.view`
  - `profile.view_own`, `profile.update_own`
  - `employees.view`
  - `sites.view`, `sites.view_active`
  - `attendance.view`, `attendance.monthly_report`
  - `salary_lists.view_guards`
  - `reports.attendance_monthly`, `reports.active_sites`

- [ ] **Security Guard**
  - `dashboard.view`
  - `profile.view_own`, `profile.update_own`
  - Optional later toggle:
  - `attendance.view` (own-only scope)
  - `salary_slips.view` (own-only scope)

- [ ] **Customer**
  - `dashboard.view`
  - `profile.view_own`, `profile.update_own`
  - `sites.view`, `sites.view_active` (customer-owned scope)
  - `allowances.view`, `allowances.manage` (customer-owned site scope)
  - `reports.site_allowance`, `reports.active_sites`
  - Optional controlled reporting:
  - `expenses.view`, `expenses.reports` (customer-owned scope)
