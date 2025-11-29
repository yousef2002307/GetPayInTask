<?php

namespace App\Http\Controllers\Api\ProductController;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Repositories\ProductRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    protected ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
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
            $product = $this->productRepository->findById($id);
            return new ProductResource($product);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error($e->getMessage());
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }
    }

  
   
}
