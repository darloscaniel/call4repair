<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EmployeeController extends Controller
{
    // Lista todos os funcionários
    public function index(): AnonymousResourceCollection
    {
        return EmployeeResource::collection(Employee::all());
    }

    // Mostra um funcionário específico
    public function show($id): EmployeeResource|JsonResponse
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json(['message' => 'Funcionário não encontrado.'], 404);
        }

        return new EmployeeResource($employee);
    }

    // Cria um novo funcionário
    public function store(StoreEmployeeRequest $request): JsonResponse
    {
        $employee = Employee::create($request->validated());

        return (new EmployeeResource($employee))
            ->response()
            ->setStatusCode(201);
    }

    // Atualiza um funcionário existente
    public function update(UpdateEmployeeRequest $request, $id): EmployeeResource|JsonResponse
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json(['message' => 'Funcionário não encontrado.'], 404);
        }

        $employee->update($request->validated());

        return new EmployeeResource($employee);
    }

    // Deleta um funcionário
    public function destroy($id): JsonResponse
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json(['message' => 'Funcionário não encontrado.'], 404);
        }

        // Desvincula todos os chamados desse funcionário antes de deletar
        $employee->calls()->detach();

        $employee->delete();

        return response()->json(['message' => 'Funcionário deletado com sucesso.']);
    }
}
