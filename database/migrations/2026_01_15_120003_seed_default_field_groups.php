<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Criar grupo padrão "Informações Gerais"
        $groupId = DB::table('field_groups')->insertGetId([
            'name' => 'Informações Gerais',
            'type' => 'contato',
            'order' => 0,
            'is_system' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Criar campos padrão
        DB::table('custom_fields')->insert([
            [
                'field_group_id' => $groupId,
                'name' => 'Nome',
                'identifier' => 'name',
                'type' => 'text',
                'order' => 0,
                'is_system' => true,
                'is_required' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'field_group_id' => $groupId,
                'name' => 'Email',
                'identifier' => 'email',
                'type' => 'text',
                'order' => 1,
                'is_system' => true,
                'is_required' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'field_group_id' => $groupId,
                'name' => 'Telefone',
                'identifier' => 'phone',
                'type' => 'phone',
                'order' => 2,
                'is_system' => true,
                'is_required' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        // Não é necessário reverter pois as tabelas serão dropadas
    }
};
