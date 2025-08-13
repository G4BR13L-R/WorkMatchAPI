<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContratanteUpdateRequest extends FormRequest
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
        $contratanteId = $this->user()->contratante ? $this->user()->contratante->id : null;

        return [
            'nome' => 'required|string|max:255',
            'telefone' => 'required|string|size:11',
            'email' => 'required|string|max:255|email|unique:users,email,' . $userId,
            'cnpj' => 'required|string|size:14|unique:contratantes,cnpj,' . $contratanteId,
            'razao_social' => 'required|string|max:255',
            'nome_fantasia' => 'required|string|max:255',
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
            'cnpj.required' => 'O campo CNPJ é obrigatório.',
            'cnpj.string' => 'O campo CNPJ deve ser uma string.',
            'cnpj.size' => 'O campo CNPJ deve ter 14 caracteres.',
            'cnpj.unique' => 'O CNPJ já está em uso.',
            'razao_social.required' => 'O campo razão social é obrigatório.',
            'razao_social.string' => 'O campo razão social deve ser uma string.',
            'razao_social.max' => 'O campo razão social não pode ter mais de 255 caracteres.',
            'nome_fantasia.required' => 'O campo nome fantasia é obrigatório.',
            'nome_fantasia.string' => 'O campo nome fantasia deve ser uma string.',
            'nome_fantasia.max' => 'O campo nome fantasia não pode ter mais de 255 caracteres.',
            'logradouro.string' => 'O campo logradouro deve ser uma string.',
            'logradouro.max' => 'O campo logradouro não pode ter mais de 255 caracteres.',
            'numero.string' => 'O campo número deve ser uma string.',
            'numero.max' => 'O campo número não pode ter mais de 20 caracteres.',
            'complemento.string' => 'O campo complemento deve ser uma string.',
            'complemento.max' => 'O campo complemento não pode ter mais de 255 caracteres.',
            'bairro.string' => 'O campo bairro deve ser uma string.',
            'bairro.max' => 'O campo bairro não pode ter mais de 255 caracteres.',
            'cidade_id.integer' => 'O campo cidade deve ser um número inteiro.',
            'cidade_id.exists' => 'A cidade selecionada não existe.',
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
            'cnpj' => preg_replace('/\D/', '', $this->cnpj),
        ]);
    }
}
