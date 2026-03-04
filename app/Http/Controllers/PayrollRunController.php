<?php

namespace App\Http\Controllers;

use App\Http\Requests\Payroll\FinalizePayrollRunRequest;
use App\Http\Requests\Payroll\RunPayrollRequest;
use App\Models\PayrollRun;
use App\Models\SalaryRecord;
use App\Models\Subscriber;
use App\Services\PayrollProcessor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class PayrollRunController extends Controller implements HasMiddleware
{
    public function __construct(private readonly PayrollProcessor $processor)
    {
    }

    public static function middleware(): array
    {
        return [
            new Middleware('checkPermission:payroll.view', only: ['index', 'show', 'create']),
            new Middleware('checkPermission:payroll.process', only: ['store']),
            new Middleware('checkPermission:payroll.finalize', only: ['finalize']),
        ];
    }

    public function index(Request $request): View
    {
        $runs = PayrollRun::query()
            ->with(['subscriber', 'processor'])
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->paginate(15);

        return view('payroll_runs.index', compact('runs'));
    }

    public function create(): View
    {
        return view('payroll_runs.create', [
            'subscribers' => Subscriber::query()->orderBy('name')->get(),
        ]);
    }

    public function store(RunPayrollRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $subscriberId = $this->resolveSubscriberId($request);

        $run = $this->processor->run(
            $subscriberId,
            (int) $data['year'],
            (int) $data['month'],
            $request->user(),
            (bool) ($data['force_reprocess'] ?? false)
        );

        return redirect()->route('payroll-runs.show', $run)
            ->with('success', 'Payroll processed successfully in draft mode.');
    }

    public function show(PayrollRun $payrollRun): View
    {
        $salaryRecords = SalaryRecord::query()
            ->with(['employee', 'site', 'components'])
            ->where('payroll_run_id', $payrollRun->id)
            ->orderBy('employee_id')
            ->paginate(20);

        return view('payroll_runs.show', [
            'payrollRun' => $payrollRun->load(['subscriber', 'processor']),
            'salaryRecords' => $salaryRecords,
        ]);
    }

    public function finalize(FinalizePayrollRunRequest $request, PayrollRun $payrollRun): RedirectResponse
    {
        $this->processor->finalize($payrollRun);

        return redirect()->route('payroll-runs.show', $payrollRun)
            ->with('success', 'Payroll run finalized. Editing/reprocessing now requires explicit reprocess action.');
    }

    protected function resolveSubscriberId(Request $request): int
    {
        if ($request->user() && $request->user()->hasRole('leadership')) {
            return (int) $request->integer('subscriber_id');
        }

        return tenant_id() ?? (int) $request->user()->subscriber_id;
    }
}
