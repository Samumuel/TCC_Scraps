<?php

namespace app\services;

use app\models\Agendamento;
use app\repositories\AgendamentoRepository;

class AgendamentoService
{
    private AgendamentoRepository $repository;

    public function __construct()
    {
        $this->repository = new AgendamentoRepository();
    }

    public function registrarInscricao(int $usuarioId, int $sessaoId, int $quantidadePessoas): array
    {
        if ($usuarioId <= 0) {
            return [
                'sucesso' => false,
                'erro' => 'acesso'
            ];
        }

        if ($quantidadePessoas <= 0) {
            return [
                'sucesso' => false,
                'erro' => 'quantidade'
            ];
        }

        $agendamento = new Agendamento();
        $agendamento->setIdUsuario($usuarioId);

        return $this->repository->registrarInscricao($agendamento, $sessaoId, $quantidadePessoas);
    }
}