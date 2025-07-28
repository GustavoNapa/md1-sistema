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
            if (!Schema::hasColumn('inscriptions', 'entry_channel')) {
                $table->unsignedBigInteger('entry_channel')->nullable();
            }
        });
        Schema::table('inscriptions', function (Blueprint $table) {
            if (Schema::hasColumn('inscriptions', 'entry_channel')) {
                $table->unsignedBigInteger('entry_channel')->nullable()->change();
                $table->foreign('entry_channel')->references('id')->on('entry_channels')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inscriptions', function (Blueprint $table) {
            $table->dropForeign(['entry_channel']);
            $table->dropColumn('entry_channel');
        });
    }
};
