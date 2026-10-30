<?php

namespace app\services;

use app\models\Sessao;
use app\repositories\SessaoRepository;
use DateTime;

class SessaoService
{
    private SessaoRepository $repository;

    public function __construct()
    {
        $this->repository = new SessaoRepository();
    }

    public function getSessoesPorAtividade(int $atividadeId): array
    {
        return $this->repository->getSessoesPorAtividade($atividadeId);
    }

    // Sessões que ainda não começaram: data futura ou de hoje com horário ainda por vir
    public function getSessoesDisponiveisPorAtividade(int $atividadeId): array
    {
        $agora = new DateTime();

        return $this->repository->getSessoesDisponiveisPorAtividade($atividadeId, $agora->format('Y-m-d'), $agora->format('H:i:s'));
    }

    public function countSessoesPorAtividade(int $atividadeId): int
    {
        return $this->repository->countSessoesPorAtividade($atividadeId);
    }

    public function saveSessao(Sessao $sessao): bool
    {
        return $this->repository->saveSessao($sessao);
    }

    public function getSessaoPorAtividade(int $idSessao, int $atividadeId): ?array
    {
        return $this->repository->getSessaoPorAtividade($idSessao, $atividadeId);
    }

    public function updateSessao(Sessao $sessao): bool
    {
        return $this->repository->updateSessao($sessao);
    }

    public function deleteSessao(int $idSessao, int $atividadeId): bool
    {
        return $this->repository->deleteSessao($idSessao, $atividadeId);
    }
}
