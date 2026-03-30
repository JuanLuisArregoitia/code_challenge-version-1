<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Models\Client;
use App\Services\ClientService;
use Illuminate\Http\JsonResponse;

class ClientController extends Controller
{
    public function __construct(
        protected ClientService $service
    ) {}

    public function index(): JsonResponse
    {
        return response()->json(['data' => $this->service->getAll()]);
    }

    public function store(ClientRequest $request): JsonResponse
    {
        $client = $this->service->create($request->validated());

        return response()->json(['data' => $client], 201);
    }

    public function show(Client $client): JsonResponse
    {
        return response()->json(['data' => $client]);
    }

    public function update(ClientRequest $request, Client $client): JsonResponse
    {
        $client = $this->service->update($client, $request->validated());

        return response()->json(['data' => $client]);
    }

    public function destroy(Client $client): JsonResponse
    {
        $this->service->delete($client);

        return response()->json(['message' => 'Client deleted successfully.']);
    }
}
