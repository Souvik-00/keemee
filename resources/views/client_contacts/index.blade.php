@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Client Contacts</h1>
        @if(auth()->user()->hasPermission('client_contacts.create'))
            <a href="{{ route('client-contacts.create') }}" class="btn btn-primary btn-sm">Add Contact</a>
        @endif
    </div>

    <form class="mb-3" method="GET" action="{{ route('client-contacts.index') }}">
        <div class="input-group">
            <input type="text" class="form-control" name="q" value="{{ request('q') }}" placeholder="Search contacts">
            <button class="btn btn-outline-secondary" type="submit">Search</button>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Customer</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Primary</th>
                    <th class="text-end">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($contacts as $contact)
                    <tr>
                        <td>{{ $contact->name }}</td>
                        <td>{{ $contact->customer?->name ?? '-' }}</td>
                        <td>{{ $contact->phone ?: '-' }}</td>
                        <td>{{ $contact->email ?: '-' }}</td>
                        <td>{{ $contact->is_primary ? 'Yes' : 'No' }}</td>
                        <td class="text-end">
                            @if(auth()->user()->hasPermission('client_contacts.update'))
                                <a href="{{ route('client-contacts.edit', $contact) }}" class="btn btn-outline-primary btn-sm">Edit</a>
                            @endif
                            @if(auth()->user()->hasPermission('client_contacts.delete'))
                                <form action="{{ route('client-contacts.destroy', $contact) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete this contact?')">Delete</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No contacts found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $contacts->links() }}</div>
@endsection
