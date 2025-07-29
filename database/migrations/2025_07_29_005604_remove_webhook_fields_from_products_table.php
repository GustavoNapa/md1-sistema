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
        Schema::table("products", function (Blueprint $table) {
            $table->dropColumn(["webhook_url", "webhook_token", "webhook_trigger_status"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("products", function (Blueprint $table) {
            $table->string("webhook_url")->nullable()->after("is_active");
            $table->string("webhook_token")->nullable()->after("webhook_url");
            $table->string("webhook_trigger_status")->default("active")->after("webhook_token");
        });
    }
};

