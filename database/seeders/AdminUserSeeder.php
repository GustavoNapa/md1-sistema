<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar o cargo de administrador
        $adminRole = Role::where('name', 'Administrador')->first();

        if (!$adminRole) {
            $this->command->error('Cargo de Administrador não encontrado. Execute primeiro o RoleSeeder.');
            return;
        }

        // Criar usuário administrador
        $admin = User::firstOrCreate(
            ['email' => 'admin@md1academy.com'],
            [
                'name' => 'Administrador do Sistema',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
                'role_id' => $adminRole->id,
            ]
        );

        if ($admin->wasRecentlyCreated) {
            $this->command->info('Usuário administrador criado com sucesso!');
            $this->command->info('E-mail: admin@md1academy.com');
            $this->command->info('Senha: admin123');
        } else {
            $this->command->info('Usuário administrador já existe.');
        }
    }
}

