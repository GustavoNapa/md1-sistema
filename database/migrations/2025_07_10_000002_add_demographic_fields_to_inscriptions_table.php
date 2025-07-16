<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('inscriptions', function (Blueprint $table) {
            $table->text('problemas_desafios')
                  ->nullable()
                  ->after('status')
                  ->comment('Problemas ou desafios enfrentados pelo aluno');
            
            $table->json('historico_faturamento')
                  ->nullable()
                  ->after('problemas_desafios')
                  ->comment('Histórico mensal de faturamento para medir evolução durante a mentoria');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inscriptions', function (Blueprint $table) {
            $table->dropColumn(['problemas_desafios', 'historico_faturamento']);
        });
    }
};

