<?php

namespace app\services;

use app\models\Sessao;
use app\repositories\SessaoRepository;

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
