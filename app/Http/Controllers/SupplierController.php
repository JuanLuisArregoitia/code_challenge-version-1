<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use App\Services\SupplierService;
use Illuminate\Http\JsonResponse;

class SupplierController extends Controller
{
    public function __construct(
        protected SupplierService $service
    ) {}

    public function index(): JsonResponse
    {
        return response()->json(['data' => $this->service->getAll()]);
    }

    public function store(SupplierRequest $request): JsonResponse
    {
        $supplier = $this->service->create($request->validated());

        return response()->json(['data' => $supplier], 201);
    }

    public function show(Supplier $supplier): JsonResponse
    {
        return response()->json(['data' => $supplier]);
    }

    public function update(SupplierRequest $request, Supplier $supplier): JsonResponse
    {
        $supplier = $this->service->update($supplier, $request->validated());

        return response()->json(['data' => $supplier]);
    }

    public function destroy(Supplier $supplier): JsonResponse
    {
        $this->service->delete($supplier);

        return response()->json(['message' => 'Supplier deleted successfully.']);
    }
}
