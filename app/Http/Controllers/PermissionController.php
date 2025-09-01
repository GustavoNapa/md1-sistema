<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PermissionController extends Controller
{
    /**
     * Display a listing of the permissions.
     */
    public function index(): View
    {
        $permissions = Permission::paginate(15);
        return view('permissions.index', compact('permissions'));
    }

    /**
     * Show the form for creating a new permission.
     */
    public function create(): View
    {
        return view('permissions.create');
    }

    /**
     * Store a newly created permission in storage.
     */
    public function store(Request $request): JsonResponse
    {
        Log::info('Creating permission', $request->all());

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Log::info('Permission created successfully', $validated);

        $permission = Permission::create($validated);

        Log::info('Permission created successfully', ['id' => $permission->id]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Permissão criada com sucesso!',
                'data' => $permission
            ]);
        }

        return response()->json(['success' => false], 400);
    }

    /**
     * Display the specified permission.
     */
    public function show(Request $request, Permission $permission)
    {
        // Defensive: check if permission exists
        if (!$permission) {
            return response()->json([
                'success' => false,
                'message' => 'Permissão não encontrada.'
            ], 404);
        }
        $permission->load('roles');
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'roles' => $permission->roles->pluck('id')->toArray(),
                    'roles_names' => $permission->roles->pluck('name')->toArray(),
                ]
            ]);
        }
        return view('permissions.show', compact('permission'));
    }

    /**
     * Show the form for editing the specified permission.
     */
    public function edit(Permission $permission): View
    {
        return view('permissions.edit', compact('permission'));
    }

    /**
     * Update the specified permission in storage.
     */
    public function update(Request $request, Permission $permission): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:permissions,slug,' . $permission->id,
        ]);

        $permission->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Permissão atualizada com sucesso!',
                'data' => $permission
            ]);
        }

        return response()->json(['success' => false], 400);
    }

    /**
     * Remove the specified permission from storage.
     */
    public function destroy(Permission $permission): JsonResponse
    {
        // Check if permission is being used by any role
        if ($permission->roles()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Não é possível excluir esta permissão pois ela está sendo usada por um ou mais cargos.'
            ], 422);
        }

        $permission->delete();

        return response()->json([
            'success' => true,
            'message' => 'Permissão excluída com sucesso!'
        ]);
    }
}

