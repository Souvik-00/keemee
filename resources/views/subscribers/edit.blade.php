@extends('layouts.app')

@section('content')
    <h1 class="h4 mb-3">Edit Subscriber</h1>
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('subscribers.update', $subscriber) }}" method="POST">
                @method('PUT')
                @include('subscribers._form')
            </form>
        </div>
    </div>
@endsection
