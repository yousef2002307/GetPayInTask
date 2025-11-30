<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class WebHookRepository
{
    public function updateorder($data)
    {

        DB::beginTransaction();
        try {
            DB::table("orders")
                ->where("id", $data["order_id"])
                ->lockForUpdate()
                ->update([
                    "payment_status" => "paid",
                    "updated_at" => now(),
                ]);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}