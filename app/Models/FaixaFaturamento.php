<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaixaFaturamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'valor_min',
        'valor_max',
    ];

    protected $casts = [
        'valor_min' => 'decimal:2',
        'valor_max' => 'decimal:2',
    ];

    /**
     * Scope para buscar por label
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('label', 'like', '%' . $search . '%');
    }

    /**
     * Accessor para formatação monetária
     */
    public function getValorMinFormattedAttribute()
    {
        return 'R$ ' . number_format($this->valor_min, 2, ',', '.');
    }

    /**
     * Accessor para formatação monetária
     */
    public function getValorMaxFormattedAttribute()
    {
        return 'R$ ' . number_format($this->valor_max, 2, ',', '.');
    }

    /**
     * Accessor para range formatado
     */
    public function getRangeFormattedAttribute()
    {
        return $this->valor_min_formatted . ' - ' . $this->valor_max_formatted;
    }

    /**
     * Verifica se um valor está dentro desta faixa
     */
    public function containsValue($value)
    {
        return $value >= $this->valor_min && $value <= $this->valor_max;
    }

    /**
     * Busca a faixa que contém um determinado valor
     */
    public static function findByValue($value)
    {
        return static::where('valor_min', '<=', $value)
            ->where('valor_max', '>=', $value)
            ->first();
    }
}

