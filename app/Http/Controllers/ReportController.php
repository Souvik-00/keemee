<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class ReportController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('checkPermission:bench.view', only: ['bench']),
            new Middleware('checkPermission:reports.active_sites', only: ['activeSites']),
            new Middleware('checkPermission:reports.employee_profile', only: ['employeeProfiles']),
        ];
    }

    public function bench(Request $request): View
    {
        $benchEmployees = Employee::query()
            ->with('subscriber')
            ->where('status', 'active')
            ->whereDoesntHave('activeAssignments')
            ->when($request->string('q')->toString(), function ($query, $search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('employee_code', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('reports.bench', compact('benchEmployees'));
    }

    public function activeSites(Request $request): View
    {
        $query = Site::query()
            ->with(['customer', 'subscriber'])
            ->where('status', 'active')
            ->when($request->string('q')->toString(), function ($query, $search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('site_code', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($customerQuery) use ($search): void {
                        $customerQuery->where('name', 'like', "%{$search}%");
                    });
            })
            ->orderBy('name');

        if ($request->user() && $request->user()->hasRole('customer')) {
            $ownedCustomerIds = Customer::query()
                ->where('user_id', $request->user()->id)
                ->pluck('id');

            $query->whereIn('customer_id', $ownedCustomerIds);
        }

        $activeSites = $query->paginate(12)->withQueryString();

        return view('reports.active-sites', compact('activeSites'));
    }

    public function employeeProfiles(Request $request): View
    {
        $employees = Employee::query()
            ->with(['subscriber', 'activeAssignments.site.customer'])
            ->when($request->string('q')->toString(), function ($query, $search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('employee_code', 'like', "%{$search}%")
                    ->orWhere('designation', 'like', "%{$search}%")
                    ->orWhere('employee_type', 'like', "%{$search}%");
            })
            ->when($request->string('employee_type')->toString(), fn ($q, $type) => $q->where('employee_type', $type))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('reports.employee-profiles', [
            'employees' => $employees,
        ]);
    }
}
