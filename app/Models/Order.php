<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'total_amount',
        'status', // 'placed', 'packing', 'out_for_delivery', 'delivered', 'cancelled'
        'payment_method',
        'payment_status',
        'transaction_id',
        'shipping_address_json',
        'estimated_delivery_time',
        'delivery_partner_name',
        'delivery_partner_phone',
    ];

    protected $casts = [
        'shipping_address_json'   => 'array',
        'total_amount'            => 'decimal:2',
        'estimated_delivery_time' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}