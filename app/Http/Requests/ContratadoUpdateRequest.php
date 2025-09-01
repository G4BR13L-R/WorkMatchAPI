<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContratadoUpdateRequest extends FormRequest
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
        $userId = $this->user()->id;
        $contratadoId = $this->user()->contratado ? $this->user()->contratado->id : null;

        return [
            'nome' => 'required|string|max:255',
            'telefone' => 'required|string|size:11',
            'email' => 'required|string|max:255|email|unique:users,email,' . $userId,
            'data_nascimento' => 'required|date',
            'cpf' => 'required|string|size:11|unique:contratados,cpf,' . $contratadoId,
            'rg' => 'nullable|string|max:20',
            'logradouro' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'complemento' => 'nullable|string|max:255',
            'bairro' => 'nullable|string|max:255',
            'cidade_id' => 'nullable|integer|exists:cidades,id',
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
            'nome.required' => 'O campo nome é obrigatório.',
            'nome.string' => 'O campo nome deve ser uma string.',
            'nome.max' => 'O campo nome não pode ter mais de 255 caracteres.',
            'telefone.required' => 'O campo telefone é obrigatório.',
            'telefone.string' => 'O campo telefone deve ser uma string.',
            'telefone.size' => 'O campo telefone deve ter 11 caracteres.',
            'email.required' => 'O campo email é obrigatório.',
            'email.string' => 'O campo email deve ser uma string.',
            'email.max' => 'O campo email não pode ter mais de 255 caracteres.',
            'email.email' => 'O campo email deve ser um endereço de email válido.',
            'email.unique' => 'O email já está em uso.',
            'data_nascimento.required' => 'O campo data de nascimento é obrigatório.',
            'data_nascimento.date' => 'O campo data de nascimento deve ser uma data válida.',
            'cpf.required' => 'O campo CPF é obrigatório.',
            'cpf.string' => 'O campo CPF deve ser uma string.',
            'cpf.size' => 'O campo CPF deve ter 11 caracteres.',
            'cpf.unique' => 'O CPF já está em uso.',
            'rg.string' => 'O campo RG deve ser uma string.',
            'rg.max' => 'O campo RG não pode ter mais de 20 caracteres.',
            'logradouro.string' => 'O campo logradouro deve ser uma string.',
            'numero.string' => 'O campo número deve ser uma string.',
            'numero.max' => 'O campo número não pode ter mais de 20 caracteres.',
            'bairro.string' => 'O campo bairro deve ser uma string.',
            'bairro.max' => 'O campo bairro não pode ter mais de 255 caracteres.',
            'cidade_id.integer' => 'O campo cidade deve ser um número inteiro.',
            'cidade_id.exists' => 'A cidade selecionada não é válida.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'nome' => trim($this->nome),
            'telefone' => preg_replace('/\D/', '', $this->telefone),
            'email' => strtolower(trim($this->email)),
            'data_nascimento' => date('Y-m-d', strtotime($this->data_nascimento)),
            'cpf' => preg_replace('/\D/', '', $this->cpf),
            'rg' => !empty($this->rg) ? preg_replace('/\D/', '', $this->rg) : null,
        ]);
    }
}
