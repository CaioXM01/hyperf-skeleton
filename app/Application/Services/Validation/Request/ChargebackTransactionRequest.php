<?php

declare(strict_types=1);

namespace App\Application\Services\Validation\Request;

use Hyperf\Validation\Request\FormRequest;

class ChargebackTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'chargeback_reason' => 'required|string|max:255',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'chargeback_reason.required' => 'O motivo do estorno é obrigatório.',
            'chargeback_reason.string' => 'O motivo do estorno deve ser uma string.',
            'chargeback_reason.max' => 'O motivo do estorno não pode exceder :max caracteres.',
        ];
    }
}
