<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $service
    ) {}

    public function index(): JsonResponse
    {
        return response()->json(['data' => $this->service->getAll()]);
    }

    public function store(ProductRequest $request): JsonResponse
    {
        $product = $this->service->create($request->validated());

        return response()->json(['data' => $product], 201);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json(['data' => $product]);
    }

    public function update(ProductRequest $request, Product $product): JsonResponse
    {
        $product = $this->service->update($product, $request->validated());

        return response()->json(['data' => $product]);
    }

    public function destroy(Product $product): JsonResponse
    {
        $this->service->delete($product);

        return response()->json(['message' => 'Product deleted successfully.']);
    }
}
