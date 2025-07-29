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
        Schema::create('faixa_faturamentos', function (Blueprint $table) {
            $table->id();
            $table->string('label', 50);
            $table->decimal('valor_min', 12, 2)->default(0);
            $table->decimal('valor_max', 12, 2);
            $table->timestamps();
            
            // Índices para performance
            $table->index(['valor_min', 'valor_max']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faixa_faturamentos');
    }
};

