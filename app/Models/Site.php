<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Site extends Model
{
    use HasFactory;
    use BelongsToTenant;

    protected $fillable = [
        'subscriber_id',
        'customer_id',
        'site_code',
        'name',
        'address',
        'status',
    ];

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(Subscriber::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(EmployeeSiteAssignment::class);
    }

    public function attendanceMonthlySummaries(): HasMany
    {
        return $this->hasMany(AttendanceMonthlySummary::class);
    }

    public function siteAllowanceConfigs(): HasMany
    {
        return $this->hasMany(SiteAllowanceConfig::class);
    }

    public function employeeExtraAllowances(): HasMany
    {
        return $this->hasMany(EmployeeExtraAllowance::class);
    }

    public function salaryRecords(): HasMany
    {
        return $this->hasMany(SalaryRecord::class);
    }

    public function siteVisits(): HasMany
    {
        return $this->hasMany(SiteVisit::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}
