<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->boolean('is_whatsapp')->default(false);
            $table->string('email')->nullable();
            $table->string('origin')->nullable(); // Campanha, Email, Facebook, etc
            $table->string('origin_other')->nullable(); // Para quando escolhe "Outro"
            $table->text('notes')->nullable();
            $table->foreignId('pipeline_id')->constrained('pipelines')->onDelete('cascade');
            $table->foreignId('pipeline_stage_id')->constrained('pipeline_stages')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Responsável
            $table->integer('stage_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
