<?php

namespace app\services;

use app\models\Sessao;
use app\repositories\SessaoRepository;
use DateTime;

class SessaoService
{
    private SessaoRepository $repository;
    private ?NotificacaoService $notificacaoService = null;

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
        $dadosAnteriores = $this->repository->getSessaoPorAtividade($sessao->getIdSessao(), (int) $sessao->getIdAtividade());
        $sessaoAnterior = $dadosAnteriores !== null ? Sessao::arrayParaObjeto($dadosAnteriores) : null;

        // RN07: mudança de data ou horário avisa os clientes com reserva ativa (buscados antes de alterar)
        $mudouDataHora = $sessaoAnterior !== null && (
            (string) $sessaoAnterior->getData() !== (string) $sessao->getData()
            || substr((string) $sessaoAnterior->getHora(), 0, 5) !== substr((string) $sessao->getHora(), 0, 5)
        );
        $clientes = $mudouDataHora ? $this->notificacaoService()->getClientesComReservaAtivaNaSessao($sessao->getIdSessao()) : [];

        if (!$this->repository->updateSessao($sessao)) {
            return false;
        }

        if (!empty($clientes)) {
            $this->notificacaoService()->notificarSessaoAlterada($clientes, (int) $sessao->getIdAtividade(), $sessaoAnterior, $sessao);
        }

        return true;
    }

    public function deleteSessao(int $idSessao, int $atividadeId): bool
    {
        // RN07: a exclusão remove as reservas da sessão, então os clientes são buscados antes
        $dadosSessao = $this->repository->getSessaoPorAtividade($idSessao, $atividadeId);
        $clientes = $dadosSessao !== null ? $this->notificacaoService()->getClientesComReservaAtivaNaSessao($idSessao) : [];

        if (!$this->repository->deleteSessao($idSessao, $atividadeId)) {
            return false;
        }

        if (!empty($clientes)) {
            $this->notificacaoService()->notificarSessaoExcluida($clientes, $atividadeId, Sessao::arrayParaObjeto($dadosSessao));
        }

        return true;
    }

    // Criado só quando necessário (ao alterar ou excluir uma sessão)
    private function notificacaoService(): NotificacaoService
    {
        return $this->notificacaoService ??= new NotificacaoService();
    }
}
