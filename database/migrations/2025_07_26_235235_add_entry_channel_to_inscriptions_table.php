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
        if (!Schema::hasColumn('inscriptions', 'entry_channel')) {
            Schema::table("inscriptions", function (Blueprint $table) {
                $table->unsignedBigInteger("entry_channel")->nullable()->after("status");
                $table->foreign("entry_channel")->references("id")->on("entry_channels")->onDelete("set null");
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('inscriptions', 'entry_channel')) {
            Schema::table("inscriptions", function (Blueprint $table) {
                // dropForeign só deve ser chamado se a FK existir; em muitos casos dropForeign(['entry_channel']) funciona.
                // Em ambientes onde o nome da constraint é diferente, esse comando pode falhar — ajuste se necessário.
                $table->dropForeign(["entry_channel"]);
                $table->dropColumn("entry_channel");
            });
        }
    }
};


