<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Phase N3 (Role & Permission System) — every action here is reachable
 * ONLY by an Admin (routes/web.php's own 'admin.' route group is
 * gated by role:Admin middleware, not a permission check — see
 * database/seeders/RolesAndPermissionsSeeder's own docblock on
 * 'view-admin-panel' for why role is the right check here, not that
 * permission).
 */
final class UserManagementController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::query()->with('roles')->orderBy('name')->paginate(20),
        ]);
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'targetUser' => $user,
            'roles' => Role::query()->orderBy('name')->get(),
            'permissions' => Permission::query()->orderBy('name')->get(),
            'userRoleNames' => $user->roles->pluck('name')->all(),
            'userPermissionNames' => $user->getDirectPermissions()->pluck('name')->all(),
        ]);
    }

    /**
     * Replaces BOTH this user's role AND their own individual
     * (directly-assigned, not role-inherited) permissions in one
     * submission — syncRoles()/syncPermissions() rather than
     * assignRole()/givePermissionTo() specifically, since an unchecked
     * checkbox on this form means "remove this", not merely "don't add
     * this": a plain assign call only ever ADDS, it never removes
     * something the person unchecked.
     *
     * Only ONE role at a time in practice, even though $request->role
     * is technically a single value (a radio group, not checkboxes, in
     * resources/views/admin/users/edit.blade.php — see that view's own
     * comment for why a single role rather than multiple was the right
     * choice here) — syncRoles() still takes an array, so this wraps
     * it in one.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'string', 'exists:roles,name'],
            'permissions' => ['array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $user->syncRoles([$validated['role']]);
        $user->syncPermissions($validated['permissions'] ?? []);

        return redirect()
            ->route('admin.users.index')
            ->with('status', "Updated {$user->name}'s role and permissions.");
    }
}