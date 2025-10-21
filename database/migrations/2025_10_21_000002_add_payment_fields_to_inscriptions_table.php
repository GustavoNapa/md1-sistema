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
            // 'Pagamento no' - onde o pagamento foi realizado (ex: Pagcorp, Conta Bancária)
            $table->string('payment_location')->nullable()->after('amount_paid');
            // 'Meio de pagamento' - pix, boleto, cartão, etc (mantemos como payment_method but ensure name)
            $table->string('payment_means')->nullable()->after('payment_location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inscriptions', function (Blueprint $table) {
            $table->dropColumn(['payment_location', 'payment_means']);
        });
    }
};
