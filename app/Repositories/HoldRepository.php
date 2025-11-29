<?php

namespace App\Repositories;

use App\Models\Hold;
use App\Models\Product;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Collection;

class HoldRepository
{
  
    public function createHold(int $productId, int $qty): ?Hold
    {
        $product = Product::findOrFail($productId);

      // xdebug_break();
        $availableStock = $this->getAvailableStock($productId);
        
        if ($availableStock < $qty) {
            return null;
        }

      
        $hold = Hold::create([
            'product_id' => $productId,
            'qty' => $qty,
            'expires_at' => Carbon::now()->addMinutes(2),
        ]);
        xdebug_break();
        $product->stock -= $qty;
        $product->save();
        $product->refresh();
//xdebug_break();
        return $hold;
    }

    public function getAvailableStock(int $productId): int
    {
        $product = Product::findOrFail($productId);
        
       
        $heldQty = Hold::where('product_id', $productId)
            ->where('expires_at', '>', Carbon::now())
            ->sum('qty');

        return $product->stock - $heldQty;
    }


    public function findById(int $id): Hold
    {
        return Hold::findOrFail($id);
    }

    
    public function deleteExpiredHolds($id): int
    {
       $theHoldRecord = Hold::where('expires_at', '<=', Carbon::now())
           ->where('id', $id)
           ->first();
       
       if (!$theHoldRecord) {
           \Log::warning('Hold not found or not expired: ' . $id);
           return 0;
       }
       
       $product = Product::findOrFail($theHoldRecord->product_id);
       $product->stock += $theHoldRecord->qty;
       $product->save();
       
       $theHoldRecord->delete();
       
       \Log::info('Hold deleted: ' . $id . ', restored ' . $theHoldRecord->qty . ' units to product ' . $product->id);
       return 1;
    }

   
    public function getActiveHoldsByProduct(int $productId): Collection
    {
        return Hold::where('product_id', $productId)
            ->where('expires_at', '>', Carbon::now())
            ->get();
    }
}
