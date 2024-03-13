<?php

declare(strict_types=1);

namespace App\Application\Services\Validation\Request;

use Hyperf\Validation\Request\FormRequest;

class UserRequest extends FormRequest
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
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'document' => 'required|string|max:20|unique:users,document',
            'password' => 'required|string|min:6',
            'balance' => 'required|numeric|min:0',
            'type' => 'required|string|in:common,shopkeeper',
        ];
    }

    /**
     * Get the error message of the defined validation rule
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'email.required' => 'O campo email é obrigatório.',
            'email.email' => 'Por favor, insira um endereço de email válido.',
            'document.required' => 'O campo documento é obrigatório.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.min' => 'A senha deve ter no mínimo :min caracteres.',
            'balance.required' => 'O campo saldo é obrigatório.',
            'balance.numeric' => 'O campo saldo deve ser um número.',
            'balance.min' => 'O saldo não pode ser negativo.',
            'type.required' => 'O campo tipo é obrigatório.',
            'type.in' => 'O campo tipo deve ser "common" ou "shopkeeper".',
        ];
    }
}
