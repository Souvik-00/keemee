@extends('layouts.app')

@section('content')
    <h1 class="h4 mb-3">Create Subscriber</h1>
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('subscribers.store') }}" method="POST">
                @include('subscribers._form')
            </form>
        </div>
    </div>
@endsection
