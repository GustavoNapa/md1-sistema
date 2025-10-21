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
            // novo: canal/onde o pagamento foi realizado (Pagcorp, Conta Bancária, Loja etc)
            $table->string('payment_channel')->nullable();
            // opcional: referência ao canal cadastrado
            $table->foreignId('payment_channel_id')->nullable()->constrained('payment_channels')->nullOnDelete();
            $table->string('status')->default('pendente'); // pendente, pago, cancelado
            

            
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
