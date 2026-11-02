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
        $localizacaoAnterior = $this->repository->getLocalizacaoPorAtividade((int) $localizacao->getIdAtividade());

        if (!$this->repository->saveOrUpdateLocalizacao($localizacao)) {
            return false;
        }

        // RN07: mudança de endereço (CEP ou complemento) avisa os clientes com reserva ativa
        $mudou = $localizacaoAnterior !== null && (
            trim((string) ($localizacaoAnterior['CEP'] ?? '')) !== trim((string) $localizacao->getCep())
            || trim((string) ($localizacaoAnterior['Complemento'] ?? '')) !== trim((string) $localizacao->getComplemento())
        );
        if ($mudou) {
            (new NotificacaoService())->notificarLocalAlterado((int) $localizacao->getIdAtividade());
        }

        return true;
    }
}
