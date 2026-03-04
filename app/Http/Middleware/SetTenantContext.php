<?php

namespace App\Http\Middleware;

use App\Models\Subscriber;
use App\Support\Tenancy\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class SetTenantContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = app('tenant');

        if (! $tenant instanceof TenantManager) {
            abort(500, 'Tenant manager is not configured.');
        }

        $user = $request->user();

        if ($user) {
            if ($user->hasRole('leadership')) {
                $selectedTenantId = $request->session()->get('active_subscriber_id');

                if ($selectedTenantId && Subscriber::query()->whereKey($selectedTenantId)->exists()) {
                    $tenant->setId((int) $selectedTenantId);
                } else {
                    $request->session()->forget('active_subscriber_id');
                }

                View::share('tenantSwitcherSubscribers', Subscriber::query()->orderBy('name')->get(['id', 'name']));
            } else {
                if (! $user->subscriber_id) {
                    abort(403, 'Your account is not assigned to a subscriber.');
                }

                $tenant->setId((int) $user->subscriber_id);
            }
        }

        View::share('activeTenantId', $tenant->id());

        return $next($request);
    }
}
