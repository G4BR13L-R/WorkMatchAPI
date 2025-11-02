<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContratadoRegisterRequest extends FormRequest
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
            'nome' => 'required|string|max:255',
            'telefone' => 'required|string|size:11',
            'email' => 'required|string|max:255|email|unique:users,email',
            'data_nascimento' => 'required|date',
            'cpf' => 'required|string|size:11|unique:contratados,cpf',
            'rg' => 'nullable|string|max:20',
            'cidade_id' => 'required|integer|exists:cidades,id',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|string|min:6',
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
            'cidade_id.required' => 'O campo cidade é obrigatório.',
            'cidade_id.integer' => 'O campo cidade deve ser um inteiro.',
            'cidade_id.exists' => 'A cidade selecionada é inválida.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.string' => 'O campo senha deve ser uma string.',
            'password.min' => 'A senha deve ter pelo menos 6 caracteres.',
            'password.confirmed' => 'As senhas não coincidem.',
            'password_confirmation.required' => 'A confirmação de senha é obrigatória.',
            'password_confirmation.string' => 'A confirmação de senha deve ser uma string.',
            'password_confirmation.min' => 'A confirmação de senha deve ter pelo menos 6 caracteres.',
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
