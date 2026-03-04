@extends('layouts.app')

@section('content')
    <h1 class="h4 mb-3">Create Employee Assignment</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('employee-assignments.store') }}">
                @csrf
                <div class="row g-3">
                    @if(auth()->user()->hasRole('leadership'))
                        <div class="col-md-4">
                            <label class="form-label" for="subscriber_id">Subscriber</label>
                            <select class="form-select" id="subscriber_id" name="subscriber_id" required>
                                <option value="">Select subscriber</option>
                                @foreach($subscribers as $subscriber)
                                    <option value="{{ $subscriber->id }}" @selected(old('subscriber_id', $activeTenantId ?? '') == $subscriber->id)>{{ $subscriber->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="col-md-4">
                        <label class="form-label" for="employee_id">Employee</label>
                        <select class="form-select" id="employee_id" name="employee_id" required>
                            <option value="">Select employee</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" @selected((string) old('employee_id') === (string) $employee->id)>
                                    {{ $employee->name }} ({{ $employee->employee_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="site_id">Site</label>
                        <select class="form-select" id="site_id" name="site_id" required>
                            <option value="">Select site</option>
                            @foreach($sites as $site)
                                <option value="{{ $site->id }}" @selected((string) old('site_id') === (string) $site->id)>
                                    {{ $site->name }} ({{ $site->site_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="assigned_from">Assigned From</label>
                        <input class="form-control" id="assigned_from" name="assigned_from" type="date" value="{{ old('assigned_from', now()->toDateString()) }}" required>
                    </div>
                </div>
                <div class="mt-4 d-flex gap-2">
                    <button class="btn btn-primary" type="submit">Assign</button>
                    <a class="btn btn-outline-secondary" href="{{ route('employee-assignments.index') }}">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
