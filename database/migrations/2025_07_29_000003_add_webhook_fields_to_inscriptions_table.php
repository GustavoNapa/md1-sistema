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
        Schema::table('inscriptions', function (Blueprint $table) {
            $table->string('natureza_juridica')->nullable()->after('client_id'); // pessoa fisica, pessoa juridica
            $table->string('cpf_cnpj')->nullable()->after('natureza_juridica');
            $table->decimal('valor_total', 10, 2)->nullable()->after('amount_paid'); // contact_produto
            $table->string('forma_pagamento_entrada')->nullable()->after('valor_total');
            $table->decimal('valor_entrada', 10, 2)->nullable()->after('forma_pagamento_entrada');
            $table->date('data_pagamento_entrada')->nullable()->after('valor_entrada');
            $table->string('forma_pagamento_restante')->nullable()->after('data_pagamento_entrada');
            $table->decimal('valor_restante', 10, 2)->nullable()->after('forma_pagamento_restante');
            $table->date('data_contrato')->nullable()->after('valor_restante');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inscriptions', function (Blueprint $table) {
            $table->dropColumn([
                'natureza_juridica',
                'cpf_cnpj',
                'valor_total',
                'forma_pagamento_entrada',
                'valor_entrada',
                'data_pagamento_entrada',
                'forma_pagamento_restante',
                'valor_restante',
                'data_contrato'
            ]);
        });
    }
};

