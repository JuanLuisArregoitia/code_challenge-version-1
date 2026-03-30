<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderDetailRequest;
use App\Models\OrderDetail;
use App\Services\OrderDetailService;
use Illuminate\Http\JsonResponse;

class OrderDetailController extends Controller
{
    public function __construct(
        protected OrderDetailService $service
    ) {}

    public function index(): JsonResponse
    {
        return response()->json(['data' => $this->service->getAll()]);
    }

    public function store(OrderDetailRequest $request): JsonResponse
    {
        $orderDetail = $this->service->create($request->validated());

        return response()->json(['data' => $orderDetail], 201);
    }

    public function show(OrderDetail $orderDetail): JsonResponse
    {
        $orderDetail->load(['order', 'product']);

        return response()->json(['data' => $orderDetail]);
    }

    public function update(OrderDetailRequest $request, OrderDetail $orderDetail): JsonResponse
    {
        $orderDetail = $this->service->update($orderDetail, $request->validated());

        return response()->json(['data' => $orderDetail]);
    }

    public function destroy(OrderDetail $orderDetail): JsonResponse
    {
        $this->service->delete($orderDetail);

        return response()->json(['message' => 'Order detail deleted successfully.']);
    }
}
