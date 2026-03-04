@extends('layouts.app')

@section('content')
    <h1 class="h4 mb-3">Close Assignment</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="mb-3 text-muted small">
                <div><strong>Employee:</strong> {{ $assignment->employee?->name }} ({{ $assignment->employee?->employee_code }})</div>
                <div><strong>Site:</strong> {{ $assignment->site?->name }} / {{ $assignment->site?->customer?->name }}</div>
                <div><strong>Assigned From:</strong> {{ optional($assignment->assigned_from)->format('Y-m-d') }}</div>
            </div>

            <form method="POST" action="{{ route('employee-assignments.update', $assignment) }}">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label" for="assigned_to">Assigned To</label>
                        <input class="form-control" id="assigned_to" name="assigned_to" type="date" value="{{ old('assigned_to', now()->toDateString()) }}" required>
                    </div>
                </div>
                <div class="mt-4 d-flex gap-2">
                    <button class="btn btn-warning" type="submit">Close Assignment</button>
                    <a class="btn btn-outline-secondary" href="{{ route('employee-assignments.index') }}">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
