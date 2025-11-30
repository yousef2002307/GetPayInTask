<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
           
            $table->unsignedInteger('stock')->default(0); 
            $table->decimal('price', 8, 2);
            $table->timestamps();
        });
        DB::table('products')->insert([
            [
                'stock' => 1055,
                'price' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
 
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
