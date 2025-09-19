<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OfertaRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'titulo' => 'required|string|max:255',
            'descricao' => 'required|string',
            'salario' => 'nullable|numeric|min:0',
            'data_inicio' => 'required|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
            'logradouro' => 'required|string|max:255',
            'numero' => 'required|string|max:20',
            'complemento' => 'required|string|max:255',
            'bairro' => 'required|string|max:255',
            'cidade_id' => 'required|integer|exists:cidades,id',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'titulo.required' => 'O campo título é obrigatório.',
            'titulo.string' => 'O campo título deve ser uma string.',
            'titulo.max' => 'O campo título não deve exceder 255 caracteres.',
            'descricao.required' => 'O campo descrição é obrigatório.',
            'descricao.string' => 'O campo descrição deve ser uma string.',
            'salario.numeric' => 'O campo salário deve ser um número.',
            'salario.min' => 'O campo salário não pode ser negativo.',
            'data_inicio.required' => 'O campo data de início é obrigatório.',
            'data_inicio.date' => 'O campo data de início deve ser uma data válida.',
            'data_fim.date' => 'O campo data de fim deve ser uma data válida.',
            'data_fim.after_or_equal' => 'O campo data de fim deve ser uma data igual ou posterior à data de início.',
            'logradouro.required' => 'O campo logradouro é obrigatório.',
            'logradouro.string' => 'O campo logradouro deve ser uma string.',
            'logradouro.max' => 'O campo logradouro não deve exceder 255 caracteres.',
            'numero.required' => 'O campo número é obrigatório.',
            'numero.string' => 'O campo número deve ser uma string.',
            'numero.max' => 'O campo número não deve exceder 20 caracteres.',
            'complemento.required' => 'O campo complemento é obrigatório.',
            'complemento.string' => 'O campo complemento deve ser uma string.',
            'complemento.max' => 'O campo complemento não deve exceder 255 caracteres.',
            'bairro.required' => 'O campo bairro é obrigatório.',
            'bairro.string' => 'O campo bairro deve ser uma string.',
            'bairro.max' => 'O campo bairro não deve exceder 255 caracteres.',
            'cidade_id.required' => 'O campo cidade é obrigatório.',
            'cidade_id.integer' => 'O campo cidade deve ser um número inteiro.',
            'cidade_id.exists' => 'A cidade selecionada é inválida.',
        ];
    }
}
