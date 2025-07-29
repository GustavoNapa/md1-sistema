<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'offer_price',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'offer_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relacionamentos
    public function inscriptions()
    {
        return $this->hasMany(Inscription::class);
    }

    public function webhooks()
    {
        return $this->hasMany(ProductWebhook::class);
    }

    // Accessors
    public function getStatusLabelAttribute()
    {
        return $this->is_active ? 'Ativo' : 'Inativo';
    }

    public function getFormattedPriceAttribute()
    {
        return 'R$ ' . number_format($this->price, 2, ',', '.');
    }

    public function getFormattedOfferPriceAttribute()
    {
        return $this->offer_price ? 'R$ ' . number_format($this->offer_price, 2, ',', '.') : null;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
