<?php

namespace app\services;

use app\models\Localizacao;
use app\repositories\LocalizacaoRepository;

class LocalizacaoService
{
    private LocalizacaoRepository $repository;

    public function __construct()
    {
        $this->repository = new LocalizacaoRepository();
    }

    public function getLocalizacaoPorAtividade(int $atividadeId): ?array
    {
        return $this->repository->getLocalizacaoPorAtividade($atividadeId);
    }

    public function saveOrUpdateLocalizacao(Localizacao $localizacao): bool
    {
        return $this->repository->saveOrUpdateLocalizacao($localizacao);
    }
}
