@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">My Profile</h1>
        @if(auth()->user()->hasPermission('profile.update_own'))
            <a class="btn btn-primary btn-sm" href="{{ route('profile.edit') }}">Edit Profile</a>
        @endif
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="text-muted small">Name</div>
                    <div>{{ $user->name }}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted small">Username</div>
                    <div>{{ $user->username }}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted small">Email</div>
                    <div>{{ $user->email }}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted small">Status</div>
                    <div>{{ ucfirst($user->status) }}</div>
                </div>
                <div class="col-12">
                    <div class="text-muted small">Roles</div>
                    <div class="d-flex gap-2 flex-wrap mt-1">
                        @forelse($user->roles as $role)
                            <span class="badge text-bg-secondary">{{ $role->name }}</span>
                        @empty
                            <span class="text-muted">No roles assigned.</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
