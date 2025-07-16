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
        Schema::create('zapsign_template_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nome do mapeamento
            $table->string('zapsign_template_id'); // ID do template no ZapSign
            $table->string('description')->nullable();
            $table->json('field_mappings'); // Mapeamento dos campos
            $table->boolean('auto_sign')->default(false); // Assinatura automática
            $table->string('signer_name')->nullable(); // Nome do assinante automático
            $table->string('signer_email')->nullable(); // Email do assinante automático
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['zapsign_template_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zapsign_template_mappings');
    }
};

