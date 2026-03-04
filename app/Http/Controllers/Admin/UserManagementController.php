<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminUser\ResetAdminUserPasswordRequest;
use App\Http\Requests\AdminUser\StoreAdminUserRequest;
use App\Http\Requests\AdminUser\ToggleAdminUserStatusRequest;
use App\Http\Requests\AdminUser\UpdateAdminUserRequest;
use App\Models\Customer;
use App\Models\Role;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->with(['subscriber', 'roles'])
            ->when($request->string('search')->toString(), function (Builder $query, string $search): void {
                $query->where(function (Builder $searchQuery) use ($search): void {
                    $searchQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('subscriber_id'), function (Builder $query) use ($request): void {
                $value = $request->input('subscriber_id');

                if ($value === 'none') {
                    $query->whereNull('subscriber_id');

                    return;
                }

                $query->where('subscriber_id', (int) $value);
            })
            ->when($request->integer('role_id'), function (Builder $query, int $roleId): void {
                $query->whereHas('roles', fn (Builder $roleQuery) => $roleQuery->where('roles.id', $roleId));
            })
            ->when($request->string('status')->toString(), fn (Builder $query, string $status) => $query->where('status', $status))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'subscribers' => Subscriber::query()->orderBy('name')->get(),
            'roles' => Role::query()->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create', [
            'subscribers' => Subscriber::query()->orderBy('name')->get(),
            'roles' => Role::query()->orderBy('name')->get(),
            'customers' => Customer::withoutGlobalScopes()->with('subscriber')->orderBy('name')->get(),
        ]);
    }

    public function store(StoreAdminUserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = User::query()->create([
            'subscriber_id' => $data['subscriber_id'] ?? null,
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['temp_password']),
            'status' => $data['status'],
            'must_change_password' => true,
        ]);

        $roleIds = collect($data['role_ids'])->map(fn ($id) => (int) $id)->all();
        $user->roles()->sync($roleIds);

        $this->syncCustomerLink($user, $roleIds, isset($data['customer_id']) ? (int) $data['customer_id'] : null);

        return redirect()->route('admin.users.index')
            ->with('success', "User created successfully. Temporary password: {$data['temp_password']}");
    }

    public function show(User $user): View
    {
        return view('admin.users.show', [
            'managedUser' => $user->load(['subscriber', 'roles']),
            'linkedCustomer' => Customer::withoutGlobalScopes()->where('user_id', $user->id)->first(),
        ]);
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'managedUser' => $user->load('roles'),
            'subscribers' => Subscriber::query()->orderBy('name')->get(),
            'roles' => Role::query()->orderBy('name')->get(),
            'customers' => Customer::withoutGlobalScopes()->with('subscriber')->orderBy('name')->get(),
            'linkedCustomerId' => Customer::withoutGlobalScopes()->where('user_id', $user->id)->value('id'),
        ]);
    }

    public function update(UpdateAdminUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        $user->update([
            'subscriber_id' => $data['subscriber_id'] ?? null,
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'status' => $data['status'],
        ]);

        $roleIds = collect($data['role_ids'])->map(fn ($id) => (int) $id)->all();
        $user->roles()->sync($roleIds);

        $this->syncCustomerLink($user, $roleIds, isset($data['customer_id']) ? (int) $data['customer_id'] : null);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function resetPasswordForm(User $user): View
    {
        return view('admin.users.reset-password', [
            'managedUser' => $user,
        ]);
    }

    public function resetPassword(ResetAdminUserPasswordRequest $request, User $user): RedirectResponse
    {
        $password = $request->validated('new_password');

        $user->forceFill([
            'password' => Hash::make($password),
            'must_change_password' => true,
        ])->save();

        return redirect()->route('admin.users.index')
            ->with('success', "Password reset successfully. Temporary password: {$password}");
    }

    public function toggleStatus(ToggleAdminUserStatusRequest $request, User $user): RedirectResponse
    {
        $status = $request->validated('status');

        if ((int) $user->id === (int) $request->user()->id && $status === 'inactive') {
            return back()->withErrors(['status' => 'You cannot disable your own account.']);
        }

        $user->update(['status' => $status]);

        return back()->with('success', "User status updated to {$status}.");
    }

    /**
     * @param array<int, int> $roleIds
     */
    protected function syncCustomerLink(User $user, array $roleIds, ?int $customerId): void
    {
        $roleSlugs = Role::query()
            ->whereIn('id', $roleIds)
            ->pluck('slug')
            ->all();

        if (! in_array('customer', $roleSlugs, true)) {
            Customer::withoutGlobalScopes()
                ->where('user_id', $user->id)
                ->update(['user_id' => null]);

            return;
        }

        if (! $customerId) {
            return;
        }

        $customer = Customer::withoutGlobalScopes()->findOrFail($customerId);

        abort_unless((int) $customer->subscriber_id === (int) $user->subscriber_id, 422, 'Customer and user subscriber must match.');
        abort_unless(! $customer->user_id || (int) $customer->user_id === (int) $user->id, 422, 'Customer is already linked to another user.');

        Customer::withoutGlobalScopes()
            ->where('user_id', $user->id)
            ->where('id', '!=', $customer->id)
            ->update(['user_id' => null]);

        $customer->update(['user_id' => $user->id]);
    }
}
