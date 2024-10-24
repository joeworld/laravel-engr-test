<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_name',
        'batch_name',
        'batch_month',
        'batch_year',
    ];

    /**
     * Get the orders associated with the batch.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
