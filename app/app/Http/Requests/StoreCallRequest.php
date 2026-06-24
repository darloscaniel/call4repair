<?php

namespace App\Http\Requests;

use App\Enums\CallStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'status'        => ['required', Rule::in(CallStatus::values())],
            'employees'     => ['sometimes', 'array'],
            'employees.*'   => ['exists:employees,id'],
        ];
    }
}
