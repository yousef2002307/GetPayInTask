<?php
namespace App\Repositories;
use App\Models\Order;
use App\Models\Hold;
use Illuminate\Support\Facades\DB;
class OrderRepository
{
    public function store($data)
    {
        DB::beginTransaction();
        try {
             DB::table('holds')->where('id', $data['hold_id'])->lockForUpdate()->update(['is_used' => true]);
            $order = Order::create($data);
           
            DB::commit();
            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}