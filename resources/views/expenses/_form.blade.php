@csrf
<div class="row g-3">
    @if(auth()->user()->hasRole('leadership') && !isset($expense))
        <div class="col-md-3">
            <label class="form-label" for="subscriber_id">Subscriber</label>
            <select class="form-select" id="subscriber_id" name="subscriber_id" required>
                <option value="">Select subscriber</option>
                @foreach($subscribers as $subscriber)
                    <option value="{{ $subscriber->id }}" @selected(old('subscriber_id', $activeTenantId ?? '') == $subscriber->id)>{{ $subscriber->name }}</option>
                @endforeach
            </select>
        </div>
    @endif

    <div class="col-md-3">
        <label class="form-label" for="customer_id">Customer</label>
        <select class="form-select" id="customer_id" name="customer_id" required>
            <option value="">Select customer</option>
            @foreach($customers as $customer)
                <option value="{{ $customer->id }}" @selected((string) old('customer_id', $expense->customer_id ?? '') === (string) $customer->id)>{{ $customer->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label" for="site_id">Site (optional)</label>
        <select class="form-select" id="site_id" name="site_id">
            <option value="">No site</option>
            @foreach($sites as $site)
                <option value="{{ $site->id }}" @selected((string) old('site_id', $expense->site_id ?? '') === (string) $site->id)>{{ $site->name }} ({{ $site->site_code }})</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label" for="expense_date">Expense Date</label>
        <input class="form-control" id="expense_date" name="expense_date" type="date" value="{{ old('expense_date', isset($expense->expense_date) ? $expense->expense_date->toDateString() : now()->toDateString()) }}" required>
    </div>

    <div class="col-md-3">
        <label class="form-label" for="category">Category</label>
        <select class="form-select" id="category" name="category" required>
            <option value="">Select category</option>
            @foreach($categories as $category)
                <option value="{{ $category }}" @selected(old('category', $expense->category ?? '') === $category)>{{ ucfirst($category) }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label" for="amount">Amount</label>
        <input class="form-control" id="amount" name="amount" type="number" min="0" step="0.01" value="{{ old('amount', $expense->amount ?? 0) }}" required>
    </div>

    <div class="col-md-6">
        <label class="form-label" for="description">Description</label>
        <textarea class="form-control" id="description" name="description" rows="2">{{ old('description', $expense->description ?? '') }}</textarea>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button class="btn btn-primary" type="submit">Save</button>
    <a class="btn btn-outline-secondary" href="{{ route('expenses.index') }}">Cancel</a>
</div>
