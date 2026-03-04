@extends('layouts.app')

@section('content')
    <h1 class="h4 mb-3">Create Employee</h1>
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('employees.store') }}" method="POST">
                @include('employees._form')
            </form>
        </div>
    </div>
@endsection
