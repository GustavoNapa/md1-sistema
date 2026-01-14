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
        Schema::table('documents', function (Blueprint $table) {
            if (!Schema::hasColumn('documents', 'nome')) {
                $table->string('nome')->nullable()->after('inscription_id');
            }
            if (!Schema::hasColumn('documents', 'file_web_view')) {
                $table->string('file_web_view')->nullable()->after('file_size');
            }
            if (!Schema::hasColumn('documents', 'token')) {
                $table->string('token')->nullable()->after('file_web_view');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['nome', 'file_web_view', 'token']);
        });
    }
};
