<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrar dados existentes do campo 'active' para o campo 'status'
        DB::table('clients')->update([
            'status' => DB::raw("CASE WHEN active = 1 THEN 'active' ELSE 'inactive' END")
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverter migração (opcional, pois não podemos garantir dados originais se houver 'paused')
        DB::table('clients')->update([
            'active' => DB::raw("CASE WHEN status = 'active' THEN 1 ELSE 0 END")
        ]);
    }
};
