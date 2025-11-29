<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

   
   
    protected $fillable = [
        'stock',
        'price',
    ];
    
    protected $casts = [
        'stock' => 'integer',
        'price' => 'decimal:2', 
    ];
}