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
        // Adicionar inscription_id à tabela achievements
        Schema::table('achievements', function (Blueprint $table) {
            $table->foreignId('inscription_id')->constrained('inscriptions')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->date('achieved_at')->nullable();
        });

        // Adicionar inscription_id à tabela onboarding_events
        Schema::table('onboarding_events', function (Blueprint $table) {
            $table->foreignId('inscription_id')->constrained('inscriptions')->onDelete('cascade');
            $table->string('event_type')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('event_date')->nullable();
        });

        // Adicionar inscription_id à tabela follow_ups
        Schema::table('follow_ups', function (Blueprint $table) {
            $table->foreignId('inscription_id')->constrained('inscriptions')->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->string('status')->default('pending');
        });

        // Adicionar inscription_id à tabela documents
        Schema::table('documents', function (Blueprint $table) {
            $table->foreignId('inscription_id')->constrained('inscriptions')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_type')->nullable();
            $table->integer('file_size')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('achievements', function (Blueprint $table) {
            $table->dropForeign(['inscription_id']);
            $table->dropColumn(['inscription_id', 'title', 'description', 'achieved_at']);
        });

        Schema::table('onboarding_events', function (Blueprint $table) {
            $table->dropForeign(['inscription_id']);
            $table->dropColumn(['inscription_id', 'event_type', 'description', 'event_date']);
        });

        Schema::table('follow_ups', function (Blueprint $table) {
            $table->dropForeign(['inscription_id']);
            $table->dropColumn(['inscription_id', 'notes', 'follow_up_date', 'status']);
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['inscription_id']);
            $table->dropColumn(['inscription_id', 'title', 'file_path', 'file_type', 'file_size']);
        });
    }
};
