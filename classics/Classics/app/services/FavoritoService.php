<?php

namespace app\services;

use app\models\Favorito;
use app\repositories\FavoritoRepository;

class FavoritoService
{
    private FavoritoRepository $repository;
    private AtividadeService $atividadeService;

    public function __construct()
    {
        $this->repository = new FavoritoRepository();
        $this->atividadeService = new AtividadeService();
    }

    public function isFavorito(int $usuarioId, int $atividadeId): bool
    {
        if ($usuarioId <= 0 || $atividadeId <= 0) {
            return false;
        }

        return $this->repository->isFavorito($usuarioId, $atividadeId);
    }

    public function getFavoritosPorUsuario(int $usuarioId): array
    {
        if ($usuarioId <= 0) {
            return [];
        }

        return $this->repository->getFavoritosPorUsuario($usuarioId);
    }

    public function alternarFavorito(int $usuarioId, int $atividadeId): array
    {
        if ($usuarioId <= 0) {
            return [
                'sucesso' => false,
                'erro' => 'acesso'
            ];
        }

        if ($atividadeId <= 0 || !$this->atividadeService->getAtividade($atividadeId)) {
            return [
                'sucesso' => false,
                'erro' => 'atividade'
            ];
        }

        if ($this->repository->isFavorito($usuarioId, $atividadeId)) {
            if (!$this->repository->deleteFavorito($usuarioId, $atividadeId)) {
                return [
                    'sucesso' => false,
                    'erro' => 'falha'
                ];
            }

            return [
                'sucesso' => true,
                'favoritado' => false
            ];
        }

        $favorito = new Favorito();
        $favorito->setIdUsuario($usuarioId);
        $favorito->setIdAtividade($atividadeId);

        if (!$this->repository->saveFavorito($favorito)) {
            return [
                'sucesso' => false,
                'erro' => 'falha'
            ];
        }

        return [
            'sucesso' => true,
            'favoritado' => true
        ];
    }
}
