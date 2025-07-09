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
        Schema::create('sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscription_id')->constrained()->onDelete('cascade');
            
            // Dados da sessão
            $table->integer('numero_sessao'); // 1, 2, 3...
            $table->string('fase')->nullable(); // fase 01, fase 02, etc
            $table->string('tipo')->nullable(); // diagnóstico, chamada start, onboarding, etc
            
            // Datas
            $table->dateTime('data_agendada')->nullable();
            $table->dateTime('data_realizada')->nullable();
            
            // Status e resultados
            $table->string('status')->default('agendada'); // agendada, realizada, cancelada
            $table->text('observacoes')->nullable();
            $table->text('resultado')->nullable();
            
            // Dados específicos para algumas sessões
            $table->decimal('media_mensal_antes', 10, 2)->nullable();
            $table->decimal('meta_mensal_desejada', 10, 2)->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
