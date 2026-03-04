@extends('layouts.app')

@section('content')
    <h1 class="h4 mb-3">Edit Attendance Summary</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('attendance-monthly-summaries.update', $summary) }}">
                @method('PUT')
                @include('attendance._form')
            </form>
        </div>
    </div>
@endsection
