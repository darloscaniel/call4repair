<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EmployeeController extends Controller
{
    // Paginated list of employees
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min(max($request->integer('per_page', 25), 1), 100);

        $query = Employee::latest('id');

        if ($search = trim((string) $request->query('search', ''))) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        return EmployeeResource::collection($query->paginate($perPage));
    }

    // Show a single employee
    public function show($id): EmployeeResource|JsonResponse
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json(['message' => __('messages.employee.not_found')], 404);
        }

        return new EmployeeResource($employee);
    }

    // Create a new employee
    public function store(StoreEmployeeRequest $request): JsonResponse
    {
        $employee = Employee::create($request->validated());

        return (new EmployeeResource($employee))
            ->response()
            ->setStatusCode(201);
    }

    // Update an existing employee
    public function update(UpdateEmployeeRequest $request, $id): EmployeeResource|JsonResponse
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json(['message' => __('messages.employee.not_found')], 404);
        }

        $employee->update($request->validated());

        return new EmployeeResource($employee);
    }

    // Delete an employee
    public function destroy($id): JsonResponse
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json(['message' => __('messages.employee.not_found')], 404);
        }

        // Detach all calls linked to this employee before deleting
        $employee->calls()->detach();

        $employee->delete();

        return response()->json(['message' => __('messages.employee.deleted')]);
    }
}
