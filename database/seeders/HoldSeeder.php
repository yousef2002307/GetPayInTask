<?php

namespace Database\Seeders;

use App\Models\Hold;
use Illuminate\Database\Seeder;
use App\Models\Product; 

class HoldSeeder extends Seeder
{
    
   
    public function run(): void
    {
       
        if (Product::count() > 0) {
          
            Hold::factory()->count(20)->create();
        } else {
            \Log::warning('Product table is empty. Skipping HoldSeeder.');
        }
    }
}