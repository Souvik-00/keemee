@extends('layouts.app')

@section('content')
    <h1 class="h4 mb-3">Edit Client Contact</h1>
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('client-contacts.update', $clientContact) }}" method="POST">
                @method('PUT')
                @include('client_contacts._form')
            </form>
        </div>
    </div>
@endsection
