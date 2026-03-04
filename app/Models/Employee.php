<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;
    use BelongsToTenant;

    protected $fillable = [
        'subscriber_id',
        'employee_code',
        'name',
        'phone',
        'email',
        'designation',
        'employee_type',
        'join_date',
        'basic_salary',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'join_date' => 'date',
            'basic_salary' => 'decimal:2',
        ];
    }

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(Subscriber::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(EmployeeSiteAssignment::class);
    }

    public function activeAssignments(): HasMany
    {
        return $this->hasMany(EmployeeSiteAssignment::class)
            ->where('is_active', true)
            ->where(function ($query): void {
                $query->whereNull('assigned_to')
                    ->orWhereDate('assigned_to', '>=', now()->toDateString());
            });
    }

    public function attendanceMonthlySummaries(): HasMany
    {
        return $this->hasMany(AttendanceMonthlySummary::class);
    }

    public function extraAllowances(): HasMany
    {
        return $this->hasMany(EmployeeExtraAllowance::class);
    }

    public function salaryStructures(): HasMany
    {
        return $this->hasMany(SalaryStructure::class);
    }

    public function salaryRecords(): HasMany
    {
        return $this->hasMany(SalaryRecord::class);
    }

    public function managedSiteVisits(): HasMany
    {
        return $this->hasMany(SiteVisit::class, 'manager_employee_id');
    }
}
