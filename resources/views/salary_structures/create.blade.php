@extends('layouts.app')

@section('content')
    <h1 class="h4 mb-3">Create Salary Structure</h1>
    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('salary-structures.store') }}">
                @include('salary_structures._form')
            </form>
        </div>
    </div>
@endsection
