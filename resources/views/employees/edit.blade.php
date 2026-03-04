@extends('layouts.app')

@section('content')
    <h1 class="h4 mb-3">Edit Employee</h1>
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('employees.update', $employee) }}" method="POST">
                @method('PUT')
                @include('employees._form')
            </form>
        </div>
    </div>
@endsection
