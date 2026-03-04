<?php

namespace App\Http\Controllers;

use App\Http\Requests\Common\ActionRequest;
use App\Http\Requests\Site\StoreSiteRequest;
use App\Http\Requests\Site\UpdateSiteRequest;
use App\Models\Customer;
use App\Models\Site;
use App\Models\Subscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class SiteController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('checkPermission:sites.view', only: ['index', 'show']),
            new Middleware('checkPermission:sites.create', only: ['create', 'store']),
            new Middleware('checkPermission:sites.update', only: ['edit', 'update']),
            new Middleware('checkPermission:sites.delete', only: ['destroy']),
        ];
    }

    public function index(Request $request): View
    {
        $query = Site::query()
            ->with('customer')
            ->when($request->string('q')->toString(), function ($query, $search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('site_code', 'like', "%{$search}%");
            })
            ->orderBy('name');

        if ($request->user() && $request->user()->hasRole('customer')) {
            $ownedCustomerIds = Customer::query()
                ->where('user_id', $request->user()->id)
                ->pluck('id');

            $query->whereIn('customer_id', $ownedCustomerIds);
        }

        $sites = $query->paginate(10)->withQueryString();

        return view('sites.index', [
            'sites' => $sites,
        ]);
    }

    public function create(): View
    {
        return view('sites.create', [
            'customers' => Customer::query()->orderBy('name')->get(),
            'subscribers' => Subscriber::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreSiteRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['subscriber_id'] = $this->resolveSubscriberId($request);

        $customerExists = Customer::query()
            ->whereKey($data['customer_id'])
            ->where('subscriber_id', $data['subscriber_id'])
            ->exists();

        abort_unless($customerExists, 422, 'Selected customer does not belong to this subscriber.');

        Site::query()->create($data);

        return redirect()
            ->route('sites.index')
            ->with('success', 'Site created successfully.');
    }

    public function edit(Site $site): View
    {
        return view('sites.edit', [
            'site' => $site,
            'customers' => Customer::query()->orderBy('name')->get(),
            'subscribers' => Subscriber::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateSiteRequest $request, Site $site): RedirectResponse
    {
        $data = $request->validated();

        $customerExists = Customer::query()
            ->whereKey($data['customer_id'])
            ->where('subscriber_id', $site->subscriber_id)
            ->exists();

        abort_unless($customerExists, 422, 'Selected customer does not belong to this subscriber.');

        $site->update($data);

        return redirect()
            ->route('sites.index')
            ->with('success', 'Site updated successfully.');
    }

    public function destroy(ActionRequest $request, Site $site): RedirectResponse
    {
        $site->delete();

        return redirect()
            ->route('sites.index')
            ->with('success', 'Site deleted successfully.');
    }

    protected function resolveSubscriberId(Request $request): int
    {
        if ($request->user() && $request->user()->hasRole('leadership')) {
            return (int) $request->integer('subscriber_id');
        }

        return tenant_id() ?? (int) $request->user()->subscriber_id;
    }
}
