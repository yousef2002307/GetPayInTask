<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'hold_id',
        'payment_status',
    ];

    protected $casts = [
        'payment_status' => 'string',
    ];

    public function hold(): BelongsTo
    {
        return $this->belongsTo(Hold::class);
    }
}
