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
        Schema::create('zapsign_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscription_id')->nullable()->constrained('inscriptions')->onDelete('set null');
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('set null');
            $table->foreignId('template_mapping_id')->nullable()->constrained('zapsign_template_mappings')->onDelete('set null');
            $table->string('zapsign_document_id')->unique(); // ID do documento no ZapSign
            $table->string('zapsign_token')->nullable(); // Token do documento
            $table->string('external_id')->nullable(); // ID externo para referência
            $table->string('name'); // Nome do documento
            $table->string('status'); // pending, signed, expired, etc.
            $table->text('original_file_url')->nullable();
            $table->text('signed_file_url')->nullable();
            $table->json('webhook_data')->nullable(); // Dados completos do webhook
            $table->timestamp('signed_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            $table->index(['inscription_id', 'status']);
            $table->index(['client_id', 'status']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zapsign_documents');
    }
};

