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
        if (!Schema::hasTable("conversation_links")) {
            Schema::create("conversation_links", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conversation_id'); // FK para whatsapp_conversations
            $table->enum('old_type', ['client', 'contact'])->nullable(); // Tipo anterior
            $table->unsignedBigInteger('old_id')->nullable(); // ID anterior
            $table->enum('new_type', ['client', 'contact'])->nullable(); // Novo tipo
            $table->unsignedBigInteger('new_id')->nullable(); // Novo ID
            $table->unsignedBigInteger('user_id'); // Usuário que fez a alteração
            $table->text('reason')->nullable(); // Motivo da alteração
            $table->timestamps();

            // Índices
            $table->index('conversation_id');
            $table->index(['old_type', 'old_id']);
            $table->index(['new_type', 'new_id']);
            $table->index('user_id');

            // Foreign keys
            $table->foreign('conversation_id')->references('id')->on('whatsapp_conversations')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversation_links');
    }
};
