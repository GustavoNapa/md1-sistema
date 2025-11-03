<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RemoveProductColumnFromInscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // If the table doesn't exist, nothing to do.
        if (! Schema::hasTable('inscriptions')) {
            return;
        }

        // On sqlite, dropping columns is not supported reliably.
        // Also guard against triggers/foreign keys referencing missing tables by temporarily disabling foreign keys.
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
            // Skip destructive column drop on sqlite — no-op to avoid errors.
            DB::statement('PRAGMA foreign_keys = ON;');
            return;
        }

        // For other DB drivers, only drop the column if it exists.
        if (Schema::hasColumn('inscriptions', 'product')) {
            Schema::table('inscriptions', function (Blueprint $table) {
                $table->dropColumn('product');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // If the table doesn't exist, nothing to revert.
        if (! Schema::hasTable('inscriptions')) {
            return;
        }

        // Recreate the column only if it is missing.
        if (! Schema::hasColumn('inscriptions', 'product')) {
            Schema::table('inscriptions', function (Blueprint $table) {
                $table->string('product')->nullable();
            });
        }
    }
};
