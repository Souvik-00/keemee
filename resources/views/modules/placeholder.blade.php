@extends('layouts.app')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <h1 class="h4 mb-2">{{ $module }}</h1>
            <p class="text-muted mb-0">This is a placeholder page for the {{ $module }} module. Implementation lands in upcoming milestones.</p>
        </div>
    </div>
@endsection
