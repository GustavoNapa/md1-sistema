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
        Schema::table('clients', function (Blueprint $table) {
            if (!\Schema::hasColumn('clients', 'sexo')) {
                $table->enum('sexo', ['masculino', 'feminino', 'outro', 'nao_informado'])
                      ->nullable()
                      ->after('email')
                      ->comment('Sexo do cliente para estudos demográficos');
            }
            if (!\Schema::hasColumn('clients', 'media_faturamento')) {
                $table->decimal('media_faturamento', 15, 2)
                      ->nullable()
                      ->after('sexo')
                      ->comment('Média de faturamento mensal do cliente');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['sexo', 'media_faturamento']);
        });
    }
};

