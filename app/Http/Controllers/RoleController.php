<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class RoleController extends Controller
{
    /**
     * Display a listing of the roles.
     */
    public function index(): View
    {
        $roles = Role::with("permissions")->paginate(15);
        $permissions = Permission::orderBy('name')->get();
        
        return view('roles.index', compact('roles', 'permissions'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create(): View
    {
        $permissions = Permission::all();
        
        return view('roles.create', compact('permissions'));
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request): JsonResponse
    {
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
        
        return view('roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role): View
    {
        $permissions = Permission::all();
        $role->load('permissions');
        
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

        return response()->json([
            'success' => true,
            'message' => 'Cargo excluído com sucesso!'
        ]);
    }


}

