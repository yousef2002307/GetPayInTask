<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Hold extends Model
{
    use HasFactory;

   
    protected $fillable = [
        'product_id',
        'qty',
        'expires_at',
        'is_expired',
        'is_used',
    ];


    protected $casts = [
        'expires_at' => 'datetime',
    ];
   
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}