@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">User Details</h1>
        <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.users.edit', $managedUser) }}">Edit</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6"><div class="text-muted small">Name</div><div>{{ $managedUser->name }}</div></div>
                <div class="col-md-6"><div class="text-muted small">Username</div><div>{{ $managedUser->username }}</div></div>
                <div class="col-md-6"><div class="text-muted small">Email</div><div>{{ $managedUser->email }}</div></div>
                <div class="col-md-6"><div class="text-muted small">Subscriber</div><div>{{ $managedUser->subscriber?->name ?? 'N/A' }}</div></div>
                <div class="col-md-6"><div class="text-muted small">Status</div><div>{{ ucfirst($managedUser->status) }}</div></div>
                <div class="col-md-6"><div class="text-muted small">Must Change Password</div><div>{{ $managedUser->must_change_password ? 'Yes' : 'No' }}</div></div>
                <div class="col-md-6"><div class="text-muted small">Last Login</div><div>{{ optional($managedUser->last_login_at)->format('Y-m-d H:i') ?? 'Never' }}</div></div>
                <div class="col-md-6"><div class="text-muted small">Roles</div><div>{{ $managedUser->roles->pluck('name')->implode(', ') ?: 'N/A' }}</div></div>
                <div class="col-md-12"><div class="text-muted small">Linked Customer</div><div>{{ $linkedCustomer?->name ?? 'N/A' }}</div></div>
            </div>
        </div>
    </div>
@endsection
