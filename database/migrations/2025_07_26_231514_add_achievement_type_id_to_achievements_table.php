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
        Schema::table('achievements', function (Blueprint $table) {
            $table->unsignedBigInteger('achievement_type_id')->nullable()->after('id');
            $table->foreign('achievement_type_id')->references('id')->on('achievement_types')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('achievements', function (Blueprint $table) {
            $table->dropForeign(['achievement_type_id']);
            $table->dropColumn('achievement_type_id');
        });
    }
};
