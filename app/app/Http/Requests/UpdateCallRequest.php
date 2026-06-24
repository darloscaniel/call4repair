<?php

namespace App\Http\Requests;

use App\Enums\CallStatus;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'status'        => ['sometimes', 'required', Rule::in(CallStatus::values())],
            'employees'     => ['sometimes', 'array'],
            'employees.*'   => ['exists:employees,id'],
        ];
    }

    /**
     * Enforce the status state machine: only allow transitions permitted from
     * the call's current status (staying on the same status is always fine).
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (! $this->filled('status')) {
                return;
            }

            $call = $this->route('call');
            $current = CallStatus::tryFrom($call->status);
            $target = CallStatus::tryFrom($this->input('status'));

            if ($current && $target && ! $current->canTransitionTo($target)) {
                $validator->errors()->add('status', __('messages.call.invalid_transition', [
                    'from' => $current->value,
                    'to'   => $target->value,
                ]));
            }
        });
    }
}
