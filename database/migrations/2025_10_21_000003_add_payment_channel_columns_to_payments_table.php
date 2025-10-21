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
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'payment_channel')) {
                $table->string('payment_channel')->nullable()->after('forma_pagamento');
            }
            if (!Schema::hasColumn('payments', 'payment_channel_id')) {
                $table->foreignId('payment_channel_id')->nullable()->after('payment_channel')->constrained('payment_channels')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'payment_channel_id')) {
                $table->dropConstrainedForeignId('payment_channel_id');
            }
            if (Schema::hasColumn('payments', 'payment_channel')) {
                $table->dropColumn('payment_channel');
            }
        });
    }
};
