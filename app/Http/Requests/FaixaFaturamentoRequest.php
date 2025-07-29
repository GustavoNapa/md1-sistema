<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FaixaFaturamentoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Apenas admins podem criar/editar faixas de faturamento
        return auth()->check() && auth()->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'label' => 'required|string|max:50',
            'valor_min' => 'required|numeric|min:0',
            'valor_max' => 'required|numeric|gt:valor_min',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'label.required' => 'O nome da faixa é obrigatório.',
            'label.max' => 'O nome da faixa não pode ter mais de 50 caracteres.',
            'valor_min.required' => 'O valor mínimo é obrigatório.',
            'valor_min.numeric' => 'O valor mínimo deve ser um número.',
            'valor_min.min' => 'O valor mínimo não pode ser negativo.',
            'valor_max.required' => 'O valor máximo é obrigatório.',
            'valor_max.numeric' => 'O valor máximo deve ser um número.',
            'valor_max.gt' => 'O valor máximo deve ser maior que o valor mínimo.',
        ];
    }
}

