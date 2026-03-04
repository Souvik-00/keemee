@extends('layouts.app')

@section('content')
    <h1 class="h4 mb-3">Create Site Allowance Config</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('site-allowance-configs.store') }}">
                @include('site_allowance_configs._form')
            </form>
        </div>
    </div>
@endsection
