<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class IntegrationSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'integration_type',
        'key',
        'value',
        'is_encrypted',
        'is_active',
    ];

    protected $casts = [
        'is_encrypted' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the value attribute, decrypting if necessary.
     */
    public function getValueAttribute($value): ?string
    {
        if ($this->is_encrypted && $value) {
            try {
                return Crypt::decryptString($value);
            } catch (\Exception $e) {
                return $value; // Return original if decryption fails
            }
        }

        return $value;
    }

    /**
     * Set the value attribute, encrypting if necessary.
     */
    public function setValueAttribute($value): void
    {
        if ($this->is_encrypted && $value) {
            $this->attributes['value'] = Crypt::encryptString($value);
        } else {
            $this->attributes['value'] = $value;
        }
    }

    /**
     * Get a setting value by integration type and key.
     */
    public static function getValue(string $integrationType, string $key, $default = null)
    {
        $setting = self::where('integration_type', $integrationType)
            ->where('key', $key)
            ->where('is_active', true)
            ->first();

        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value.
     */
    public static function setValue(string $integrationType, string $key, $value, bool $isEncrypted = false): self
    {
        return self::updateOrCreate(
            [
                'integration_type' => $integrationType,
                'key' => $key,
            ],
            [
                'value' => $value,
                'is_encrypted' => $isEncrypted,
                'is_active' => true,
            ]
        );
    }

    /**
     * Get all settings for an integration type.
     */
    public static function getIntegrationSettings(string $integrationType): array
    {
        return self::where('integration_type', $integrationType)
            ->where('is_active', true)
            ->pluck('value', 'key')
            ->toArray();
    }

    /**
     * Scope for active settings.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for specific integration type.
     */
    public function scopeForIntegration($query, string $integrationType)
    {
        return $query->where('integration_type', $integrationType);
    }
}

