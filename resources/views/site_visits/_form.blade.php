@csrf
<div class="row g-3">
    @if(auth()->user()->hasRole('leadership') && !isset($siteVisit))
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
        <label class="form-label" for="site_id">Site</label>
        <select class="form-select" id="site_id" name="site_id" required>
            <option value="">Select site</option>
            @foreach($sites as $site)
                <option value="{{ $site->id }}" @selected((string) old('site_id', $siteVisit->site_id ?? '') === (string) $site->id)>{{ $site->name }} ({{ $site->site_code }})</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label" for="manager_employee_id">Manager</label>
        <select class="form-select" id="manager_employee_id" name="manager_employee_id" required>
            <option value="">Select manager</option>
            @foreach($managers as $manager)
                <option value="{{ $manager->id }}" @selected((string) old('manager_employee_id', $siteVisit->manager_employee_id ?? '') === (string) $manager->id)>{{ $manager->name }} ({{ $manager->employee_code }})</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2">
        <label class="form-label" for="visit_date">Visit Date</label>
        <input class="form-control" id="visit_date" name="visit_date" type="date" value="{{ old('visit_date', isset($siteVisit->visit_date) ? $siteVisit->visit_date->toDateString() : now()->toDateString()) }}" required>
    </div>

    <div class="col-md-2">
        <label class="form-label" for="in_time">In Time</label>
        <input class="form-control" id="in_time" name="in_time" type="time" value="{{ old('in_time', $siteVisit->in_time ?? '') }}" required>
    </div>

    <div class="col-md-2">
        <label class="form-label" for="out_time">Out Time</label>
        <input class="form-control" id="out_time" name="out_time" type="time" value="{{ old('out_time', $siteVisit->out_time ?? '') }}">
    </div>

    <div class="col-md-12">
        <label class="form-label" for="remarks">Remarks</label>
        <textarea class="form-control" id="remarks" name="remarks" rows="3">{{ old('remarks', $siteVisit->remarks ?? '') }}</textarea>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button class="btn btn-primary" type="submit">Save</button>
    <a class="btn btn-outline-secondary" href="{{ route('site-visits.index') }}">Cancel</a>
</div>
