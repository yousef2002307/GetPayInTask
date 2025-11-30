<?php

namespace App\Http\Controllers\Api\OrderController;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Repositories\OrderRepository;
class OrderController extends Controller
{
    public function store(OrderRequest $request)
    {
        try {
            $orderRepository = new OrderRepository();
            $orderRepository->store($request->only('hold_id'));
            return response()->json([
                'message' => 'Order created successfully',
            ], 201);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json([
                'message' => 'Failed to create order',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
}
