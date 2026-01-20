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
        Schema::table('clients', function (Blueprint $table) {
            // Alterando o campo active para incluir status 'paused'
            // Status: active, inactive, paused
            $table->string('status', 20)->default('active')->after('active');
            
            // Campos para controle de pausa
            $table->date('pause_start_date')->nullable()->after('status');
            $table->date('pause_end_date')->nullable()->after('pause_start_date');
            $table->text('pause_reason')->nullable()->after('pause_end_date');
            
            // Campo de fase (para cálculo das 27 semanas)
            $table->string('phase', 50)->nullable()->after('pause_reason');
            $table->date('phase_start_date')->nullable()->after('phase');
            $table->integer('phase_week')->nullable()->after('phase_start_date')->comment('Semana atual dentro da fase (1-27)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'pause_start_date',
                'pause_end_date',
                'pause_reason',
                'phase',
                'phase_start_date',
                'phase_week'
            ]);
        });
    }
};
