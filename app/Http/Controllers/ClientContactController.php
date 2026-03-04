<?php

namespace App\Http\Controllers;

use App\Http\Requests\Common\ActionRequest;
use App\Http\Requests\ClientContact\StoreClientContactRequest;
use App\Http\Requests\ClientContact\UpdateClientContactRequest;
use App\Models\ClientContact;
use App\Models\Customer;
use App\Models\Subscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class ClientContactController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('checkPermission:client_contacts.view', only: ['index', 'show']),
            new Middleware('checkPermission:client_contacts.create', only: ['create', 'store']),
            new Middleware('checkPermission:client_contacts.update', only: ['edit', 'update']),
            new Middleware('checkPermission:client_contacts.delete', only: ['destroy']),
        ];
    }

    public function index(Request $request): View
    {
        $contacts = ClientContact::query()
            ->with('customer')
            ->when($request->string('q')->toString(), function ($query, $search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('client_contacts.index', [
            'contacts' => $contacts,
        ]);
    }

    public function create(): View
    {
        return view('client_contacts.create', [
            'customers' => Customer::query()->orderBy('name')->get(),
            'subscribers' => Subscriber::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreClientContactRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['subscriber_id'] = $this->resolveSubscriberId($request);

        $customerExists = Customer::query()
            ->whereKey($data['customer_id'])
            ->where('subscriber_id', $data['subscriber_id'])
            ->exists();

        abort_unless($customerExists, 422, 'Selected customer does not belong to this subscriber.');

        $data['is_primary'] = (bool) ($data['is_primary'] ?? false);
        ClientContact::query()->create($data);

        return redirect()
            ->route('client-contacts.index')
            ->with('success', 'Client contact created successfully.');
    }

    public function edit(ClientContact $clientContact): View
    {
        return view('client_contacts.edit', [
            'clientContact' => $clientContact,
            'customers' => Customer::query()->orderBy('name')->get(),
            'subscribers' => Subscriber::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateClientContactRequest $request, ClientContact $clientContact): RedirectResponse
    {
        $data = $request->validated();

        $customerExists = Customer::query()
            ->whereKey($data['customer_id'])
            ->where('subscriber_id', $clientContact->subscriber_id)
            ->exists();

        abort_unless($customerExists, 422, 'Selected customer does not belong to this subscriber.');

        $data['is_primary'] = (bool) ($data['is_primary'] ?? false);
        $clientContact->update($data);

        return redirect()
            ->route('client-contacts.index')
            ->with('success', 'Client contact updated successfully.');
    }

    public function destroy(ActionRequest $request, ClientContact $clientContact): RedirectResponse
    {
        $clientContact->delete();

        return redirect()
            ->route('client-contacts.index')
            ->with('success', 'Client contact deleted successfully.');
    }

    protected function resolveSubscriberId(Request $request): int
    {
        if ($request->user() && $request->user()->hasRole('leadership')) {
            return (int) $request->integer('subscriber_id');
        }

        return tenant_id() ?? (int) $request->user()->subscriber_id;
    }
}
