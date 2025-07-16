<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'vendor_id',
        'product_id',
        'class_group',
        'status',
        'classification',
        'has_medboss',
        'crmb_number',
        'start_date',
        'original_end_date',
        'actual_end_date',
        'platform_release_date',
        'calendar_week',
        'current_week',
        'amount_paid',
        'payment_method',
        'commercial_notes',
        'general_notes'
    ];

    protected $casts = [
        'start_date' => 'date',
        'original_end_date' => 'date',
        'actual_end_date' => 'date',
        'platform_release_date' => 'date',
        'has_medboss' => 'boolean',
        'amount_paid' => 'decimal:2'
    ];

    // Relacionamentos
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function preceptorRecords()
    {
        return $this->hasMany(PreceptorRecord::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    public function diagnostics()
    {
        return $this->hasMany(Diagnostic::class);
    }

    public function onboardingEvents()
    {
        return $this->hasMany(OnboardingEvent::class);
    }

    public function achievements()
    {
        return $this->hasMany(Achievement::class);
    }

    public function followUps()
    {
        return $this->hasMany(FollowUp::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    // Accessors
    public function getStatusLabelAttribute()
    {
        $labels = [
            'active' => 'Ativo',
            'paused' => 'Pausado',
            'cancelled' => 'Cancelado',
            'completed' => 'Concluído'
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public function getStatusBadgeClassAttribute()
    {
        $classes = [
            'active' => 'bg-success',
            'paused' => 'bg-warning',
            'cancelled' => 'bg-danger',
            'completed' => 'bg-info'
        ];

        return $classes[$this->status] ?? 'bg-secondary';
    }

    public function getFormattedAmountAttribute()
    {
        return $this->amount_paid ? 'R$ ' . number_format($this->amount_paid, 2, ',', '.') : '-';
    }

    public function getPaymentMethodLabelAttribute()
    {
        $labels = [
            'credit_card' => 'Cartão de Crédito',
            'debit_card' => 'Cartão de Débito',
            'bank_transfer' => 'Transferência Bancária',
            'pix' => 'PIX',
            'boleto' => 'Boleto',
            'cash' => 'Dinheiro',
            'installments' => 'Parcelado'
        ];

        return $labels[$this->payment_method] ?? $this->payment_method;
    }

    public function getMedbossLabelAttribute()
    {
        return $this->has_medboss ? 'Sim' : 'Não';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByProduct($query, $product)
    {
        return $query->where('product', $product);
    }

    public function scopeByVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

}
