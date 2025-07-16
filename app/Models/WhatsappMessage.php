<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'phone',
        'message',
        'type',
        'status',
        'sent_at',
        'response',
        'external_id'
    ];

    protected $casts = [
        'sent_at' => 'datetime'
    ];

    // Relacionamentos
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Accessors
    public function getFormattedPhoneAttribute()
    {
        $phone = preg_replace('/[^0-9]/', '', $this->phone);
        if (strlen($phone) == 11) {
            return '(' . substr($phone, 0, 2) . ') ' . substr($phone, 2, 5) . '-' . substr($phone, 7, 4);
        }
        return $this->phone;
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'Pendente',
            'sent' => 'Enviado',
            'delivered' => 'Entregue',
            'read' => 'Lido',
            'failed' => 'Falhou',
            default => 'Desconhecido'
        };
    }

    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'sent' => 'Enviado',
            'received' => 'Recebido',
            default => 'Desconhecido'
        };
    }
}
