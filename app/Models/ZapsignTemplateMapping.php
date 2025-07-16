<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ZapsignTemplateMapping extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'zapsign_template_id',
        'description',
        'field_mappings',
        'auto_sign',
        'signer_name',
        'signer_email',
        'is_active',
    ];

    protected $casts = [
        'field_mappings' => 'array',
        'auto_sign' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the documents created from this template mapping.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(ZapsignDocument::class, 'template_mapping_id');
    }

    /**
     * Get the field mappings formatted for display.
     */
    public function getFormattedFieldMappingsAttribute(): array
    {
        if (!$this->field_mappings || !is_array($this->field_mappings)) {
            return [];
        }

        return collect($this->field_mappings)->map(function ($mapping) {
            return [
                'zapsign_field' => $mapping['zapsign_field'] ?? '',
                'system_field' => $mapping['system_field'] ?? '',
                'field_type' => $mapping['field_type'] ?? 'text',
                'default_value' => $mapping['default_value'] ?? '',
            ];
        })->toArray();
    }

    /**
     * Get available system fields for mapping.
     */
    public static function getAvailableSystemFields(): array
    {
        return [
            'client.name' => 'Nome do Cliente',
            'client.email' => 'E-mail do Cliente',
            'client.cpf' => 'CPF do Cliente',
            'client.cnpj' => 'CNPJ do Cliente',
            'client.phone' => 'Telefone do Cliente',
            'client.address' => 'Endereço do Cliente',
            'client.city' => 'Cidade do Cliente',
            'client.state' => 'Estado do Cliente',
            'client.zip_code' => 'CEP do Cliente',
            'inscription.start_date' => 'Data de Início da Inscrição',
            'inscription.end_date' => 'Data de Término da Inscrição',
            'inscription.amount_paid' => 'Valor Pago',
            'inscription.class_group' => 'Turma',
            'product.name' => 'Nome do Produto',
            'product.price' => 'Preço do Produto',
            'vendor.name' => 'Nome do Vendedor',
            'vendor.email' => 'E-mail do Vendedor',
        ];
    }

    /**
     * Resolve field value from inscription data.
     */
    public function resolveFieldValue(string $fieldPath, $inscription): string
    {
        $parts = explode('.', $fieldPath);
        $value = $inscription;

        foreach ($parts as $part) {
            if (is_object($value) && isset($value->$part)) {
                $value = $value->$part;
            } elseif (is_array($value) && isset($value[$part])) {
                $value = $value[$part];
            } else {
                return '';
            }
        }

        // Format specific field types
        if ($value instanceof \Carbon\Carbon) {
            return $value->format('d/m/Y');
        }

        if (is_numeric($value) && str_contains($fieldPath, 'amount')) {
            return 'R$ ' . number_format($value, 2, ',', '.');
        }

        return (string) $value;
    }

    /**
     * Scope for active mappings.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

