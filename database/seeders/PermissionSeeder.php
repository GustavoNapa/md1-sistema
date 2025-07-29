<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Criar permissões
        $permissions = [
            // Gestão de Usuários
            'users.view' => 'Visualizar Usuários',
            'users.create' => 'Criar Usuários',
            'users.edit' => 'Editar Usuários',
            'users.delete' => 'Excluir Usuários',
            
            // Gestão de Clientes
            'clients.view' => 'Visualizar Clientes',
            'clients.create' => 'Criar Clientes',
            'clients.edit' => 'Editar Clientes',
            'clients.delete' => 'Excluir Clientes',
            
            // Gestão de Produtos
            'products.view' => 'Visualizar Produtos',
            'products.create' => 'Criar Produtos',
            'products.edit' => 'Editar Produtos',
            'products.delete' => 'Excluir Produtos',
            
            // Gestão de Inscrições
            'inscriptions.view' => 'Visualizar Inscrições',
            'inscriptions.create' => 'Criar Inscrições',
            'inscriptions.edit' => 'Editar Inscrições',
            'inscriptions.delete' => 'Excluir Inscrições',
            'inscriptions.kanban' => 'Visualizar Kanban de Inscrições',
            
            // Gestão de Pagamentos
            'payments.view' => 'Visualizar Pagamentos',
            'payments.create' => 'Criar Pagamentos',
            'payments.edit' => 'Editar Pagamentos',
            'payments.delete' => 'Excluir Pagamentos',
            
            // Gestão de Conquistas
            'achievements.view' => 'Visualizar Conquistas',
            'achievements.create' => 'Criar Conquistas',
            'achievements.edit' => 'Editar Conquistas',
            'achievements.delete' => 'Excluir Conquistas',
            
            // Gestão de Bônus
            'bonuses.view' => 'Visualizar Bônus',
            'bonuses.create' => 'Criar Bônus',
            'bonuses.edit' => 'Editar Bônus',
            'bonuses.delete' => 'Excluir Bônus',
            
            // Gestão de Faturamento
            'faturamentos.view' => 'Visualizar Faturamentos',
            'faturamentos.create' => 'Criar Faturamentos',
            'faturamentos.edit' => 'Editar Faturamentos',
            'faturamentos.delete' => 'Excluir Faturamentos',
            
            // Gestão de Renovações
            'renovacoes.view' => 'Visualizar Renovações',
            'renovacoes.create' => 'Criar Renovações',
            'renovacoes.edit' => 'Editar Renovações',
            'renovacoes.delete' => 'Excluir Renovações',
            
            // Gestão de Webhooks
            'webhooks.view' => 'Visualizar Webhooks',
            'webhooks.create' => 'Criar Webhooks',
            'webhooks.edit' => 'Editar Webhooks',
            'webhooks.delete' => 'Excluir Webhooks',
            'webhooks.resend' => 'Reenviar Webhooks',
            
            // Integração WhatsApp
            'whatsapp.view' => 'Visualizar WhatsApp',
            'whatsapp.send' => 'Enviar Mensagens WhatsApp',
            'whatsapp.conversations' => 'Gerenciar Conversas WhatsApp',
            
            // Relatórios
            'reports.view' => 'Visualizar Relatórios',
            'reports.export' => 'Exportar Relatórios',
            
            // Importação de Dados
            'import.clients' => 'Importar Clientes',
            'import.inscriptions' => 'Importar Inscrições',
            'import.payments' => 'Importar Pagamentos',
            
            // Configurações do Sistema
            'settings.view' => 'Visualizar Configurações',
            'settings.edit' => 'Editar Configurações',
            'feature-flags.view' => 'Visualizar Feature Flags',
            'feature-flags.edit' => 'Editar Feature Flags',
            
            // Gestão de Permissões e Cargos
            'roles.view' => 'Visualizar Cargos',
            'roles.create' => 'Criar Cargos',
            'roles.edit' => 'Editar Cargos',
            'roles.delete' => 'Excluir Cargos',
            'permissions.view' => 'Visualizar Permissões',
            'permissions.assign' => 'Atribuir Permissões',
        ];

        foreach ($permissions as $name => $description) {
            Permission::firstOrCreate(['name' => $name], ['guard_name' => 'web']);
        }

        // Criar cargos
        $roles = [
            'head-cs' => [
                'name' => 'Head de CS',
                'permissions' => array_keys($permissions), // Todas as permissões
            ],
            'especialista-suporte-ti' => [
                'name' => 'Especialista de Suporte (TI)',
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
            'coordenador-mentoria' => [
                'name' => 'Coordenador de Mentoria',
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
            'especialista-customer-success' => [
                'name' => 'Especialista em Customer Success',
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
            'especialista-suporte-cliente' => [
                'name' => 'Especialista em Suporte ao Cliente',
                'permissions' => [
                    'clients.view',
                    'inscriptions.view',
                    'payments.view',
                    'whatsapp.view', 'whatsapp.send', 'whatsapp.conversations',
                    'reports.view',
                ],
            ],
        ];

        foreach ($roles as $slug => $roleData) {
            $role = Role::firstOrCreate(['name' => $slug], ['guard_name' => 'web']);
            $role->syncPermissions($roleData['permissions']);
        }
    }
}

