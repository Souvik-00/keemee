@extends('layouts.app')

@section('content')
    <h1 class="h4 mb-3">Reports Hub</h1>
    <p class="text-muted">All report entry points with supported filters.</p>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Report</th>
                        <th>Supported Filters</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($links as $link)
                        <tr>
                            <td>{{ $link['label'] }}</td>
                            <td>
                                @if(empty($link['filters']))
                                    <span class="text-muted">None</span>
                                @else
                                    {{ implode(', ', $link['filters']) }}
                                @endif
                            </td>
                            <td class="text-end">
                                <a class="btn btn-outline-primary btn-sm" href="{{ $link['url'] }}">Open</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">No reports are available for your role.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
