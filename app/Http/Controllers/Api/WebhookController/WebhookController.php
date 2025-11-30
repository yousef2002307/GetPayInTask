<?php

namespace App\Http\Controllers\Api\WebhookController;

use App\Http\Controllers\Controller;
use App\Http\Requests\WebHookRequest;

class WebhookController extends Controller
{
    public function index(WebHookRequest $request)
    {
        try {
            $repository = new \App\Repositories\WebHookRepository();
            $repository->updateorder($request->only(['order_id']));
            
            return response()->json(['message' => 'Order updated successfully']);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['message' => 'Failed to update order'], 500);
        }
    }
}
