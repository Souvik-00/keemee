@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Payroll Runs</h1>
        @if(auth()->user()->hasPermission('payroll.process'))
            <a href="{{ route('payroll-runs.create') }}" class="btn btn-primary btn-sm">Run Payroll</a>
        @endif
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Period</th>
                        <th>Subscriber</th>
                        <th>Status</th>
                        <th>Processed By</th>
                        <th>Processed At</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($runs as $run)
                        <tr>
                            <td>{{ $run->year }}-{{ str_pad((string) $run->month, 2, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $run->subscriber?->name ?? '-' }}</td>
                            <td><span class="badge text-bg-{{ $run->status === 'finalized' ? 'success' : 'warning' }}">{{ ucfirst($run->status) }}</span></td>
                            <td>{{ $run->processor?->name ?? '-' }}</td>
                            <td>{{ optional($run->processed_at)->format('Y-m-d H:i') ?: '-' }}</td>
                            <td class="text-end">
                                <a href="{{ route('payroll-runs.show', $run) }}" class="btn btn-outline-primary btn-sm">Open</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No payroll runs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $runs->links() }}</div>
@endsection
