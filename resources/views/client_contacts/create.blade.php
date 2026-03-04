@extends('layouts.app')

@section('content')
    <h1 class="h4 mb-3">Create Client Contact</h1>
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('client-contacts.store') }}" method="POST">
                @include('client_contacts._form')
            </form>
        </div>
    </div>
@endsection
