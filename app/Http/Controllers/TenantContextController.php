<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tenant\SwitchTenantRequest;
use App\Models\Subscriber;
use Illuminate\Http\RedirectResponse;

class TenantContextController extends Controller
{
    public function switch(SwitchTenantRequest $request): RedirectResponse
    {
        abort_unless($request->user() && $request->user()->hasRole('leadership'), 403);

        $subscriberId = $request->validated('subscriber_id');

        if (! $subscriberId || $subscriberId === 'all') {
            $request->session()->forget('active_subscriber_id');

            return back()->with('success', 'Tenant context set to all tenants.');
        }

        $subscriber = Subscriber::query()->findOrFail((int) $subscriberId);
        $request->session()->put('active_subscriber_id', $subscriber->id);

        return back()->with('success', "Tenant context switched to {$subscriber->name}.");
    }
}
