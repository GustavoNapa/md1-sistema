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
        Schema::create('inscription_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscription_id')->constrained()->onDelete('cascade');
            $table->string('title'); // Nome/título do documento
            $table->enum('type', ['upload', 'link']); // Tipo: arquivo ou link
            $table->string('file_path')->nullable(); // Caminho do arquivo (se upload)
            $table->string('file_name')->nullable(); // Nome original do arquivo
            $table->bigInteger('file_size')->nullable(); // Tamanho do arquivo em bytes
            $table->string('mime_type')->nullable(); // Tipo MIME do arquivo
            $table->text('external_url')->nullable(); // URL externa (se link)
            $table->enum('category', [
                'contrato', 
                'documento_pessoal', 
                'certificado', 
                'comprovante_pagamento',
                'material_curso',
                'outros'
            ])->default('outros'); // Categoria do documento
            $table->text('description')->nullable(); // Descrição opcional
            $table->boolean('is_required')->default(false); // Se é obrigatório
            $table->boolean('is_verified')->default(false); // Se foi verificado
            $table->timestamp('verified_at')->nullable(); // Quando foi verificado
            $table->foreignId('verified_by')->nullable()->constrained('users'); // Quem verificou
            $table->timestamps();
            
            // Índices para performance
            $table->index(['inscription_id', 'category']);
            $table->index(['type', 'is_required']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscription_documents');
    }
};

