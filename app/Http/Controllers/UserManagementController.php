<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(): View
    {
        $users = User::with('role')->paginate(15);
        
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        $roles = Role::where('status', true)->get();
        
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            // 'role_id' => 'nullable|exists:roles,id', // Removido, Spatie Permission gerencia isso
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Atribuir cargo usando Spatie Permission
        if (isset($validated['role_id'])) {
            $role = \Spatie\Permission\Models\Role::findById($validated['role_id']);
            if ($role) {
                $user->assignRole($role);
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Usuário criado com sucesso!',
                'data' => $user->load('role')
            ]);
        }

        return response()->json(['success' => false], 400);
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): View
    {
        $user->load('role');
        
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        $roles = Role::where('status', true)->get();
        $user->load('role');
        
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            // 'role_id' => 'nullable|exists:roles,id', // Removido, Spatie Permission gerencia isso
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            // 'role_id' => $validated['role_id'] ?? null, // Removido, Spatie Permission gerencia isso
        ];

        if (isset($validated['role_id'])) {
            $role = \Spatie\Permission\Models\Role::findById($validated['role_id']);
            if ($role) {
                $user->syncRoles($role);
            }
        }

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Usuário atualizado com sucesso!',
                'data' => $user->load('role')
            ]);
        }

        return response()->json(['success' => false], 400);
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): JsonResponse
    {
        // Prevent deletion of current user
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Você não pode excluir sua própria conta.'
            ], 422);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Usuário excluído com sucesso!'
        ]);
    }

    /**
     * Assign role to user.
     */
    public function assignRole(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $role = \Spatie\Permission\Models\Role::findById($validated['role_id']);
        if ($role) {
            $user->assignRole($role);
        }

        return response()->json([
            'success' => true,
            'message' => 'Cargo atribuído com sucesso!',
            'data' => $user->load('roles')
        ]);
    }

    /**
     * Remove role from user.
     */
    public function removeRole(User $user): JsonResponse
    {
        $user->syncRoles([]); // Remove all roles from the user

        return response()->json([
            'success' => true,
            'message' => 'Cargo removido com sucesso!',
            'data' => $user->load('roles')
        ]);
    }
}

