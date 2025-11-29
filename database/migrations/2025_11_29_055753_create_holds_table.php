<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
 
    public function up(): void
    {
      
        Schema::create('holds', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')->constrained()->onDelete('cascade');

            $table->unsignedInteger('qty');

           
            $table->timestamp('expires_at')->index();

            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('holds');
    }
};