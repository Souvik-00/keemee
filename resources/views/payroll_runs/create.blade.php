@extends('layouts.app')

@section('content')
    <h1 class="h4 mb-3">Run Payroll</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('payroll-runs.store') }}">
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

                    <div class="col-md-2">
                        <label class="form-label" for="year">Year</label>
                        <input class="form-control" id="year" name="year" type="number" min="2000" max="2100" value="{{ old('year', now()->year) }}" required>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label" for="month">Month</label>
                        <input class="form-control" id="month" name="month" type="number" min="1" max="12" value="{{ old('month', now()->month) }}" required>
                    </div>

                    <div class="col-md-12">
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" id="force_reprocess" name="force_reprocess" value="1" @checked(old('force_reprocess'))>
                            <label class="form-check-label" for="force_reprocess">
                                Explicit action: allow reprocessing even if run is already finalized for this month.
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button class="btn btn-primary" type="submit">Process Payroll</button>
                    <a class="btn btn-outline-secondary" href="{{ route('payroll-runs.index') }}">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
