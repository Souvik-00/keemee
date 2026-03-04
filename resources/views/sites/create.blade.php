@extends('layouts.app')

@section('content')
    <h1 class="h4 mb-3">Create Site</h1>
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('sites.store') }}" method="POST">
                @include('sites._form')
            </form>
        </div>
    </div>
@endsection
