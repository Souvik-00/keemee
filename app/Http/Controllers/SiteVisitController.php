<?php

namespace App\Http\Controllers;

use App\Http\Requests\SiteVisit\StoreSiteVisitRequest;
use App\Http\Requests\SiteVisit\UpdateSiteVisitRequest;
use App\Models\Employee;
use App\Models\Site;
use App\Models\SiteVisit;
use App\Models\Subscriber;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class SiteVisitController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('checkPermission:site_visits.view', only: ['index']),
            new Middleware('checkPermission:site_visits.create', only: ['create', 'store']),
            new Middleware('checkPermission:site_visits.update', only: ['edit', 'update']),
            new Middleware('checkPermission:site_visits.manager_report', only: ['managerReport']),
        ];
    }

    public function index(Request $request): View
    {
        $visits = SiteVisit::query()
            ->with(['site.customer', 'managerEmployee', 'subscriber'])
            ->when($request->integer('site_id'), fn (Builder $q, $siteId) => $q->where('site_id', $siteId))
            ->when($request->integer('manager_employee_id'), fn (Builder $q, $managerId) => $q->where('manager_employee_id', $managerId))
            ->orderByDesc('visit_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('site_visits.index', [
            'visits' => $visits,
            'sites' => $this->accessibleSites(),
            'managers' => $this->managerEmployees(),
        ]);
    }

    public function create(): View
    {
        return view('site_visits.create', [
            'subscribers' => Subscriber::query()->orderBy('name')->get(),
            'sites' => $this->accessibleSites(),
            'managers' => $this->managerEmployees(),
        ]);
    }

    public function store(StoreSiteVisitRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $subscriberId = $this->resolveSubscriberId($request);

        $site = Site::withoutGlobalScopes()->findOrFail((int) $data['site_id']);
        $manager = Employee::withoutGlobalScopes()->findOrFail((int) $data['manager_employee_id']);

        $this->assertTenantConsistency($subscriberId, $site, $manager);

        SiteVisit::query()->create([
            'subscriber_id' => $subscriberId,
            'site_id' => $site->id,
            'manager_employee_id' => $manager->id,
            'visit_date' => $data['visit_date'],
            'in_time' => $data['in_time'],
            'out_time' => $data['out_time'] ?? null,
            'remarks' => $data['remarks'] ?? null,
        ]);

        return redirect()->route('site-visits.index')
            ->with('success', 'Site visit logged successfully.');
    }

    public function edit(SiteVisit $siteVisit): View
    {
        return view('site_visits.edit', [
            'siteVisit' => $siteVisit,
            'subscribers' => Subscriber::query()->orderBy('name')->get(),
            'sites' => $this->accessibleSites(),
            'managers' => $this->managerEmployees(),
        ]);
    }

    public function update(UpdateSiteVisitRequest $request, SiteVisit $siteVisit): RedirectResponse
    {
        $data = $request->validated();

        $site = Site::withoutGlobalScopes()->findOrFail((int) $data['site_id']);
        $manager = Employee::withoutGlobalScopes()->findOrFail((int) $data['manager_employee_id']);

        $this->assertTenantConsistency((int) $siteVisit->subscriber_id, $site, $manager);

        $siteVisit->update([
            'site_id' => $site->id,
            'manager_employee_id' => $manager->id,
            'visit_date' => $data['visit_date'],
            'in_time' => $data['in_time'],
            'out_time' => $data['out_time'] ?? null,
            'remarks' => $data['remarks'] ?? null,
        ]);

        return redirect()->route('site-visits.index')
            ->with('success', 'Site visit updated successfully.');
    }

    public function managerReport(Request $request): View
    {
        $dateFrom = $request->date('date_from')?->toDateString();
        $dateTo = $request->date('date_to')?->toDateString();

        $query = SiteVisit::query()
            ->with(['site.customer', 'managerEmployee', 'subscriber'])
            ->when($request->integer('manager_employee_id'), fn (Builder $q, $managerId) => $q->where('manager_employee_id', $managerId))
            ->when($request->integer('site_id'), fn (Builder $q, $siteId) => $q->where('site_id', $siteId));

        if ($dateFrom) {
            $query->whereDate('visit_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('visit_date', '<=', $dateTo);
        }

        $visits = $query
            ->orderByDesc('visit_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $summary = (clone $query)
            ->selectRaw('manager_employee_id, COUNT(*) as total_visits')
            ->groupBy('manager_employee_id')
            ->with('managerEmployee')
            ->get();

        return view('site_visits.report', [
            'visits' => $visits,
            'summary' => $summary,
            'sites' => $this->accessibleSites(),
            'managers' => $this->managerEmployees(),
        ]);
    }

    protected function resolveSubscriberId(Request $request): int
    {
        if ($request->user() && $request->user()->hasRole('leadership')) {
            return (int) $request->integer('subscriber_id');
        }

        return tenant_id() ?? (int) $request->user()->subscriber_id;
    }

    protected function assertTenantConsistency(int $subscriberId, Site $site, Employee $manager): void
    {
        abort_unless($site->subscriber_id === $subscriberId, 422, 'Selected site does not belong to subscriber.');
        abort_unless($manager->subscriber_id === $subscriberId, 422, 'Selected manager does not belong to subscriber.');
        abort_unless($manager->employee_type === 'manager', 422, 'Selected employee is not a manager.');
    }

    protected function accessibleSites()
    {
        return Site::query()->where('status', 'active')->orderBy('name')->get();
    }

    protected function managerEmployees()
    {
        return Employee::query()
            ->where('status', 'active')
            ->where('employee_type', 'manager')
            ->orderBy('name')
            ->get();
    }
}
