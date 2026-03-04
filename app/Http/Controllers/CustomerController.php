<?php

namespace App\Http\Controllers;

use App\Http\Requests\Common\ActionRequest;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Models\Customer;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class CustomerController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('checkPermission:customers.view', only: ['index', 'show']),
            new Middleware('checkPermission:customers.create', only: ['create', 'store']),
            new Middleware('checkPermission:customers.update', only: ['edit', 'update']),
            new Middleware('checkPermission:customers.delete', only: ['destroy']),
        ];
    }

    public function index(Request $request): View
    {
        $customers = Customer::query()
            ->with(['subscriber', 'user'])
            ->when($request->string('q')->toString(), function ($query, $search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('customers.index', compact('customers'));
    }

    public function create(): View
    {
        return view('customers.create', [
            'subscribers' => Subscriber::query()->orderBy('name')->get(),
            'users' => User::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['subscriber_id'] = $this->resolveSubscriberId($request);

        $this->assertUserBelongsToSubscriber($data['user_id'] ?? null, $data['subscriber_id']);
        Customer::query()->create($data);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer created successfully.');
    }

    public function edit(Customer $customer): View
    {
        return view('customers.edit', [
            'customer' => $customer,
            'subscribers' => Subscriber::query()->orderBy('name')->get(),
            'users' => User::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $data = $request->validated();
        $this->assertUserBelongsToSubscriber($data['user_id'] ?? null, $customer->subscriber_id);

        $customer->update($data);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    public function destroy(ActionRequest $request, Customer $customer): RedirectResponse
    {
        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer deleted successfully.');
    }

    protected function resolveSubscriberId(Request $request): int
    {
        if ($request->user() && $request->user()->hasRole('leadership')) {
            return (int) $request->integer('subscriber_id');
        }

        return tenant_id() ?? (int) $request->user()->subscriber_id;
    }

    protected function assertUserBelongsToSubscriber(?int $userId, int $subscriberId): void
    {
        if (! $userId) {
            return;
        }

        $exists = User::query()
            ->whereKey($userId)
            ->where('subscriber_id', $subscriberId)
            ->exists();

        abort_unless($exists, 422, 'Selected portal user does not belong to this subscriber.');
    }
}
