@csrf
<div class="row g-3">
    @if(auth()->user()->hasRole('leadership') && !isset($clientContact))
        <div class="col-md-4">
            <label class="form-label" for="subscriber_id">Subscriber</label>
            <select class="form-select" id="subscriber_id" name="subscriber_id" required>
                <option value="">Select subscriber</option>
                @foreach($subscribers as $subscriber)
                    <option value="{{ $subscriber->id }}" @selected(old('subscriber_id', $activeTenantId ?? '') == $subscriber->id)>{{ $subscriber->name }}</option>
                @endforeach
            </select>
        </div>
    @endif
    <div class="col-md-4">
        <label class="form-label" for="customer_id">Customer</label>
        <select class="form-select" id="customer_id" name="customer_id" required>
            <option value="">Select customer</option>
            @foreach($customers as $customer)
                <option value="{{ $customer->id }}" @selected((string) old('customer_id', $clientContact->customer_id ?? '') === (string) $customer->id)>{{ $customer->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="name">Name</label>
        <input class="form-control" id="name" name="name" type="text" value="{{ old('name', $clientContact->name ?? '') }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="phone">Phone</label>
        <input class="form-control" id="phone" name="phone" type="text" value="{{ old('phone', $clientContact->phone ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="email">Email</label>
        <input class="form-control" id="email" name="email" type="email" value="{{ old('email', $clientContact->email ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="designation">Designation</label>
        <input class="form-control" id="designation" name="designation" type="text" value="{{ old('designation', $clientContact->designation ?? '') }}">
    </div>
    <div class="col-12 form-check ms-2 mt-2">
        <input class="form-check-input" id="is_primary" name="is_primary" type="checkbox" value="1" @checked(old('is_primary', $clientContact->is_primary ?? false))>
        <label class="form-check-label" for="is_primary">Primary contact</label>
    </div>
</div>
<div class="mt-4 d-flex gap-2">
    <button class="btn btn-primary" type="submit">Save</button>
    <a class="btn btn-outline-secondary" href="{{ route('client-contacts.index') }}">Cancel</a>
</div>
