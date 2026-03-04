# Project Specification: Security Service Management System

## Overview
- Build a Security Service Management System using Laravel 12, Blade, Bootstrap 5, and MySQL.
- Use session-based authentication and RBAC with role/permission middleware.
- Enforce tenant scoping with `subscriber_id` across all business tables.

## Core Features and Modules
- Authentication + Authorization
  - Email/username login with password.
  - Role-based and permission-based access control.
- Tenant and Master Data
  - Subscribers (tenants), customers, client contacts, sites, and employees.
  - Active/inactive site management.
- Assignments and Bench Management
  - Employee-to-site assignments with active lifecycle.
  - Bench report for employees with no active site assignment.
- Attendance
  - Monthly employee attendance summary.
  - Present/absent counts and computed attendance percentage.
- Allowances
  - Site-wise allowance configuration.
  - Employee extra allowances (food/night shift/other).
- Payroll
  - Monthly payroll runs and salary records.
  - Salary component breakdown and monthly salary slips.
- Operations
  - Site visit tracking with date, in/out time, and manager mapping.
- Expenses
  - Site/customer expense capture.
  - Monthly, quarterly, and yearly report aggregation.

## Business Rules
- Bench employee:
  - Employee with no active assignment to any site.
- Active site:
  - Site status must be `active`.
- Attendance percentage:
  - `present / (present + absent) * 100`
- Monthly salary formula:
  - `Basic + Extra Allowances - Deductions (PF/ESI/etc)`

## Reports Required
- Salary lists (all / guards / site managers)
- Monthly salary slips
- Employee profile report
- Monthly attendance report
- Bench list
- Active sites list (with customer)
- Site allowance report
- Manager-wise site visit report
- Site/customer expense reports (monthly/quarterly/yearly)
- Roles list and role-permission matrix

## Roles Covered
- Leadership: full access
- Manager: profile, salary work, site visits, attendance/reporting scope
- Security Guard Manager: profile + guard attendance reporting
- Security Guard: profile (optional own attendance/salary later)
- Customer: profile + site list + site allowance configuration

## Tenant Visibility and Scope Enforcement
- Leadership tenant behavior:
  - Leadership can view all tenants by default.
  - Leadership can optionally switch active tenant context from the top navigation to scope listing/forms to one tenant.
- Non-leadership tenant behavior:
  - Data is always scoped to the logged-in user's `subscriber_id`.
- Enforcement strategy:
  - `SetTenantContext` middleware resolves tenant context per request.
  - `tenant_id()` helper and app tenant manager are available globally.
  - Tenant-scoped models apply a global tenant scope and auto-fill `subscriber_id` on create.
  - Controller-level checks prevent cross-tenant linking (for example, customer-contact/site tenant mismatches).

## Delivery Expectations
- Build in small, shippable milestones.
- Keep migrations/seeders aligned with all planned tables.
- Use validation, named routes, middleware enforcement, and flash messaging consistently.
- After each milestone, run `php artisan migrate:fresh --seed` and resolve failures before proceeding.
