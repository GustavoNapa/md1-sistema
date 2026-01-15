<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_group_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('identifier')->unique(); // identificador único do campo (ex: crm, endereco)
            $table->string('type'); // text, number, monetary, rich_text, phone, select, multi_select
            $table->json('options')->nullable(); // para campos de seleção
            $table->integer('order')->default(0);
            $table->boolean('is_system')->default(false); // true para campos que não podem ser deletados
            $table->boolean('is_required')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_fields');
    }
};
