<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class RoleController extends Controller
{
    /**
     * Display a listing of the roles.
     */
    public function index(Request $request): View | JsonResponse
    {
        if ($request->expectsJson() || $request->ajax()) {
            Log::info('Fetching roles for AJAX request');
            // Retorna todos os cargos para o select
            return response()->json(Role::orderBy('name')->get(['id', 'name']));
        }
        
        $q = $request->input('q');

        $rolesQuery = Role::with("permissions");
        if ($q) {
            $rolesQuery->where('name', 'like', "%{$q}%");
        }

        $roles = $rolesQuery->orderBy('name')->paginate(15)->appends($request->except('page'));
        $permissions = Permission::orderBy('name')->get();

        Log::info('Fetching roles and permissions');

        return view('roles.index', compact('roles', 'permissions'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create(): View
    {
    $permissions = Permission::orderBy('name')->get();

        Log::info('Fetching permissions for role creation');

        return view('roles.create', compact('permissions'));
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request): JsonResponse
    {
        Log::info('Storing new role');

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
        ]);

        if (isset($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cargo criado com sucesso!',
                'data' => $role->load('permissions')
            ]);
        }

        Log::info('Role created successfully', ['role' => $role]);

        return response()->json(['success' => false], 400);
    }

    /**
     * Display the specified role.
     */
    public function show(Request $request, Role $role)
    {
        $role->load('permissions');
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'permissions' => $role->permissions->pluck('id')->toArray(),
                    'permissions_names' => $role->permissions->pluck('name')->toArray(),
                    'users_count' => $role->users->count(),
                ]
            ]);
        }

        Log::info('Fetching role details', ['role' => $role]);

        return view('roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role): View
    {
    // carregar permissões ordenadas por nome para consistência
    $permissions = Permission::orderBy('name')->get();
        $role->load('permissions');

        Log::info('Fetching permissions for role editing', ['role' => $role]);

        return view('roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, Role $role): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update([
            'name' => $validated['name'],
        ]);

        if (isset($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cargo atualizado com sucesso!',
                'data' => $role->load('permissions')
            ]);
        }

        Log::info('Role updated successfully', ['role' => $role]);

        return response()->json(['success' => false], 400);
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Role $role): JsonResponse
    {
        // Check if role is being used by any user
        if ($role->users()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Não é possível excluir este cargo pois ele está sendo usado por um ou mais usuários.'
            ], 422);
        }

        $role->delete();

        Log::info('Role deleted successfully', ['role' => $role]);

        return response()->json([
            'success' => true,
            'message' => 'Cargo excluído com sucesso!'
        ]);
    }


}

