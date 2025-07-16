<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            [
                'slug' => 'manage-users',
                'name' => 'Gerenciar Usuários',
            ],
            [
                'slug' => 'manage-roles',
                'name' => 'Gerenciar Cargos',
            ],
            [
                'slug' => 'manage-permissions',
                'name' => 'Gerenciar Permissões',
            ],
            [
                'slug' => 'manage-clients',
                'name' => 'Gerenciar Clientes',
            ],
            [
                'slug' => 'manage-products',
                'name' => 'Gerenciar Produtos',
            ],
            [
                'slug' => 'manage-inscriptions',
                'name' => 'Gerenciar Inscrições',
            ],
            [
                'slug' => 'view-reports',
                'name' => 'Visualizar Relatórios',
            ],
            [
                'slug' => 'import-data',
                'name' => 'Importar Dados',
            ],
            [
                'slug' => 'manage-integrations',
                'name' => 'Gerenciar Integrações',
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['slug' => $permission['slug']],
                ['name' => $permission['name']]
            );
        }
    }
}

