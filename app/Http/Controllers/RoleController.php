<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
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
        $roles = Role::with(['permissions', 'users'])->paginate(15);
        
        return view('roles.index', compact('roles'));
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
            'status' => 'boolean',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'status' => $validated['status'] ?? true,
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
    public function show(Role $role): View
    {
        $role->load(['permissions', 'users']);
        
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
            'status' => 'boolean',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update([
            'name' => $validated['name'],
            'status' => $validated['status'] ?? $role->status,
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

    /**
     * Toggle role status.
     */
    public function toggleStatus(Role $role): JsonResponse
    {
        $role->update(['status' => !$role->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status do cargo alterado com sucesso!',
            'data' => $role
        ]);
    }
}

