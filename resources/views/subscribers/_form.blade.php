@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="name">Name</label>
        <input class="form-control" id="name" name="name" type="text" value="{{ old('name', $subscriber->name ?? '') }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label" for="code">Code</label>
        <input class="form-control" id="code" name="code" type="text" value="{{ old('code', $subscriber->code ?? '') }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label" for="status">Status</label>
        <select class="form-select" id="status" name="status" required>
            @foreach(['active' => 'Active', 'inactive' => 'Inactive'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $subscriber->status ?? 'active') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="mt-4 d-flex gap-2">
    <button class="btn btn-primary" type="submit">Save</button>
    <a class="btn btn-outline-secondary" href="{{ route('subscribers.index') }}">Cancel</a>
</div>
