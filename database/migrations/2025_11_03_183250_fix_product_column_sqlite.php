<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only fix for SQLite - other DBs should use the previous migration
        if (DB::getDriverName() !== 'sqlite') {
            return;
        }

        // Check if product column exists and needs to be removed
        if (!Schema::hasColumn('inscriptions', 'product')) {
            return;
        }

        DB::statement('PRAGMA foreign_keys = OFF;');
        
        // Create new table without product column
        Schema::create('inscriptions_new', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('vendor_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->string('class_group')->nullable();
            $table->enum('status', ['active', 'paused', 'cancelled', 'completed'])->default('active');
            $table->string('classification')->nullable();
            $table->boolean('has_medboss')->default(false);
            $table->string('crmb_number')->nullable();
            $table->date('start_date')->nullable();
            $table->date('original_end_date')->nullable();
            $table->date('actual_end_date')->nullable();
            $table->date('platform_release_date')->nullable();
            $table->integer('calendar_week')->nullable();
            $table->integer('current_week')->nullable();
            $table->decimal('amount_paid', 10, 2)->nullable();
            $table->string('payment_method')->nullable();
            $table->text('commercial_notes')->nullable();
            $table->text('general_notes')->nullable();
            $table->text('problemas_desafios')->nullable();
            $table->json('historico_faturamento')->nullable();
            $table->unsignedBigInteger('entry_channel')->nullable();
            $table->boolean('contrato_assinado')->default(false);
            $table->boolean('contrato_na_pasta')->default(false);
            $table->string('contract_folder_link')->nullable();
            $table->enum('natureza_juridica', ['pessoa fisica', 'pessoa juridica'])->nullable();
            $table->string('cpf_cnpj')->nullable();
            $table->decimal('valor_total', 10, 2)->nullable();
            $table->string('forma_pagamento_entrada')->nullable();
            $table->decimal('valor_entrada', 10, 2)->nullable();
            $table->date('data_pagamento_entrada')->nullable();
            $table->string('forma_pagamento_restante')->nullable();
            $table->decimal('valor_restante', 10, 2)->nullable();
            $table->date('data_contrato')->nullable();
            $table->timestamps();
        });

        // Copy data from old table (excluding product column)
        DB::statement('INSERT INTO inscriptions_new (
            id, client_id, vendor_id, product_id, class_group, status, classification, has_medboss, 
            crmb_number, start_date, original_end_date, actual_end_date, platform_release_date, 
            calendar_week, current_week, amount_paid, payment_method, commercial_notes, general_notes,
            problemas_desafios, historico_faturamento, entry_channel, contrato_assinado, contrato_na_pasta,
            contract_folder_link, natureza_juridica, cpf_cnpj, valor_total, forma_pagamento_entrada,
            valor_entrada, data_pagamento_entrada, forma_pagamento_restante, valor_restante, data_contrato,
            created_at, updated_at
        ) SELECT 
            id, client_id, vendor_id, product_id, class_group, status, classification, has_medboss, 
            crmb_number, start_date, original_end_date, actual_end_date, platform_release_date, 
            calendar_week, current_week, amount_paid, payment_method, commercial_notes, general_notes,
            problemas_desafios, historico_faturamento, entry_channel, contrato_assinado, contrato_na_pasta,
            contract_folder_link, natureza_juridica, cpf_cnpj, valor_total, forma_pagamento_entrada,
            valor_entrada, data_pagamento_entrada, forma_pagamento_restante, valor_restante, data_contrato,
            created_at, updated_at
        FROM inscriptions');

        // Drop old table
        Schema::drop('inscriptions');
        
        // Rename new table
        Schema::rename('inscriptions_new', 'inscriptions');
        
        DB::statement('PRAGMA foreign_keys = ON;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is not easily reversible since we're dropping data
        // The old migrations can be used to recreate the original structure
    }
};
