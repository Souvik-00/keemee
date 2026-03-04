<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\EmployeeExtraAllowance;
use App\Models\EmployeeSiteAssignment;
use App\Models\PayrollRun;
use App\Models\SalaryRecord;
use App\Models\SalaryRecordComponent;
use App\Models\SalaryStructure;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class PayrollProcessor
{
    public function run(int $subscriberId, int $year, int $month, User $processor, bool $forceReprocess = false): PayrollRun
    {
        return DB::transaction(function () use ($subscriberId, $year, $month, $processor, $forceReprocess): PayrollRun {
            $payrollRun = PayrollRun::withoutGlobalScopes()
                ->where('subscriber_id', $subscriberId)
                ->where('year', $year)
                ->where('month', $month)
                ->first();

            if ($payrollRun && $payrollRun->isFinalized() && ! $forceReprocess) {
                abort(422, 'Payroll run is finalized. Use explicit reprocess action to regenerate this month.');
            }

            if (! $payrollRun) {
                $payrollRun = PayrollRun::query()->create([
                    'subscriber_id' => $subscriberId,
                    'year' => $year,
                    'month' => $month,
                    'status' => 'draft',
                    'processed_by' => $processor->id,
                    'processed_at' => now(),
                ]);
            } else {
                $payrollRun->update([
                    'status' => 'draft',
                    'processed_by' => $processor->id,
                    'processed_at' => now(),
                ]);
            }

            SalaryRecordComponent::withoutGlobalScopes()
                ->whereIn('salary_record_id', function ($query) use ($payrollRun): void {
                    $query->select('id')
                        ->from('salary_records')
                        ->where('payroll_run_id', $payrollRun->id);
                })->delete();

            SalaryRecord::withoutGlobalScopes()->where('payroll_run_id', $payrollRun->id)->delete();

            $periodStart = Carbon::create($year, $month, 1)->startOfMonth();
            $periodEnd = Carbon::create($year, $month, 1)->endOfMonth();

            $employees = Employee::withoutGlobalScopes()
                ->where('subscriber_id', $subscriberId)
                ->where('status', 'active')
                ->orderBy('id')
                ->get();

            foreach ($employees as $employee) {
                $structure = SalaryStructure::withoutGlobalScopes()
                    ->where('subscriber_id', $subscriberId)
                    ->where('employee_id', $employee->id)
                    ->whereDate('effective_from', '<=', $periodEnd->toDateString())
                    ->where(function (Builder $query) use ($periodStart): void {
                        $query->whereNull('effective_to')
                            ->orWhereDate('effective_to', '>=', $periodStart->toDateString());
                    })
                    ->orderByDesc('effective_from')
                    ->first();

                $basic = (float) ($structure?->basic_salary ?? $employee->basic_salary);
                $pfPercent = (float) ($structure?->pf_percent ?? 0);
                $esiPercent = (float) ($structure?->esi_percent ?? 0);
                $otherDeduction = (float) ($structure?->other_deduction_fixed ?? 0);

                $extraAllowanceTotal = (float) EmployeeExtraAllowance::withoutGlobalScopes()
                    ->where('subscriber_id', $subscriberId)
                    ->where('employee_id', $employee->id)
                    ->where('year', $year)
                    ->where('month', $month)
                    ->sum('amount');

                $pfAmount = round(($basic * $pfPercent) / 100, 2);
                $esiAmount = round(($basic * $esiPercent) / 100, 2);
                $deductionTotal = round($pfAmount + $esiAmount + $otherDeduction, 2);
                $netSalary = round($basic + $extraAllowanceTotal - $deductionTotal, 2);

                $siteId = EmployeeSiteAssignment::withoutGlobalScopes()
                    ->where('subscriber_id', $subscriberId)
                    ->where('employee_id', $employee->id)
                    ->where('is_active', true)
                    ->whereDate('assigned_from', '<=', $periodEnd->toDateString())
                    ->where(function (Builder $query) use ($periodStart): void {
                        $query->whereNull('assigned_to')
                            ->orWhereDate('assigned_to', '>=', $periodStart->toDateString());
                    })
                    ->orderByDesc('assigned_from')
                    ->value('site_id');

                $salaryRecord = SalaryRecord::query()->create([
                    'subscriber_id' => $subscriberId,
                    'payroll_run_id' => $payrollRun->id,
                    'employee_id' => $employee->id,
                    'site_id' => $siteId,
                    'year' => $year,
                    'month' => $month,
                    'basic_amount' => $basic,
                    'extra_allowance_total' => $extraAllowanceTotal,
                    'deduction_total' => $deductionTotal,
                    'net_salary' => $netSalary,
                    'slip_no' => sprintf('SLIP-%04d%02d-%05d-%05d', $year, $month, $employee->id, $payrollRun->id),
                ]);

                $components = [
                    ['component_type' => 'earning', 'component_name' => 'Basic Salary', 'amount' => $basic],
                    ['component_type' => 'earning', 'component_name' => 'Extra Allowances', 'amount' => $extraAllowanceTotal],
                    ['component_type' => 'deduction', 'component_name' => 'PF', 'amount' => $pfAmount],
                    ['component_type' => 'deduction', 'component_name' => 'ESI', 'amount' => $esiAmount],
                    ['component_type' => 'deduction', 'component_name' => 'Other Deduction', 'amount' => $otherDeduction],
                ];

                foreach ($components as $component) {
                    SalaryRecordComponent::query()->create([
                        'subscriber_id' => $subscriberId,
                        'salary_record_id' => $salaryRecord->id,
                        'component_type' => $component['component_type'],
                        'component_name' => $component['component_name'],
                        'amount' => round((float) $component['amount'], 2),
                    ]);
                }
            }

            return $payrollRun->fresh(['salaryRecords.employee', 'salaryRecords.site', 'processor']);
        });
    }

    public function finalize(PayrollRun $payrollRun): PayrollRun
    {
        if ($payrollRun->isFinalized()) {
            return $payrollRun;
        }

        $payrollRun->update([
            'status' => 'finalized',
        ]);

        return $payrollRun->fresh();
    }
}
