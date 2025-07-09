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
        Schema::create('inscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('vendor_id')->nullable()->constrained()->onDelete('set null');
            
            // Dados da inscrição
            $table->string('produto'); // mentoria, mastermind, curso
            $table->string('turma')->nullable();
            $table->string('status')->default('ativo'); // ativo, pausado, cancelado, concluido
            $table->string('classificacao')->nullable();
            $table->boolean('medboss')->default(false);
            $table->string('crmb')->nullable();
            
            // Datas importantes
            $table->date('data_inicio')->nullable();
            $table->date('data_termino_original')->nullable();
            $table->date('data_termino_real')->nullable();
            $table->date('data_liberacao_plataforma')->nullable();
            
            // Controle de semanas
            $table->integer('semana_calendario')->nullable(); // 27 semanas
            $table->integer('semana_real')->nullable();
            
            // Valores financeiros
            $table->decimal('valor_pago', 10, 2)->nullable();
            $table->string('forma_pagamento')->nullable();
            
            // Observações
            $table->text('obs_comercial')->nullable();
            $table->text('obs_geral')->nullable();
            $table->text('motivo_alteracao_data')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscriptions');
    }
};
