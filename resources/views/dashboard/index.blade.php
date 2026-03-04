@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">{{ str_replace('_', ' ', ucfirst($role)) }} Portal</h1>
            <p class="text-muted mb-0">Role-specific dashboard with scoped quick actions.</p>
        </div>
        @if(auth()->user()->hasPermission('profile.view_own'))
            <a href="{{ route('profile.show') }}" class="btn btn-outline-primary btn-sm">My Profile</a>
        @endif
    </div>

    <div class="row g-3 mb-4">
        @foreach($cards as $card)
            <div class="col-sm-6 col-lg-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="text-muted small">{{ $card['label'] }}</div>
                        <div class="h5 mb-0">{{ $card['value'] }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header">Quick Links</div>
        <div class="card-body">
            <div class="d-flex gap-2 flex-wrap">
                @forelse($quickLinks as $link)
                    <a href="{{ $link['url'] }}" class="btn btn-outline-primary btn-sm">{{ $link['label'] }}</a>
                @empty
                    <span class="text-muted">No links available for your role.</span>
                @endforelse
            </div>
        </div>
    </div>

    @if($role === 'leadership')
        <div class="alert alert-light border">Leadership has full-access visibility, tenant switcher, and cross-module controls.</div>
    @endif

    @if($role === 'manager')
        <div class="alert alert-light border">Manager portal focuses on assignments, attendance, payroll operations, and site visits.</div>
    @endif

    @if($role === 'security_guard_manager')
        <div class="alert alert-light border">Security Guard Manager portal is limited to guard attendance and active site oversight.</div>
    @endif

    @if($role === 'security_guard')
        <div class="alert alert-light border">Security Guard portal currently supports own profile. Own attendance/salary views are future toggles.</div>
    @endif

    @if($role === 'customer')
        <div class="card shadow-sm mb-4">
            <div class="card-header">My Sites (Customer-scoped)</div>
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Site</th>
                            <th>Code</th>
                            <th>Status</th>
                            <th>Customer</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customerSites as $site)
                            <tr>
                                <td>{{ $site->name }}</td>
                                <td>{{ $site->site_code }}</td>
                                <td>{{ ucfirst($site->status) }}</td>
                                <td>{{ $site->customer?->name }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No owned sites found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header">My Active Site Allowance Configs (Customer-scoped)</div>
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Site</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Effective</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customerAllowances as $allowance)
                            <tr>
                                <td>{{ $allowance->site?->name }}</td>
                                <td>{{ str_replace('_', ' ', ucfirst($allowance->allowance_type)) }}</td>
                                <td>{{ number_format((float) $allowance->amount, 2) }}</td>
                                <td>{{ optional($allowance->effective_from)->format('Y-m-d') }} to {{ optional($allowance->effective_to)->format('Y-m-d') ?: 'Open' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No active allowances found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection
