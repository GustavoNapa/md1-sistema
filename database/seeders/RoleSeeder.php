<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Criar cargos
        $roles = [
            // Cargo de Administrador (todas as permissões)
            [
                'name' => 'head-cs',
                'display_name' => 'Head de CS',
                'permissions' => Permission::all()->pluck('name')->toArray(),
            ],
            // Cargo de Especialista de Suporte (TI)
            [
                'name' => 'especialista-suporte-ti',
                'display_name' => 'Especialista de Suporte (TI)',
                'permissions' => [
                    'users.view', 'users.create', 'users.edit',
                    'clients.view', 'clients.create', 'clients.edit',
                    'products.view', 'products.create', 'products.edit',
                    'inscriptions.view', 'inscriptions.create', 'inscriptions.edit',
                    'payments.view', 'payments.create', 'payments.edit',
                    'webhooks.view', 'webhooks.resend',
                    'whatsapp.view', 'whatsapp.conversations',
                    'settings.view', 'settings.edit',
                    'feature-flags.view', 'feature-flags.edit',
                    'import.clients', 'import.inscriptions', 'import.payments',
                ],
            ],
            // Cargo de Coordenador de Mentoria
            [
                'name' => 'coordenador-mentoria',
                'display_name' => 'Coordenador de Mentoria',
                'permissions' => [
                    'clients.view', 'clients.edit',
                    'inscriptions.view', 'inscriptions.edit', 'inscriptions.kanban',
                    'payments.view',
                    'achievements.view', 'achievements.create', 'achievements.edit',
                    'bonuses.view', 'bonuses.create', 'bonuses.edit',
                    'whatsapp.view', 'whatsapp.send', 'whatsapp.conversations',
                    'reports.view', 'reports.export',
                ],
            ],
            // Cargo de Especialista em Customer Success
            [
                'name' => 'especialista-customer-success',
                'display_name' => 'Especialista em Customer Success',
                'permissions' => [
                    'clients.view', 'clients.edit',
                    'inscriptions.view', 'inscriptions.edit', 'inscriptions.kanban',
                    'payments.view',
                    'faturamentos.view', 'faturamentos.create', 'faturamentos.edit',
                    'renovacoes.view', 'renovacoes.create', 'renovacoes.edit',
                    'whatsapp.view', 'whatsapp.send', 'whatsapp.conversations',
                    'reports.view', 'reports.export',
                ],
            ],
            // Cargo de Especialista em Suporte ao Cliente
            [
                'name' => 'especialista-suporte-cliente',
                'display_name' => 'Especialista em Suporte ao Cliente',
                'permissions' => [
                    'clients.view',
                    'inscriptions.view',
                    'payments.view',
                    'whatsapp.view', 'whatsapp.send', 'whatsapp.conversations',
                    'reports.view',
                ],
            ],
        ];

        foreach ($roles as $roleData) {
            $role = Role::firstOrCreate(['name' => $roleData['name']], ['guard_name' => 'web']);
            $role->syncPermissions($roleData['permissions']);
        }
    }
}


