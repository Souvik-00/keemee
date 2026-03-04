@extends('layouts.app')

@section('content')
    <h1 class="h4 mb-3">Edit Customer</h1>
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('customers.update', $customer) }}" method="POST">
                @method('PUT')
                @include('customers._form')
            </form>
        </div>
    </div>
@endsection
