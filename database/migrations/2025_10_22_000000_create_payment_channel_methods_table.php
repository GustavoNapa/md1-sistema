<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentChannelMethodsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_channel_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_channel_id')->constrained('payment_channels')->onDelete('cascade');
            $table->string('name'); // ex: "A vista", "1x", "2x", ...
            $table->integer('installments')->nullable()->comment('Número de parcelas quando aplicável (1 = à vista)');
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->unique(['payment_channel_id', 'name'], 'pcm_channel_name_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_channel_methods');
    }
}
