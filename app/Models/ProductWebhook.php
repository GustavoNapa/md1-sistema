<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductWebhook extends Model
{
    use HasFactory;

    protected $fillable = [
        "product_id",
        "webhook_url",
        "webhook_token",
        "webhook_trigger_status",
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function webhookLogs()
    {
        return $this->hasMany(WebhookLog::class, "webhook_url", "webhook_url");
    }
}

