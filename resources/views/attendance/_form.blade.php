@csrf
<div class="row g-3">
    @if(auth()->user()->hasRole('leadership') && !isset($summary))
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
        <label class="form-label" for="employee_id">Employee</label>
        <select class="form-select" id="employee_id" name="employee_id" required>
            <option value="">Select employee</option>
            @foreach($employees as $employee)
                <option value="{{ $employee->id }}" @selected((string) old('employee_id', $summary->employee_id ?? '') === (string) $employee->id)>
                    {{ $employee->name }} ({{ $employee->employee_code }})
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label" for="site_id">Site (optional)</label>
        <select class="form-select" id="site_id" name="site_id">
            <option value="">None</option>
            @foreach($sites as $site)
                <option value="{{ $site->id }}" @selected((string) old('site_id', $summary->site_id ?? '') === (string) $site->id)>
                    {{ $site->name }} ({{ $site->site_code }})
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-1">
        <label class="form-label" for="year">Year</label>
        <input class="form-control" id="year" name="year" type="number" min="2000" max="2100" value="{{ old('year', $summary->year ?? now()->year) }}" required>
    </div>

    <div class="col-md-1">
        <label class="form-label" for="month">Month</label>
        <input class="form-control" id="month" name="month" type="number" min="1" max="12" value="{{ old('month', $summary->month ?? now()->month) }}" required>
    </div>

    <div class="col-md-2">
        <label class="form-label" for="present_days">Present Days</label>
        <input class="form-control js-present" id="present_days" name="present_days" type="number" min="0" max="31" value="{{ old('present_days', $summary->present_days ?? 0) }}" required>
    </div>

    <div class="col-md-2">
        <label class="form-label" for="absent_days">Absent Days</label>
        <input class="form-control js-absent" id="absent_days" name="absent_days" type="number" min="0" max="31" value="{{ old('absent_days', $summary->absent_days ?? 0) }}" required>
    </div>

    <div class="col-md-2">
        <label class="form-label" for="attendance_percent_preview">Attendance %</label>
        <input class="form-control" id="attendance_percent_preview" type="text" value="{{ number_format((float) old('attendance_percent', $summary->attendance_percent ?? 0), 2) }}" readonly>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button class="btn btn-primary" type="submit">Save</button>
    <a class="btn btn-outline-secondary" href="{{ route('attendance-monthly-summaries.index') }}">Cancel</a>
</div>

<script>
    (() => {
        const presentInput = document.querySelector('.js-present');
        const absentInput = document.querySelector('.js-absent');
        const preview = document.getElementById('attendance_percent_preview');

        if (!presentInput || !absentInput || !preview) {
            return;
        }

        const updatePercent = () => {
            const present = Number(presentInput.value || 0);
            const absent = Number(absentInput.value || 0);
            const total = present + absent;

            if (total <= 0) {
                preview.value = '0.00';
                return;
            }

            preview.value = ((present / total) * 100).toFixed(2);
        };

        presentInput.addEventListener('input', updatePercent);
        absentInput.addEventListener('input', updatePercent);
        updatePercent();
    })();
</script>
