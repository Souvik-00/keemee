<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'status',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function clientContacts(): HasMany
    {
        return $this->hasMany(ClientContact::class);
    }

    public function sites(): HasMany
    {
        return $this->hasMany(Site::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function employeeAssignments(): HasMany
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

    public function salaryStructures(): HasMany
    {
        return $this->hasMany(SalaryStructure::class);
    }

    public function payrollRuns(): HasMany
    {
        return $this->hasMany(PayrollRun::class);
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
