<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductRepository
{
    
    public function findById(int $id): Product
    {

        return Product::findOrFail($id);
    }

   
}
