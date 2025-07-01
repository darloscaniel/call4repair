<?php

namespace App\Http\Controllers;

use App\Models\Call;
use App\Models\Employee;  // Importar Employee para o validator 'exists'
use Illuminate\Http\Request;

class CallController extends Controller
{
    public function index()
    {
        // Retorna todos os chamados com os funcionários relacionados
        return Call::with('employees')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'description' => 'required|string|max:1000',
            'status' => 'required|in:aberto,em_andamento,concluido,recusado',
            'employees' => 'array',
            'employees.*' => 'exists:employees,id',  // valida se cada id existe na tabela employees
        ]);

        // Criar chamado
        $call = Call::create([
            'customer_name' => $validated['customer_name'],
            'phone' => $validated['phone'],
            'description' => $validated['description'],
            'status' => $validated['status'],
        ]);

        // Sincronizar funcionários, se enviados
        if (!empty($validated['employees'])) {
            $call->employees()->sync($validated['employees']);
        }

        return response()->json($call->load('employees'), 201);
    }

    public function show(Call $call)
    {
        return $call->load('employees');
    }

    public function update(Request $request, Call $call)
    {
        $validated = $request->validate([
            'customer_name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:20',
            'description' => 'sometimes|required|string|max:1000',
            'status' => 'sometimes|required|in:aberto,em_andamento,concluido,recusado',
            'employees' => 'sometimes|array',
            'employees.*' => 'exists:employees,id',
        ]);

        $call->update($request->only(['customer_name','phone', 'description', 'status']));

        if (isset($validated['employees'])) {
            $call->employees()->sync($validated['employees']);
        }

        return $call->load('employees');
    }

    public function destroy(Call $call)
    {
        $call->employees()->detach(); // Remove relacionamentos antes de deletar
        $call->delete();

        return response()->noContent();
    }
}
