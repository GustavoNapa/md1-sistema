<?php

/**
 * ============================================================
 * AJUSTES NO MODELO Bonus.php
 * ============================================================
 * 
 * O modelo atual usa 'subscription_id' mas a tabela de inscrições
 * é 'inscriptions'. Existem duas opções:
 * 
 * OPÇÃO 1: Manter como está (se já existe tabela com subscription_id)
 *          Neste caso, o relacionamento no Inscription.php já está correto.
 * 
 * OPÇÃO 2: Usar inscription_id (mais semântico)
 *          Neste caso, precisa de migration e ajuste nos modelos.
 */

// ============================================================
// OPÇÃO 1: MODELO BONUS.PHP USANDO subscription_id (ATUAL)
// ============================================================
// Se quiser manter o campo subscription_id apontando para inscriptions.id,
// o modelo Bonus deve ter:

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bonus extends Model
{
    use HasFactory;

    protected $fillable = [
        "subscription_id",  // Este campo armazena o inscription_id
        "description",
        "release_date",
        "expiration_date",
    ];

    protected $casts = [
        "release_date" => "date",
        "expiration_date" => "date",
    ];

    /**
     * Relacionamento com Inscription (não Subscription)
     * subscription_id na verdade referencia inscriptions.id
     */
    public function inscription()
    {
        return $this->belongsTo(Inscription::class, 'subscription_id');
    }
    
    // Alias para compatibilidade
    public function subscription()
    {
        return $this->inscription();
    }
}


// ============================================================
// VERIFICAÇÃO: RELACIONAMENTO NO INSCRIPTION.PHP
// ============================================================
// O relacionamento já está correto no Inscription.php:

public function bonuses()
{
    return $this->hasMany(\App\Models\Bonus::class, 'subscription_id', 'id');
}


// ============================================================
// OPÇÃO 2 (ALTERNATIVA): MIGRATION PARA USAR inscription_id
// ============================================================
// Se preferir usar inscription_id em vez de subscription_id,
// execute esta migration:

/*
php artisan make:migration rename_subscription_id_to_inscription_id_in_bonuses_table

// Conteúdo da migration:
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bonuses', function (Blueprint $table) {
            // Renomear coluna
            $table->renameColumn('subscription_id', 'inscription_id');
        });
    }

    public function down(): void
    {
        Schema::table('bonuses', function (Blueprint $table) {
            $table->renameColumn('inscription_id', 'subscription_id');
        });
    }
};

// Se usar esta migration, atualize os modelos:

// Bonus.php:
// protected $fillable = ['inscription_id', 'description', ...];
// public function inscription() { return $this->belongsTo(Inscription::class); }

// Inscription.php:
// public function bonuses() { return $this->hasMany(Bonus::class); }


// ============================================================
// VERIFICAR SE A TABELA BONUSES EXISTE
// ============================================================
// Se a tabela não existir, crie com esta migration:

/*
php artisan make:migration create_bonuses_table

// Conteúdo:
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bonuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')  // ou inscription_id
                  ->constrained('inscriptions')
                  ->cascadeOnDelete();
            $table->string('description', 500);
            $table->date('release_date')->nullable();
            $table->date('expiration_date')->nullable();
            $table->timestamps();
            
            // Índice para busca
            $table->index('subscription_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bonuses');
    }
};
