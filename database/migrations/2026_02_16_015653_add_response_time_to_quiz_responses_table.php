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
            if (!Schema::connection('quiz_mysql')->hasColumn('quiz_responses', 'response_time_minutes')) {
                $table->integer('response_time_minutes')->unsigned()->nullable()->after('summary');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('quiz_mysql')->table('quiz_responses', function (Blueprint $table) {
            if (Schema::connection('quiz_mysql')->hasColumn('quiz_responses', 'response_time_minutes')) {
                $table->dropColumn('response_time_minutes');
            }
        });
    }
};