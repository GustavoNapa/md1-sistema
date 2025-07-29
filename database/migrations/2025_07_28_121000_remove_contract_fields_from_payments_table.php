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
        Schema::table("payments", function (Blueprint $table) {
            // $table->dropColumn(["contrato_assinado", "contrato_na_pasta"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->boolean('contrato_assinado')->default(false);
            $table->boolean('contrato_na_pasta')->default(false);
        });
    }
};
