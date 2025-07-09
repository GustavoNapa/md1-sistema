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
        Schema::create('inscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('vendor_id')->nullable()->constrained()->onDelete('set null');
            $table->string('product');
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
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscriptions');
    }
};

