<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
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
        $employeeId = $this->route('employee');

        return [
            'name'  => ['sometimes', 'required', 'string', 'max:255'],
            'age'   => ['sometimes', 'required', 'integer', 'min:0'],
            'phone' => ['sometimes', 'required', 'string', 'max:20'],
            'email' => ['sometimes', 'required', 'email', Rule::unique('employees', 'email')->ignore($employeeId)],
        ];
    }
}
