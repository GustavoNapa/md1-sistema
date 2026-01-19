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
        Schema::table('sessions', function (Blueprint $table) {
            // Preceptor
            $table->foreignId('preceptor_record_id')->nullable()->after('inscription_id')->constrained('preceptor_records')->onDelete('set null');
            
            // Semana do mês
            $table->integer('semana_mes')->nullable()->after('fase'); // 1-5
            
            // Confirmação 24h antes
            $table->boolean('confirmou_24h')->nullable()->after('status');
            
            // Médico confirmou
            $table->enum('medico_confirmou', ['confirmou', 'desmarcou', 'nao_respondeu'])->nullable()->after('confirmou_24h');
            $table->text('motivo_desmarcou')->nullable()->after('medico_confirmou');
            
            // Comparecimento
            $table->boolean('medico_compareceu')->nullable()->after('motivo_desmarcou');
            
            // No Show
            $table->enum('status_reagendamento', ['reagendado', 'em_processo', 'sem_comunicacao'])->nullable()->after('medico_compareceu');
            $table->date('data_remarcada')->nullable()->after('status_reagendamento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->dropForeign(['preceptor_record_id']);
            $table->dropColumn([
                'preceptor_record_id',
                'semana_mes',
                'confirmou_24h',
                'medico_confirmou',
                'motivo_desmarcou',
                'medico_compareceu',
                'status_reagendamento',
                'data_remarcada'
            ]);
        });
    }
};
