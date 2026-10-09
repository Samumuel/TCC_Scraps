<?php 

namespace app\helpers;

class Validador {

    private array $erros = [];

    public function obrigatorio(string $campo, mixed $valor, ?string $mensagem = null) {

        //! = = 
        if (empty($valor) && $valor !== '0') {
            $this->erros[$campo] = $mensagem ?? "O campo {$campo} é obrigatório";
        }

        return $this;

    }
    //valida os emails
    public function email(string $campo, mixed $valor): self
    {
        if ($valor === null || $valor === '') {
            return $this;
        }

        if (filter_var((string) $valor, FILTER_VALIDATE_EMAIL) === false) {
            $this->erros[$campo] = 'E-mail inválido.';
        }

        return $this;
    }
    // valida os cpfs
    public function cpf(string $campo, mixed $valor): self
    {
        if ($valor === null || $valor === '') {
            return $this;
        }

        $cpf = (string) $valor;
        if (!preg_match('/^\d{11}$/', $cpf) || preg_match('/^(\d)\1{10}$/', $cpf)) {
            $this->erros[$campo] = 'O CPF deve conter 11 números e ser válido.';
            return $this;
        }

        $soma = 0;
        for ($indice = 0; $indice < 9; $indice++) {
            $soma += (int) $cpf[$indice] * (10 - $indice);
        }
        $primeiroDigito = ($soma * 10) % 11;
        $primeiroDigito = $primeiroDigito === 10 ? 0 : $primeiroDigito;

        $soma = 0;
        for ($indice = 0; $indice < 10; $indice++) {
            $soma += (int) $cpf[$indice] * (11 - $indice);
        }
        $segundoDigito = ($soma * 10) % 11;
        $segundoDigito = $segundoDigito === 10 ? 0 : $segundoDigito;

        if ((int) $cpf[9] !== $primeiroDigito || (int) $cpf[10] !== $segundoDigito) {
            $this->erros[$campo] = 'O CPF deve conter 11 números e ser válido.';
        }

        return $this;
    }
    //valida os npjs
    public function cnpj(string $campo, mixed $valor): self
    {
        if ($valor === null || $valor === '') {
            return $this;
        }

        $cnpj = (string) $valor;
        if (!preg_match('/^\d{14}$/', $cnpj) || preg_match('/^(\d)\1{13}$/', $cnpj)) {
            $this->erros[$campo] = 'O CNPJ deve conter 14 números e ser válido.';
            return $this;
        }

        // Primeiro dígito verificador
        $pesos = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $soma = 0;
        for ($indice = 0; $indice < 12; $indice++) {
            $soma += (int) $cnpj[$indice] * $pesos[$indice];
        }
        $primeiroDigito = $soma % 11 < 2 ? 0 : 11 - ($soma % 11);

        // Segundo dígito verificador
        $pesos = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $soma = 0;
        for ($indice = 0; $indice < 13; $indice++) {
            $soma += (int) $cnpj[$indice] * $pesos[$indice];
        }
        $segundoDigito = $soma % 11 < 2 ? 0 : 11 - ($soma % 11);

        if ((int) $cnpj[12] !== $primeiroDigito || (int) $cnpj[13] !== $segundoDigito) {
            $this->erros[$campo] = 'O CNPJ deve conter 14 números e ser válido.';
        }

        return $this;
    }
    //valida os telefones
    public function telefone(string $campo, mixed $valor): self
    {
        if ($valor === null || $valor === '') {
            return $this;
        }

        $telefone = (string) $valor;
        if (!preg_match('/^\d{10,11}$/', $telefone)) {
            $this->erros[$campo] = 'O telefone deve conter apenas números e ter 10 ou 11 dígitos.';
        }

        return $this;
    }


    public function temErros() : bool {

        return !empty($this->erros);

    }

    public function getErros(){
        return $this->erros;
    }

}

