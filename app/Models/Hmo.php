<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hmo extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'code',
        'batch_criteria',
    ];

    /**
     * Get the orders associated with the HMO.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
