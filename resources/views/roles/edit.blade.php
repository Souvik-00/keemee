@extends('layouts.app')

@section('content')
    <h1 class="h4 mb-3">Edit Role Permissions: {{ $role->name }}</h1>

    <form method="POST" action="{{ route('roles.update', $role) }}">
        @csrf
        @method('PUT')

        @foreach($permissionsByModule as $module => $permissions)
            <div class="card shadow-sm mb-3">
                <div class="card-header">{{ ucfirst($module) }}</div>
                <div class="card-body">
                    <div class="row g-2">
                        @foreach($permissions as $permission)
                            <div class="col-md-4">
                                <label class="form-check-label d-flex gap-2 align-items-start">
                                    <input class="form-check-input mt-1" type="checkbox" name="permission_ids[]" value="{{ $permission->id }}" @checked($role->permissions->contains('id', $permission->id))>
                                    <span>
                                        <span class="d-block">{{ $permission->name }}</span>
                                        <code class="small">{{ $permission->slug }}</code>
                                    </span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach

        <div class="d-flex gap-2">
            <button class="btn btn-primary" type="submit">Save Permissions</button>
            <a class="btn btn-outline-secondary" href="{{ route('roles.matrix') }}">Cancel</a>
        </div>
    </form>
@endsection
