@extends('layouts.app')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header">
            <h1 class="h5 mb-0">Edit Expense</h1>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('expenses.update', $expense) }}">
                @method('PUT')
                @include('expenses._form')
            </form>
        </div>
    </div>
@endsection
