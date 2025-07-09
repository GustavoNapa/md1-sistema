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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscription_id')->constrained()->onDelete('cascade');
            
            // Dados do pagamento
            $table->decimal('valor', 10, 2);
            $table->date('data_pagamento')->nullable();
            $table->string('forma_pagamento')->nullable(); // cartão, boleto, pix, etc
            $table->string('status')->default('pendente'); // pendente, pago, cancelado
            
            // Controle de contratos
            $table->boolean('contrato_assinado')->default(false);
            $table->boolean('contrato_na_pasta')->default(false);
            
            // Observações
            $table->text('observacoes')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
