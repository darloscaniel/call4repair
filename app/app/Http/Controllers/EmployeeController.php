<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    // Lista todos os funcionários
    public function index()
    {
        // Opcional: carregar chamados relacionados, caso queira
        // return response()->json(Employee::with('calls')->get());

        return response()->json(Employee::all());
    }

    // Mostra um funcionário específico
    public function show($id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json(['message' => 'Funcionário não encontrado.'], 404);
        }

        return response()->json($employee);
    }

    // Cria um novo funcionário
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'age'   => 'required|integer|min:0',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:employees,email',
        ]);

        $employee = Employee::create($validated);

        return response()->json($employee, 201);
    }

    // Atualiza um funcionário existente
    public function update(Request $request, $id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json(['message' => 'Funcionário não encontrado.'], 404);
        }

        $validated = $request->validate([
            'name'  => 'sometimes|required|string|max:255',
            'age'   => 'sometimes|required|integer|min:0',
            'phone' => 'sometimes|required|string|max:20',
            'email' => "sometimes|required|email|unique:employees,email,$id",
        ]);

        $employee->update($validated);

        return response()->json($employee);
    }

    // Deleta um funcionário
    public function destroy($id)
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
