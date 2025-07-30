<?php

namespace App\Models;

use App\Jobs\ProcessInscriptionWebhook;
use App\Models\ProductWebhook;
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
        'general_notes',
        'problemas_desafios',
        'historico_faturamento',
        'entry_channel',
        'contrato_assinado',
        'contrato_na_pasta',
        'contract_folder_link',
        'natureza_juridica',
        'cpf_cnpj',
        'valor_total',
        'forma_pagamento_entrada',
        'valor_entrada',
        'data_pagamento_entrada',
        'forma_pagamento_restante',
        'valor_restante',
        'data_contrato'
    ];

    protected $casts = [
        'start_date' => 'date',
        'original_end_date' => 'date',
        'actual_end_date' => 'date',
        'platform_release_date' => 'date',
        'has_medboss' => 'boolean',
        'amount_paid' => 'decimal:2',
        'historico_faturamento' => 'array',
        'valor_total' => 'decimal:2',
        'valor_entrada' => 'decimal:2',
        'valor_restante' => 'decimal:2',
        'data_pagamento_entrada' => 'date',
        'data_contrato' => 'date'
    ];

    protected static function boot()
    {
        parent::boot();

        // Webhooks agora são disparados via eventos específicos no controller
        // para garantir que todos os dados relacionados (endereços, pagamentos) 
        // sejam criados antes do envio
    }

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
        return $this->hasMany(InscriptionDocument::class);
    }


    public function bonuses()
    {
        return $this->hasMany(\App\Models\Bonus::class, 'subscription_id', 'id');
    }

    public function faturamentos()
    {
        return $this->hasMany(Faturamento::class);
    }

    public function renovacoes()
    {
        return $this->hasMany(Renovacao::class);
    }

    public function entryChannel()
    {
        return $this->belongsTo(EntryChannel::class, 'entry_channel');
    }

    /**
     * Obtém a faixa de faturamento baseada no valor pago
     */
    public function getFaixaFaturamento()
    {
        if (!$this->amount_paid) {
            return null;
        }
        
        return FaixaFaturamento::findByValue((float)$this->amount_paid);
    }

    /**
     * Accessor para obter o label da faixa de faturamento
     */
    public function getFaixaFaturamentoLabelAttribute()
    {
        $faixa = $this->getFaixaFaturamento();
        return $faixa ? $faixa->label : 'Não definida';
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

    public function getHistoricoFaturamentoFormattedAttribute()
    {
        if (!$this->historico_faturamento || !is_array($this->historico_faturamento)) {
            return [];
        }

        return collect($this->historico_faturamento)->map(function ($item) {
            return [
                'mes' => $item['mes'] ?? '',
                'ano' => $item['ano'] ?? '',
                'valor' => isset($item['valor']) ? 'R$ ' . number_format($item['valor'], 2, ',', '.') : 'R$ 0,00',
                'valor_raw' => $item['valor'] ?? 0
            ];
        })->toArray();
    }

    public function addFaturamentoMensal(int $mes, int $ano, float $valor): void
    {
        $historico = $this->historico_faturamento ?? [];
        
        // Verificar se já existe registro para este mês/ano
        $index = collect($historico)->search(function ($item) use ($mes, $ano) {
            return $item['mes'] == $mes && $item['ano'] == $ano;
        });

        if ($index !== false) {
            // Atualizar registro existente
            $historico[$index]['valor'] = $valor;
        } else {
            // Adicionar novo registro
            $historico[] = [
                'mes' => $mes,
                'ano' => $ano,
                'valor' => $valor
            ];
        }

        // Ordenar por ano e mês
        usort($historico, function ($a, $b) {
            if ($a['ano'] == $b['ano']) {
                return $a['mes'] <=> $b['mes'];
            }
            return $a['ano'] <=> $b['ano'];
        });

        $this->historico_faturamento = $historico;
        $this->save();
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


