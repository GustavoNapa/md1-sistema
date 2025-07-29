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
        if (!Schema::hasTable('whatsapp_conversations')) {
            Schema::create('whatsapp_conversations', function (Blueprint $table) {
                $table->id();
                $table->string('contact_phone')->index(); // Número do WhatsApp normalizado
                $table->string('contact_name')->nullable(); // Nome do contato no WhatsApp
                $table->string('instance_name'); // Nome da instância Evolution
                $table->unsignedBigInteger('client_id')->nullable(); // FK para clientes
                $table->unsignedBigInteger('contact_id')->nullable(); // FK para contatos
                $table->timestamp('last_message_at')->nullable(); // Última mensagem
                $table->integer('unread_count')->default(0); // Mensagens não lidas
                $table->boolean('is_active')->default(true); // Conversa ativa
                $table->json('metadata')->nullable(); // Dados extras do WhatsApp
                $table->timestamps();

                // Índices
                $table->index(['contact_phone', 'instance_name']);
                $table->index('last_message_at');
                $table->index(['client_id', 'contact_id']);

                // Foreign keys (assumindo que existem tabelas clients e contacts)
                $table->foreign('client_id')->references('id')->on('clients')->onDelete('set null');
                // $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_conversations');
    }
};
