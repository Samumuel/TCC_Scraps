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
}
