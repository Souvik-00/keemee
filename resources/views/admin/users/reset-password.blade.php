@extends('layouts.app')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header">
            <h1 class="h5 mb-0">Reset Password: {{ $managedUser->name }}</h1>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.users.reset-password', $managedUser) }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="new_password">New Temporary Password</label>
                        <input class="form-control" id="new_password" name="new_password" type="password" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="new_password_confirmation">Confirm Password</label>
                        <input class="form-control" id="new_password_confirmation" name="new_password_confirmation" type="password" required>
                    </div>
                </div>
                <div class="mt-4 d-flex gap-2">
                    <button class="btn btn-warning" type="submit">Reset Password</button>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.users.index') }}">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
