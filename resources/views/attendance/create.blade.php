@extends('layouts.app')

@section('content')
    <h1 class="h4 mb-3">Create Attendance Summary</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('attendance-monthly-summaries.store') }}">
                @include('attendance._form')
            </form>
        </div>
    </div>
@endsection
