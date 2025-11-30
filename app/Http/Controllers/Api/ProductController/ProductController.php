<?php

namespace App\Http\Controllers\Api\ProductController;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Repositories\ProductRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Services\RedisService;

class ProductController extends Controller
{
    protected ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository,RedisService $redisService)
    {
        $this->productRepository = $productRepository;
        $this->redisService = $redisService;
    }


    /**
     * Display the specified product
     *
     * @param int $id
     * @return ProductResource|JsonResponse
     */
    public function show(int $id): ProductResource|JsonResponse
    {
        try {
          $key = "product_{$id}";
          if ($this->redisService->exists($key)) {
            \Log::info("Product found in cache");
            return response()->json([
                'data' => $this->redisService->get($key),
                "status"=>"from cache"
            ]);
          }
            $product = $this->productRepository->findById($id);
            $this->redisService->store($key, $product);
            \Log::info("Product stored in cache");
            //xdebug_break();
            return new ProductResource($product);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error($e->getMessage());
            return response()->json([
                'message' => 'Product not found',
                "status"=>"from db"
            ], 404);
        }
    }

  
   
}
