<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar cargo de Administrador
        $adminRole = Role::firstOrCreate(
            ['name' => 'Administrador'],
            ['status' => true]
        );

        // Atribuir todas as permissões ao administrador
        $allPermissions = Permission::all();
        $adminRole->permissions()->sync($allPermissions->pluck('id'));

        // Criar cargo de Vendedor
        $vendedorRole = Role::firstOrCreate(
            ['name' => 'Vendedor'],
            ['status' => true]
        );

        // Atribuir permissões específicas ao vendedor
        $vendedorPermissions = Permission::whereIn('slug', [
            'manage-clients',
            'manage-inscriptions',
            'view-reports'
        ])->get();
        $vendedorRole->permissions()->sync($vendedorPermissions->pluck('id'));

        // Criar cargo de Suporte
        $suporteRole = Role::firstOrCreate(
            ['name' => 'Suporte'],
            ['status' => true]
        );

        // Atribuir permissões específicas ao suporte
        $suportePermissions = Permission::whereIn('slug', [
            'manage-clients',
            'view-reports'
        ])->get();
        $suporteRole->permissions()->sync($suportePermissions->pluck('id'));
    }
}

