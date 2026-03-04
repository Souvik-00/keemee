@extends('layouts.app')

@section('content')
    <h1 class="h4 mb-3">Create Employee Extra Allowance</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('employee-extra-allowances.store') }}">
                @include('employee_extra_allowances._form')
            </form>
        </div>
    </div>
@endsection
