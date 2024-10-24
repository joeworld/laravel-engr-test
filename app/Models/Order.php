<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_name',
        'hmo_id',
        'batch_id',
        'encounter_date',
        'order_amount',
    ];

    protected $casts = [
        'encounter_date' => 'date',
    ];

    /**
     * Get the HMO associated with the order.
     */
    public function hmo()
    {
        return $this->belongsTo(HMO::class);
    }

    /**
     * Get the batch associated with the order.
     */
    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    /**
     * Get the items associated with the order.
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}