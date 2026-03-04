@extends('layouts.app')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header">
            <h1 class="h5 mb-0">Edit User</h1>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.users.update', $managedUser) }}">
                @method('PUT')
                @include('admin.users._form')
            </form>
        </div>
    </div>
@endsection
