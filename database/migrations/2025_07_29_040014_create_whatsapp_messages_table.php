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
        if (!Schema::hasTable("whatsapp_messages")) {
            Schema::create("whatsapp_messages", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conversation_id'); // FK para whatsapp_conversations
            $table->string('message_id')->unique(); // ID único da mensagem no WhatsApp
            $table->enum('direction', ['inbound', 'outbound']); // Entrada ou saída
            $table->enum('type', ['text', 'image', 'audio', 'video', 'document', 'location', 'contact', 'sticker']); // Tipo da mensagem
            $table->text('content')->nullable(); // Conteúdo da mensagem (texto)
            $table->json('media')->nullable(); // Dados de mídia (URL, caption, etc.)
            $table->string('from_phone'); // Número de origem
            $table->string('to_phone'); // Número de destino
            $table->unsignedBigInteger('user_id')->nullable(); // Usuário que enviou (se outbound)
            $table->enum('status', ['pending', 'sent', 'delivered', 'read', 'failed'])->default('pending'); // Status da mensagem
            $table->timestamp('sent_at')->nullable(); // Quando foi enviada
            $table->timestamp('delivered_at')->nullable(); // Quando foi entregue
            $table->timestamp('read_at')->nullable(); // Quando foi lida
            $table->json('raw_data')->nullable(); // Dados brutos do webhook
            $table->timestamps();

            // Índices
            $table->index('conversation_id');
            $table->index(['message_id', 'direction']);
            $table->index('sent_at');
            $table->index(['from_phone', 'to_phone']);

            // Foreign keys
            $table->foreign('conversation_id')->references('id')->on('whatsapp_conversations')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');
    }
};
