<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Cpf implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $cpf = preg_replace('/\D/', '', $value);

        if (!$cpf || !ctype_digit($cpf)) {
            $fail('O CPF informado deve conter apenas números.');
            return;
        }

        if (strlen($cpf) !== 11) {
            $fail('O CPF deve ter exatamente 11 dígitos.');
            return;
        }

        if (preg_match('/(\d)\1{10}/', $cpf)) {
            $fail('O CPF informado contém todos os dígitos iguais, e portanto é inválido.');
            return;
        }

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }

            $d = ((10 * $d) % 11) % 10;

            if ($cpf[$c] != $d) {
                $fail('O CPF informado não passou na validação dos dígitos verificadores.');
                return;
            }
        }
    }
}
