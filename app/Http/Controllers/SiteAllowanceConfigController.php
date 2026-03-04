<?php

namespace App\Http\Controllers;

use App\Http\Requests\Common\ActionRequest;
use App\Http\Requests\SiteAllowanceConfig\StoreSiteAllowanceConfigRequest;
use App\Http\Requests\SiteAllowanceConfig\UpdateSiteAllowanceConfigRequest;
use App\Models\Customer;
use App\Models\Site;
use App\Models\SiteAllowanceConfig;
use App\Models\Subscriber;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class SiteAllowanceConfigController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('checkPermission:allowances.view', only: ['index', 'report']),
            new Middleware('checkPermission:allowances.manage', except: ['index', 'report']),
            new Middleware('checkPermission:reports.site_allowance', only: ['report']),
        ];
    }

    public function index(Request $request): View
    {
        $query = SiteAllowanceConfig::query()->with(['site.customer', 'customer', 'subscriber']);

        if ($this->isCustomerUser($request)) {
            $query->whereIn('customer_id', $this->ownedCustomerIds($request));
        }

        $configs = $query
            ->when($request->integer('site_id'), fn ($q, $siteId) => $q->where('site_id', $siteId))
            ->when($request->string('allowance_type')->toString(), fn ($q, $type) => $q->where('allowance_type', $type))
            ->orderByDesc('effective_from')
            ->paginate(15)
            ->withQueryString();

        return view('site_allowance_configs.index', [
            'configs' => $configs,
            'sites' => $this->accessibleSites($request),
        ]);
    }

    public function create(Request $request): View
    {
        return view('site_allowance_configs.create', [
            'subscribers' => Subscriber::query()->orderBy('name')->get(),
            'sites' => $this->accessibleSites($request),
            'customers' => $this->accessibleCustomers($request),
        ]);
    }

    public function store(StoreSiteAllowanceConfigRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $subscriberId = $this->resolveSubscriberId($request);

        $site = Site::withoutGlobalScopes()->findOrFail((int) $data['site_id']);
        $customer = Customer::withoutGlobalScopes()->findOrFail((int) $data['customer_id']);

        $this->assertOwnership($request, $site, $customer, $subscriberId);

        SiteAllowanceConfig::query()->create([
            'subscriber_id' => $subscriberId,
            'site_id' => $site->id,
            'customer_id' => $customer->id,
            'allowance_type' => $data['allowance_type'],
            'amount' => (float) $data['amount'],
            'effective_from' => $data['effective_from'],
            'effective_to' => $data['effective_to'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? false),
        ]);

        return redirect()->route('site-allowance-configs.index')
            ->with('success', 'Site allowance config created successfully.');
    }

    public function edit(SiteAllowanceConfig $siteAllowanceConfig, Request $request): View
    {
        $this->assertExistingRecordAccess($request, $siteAllowanceConfig);

        return view('site_allowance_configs.edit', [
            'config' => $siteAllowanceConfig,
            'subscribers' => Subscriber::query()->orderBy('name')->get(),
            'sites' => $this->accessibleSites($request),
            'customers' => $this->accessibleCustomers($request),
        ]);
    }

    public function update(UpdateSiteAllowanceConfigRequest $request, SiteAllowanceConfig $siteAllowanceConfig): RedirectResponse
    {
        $this->assertExistingRecordAccess($request, $siteAllowanceConfig);

        $data = $request->validated();
        $site = Site::withoutGlobalScopes()->findOrFail((int) $data['site_id']);
        $customer = Customer::withoutGlobalScopes()->findOrFail((int) $data['customer_id']);

        $this->assertOwnership($request, $site, $customer, (int) $siteAllowanceConfig->subscriber_id);

        $siteAllowanceConfig->update([
            'site_id' => $site->id,
            'customer_id' => $customer->id,
            'allowance_type' => $data['allowance_type'],
            'amount' => (float) $data['amount'],
            'effective_from' => $data['effective_from'],
            'effective_to' => $data['effective_to'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? false),
        ]);

        return redirect()->route('site-allowance-configs.index')
            ->with('success', 'Site allowance config updated successfully.');
    }

    public function destroy(SiteAllowanceConfig $siteAllowanceConfig, ActionRequest $request): RedirectResponse
    {
        $this->assertExistingRecordAccess($request, $siteAllowanceConfig);

        $siteAllowanceConfig->delete();

        return redirect()->route('site-allowance-configs.index')
            ->with('success', 'Site allowance config deleted successfully.');
    }

    public function report(Request $request): View
    {
        $year = (int) ($request->integer('year') ?: now()->year);
        $month = (int) ($request->integer('month') ?: now()->month);

        $periodStart = now()->setDate($year, $month, 1)->startOfMonth()->toDateString();
        $periodEnd = now()->setDate($year, $month, 1)->endOfMonth()->toDateString();

        $query = SiteAllowanceConfig::query()
            ->with(['site.customer', 'customer', 'subscriber'])
            ->whereDate('effective_from', '<=', $periodEnd)
            ->where(function ($q) use ($periodStart): void {
                $q->whereNull('effective_to')
                    ->orWhereDate('effective_to', '>=', $periodStart);
            });

        if ($this->isCustomerUser($request)) {
            $query->whereIn('customer_id', $this->ownedCustomerIds($request));
        }

        $query
            ->when($request->integer('site_id'), fn ($q, $siteId) => $q->where('site_id', $siteId))
            ->when($request->string('allowance_type')->toString(), fn ($q, $type) => $q->where('allowance_type', $type));

        $configs = $query
            ->orderBy('site_id')
            ->orderBy('allowance_type')
            ->paginate(20)
            ->withQueryString();

        $totalsByType = (clone $query)
            ->selectRaw('allowance_type, SUM(amount) as total_amount')
            ->groupBy('allowance_type')
            ->get();

        return view('site_allowance_configs.report', [
            'configs' => $configs,
            'totalsByType' => $totalsByType,
            'sites' => $this->accessibleSites($request),
            'filters' => compact('year', 'month'),
        ]);
    }

    protected function isCustomerUser(Request $request): bool
    {
        return (bool) ($request->user() && $request->user()->hasRole('customer'));
    }

    /**
     * @return array<int, int>
     */
    protected function ownedCustomerIds(Request $request): array
    {
        return Customer::query()
            ->where('user_id', $request->user()->id)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    protected function accessibleSites(Request $request): Collection
    {
        $query = Site::query()->where('status', 'active')->orderBy('name');

        if ($this->isCustomerUser($request)) {
            $query->whereIn('customer_id', $this->ownedCustomerIds($request));
        }

        return $query->get();
    }

    protected function accessibleCustomers(Request $request): Collection
    {
        $query = Customer::query()->orderBy('name');

        if ($this->isCustomerUser($request)) {
            $query->whereIn('id', $this->ownedCustomerIds($request));
        }

        return $query->get();
    }

    protected function resolveSubscriberId(Request $request): int
    {
        if ($request->user() && $request->user()->hasRole('leadership')) {
            return (int) $request->integer('subscriber_id');
        }

        return tenant_id() ?? (int) $request->user()->subscriber_id;
    }

    protected function assertOwnership(Request $request, Site $site, Customer $customer, int $subscriberId): void
    {
        abort_unless($site->subscriber_id === $subscriberId, 422, 'Site does not belong to selected subscriber.');
        abort_unless($customer->subscriber_id === $subscriberId, 422, 'Customer does not belong to selected subscriber.');
        abort_unless($site->customer_id === $customer->id, 422, 'Site must belong to selected customer.');

        if ($this->isCustomerUser($request)) {
            $ownedCustomerIds = $this->ownedCustomerIds($request);

            abort_unless(in_array((int) $customer->id, $ownedCustomerIds, true), 403, 'You can manage allowances only for your own customer sites.');
        }
    }

    protected function assertExistingRecordAccess(Request $request, SiteAllowanceConfig $config): void
    {
        if ($this->isCustomerUser($request)) {
            $ownedCustomerIds = $this->ownedCustomerIds($request);

            abort_unless(in_array((int) $config->customer_id, $ownedCustomerIds, true), 403, 'You can manage allowances only for your own customer sites.');
        }
    }
}
