<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierProductRequest;
use App\Models\SupplierProduct;
use App\Services\SupplierProductService;
use Illuminate\Http\JsonResponse;

class SupplierProductController extends Controller
{
    public function __construct(
        protected SupplierProductService $service
    ) {}

    public function index(): JsonResponse
    {
        return response()->json(['data' => $this->service->getAll()]);
    }

    public function store(SupplierProductRequest $request): JsonResponse
    {
        $supplierProduct = $this->service->create($request->validated());

        return response()->json(['data' => $supplierProduct], 201);
    }

    public function show(SupplierProduct $supplierProduct): JsonResponse
    {
        $supplierProduct->load(['supplier', 'product']);

        return response()->json(['data' => $supplierProduct]);
    }

    public function update(SupplierProductRequest $request, SupplierProduct $supplierProduct): JsonResponse
    {
        $supplierProduct = $this->service->update($supplierProduct, $request->validated());

        return response()->json(['data' => $supplierProduct]);
    }

    public function destroy(SupplierProduct $supplierProduct): JsonResponse
    {
        $this->service->delete($supplierProduct);

        return response()->json(['message' => 'Supplier product deleted successfully.']);
    }
}
