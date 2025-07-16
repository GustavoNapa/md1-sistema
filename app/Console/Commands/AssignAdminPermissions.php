<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Role;
use Illuminate\Console\Command;

class AssignAdminPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:assign-permissions {--email= : Email do usuário para atribuir permissões de admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Atribui permissões de administrador a um usuário';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Listar todos os usuários
        $this->info('Usuários no sistema:');
        $users = User::with('role')->get();
        
        foreach ($users as $user) {
            $roleName = $user->role ? $user->role->name : 'Sem role';
            $this->line("ID: {$user->id} | Nome: {$user->name} | Email: {$user->email} | Role: {$roleName}");
        }
        
        // Se um email foi fornecido via opção
        $email = $this->option('email');
        
        if (!$email) {
            $email = $this->ask('Digite o email do usuário que deve receber permissões de admin');
        }
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("Usuário com email {$email} não encontrado!");
            return 1;
        }
        
        // Buscar role de administrador
        $adminRole = Role::where('name', 'Administrador')->first();
        
        if (!$adminRole) {
            $this->error('Role de Administrador não encontrada! Execute o seeder primeiro.');
            return 1;
        }
        
        // Atribuir role de admin ao usuário
        $user->role_id = $adminRole->id;
        $user->save();
        
        $this->info("Permissões de administrador atribuídas com sucesso ao usuário: {$user->name} ({$user->email})");
        
        // Mostrar permissões do usuário
        $permissions = $user->getPermissions();
        $this->info('Permissões do usuário:');
        foreach ($permissions as $permission) {
            $this->line("- {$permission->name} ({$permission->slug})");
        }
        
        return 0;
    }
}
