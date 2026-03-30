<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $service
    ) {}

    public function index(): JsonResponse
    {
        return response()->json(['data' => $this->service->getAll()]);
    }

    public function store(OrderRequest $request): JsonResponse
    {
        $order = $this->service->create($request->validated());

        return response()->json(['data' => $order], 201);
    }

    public function show(Order $order): JsonResponse
    {
        $order->load(['client', 'orderDetails.product']);

        return response()->json(['data' => $order]);
    }

    public function update(OrderRequest $request, Order $order): JsonResponse
    {
        $order = $this->service->update($order, $request->validated());

        return response()->json(['data' => $order]);
    }

    public function destroy(Order $order): JsonResponse
    {
        $this->service->delete($order);

        return response()->json(['message' => 'Order deleted successfully.']);
    }
}
