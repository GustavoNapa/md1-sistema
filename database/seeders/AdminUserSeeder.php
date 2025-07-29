<?php

namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar o cargo de administrador pelo 'name' (slug) definido no PermissionSeeder
        $adminRole = Role::where("name", "head-cs")->first();

        if (!$adminRole) {
            $this->command->error("Cargo 'head-cs' não encontrado. Execute primeiro o PermissionSeeder e o RoleSeeder.");
            return;
        }

        // Criar usuário administrador
        $admin = User::firstOrCreate(
            ["email" => "admin@md1academy.com"],
            [
                "name" => "Administrador do Sistema",
                "password" => Hash::make("admin123"),
                "email_verified_at" => now(),
                // Não atribuímos role_id diretamente aqui, pois o Spatie Permission gerencia isso
            ]
        );

        // Atribuir o cargo ao usuário usando o método do Spatie Permission
        $admin->assignRole("head-cs");

        if ($admin->wasRecentlyCreated) {
            $this->command->info("Usuário administrador criado com sucesso!");
            $this->command->info("E-mail: admin@md1academy.com");
            $this->command->info("Senha: admin123");
        } else {
            $this->command->info("Usuário administrador já existe.");
        }
    }
}


