<?php

namespace App\Http\Controllers;

use App\Http\Requests\Role\UpdateRolePermissionsRequest;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class RoleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('checkPermission:rbac.roles.view', only: ['index']),
            new Middleware('checkPermission:rbac.matrix.view', only: ['matrix']),
            new Middleware('checkPermission:rbac.roles.manage', only: ['edit', 'update']),
        ];
    }

    public function index(Request $request): View
    {
        $roles = Role::query()
            ->withCount('users')
            ->with('permissions')
            ->when($request->string('q')->toString(), function ($query, $search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->get();

        return view('roles.index', [
            'roles' => $roles,
        ]);
    }

    public function matrix(Request $request): View
    {
        $permissions = Permission::query()
            ->when($request->string('module')->toString(), fn ($q, $module) => $q->where('module', $module))
            ->orderBy('module')
            ->orderBy('name')
            ->get();

        $roles = Role::query()
            ->with(['permissions' => fn ($q) => $q->select('permissions.id')])
            ->orderBy('name')
            ->get();

        $modules = Permission::query()->distinct()->orderBy('module')->pluck('module');

        return view('roles.matrix', [
            'roles' => $roles,
            'permissions' => $permissions,
            'modules' => $modules,
        ]);
    }

    public function edit(Role $role, Request $request): View
    {
        $this->assertLeadershipManager($request);

        abort_if($role->slug === 'leadership', 422, 'Leadership role permissions are fixed.');

        $permissions = Permission::query()->orderBy('module')->orderBy('name')->get()->groupBy('module');

        return view('roles.edit', [
            'role' => $role->load('permissions:id'),
            'permissionsByModule' => $permissions,
        ]);
    }

    public function update(UpdateRolePermissionsRequest $request, Role $role): RedirectResponse
    {
        $this->assertLeadershipManager($request);

        abort_if($role->slug === 'leadership', 422, 'Leadership role permissions are fixed.');

        $permissionIds = collect($request->validated('permission_ids', []))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $role->permissions()->sync($permissionIds);

        return redirect()->route('roles.matrix')
            ->with('success', "Permissions updated for role {$role->name}.");
    }

    protected function assertLeadershipManager(Request $request): void
    {
        abort_unless($request->user() && $request->user()->hasRole('leadership'), 403, 'Only Leadership can manage role permissions.');
    }
}
