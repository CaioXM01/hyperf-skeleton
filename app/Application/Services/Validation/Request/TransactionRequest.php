<?php

declare(strict_types=1);

namespace App\Application\Services\Validation\Request;

use Hyperf\Validation\Request\FormRequest;

class TransactionRequest extends FormRequest
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
            'value' => 'required|numeric|min:0.01',
            'payer' => 'required|integer|min:1',
            'payee' => 'required|integer|min:1',
        ];
    }

    /**
     * Get the error message of the defined validation rule
     */
    public function messages(): array
    {
        return [
            'value.required' => 'O campo valor é obrigatório.',
            'value.numeric' => 'O campo valor deve ser um número.',
            'value.min' => 'O valor deve ser maior que zero.',
            'payer.required' => 'O campo pagador é obrigatório.',
            'payer.integer' => 'O campo pagador deve ser um número inteiro.',
            'payer.min' => 'O valor do pagador deve ser maior ou igual a 1.',
            'payee.required' => 'O campo beneficiário é obrigatório.',
            'payee.integer' => 'O campo beneficiário deve ser um número inteiro.',
            'payee.min' => 'O valor do beneficiário deve ser maior ou igual a 1.',
        ];
    }
}
