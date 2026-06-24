<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCallRequest;
use App\Http\Requests\UpdateCallRequest;
use App\Http\Resources\CallResource;
use App\Models\Call;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CallController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        // Paginated list of calls with their related employees
        $perPage = min(max($request->integer('per_page', 25), 1), 100);

        $query = Call::with('employees')->latest('id');

        if ($search = trim((string) $request->query('search', ''))) {
            $query->where('customer_name', 'like', "%{$search}%");
        }

        return CallResource::collection($query->paginate($perPage));
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
        $call->employees()->detach(); // Remove relations before deleting
        $call->delete();

        return response()->noContent();
    }
}
