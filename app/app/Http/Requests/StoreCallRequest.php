<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCallRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'phone'         => ['required', 'string', 'max:20'],
            'description'   => ['required', 'string', 'max:1000'],
            'status'        => ['required', 'in:aberto,em_andamento,concluido,recusado'],
            'employees'     => ['sometimes', 'array'],
            'employees.*'   => ['exists:employees,id'],
        ];
    }
}
