<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCallRequest extends FormRequest
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
            'customer_name' => ['sometimes', 'required', 'string', 'max:255'],
            'phone'         => ['sometimes', 'nullable', 'string', 'max:20'],
            'description'   => ['sometimes', 'required', 'string', 'max:1000'],
            'status'        => ['sometimes', 'required', 'in:open,in_progress,done,rejected'],
            'employees'     => ['sometimes', 'array'],
            'employees.*'   => ['exists:employees,id'],
        ];
    }
}
