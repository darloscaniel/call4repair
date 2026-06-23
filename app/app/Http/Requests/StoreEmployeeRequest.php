<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
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
            'name'  => ['required', 'string', 'max:255'],
            'age'   => ['required', 'integer', 'min:0'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'unique:employees,email'],
        ];
    }
}
