<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Cnpj implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $cnpj = preg_replace('/\D/', '', $value);

        if (!$cnpj || !ctype_digit($cnpj)) {
            $fail('O CNPJ deve conter apenas números.');
            return;
        }

        if (strlen($cnpj) != 14) {
            $fail('O CNPJ deve ter exatamente 14 dígitos.');
            return;
        }

        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            $fail('O CNPJ informado contém todos os dígitos iguais e é inválido.');
            return;
        }

        for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }

        $resto = $soma % 11;

        if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto)) {
            $fail('O CNPJ informado não passou na validação do primeiro dígito verificador.');
            return;
        }

        for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }

        $resto = $soma % 11;
        $digitoVerificador = ($resto < 2 ? 0 : 11 - $resto);

        if ($cnpj[13] != $digitoVerificador) {
            $fail('O CNPJ informado não passou na validação do segundo dígito verificador.');
            return;
        }
    }
}
