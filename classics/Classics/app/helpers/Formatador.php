<?php

namespace app\helpers;

class Formatador
{
    // Converte a duração gravada no banco (HH:MM:SS) para o formato de exibição, ex: "1h 30min"
    public static function duracao(?string $duracao): string
    {
        $duracao = trim((string) $duracao);

        if (preg_match('/^(\d+):([0-5]\d)(:[0-5]\d)?$/', $duracao, $partes) !== 1) {
            return $duracao !== '' ? $duracao : '-';
        }

        $horas = (int) $partes[1];
        $minutos = (int) $partes[2];

        if ($horas > 0 && $minutos > 0) {
            return $horas . 'h ' . $minutos . 'min';
        }

        if ($horas > 0) {
            return $horas . 'h';
        }

        return $minutos . 'min';
    }

    // Formata a média de avaliações com uma casa decimal, ex: "4,5"
    public static function media(float $media): string
    {
        return number_format($media, 1, ',', '');
    }

    // 12345678909 => 123.456.789-09
    public static function cpf(?string $cpf): string
    {
        $numeros = preg_replace('/\D/', '', (string) $cpf);

        return strlen($numeros) === 11
            ? preg_replace('/^(\d{3})(\d{3})(\d{3})(\d{2})$/', '$1.$2.$3-$4', $numeros)
            : ($numeros !== '' ? $numeros : '-');
    }

    // 11222333000181 => 11.222.333/0001-81
    public static function cnpj(?string $cnpj): string
    {
        $numeros = preg_replace('/\D/', '', (string) $cnpj);

        return strlen($numeros) === 14
            ? preg_replace('/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/', '$1.$2.$3/$4-$5', $numeros)
            : ($numeros !== '' ? $numeros : '-');
    }

    // 11987654321 => (11) 98765-4321 | 1134567890 => (11) 3456-7890
    public static function telefone(?string $telefone): string
    {
        $numeros = preg_replace('/\D/', '', (string) $telefone);

        if (strlen($numeros) === 11) {
            return preg_replace('/^(\d{2})(\d{5})(\d{4})$/', '($1) $2-$3', $numeros);
        }

        if (strlen($numeros) === 10) {
            return preg_replace('/^(\d{2})(\d{4})(\d{4})$/', '($1) $2-$3', $numeros);
        }

        return $numeros !== '' ? $numeros : '-';
    }
}
