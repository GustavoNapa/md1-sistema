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
        Schema::table('sessions', function (Blueprint $table) {
            // Implementação e Desenvolvimento
            $table->string('implementacao_fase')->nullable()->after('resultado')->comment('0-25, 25-50, 50-75, 75-100, 100');
            $table->string('impacto_faturamento')->nullable()->after('implementacao_fase')->comment('sem_impacto, baixo, medio, alto, muito_alto');
            $table->text('dificuldades_travas')->nullable()->after('impacto_faturamento');
            $table->text('desenvolvimento_ultima_preceptoria')->nullable()->after('dificuldades_travas');
            $table->text('avancos_importantes')->nullable()->after('desenvolvimento_ultima_preceptoria');
            
            // Depoimentos e Indicações
            $table->text('momento_depoimento')->nullable()->after('avancos_importantes');
            $table->boolean('conseguiu_indicacao')->default(false)->after('momento_depoimento');
            $table->text('detalhes_indicacao')->nullable()->after('conseguiu_indicacao');
            
            // Faturamento (campos que estavam sendo enviados do frontend)
            $table->string('faturamento_mes_ano', 7)->nullable()->after('detalhes_indicacao')->comment('Formato: YYYY-MM');
            $table->decimal('faturamento_valor', 10, 2)->nullable()->after('faturamento_mes_ano');
            $table->date('faturamento_data_vencimento')->nullable()->after('faturamento_valor');
            $table->enum('faturamento_status', ['pendente', 'pago', 'vencido', 'cancelado'])->nullable()->after('faturamento_data_vencimento');
            $table->text('faturamento_observacoes')->nullable()->after('faturamento_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->dropColumn([
                'implementacao_fase',
                'impacto_faturamento',
                'dificuldades_travas',
                'desenvolvimento_ultima_preceptoria',
                'avancos_importantes',
                'momento_depoimento',
                'conseguiu_indicacao',
                'detalhes_indicacao',
                'faturamento_mes_ano',
                'faturamento_valor',
                'faturamento_data_vencimento',
                'faturamento_status',
                'faturamento_observacoes',
            ]);
        });
    }
};
