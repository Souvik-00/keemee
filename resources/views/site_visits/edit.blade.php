@extends('layouts.app')

@section('content')
    <h1 class="h4 mb-3">Edit Site Visit</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('site-visits.update', $siteVisit) }}">
                @method('PUT')
                @include('site_visits._form')
            </form>
        </div>
    </div>
@endsection
