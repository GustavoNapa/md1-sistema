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
        Schema::connection('quiz_mysql')->table('quiz_responses', function (Blueprint $table) {
            if (!Schema::connection('quiz_mysql')->hasColumn('quiz_responses', 'name')) {
                $table->string('name')->nullable()->after('email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('quiz_mysql')->table('quiz_responses', function (Blueprint $table) {
            if (Schema::connection('quiz_mysql')->hasColumn('quiz_responses', 'name')) {
                $table->dropColumn('name');
            }
        });
    }
};
