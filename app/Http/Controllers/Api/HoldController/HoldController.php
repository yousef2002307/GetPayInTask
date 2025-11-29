<?php

namespace App\Http\Controllers\Api\HoldController;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateHoldRequest;
use App\Http\Resources\HoldResource;
use App\Repositories\HoldRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class HoldController extends Controller
{
    protected HoldRepository $holdRepository;

    public function __construct(HoldRepository $holdRepository)
    {
        $this->holdRepository = $holdRepository;
    }

 
    public function store(CreateHoldRequest $request): HoldResource|JsonResponse
    {
        try {
            $validated = $request->validated();
            
            $hold = $this->holdRepository->createHold(
                $validated['product_id'],
                $validated['qty']
            );
//xdebug_break();
            if (!$hold) {
                return response()->json([
                    'message' => 'Insufficient stock available',
                    'available_stock' => $this->holdRepository->getAvailableStock($validated['product_id'])
                ], 422);
            }
//xdebug_break();
            return new HoldResource($hold);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Product not found: ' . $e->getMessage());
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error creating hold: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to create hold'
            ], 500);
        }
    }

}
