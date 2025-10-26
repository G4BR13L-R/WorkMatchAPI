<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AvaliacaoRequest extends FormRequest
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
            'autor_id' => 'required|integer',
            'autor_tipo' => 'required|string|in:contratante,contratado',
            'destinatario_id' => 'required|integer',
            'destinatario_tipo' => 'required|string|in:contratante,contratado',
            'oferta_id' => 'required|integer|exists:ofertas,id',
            'nota' => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string|max:1000',
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
            'autor_id.required' => 'O ID do autor é obrigatório.',
            'autor_id.integer' => 'O ID do autor deve ser um número inteiro.',
            'autor_tipo.required' => 'O tipo do autor é obrigatório.',
            'autor_tipo.in' => 'O tipo do autor deve ser "contratante" ou "contratado".',
            'destinatario_id.required' => 'O ID do destinatário é obrigatório.',
            'destinatario_id.integer' => 'O ID do destinatário deve ser um número inteiro.',
            'destinatario_tipo.required' => 'O tipo do destinatário é obrigatório.',
            'destinatario_tipo.in' => 'O tipo do destinatário deve ser "contratante" ou "contratado".',
            'oferta_id.required' => 'O ID da oferta é obrigatório.',
            'oferta_id.integer' => 'O ID da oferta deve ser um número inteiro.',
            'oferta_id.exists' => 'O ID da oferta deve existir na tabela de ofertas.',
            'nota.required' => 'A nota é obrigatória.',
            'nota.integer' => 'A nota deve ser um número inteiro.',
            'nota.min' => 'A nota deve ser pelo menos 1.',
            'nota.max' => 'A nota deve ser no máximo 5.',
            'comentario.string' => 'O comentário deve ser uma string.',
            'comentario.max' => 'O comentário não pode ter mais de 1000 caracteres.',
        ];
    }
}
