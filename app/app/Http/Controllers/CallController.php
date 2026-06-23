<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCallRequest;
use App\Http\Requests\UpdateCallRequest;
use App\Http\Resources\CallResource;
use App\Models\Call;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CallController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        // Retorna todos os chamados com os funcionários relacionados
        return CallResource::collection(Call::with('employees')->get());
    }

    public function store(StoreCallRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $call = Call::create([
            'customer_name' => $validated['customer_name'],
            'phone'         => $validated['phone'],
            'description'   => $validated['description'],
            'status'        => $validated['status'],
        ]);

        if (!empty($validated['employees'])) {
            $call->employees()->sync($validated['employees']);
        }

        return (new CallResource($call->load('employees')))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Call $call): CallResource
    {
        return new CallResource($call->load('employees'));
    }

    public function update(UpdateCallRequest $request, Call $call): CallResource
    {
        $validated = $request->validated();

        $call->update(collect($validated)->except('employees')->toArray());

        if (array_key_exists('employees', $validated)) {
            $call->employees()->sync($validated['employees'] ?? []);
        }

        return new CallResource($call->load('employees'));
    }

    public function destroy(Call $call): Response
    {
        $call->employees()->detach(); // Remove relacionamentos antes de deletar
        $call->delete();

        return response()->noContent();
    }
}
