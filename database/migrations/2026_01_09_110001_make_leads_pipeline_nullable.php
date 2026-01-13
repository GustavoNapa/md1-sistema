<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Para SQLite, precisamos recriar a tabela
        if (DB::connection()->getDriverName() === 'sqlite') {
            // Salvar dados existentes
            $leads = DB::table('leads')->get();
            
            // Dropar e recriar a tabela
            Schema::dropIfExists('leads');
            
            Schema::create('leads', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('phone');
                $table->boolean('is_whatsapp')->default(false);
                $table->string('email')->nullable();
                $table->string('origin')->nullable();
                $table->string('origin_other')->nullable();
                $table->text('notes')->nullable();
                $table->foreignId('pipeline_id')->nullable()->constrained('pipelines')->onDelete('set null');
                $table->foreignId('pipeline_stage_id')->nullable()->constrained('pipeline_stages')->onDelete('set null');
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->integer('stage_order')->default(0);
                $table->boolean('is_archived')->default(false);
                $table->timestamps();
                $table->softDeletes();
            });
            
            // Restaurar dados
            foreach ($leads as $lead) {
                DB::table('leads')->insert((array) $lead);
            }
        } else {
            // Para outros bancos de dados
            Schema::table('leads', function (Blueprint $table) {
                $table->dropForeign(['pipeline_id']);
                $table->dropForeign(['pipeline_stage_id']);
                
                $table->foreignId('pipeline_id')->nullable()->change();
                $table->foreignId('pipeline_stage_id')->nullable()->change();
                
                $table->foreign('pipeline_id')->references('id')->on('pipelines')->onDelete('set null');
                $table->foreign('pipeline_stage_id')->references('id')->on('pipeline_stages')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        // Não há necessidade de reverter para SQLite
        if (DB::connection()->getDriverName() !== 'sqlite') {
            Schema::table('leads', function (Blueprint $table) {
                $table->dropForeign(['pipeline_id']);
                $table->dropForeign(['pipeline_stage_id']);
                
                $table->foreignId('pipeline_id')->nullable(false)->change();
                $table->foreignId('pipeline_stage_id')->nullable(false)->change();
                
                $table->foreign('pipeline_id')->references('id')->on('pipelines')->onDelete('cascade');
                $table->foreign('pipeline_stage_id')->references('id')->on('pipeline_stages')->onDelete('cascade');
            });
        }
    }
};
