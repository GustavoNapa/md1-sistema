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
        Schema::create('preceptor_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscription_id')->constrained()->onDelete('cascade');
            
            // Dados do preceptor
            $table->string('nome_preceptor')->nullable();
            $table->text('historico_preceptor')->nullable();
            $table->date('data_preceptor_informado')->nullable();
            $table->date('data_preceptor_contato')->nullable();
            
            // Dados da secretária/clínica
            $table->string('nome_secretaria')->nullable();
            $table->string('email_clinica')->nullable();
            $table->string('whatsapp_clinica')->nullable();
            
            // Controles
            $table->boolean('usm')->default(false);
            $table->boolean('acesso_vitrine_gmc')->default(false);
            $table->boolean('medico_celebridade')->default(false);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preceptor_records');
    }
};
