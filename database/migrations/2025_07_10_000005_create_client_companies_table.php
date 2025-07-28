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
        if (!Schema::hasTable('client_companies')) {
            Schema::create('client_companies', function (Blueprint $table) {
                $table->id();
                $table->foreignId('client_id')->constrained()->onDelete('cascade');
                $table->string('name');
                $table->string('cnpj')->nullable();
                $table->enum('type', ['clinic', 'laboratory', 'hospital', 'office', 'other'])->default('clinic');
                $table->string('address')->nullable();
                $table->string('city')->nullable();
                $table->string('state', 2)->nullable();
                $table->string('zip_code')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->string('website')->nullable();
                $table->boolean('is_main')->default(false);
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['client_id', 'is_main']);
                $table->index(['client_id', 'type']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_companies');
    }
};

