<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Keemee Security Service Management' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { min-height: calc(100vh - 56px); }
        .sidebar .nav-link.active { font-weight: 600; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('dashboard.index') }}">Keemee SSMS</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                @auth
                    @if(auth()->user()->hasRole('leadership'))
                        <li class="nav-item me-lg-3">
                            <form action="{{ route('tenant.switch') }}" method="POST" class="d-flex gap-2 align-items-center">
                                @csrf
                                <select name="subscriber_id" class="form-select form-select-sm">
                                    <option value="all" @if(empty($activeTenantId)) selected @endif>All Tenants</option>
                                    @foreach(($tenantSwitcherSubscribers ?? []) as $tenantOption)
                                        <option value="{{ $tenantOption->id }}" @if(($activeTenantId ?? null) === $tenantOption->id) selected @endif>
                                            {{ $tenantOption->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button class="btn btn-outline-light btn-sm" type="submit">Switch</button>
                            </form>
                        </li>
                    @endif
                    <li class="nav-item me-lg-3 text-white-50 small">{{ auth()->user()->name }}</li>
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
                        </form>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        @auth
            <aside class="col-md-3 col-lg-2 bg-white border-end sidebar p-3">
                <div class="fw-semibold text-uppercase text-muted small mb-2">Modules</div>
                <div class="nav flex-column nav-pills gap-1">
                    <a class="nav-link @if(request()->routeIs('dashboard.*')) active @endif" href="{{ route('dashboard.index') }}">Dashboard</a>
                    @if(auth()->user()->hasPermission('profile.view_own'))
                        <a class="nav-link @if(request()->routeIs('profile.*')) active @endif" href="{{ route('profile.show') }}">My Profile</a>
                    @endif
                    <a class="nav-link @if(request()->routeIs('modules.auth-rbac')) active @endif" href="{{ route('modules.auth-rbac') }}">Auth + RBAC</a>
                    <a class="nav-link @if(request()->routeIs('modules.masters')) active @endif" href="{{ route('modules.masters') }}">Masters</a>
                    <a class="nav-link @if(request()->routeIs('modules.attendance')) active @endif" href="{{ route('modules.attendance') }}">Attendance</a>
                    <a class="nav-link @if(request()->routeIs('modules.payroll')) active @endif" href="{{ route('modules.payroll') }}">Payroll</a>
                    <a class="nav-link @if(request()->routeIs('modules.allowances')) active @endif" href="{{ route('modules.allowances') }}">Allowances</a>
                    <a class="nav-link @if(request()->routeIs('modules.operations')) active @endif" href="{{ route('modules.operations') }}">Operations</a>
                    <a class="nav-link @if(request()->routeIs('modules.expenses')) active @endif" href="{{ route('modules.expenses') }}">Expenses</a>
                    <a class="nav-link @if(request()->routeIs('modules.reports')) active @endif" href="{{ route('modules.reports') }}">Reports</a>
                </div>
                <hr>
                <div class="fw-semibold text-uppercase text-muted small mb-2">Master Data</div>
                <div class="nav flex-column nav-pills gap-1">
                    @if(auth()->user()->hasPermission('subscribers.view'))
                        <a class="nav-link @if(request()->routeIs('subscribers.*')) active @endif" href="{{ route('subscribers.index') }}">Subscribers</a>
                    @endif
                    @if(auth()->user()->hasRole('leadership'))
                        <a class="nav-link @if(request()->routeIs('admin.users.*')) active @endif" href="{{ route('admin.users.index') }}">Users Management</a>
                    @endif
                    @if(auth()->user()->hasPermission('rbac.roles.view'))
                        <a class="nav-link @if(request()->routeIs('roles.index')) active @endif" href="{{ route('roles.index') }}">Roles</a>
                    @endif
                    @if(auth()->user()->hasPermission('rbac.matrix.view'))
                        <a class="nav-link @if(request()->routeIs('roles.matrix')) active @endif" href="{{ route('roles.matrix') }}">Roles Matrix</a>
                    @endif
                    @if(auth()->user()->hasPermission('customers.view'))
                        <a class="nav-link @if(request()->routeIs('customers.*')) active @endif" href="{{ route('customers.index') }}">Customers</a>
                    @endif
                    @if(auth()->user()->hasPermission('client_contacts.view'))
                        <a class="nav-link @if(request()->routeIs('client-contacts.*')) active @endif" href="{{ route('client-contacts.index') }}">Client Contacts</a>
                    @endif
                    @if(auth()->user()->hasPermission('sites.view'))
                        <a class="nav-link @if(request()->routeIs('sites.*')) active @endif" href="{{ route('sites.index') }}">Sites</a>
                    @endif
                    @if(auth()->user()->hasPermission('employees.view'))
                        <a class="nav-link @if(request()->routeIs('employees.*')) active @endif" href="{{ route('employees.index') }}">Employees</a>
                    @endif
                    @if(auth()->user()->hasPermission('assignments.view'))
                        <a class="nav-link @if(request()->routeIs('employee-assignments.*')) active @endif" href="{{ route('employee-assignments.index') }}">Assignments</a>
                    @endif
                    @if(auth()->user()->hasPermission('attendance.view'))
                        <a class="nav-link @if(request()->routeIs('attendance-monthly-summaries.*')) active @endif" href="{{ route('attendance-monthly-summaries.index') }}">Attendance Entry</a>
                    @endif
                    @if(auth()->user()->hasPermission('allowances.view'))
                        <a class="nav-link @if(request()->routeIs('site-allowance-configs.*')) active @endif" href="{{ route('site-allowance-configs.index') }}">Site Allowances</a>
                    @endif
                    @if(auth()->user()->hasPermission('allowances.view'))
                        <a class="nav-link @if(request()->routeIs('employee-extra-allowances.*')) active @endif" href="{{ route('employee-extra-allowances.index') }}">Extra Allowances</a>
                    @endif
                    @if(auth()->user()->hasPermission('payroll.view'))
                        <a class="nav-link @if(request()->routeIs('salary-structures.*')) active @endif" href="{{ route('salary-structures.index') }}">Salary Structures</a>
                    @endif
                    @if(auth()->user()->hasPermission('payroll.view'))
                        <a class="nav-link @if(request()->routeIs('payroll-runs.*')) active @endif" href="{{ route('payroll-runs.index') }}">Payroll Runs</a>
                    @endif
                    @if(auth()->user()->hasPermission('site_visits.view'))
                        <a class="nav-link @if(request()->routeIs('site-visits.*')) active @endif" href="{{ route('site-visits.index') }}">Site Visits</a>
                    @endif
                    @if(auth()->user()->hasPermission('expenses.view'))
                        <a class="nav-link @if(request()->routeIs('expenses.*')) active @endif" href="{{ route('expenses.index') }}">Expenses</a>
                    @endif
                </div>
                <hr>
                <div class="fw-semibold text-uppercase text-muted small mb-2">Reports</div>
                <div class="nav flex-column nav-pills gap-1">
                    @if(auth()->user()->hasPermission('dashboard.view'))
                        <a class="nav-link @if(request()->routeIs('reports.hub')) active @endif" href="{{ route('reports.hub') }}">Reports Hub</a>
                    @endif
                    @if(auth()->user()->hasPermission('reports.employee_profile'))
                        <a class="nav-link @if(request()->routeIs('reports.employee-profiles')) active @endif" href="{{ route('reports.employee-profiles') }}">Employee Profiles</a>
                    @endif
                    @if(auth()->user()->hasPermission('attendance.monthly_report'))
                        <a class="nav-link @if(request()->routeIs('attendance.report')) active @endif" href="{{ route('attendance.report') }}">Attendance Monthly</a>
                    @endif
                    @if(auth()->user()->hasPermission('bench.view'))
                        <a class="nav-link @if(request()->routeIs('reports.bench')) active @endif" href="{{ route('reports.bench') }}">Bench Report</a>
                    @endif
                    @if(auth()->user()->hasPermission('reports.active_sites'))
                        <a class="nav-link @if(request()->routeIs('reports.active-sites')) active @endif" href="{{ route('reports.active-sites') }}">Active Sites</a>
                    @endif
                    @if(auth()->user()->hasPermission('reports.site_allowance'))
                        <a class="nav-link @if(request()->routeIs('site-allowance-report')) active @endif" href="{{ route('site-allowance-report') }}">Site Allowance</a>
                    @endif
                    @if(auth()->user()->hasPermission('payroll.view'))
                        <a class="nav-link @if(request()->routeIs('salary-reports.list')) active @endif" href="{{ route('salary-reports.list') }}">Salary List</a>
                    @endif
                    @if(auth()->user()->hasPermission('site_visits.manager_report'))
                        <a class="nav-link @if(request()->routeIs('site-visits.report')) active @endif" href="{{ route('site-visits.report') }}">Manager Visits</a>
                    @endif
                    @if(auth()->user()->hasPermission('expenses.reports'))
                        <a class="nav-link @if(request()->routeIs('expenses.site-report')) active @endif" href="{{ route('expenses.site-report') }}">Site Expenses</a>
                        <a class="nav-link @if(request()->routeIs('expenses.customer-report')) active @endif" href="{{ route('expenses.customer-report') }}">Customer Expenses</a>
                    @endif
                </div>
            </aside>
            <main class="col-md-9 col-lg-10 p-4">
                @include('partials.flash-messages')
                @yield('content')
            </main>
        @else
            <main class="col-12 p-4">
                @include('partials.flash-messages')
                @yield('content')
            </main>
        @endauth
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
