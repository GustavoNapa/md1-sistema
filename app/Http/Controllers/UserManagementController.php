<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserManagementController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(): View
    {
        $users = User::with('roles')->paginate(15);
        
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
        Log::info('Storing new user', ['request' => $request->all()]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            // 'role_id' => 'nullable|exists:roles,id', // Removido, Spatie Permission gerencia isso
        ]);

        Log::info('Validate PASS');

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Atribuir cargo usando Spatie Permission
        if (isset($request->role_id)) {
            Log::info('Assigning role to user', ['role_id' => $request->role_id]);
            
            $role = \Spatie\Permission\Models\Role::findById($request->role_id);
            if ($role) {
                $user->syncRoles($role);
            }

            Log::info('User role assigned successfully', ['user' => $user, 'role' => $role]);
        }

        if ($request->expectsJson()) {
            Log::info('Returning JSON response for user creation');

            return response()->json([
                'success' => true,
                'message' => 'Usuário criado com sucesso!',
                'data' => $user->load('roles')
            ]);
        }

        Log::info('User created successfully', ['user' => $user]);

        return response()->json(['success' => false], 400);
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): View | JsonResponse
    {
        $user->load('roles');

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role_id' => $user->role ? $user->role->id : null,
                    // Adicione outros campos necessários
                ]
            ]);
        }
        
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        $roles = Role::where('status', true)->get();
        $user->load('roles');
        
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user): JsonResponse
    {
        Log::info('Update user request data: ', $request->all());
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            // 'role_id' => 'nullable|exists:roles,id', // Removido, Spatie Permission gerencia isso
        ]);

        Log::info('Update user validated data: ', $validated);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            // 'role_id' => $request->role_id ?? null, // Removido, Spatie Permission gerencia isso
        ];

        if (isset($request->role_id)) {
            Log::info('Update user role data: ', ['role_id' => $request->role_id]);

            $role = \Spatie\Permission\Models\Role::findById($request->role_id);
            if ($role) {
                $user->syncRoles($role);
            }

            Log::info('Updated user role data: ', $role ? $role->toArray() : null);
        }

        if (!empty($validated['password']) && !empty($validated['password_confirmation'])) {
            Log::info('Update user password data: ', ['password' => $validated['password']]);

            $updateData['password'] = Hash::make($validated['password']);

            Log::info('Updated user password data: ', $updateData['password']);
        }

        $user->update($updateData);

        Log::info('Updated user final data: ', $user->toArray());

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Usuário atualizado com sucesso!',
                'data' => $user->load('roles')
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

        $role = \Spatie\Permission\Models\Role::findById($request->role_id);
        if ($role) {
            $user->syncRoles($role);
        }

        Log::info('Assingned role to user:', ['role' => $role->name, 'user' => ['name' => $user->name, 'email' => $user->email]]);

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

