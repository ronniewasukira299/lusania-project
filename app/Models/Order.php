<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'status',               // Critical: for pending → assigned → in_transit → delivered
        'total_amount',
        'delivery_address',     // Fixed typo (assuming column is 'delivery_address')
        'payment_method',       // Keep for Cash on Delivery
    ];

    public function user(): BelongsTo  // Renamed for clarity (optional but recommended)
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function assignment(): HasOne  // Fixed: singular + correct name
    {
        return $this->hasOne(Assignment::class);  // Assuming model is Assignment.php
        // If pivot/foreign key is custom, add: ->foreignKey('order_id')
    }
}