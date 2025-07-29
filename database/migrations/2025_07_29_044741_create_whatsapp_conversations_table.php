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
        Schema::create('whatsapp_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('contact_phone', 20)->index(); // Telefone do contato
            $table->string('contact_name')->nullable(); // Nome do contato
            $table->string('instance_name')->nullable(); // Nome da instância Evolution
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('set null'); // Cliente associado
            $table->foreignId('contact_id')->nullable()->constrained('client_phones')->onDelete('set null'); // Contato específico
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Atendente atribuído
            $table->integer('unread_count')->default(0); // Contador de mensagens não lidas
            $table->timestamp('last_message_at')->nullable(); // Data da última mensagem
            $table->boolean('is_active')->default(true); // Conversa ativa
            $table->timestamps();
            
            // Índices para performance
            $table->index(['contact_phone', 'instance_name']);
            $table->index(['client_id', 'is_active']);
            $table->index(['user_id', 'unread_count']);
            $table->index('last_message_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_conversations');
    }
};

