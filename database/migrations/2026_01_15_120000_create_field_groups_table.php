<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('field_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('contato'); // contato, empresa, negocio
            $table->integer('order')->default(0);
            $table->boolean('is_system')->default(false); // true para grupos que não podem ser deletados
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('field_groups');
    }
};
