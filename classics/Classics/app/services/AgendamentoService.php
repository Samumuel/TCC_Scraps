<?php

namespace app\services;

use app\models\Agendamento;
use app\models\Sessao;
use app\repositories\AgendamentoRepository;
use app\repositories\AvaliacaoRepository;
use DateTime;
use Throwable;

class AgendamentoService
{
    private const STATUS_PENDENTE = 'pendente';
    private const STATUS_ATIVO = 'aprovado';
    private const STATUS_CANCELADO = 'cancelado';
    private const STATUS_RECUSADO = 'recusado';

    private AgendamentoRepository $repository;
    private SessaoService $sessaoService;
    private AvaliacaoRepository $avaliacaoRepository;
    private NotificacaoService $notificacaoService;

    public function __construct()
    {
        $this->repository = new AgendamentoRepository();
        $this->sessaoService = new SessaoService();
        $this->avaliacaoRepository = new AvaliacaoRepository();
        $this->notificacaoService = new NotificacaoService();
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

        $resultado = $this->repository->registrarInscricao($agendamento, $sessaoId, $quantidadePessoas);

        if (!empty($resultado['sucesso'])) {
            $atividadeId = (int) ($resultado['atividade_id'] ?? 0);
            $dadosSessao = $this->sessaoService->getSessaoPorAtividade($sessaoId, $atividadeId);
            if ($dadosSessao !== null) {
                $this->notificacaoService->notificarNovaSolicitacao($atividadeId, Sessao::arrayParaObjeto($dadosSessao), $usuarioId, $quantidadePessoas);
            }
        }

        return $resultado;
    }

    public function getAgendamentosPorUsuario(int $usuarioId): array
    {
        if ($usuarioId <= 0) {
            return [];
        }

        $agendamentos = $this->repository->getAgendamentosPorUsuario($usuarioId);
        $avaliacoes = $this->avaliacaoRepository->getAvaliacoesPorUsuario($usuarioId);

        foreach ($agendamentos as $indice => $item) {
            $agendamentos[$indice]['pode_alterar'] = $this->podeAlterar($item['agendamento'], $item['sessao']);
            $agendamentos[$indice]['pode_avaliar'] = $this->podeAvaliar($item['agendamento'], $item['sessao'], $item['atividade_duracao']);
            $agendamentos[$indice]['avaliacao'] = $avaliacoes[(int) $item['agendamento']->getIdAtividade()] ?? null;
        }

        return $agendamentos;
    }

    public function atualizarQuantidadePessoas(int $usuarioId, int $agendamentoId, int $novaQuantidade): array
    {
        $carregado = $this->carregarAgendamentoAlteravel($usuarioId, $agendamentoId);
        if (empty($carregado['sucesso'])) {
            return $carregado;
        }

        /** @var Agendamento $agendamento */
        $agendamento = $carregado['agendamento'];
        /** @var Sessao $sessao */
        $sessao = $carregado['sessao'];

        if ($novaQuantidade <= 0) {
            return [
                'sucesso' => false,
                'erro' => 'quantidade'
            ];
        }

        $qtdMinima = (int) $sessao->getQtdMin();
        if ($qtdMinima > 0 && $novaQuantidade < $qtdMinima) {
            return [
                'sucesso' => false,
                'erro' => 'minimo'
            ];
        }

        $qtdMaxima = (int) $sessao->getQtdMax();
        if ($qtdMaxima > 0 && $novaQuantidade > $qtdMaxima) {
            return [
                'sucesso' => false,
                'erro' => 'lotacao'
            ];
        }

        $quantidadeAtual = (int) $agendamento->getQuantidadePessoas();
        if ($novaQuantidade === $quantidadeAtual) {
            return [
                'sucesso' => true
            ];
        }

        // As vagas já reservadas por este agendamento contam como livres
        $vagasLivres = (int) $sessao->getQtdDisponivel() + $quantidadeAtual;
        if ($novaQuantidade > $vagasLivres) {
            return [
                'sucesso' => false,
                'erro' => 'lotacao'
            ];
        }

        // Aumentar a quantidade de um agendamento confirmado exige nova aprovação da empresa
        $voltaParaPendente = $agendamento->getStatus() === self::STATUS_ATIVO && $novaQuantidade > $quantidadeAtual;
        $novoStatus = $voltaParaPendente ? self::STATUS_PENDENTE : null;

        if (!$this->repository->atualizarQuantidadePessoas($agendamento, $novaQuantidade, $novoStatus)) {
            return [
                'sucesso' => false,
                'erro' => 'falha'
            ];
        }

        if ($voltaParaPendente) {
            $this->notificacaoService->notificarReservaAlteradaPeloCliente($agendamento, $sessao);
        }

        return [
            'sucesso' => true,
            'pendente' => $voltaParaPendente
        ];
    }

    public function cancelarAgendamento(int $usuarioId, int $agendamentoId): array
    {
        $carregado = $this->carregarAgendamentoAlteravel($usuarioId, $agendamentoId);
        if (empty($carregado['sucesso'])) {
            return $carregado;
        }

        if (!$this->repository->cancelarAgendamento($carregado['agendamento'], self::STATUS_CANCELADO)) {
            return [
                'sucesso' => false,
                'erro' => 'falha'
            ];
        }

        $this->notificacaoService->notificarCancelamentoPeloCliente($carregado['agendamento'], $carregado['sessao']);

        return [
            'sucesso' => true
        ];
    }

    public function getAgendamentosPorEmpresa(int $empresaId, int $atividadeId = 0): array
    {
        if ($empresaId <= 0) {
            return [];
        }

        $agendamentos = $this->repository->getAgendamentosPorEmpresa($empresaId, $atividadeId);

        foreach ($agendamentos as $indice => $item) {
            $agendamentos[$indice]['pode_alterar'] = $this->podeAlterar($item['agendamento'], $item['sessao']);
        }

        return $agendamentos;
    }

    public function cancelarAgendamentoDaEmpresa(int $empresaId, int $agendamentoId): array
    {
        $carregado = $this->carregarAgendamentoAlteravelDaEmpresa($empresaId, $agendamentoId);
        if (empty($carregado['sucesso'])) {
            return $carregado;
        }

        if (!$this->repository->cancelarAgendamento($carregado['agendamento'], self::STATUS_CANCELADO)) {
            return [
                'sucesso' => false,
                'erro' => 'falha'
            ];
        }

        // RF08/RF13: o cliente é notificado pela plataforma
        $this->notificacaoService->notificarReservaCanceladaPelaEmpresa($carregado['agendamento'], $carregado['sessao']);

        return [
            'sucesso' => true
        ];
    }

    public function aprovarAgendamentoDaEmpresa(int $empresaId, int $agendamentoId): array
    {
        $carregado = $this->carregarAgendamentoPendenteDaEmpresa($empresaId, $agendamentoId);
        if (empty($carregado['sucesso'])) {
            return $carregado;
        }

        if (!$this->repository->aprovarAgendamento($carregado['agendamento'], self::STATUS_ATIVO)) {
            return [
                'sucesso' => false,
                'erro' => 'falha'
            ];
        }

        $this->notificacaoService->notificarReservaAprovada($carregado['agendamento'], $carregado['sessao']);

        return [
            'sucesso' => true
        ];
    }

    public function recusarAgendamentoDaEmpresa(int $empresaId, int $agendamentoId): array
    {
        $carregado = $this->carregarAgendamentoPendenteDaEmpresa($empresaId, $agendamentoId);
        if (empty($carregado['sucesso'])) {
            return $carregado;
        }

        // Recusar devolve as vagas reservadas, assim como o cancelamento
        if (!$this->repository->cancelarAgendamento($carregado['agendamento'], self::STATUS_RECUSADO)) {
            return [
                'sucesso' => false,
                'erro' => 'falha'
            ];
        }

        $this->notificacaoService->notificarReservaRecusada($carregado['agendamento'], $carregado['sessao']);

        return [
            'sucesso' => true
        ];
    }

    private function carregarAgendamentoPendenteDaEmpresa(int $empresaId, int $agendamentoId): array
    {
        $carregado = $this->carregarAgendamentoAlteravelDaEmpresa($empresaId, $agendamentoId);
        if (empty($carregado['sucesso'])) {
            return $carregado;
        }

        if ($carregado['agendamento']->getStatus() !== self::STATUS_PENDENTE) {
            return [
                'sucesso' => false,
                'erro' => 'status'
            ];
        }

        return $carregado;
    }

    private function carregarAgendamentoAlteravel(int $usuarioId, int $agendamentoId): array
    {
        if ($usuarioId <= 0) {
            return [
                'sucesso' => false,
                'erro' => 'acesso'
            ];
        }

        $agendamento = $agendamentoId > 0 ? $this->repository->getAgendamentoPorUsuario($agendamentoId, $usuarioId) : null;

        return $this->validarAgendamentoAlteravel($agendamento);
    }

    private function carregarAgendamentoAlteravelDaEmpresa(int $empresaId, int $agendamentoId): array
    {
        if ($empresaId <= 0) {
            return [
                'sucesso' => false,
                'erro' => 'acesso'
            ];
        }

        $agendamento = $agendamentoId > 0 ? $this->repository->getAgendamentoPorEmpresa($agendamentoId, $empresaId) : null;

        return $this->validarAgendamentoAlteravel($agendamento);
    }

    private function validarAgendamentoAlteravel(?Agendamento $agendamento): array
    {
        if ($agendamento === null) {
            return [
                'sucesso' => false,
                'erro' => 'agendamento'
            ];
        }

        $dadosSessao = $this->sessaoService->getSessaoPorAtividade((int) $agendamento->getIdSessao(), (int) $agendamento->getIdAtividade());
        if ($dadosSessao === null) {
            return [
                'sucesso' => false,
                'erro' => 'sessao'
            ];
        }

        $sessao = Sessao::arrayParaObjeto($dadosSessao);
        if (!$this->podeAlterar($agendamento, $sessao)) {
            return [
                'sucesso' => false,
                'erro' => 'bloqueado'
            ];
        }

        return [
            'sucesso' => true,
            'agendamento' => $agendamento,
            'sessao' => $sessao
        ];
    }

    // RF12: só avalia quem teve o agendamento aprovado e cuja sessão já terminou (início + duração)
    private function podeAvaliar(Agendamento $agendamento, Sessao $sessao, string $duracao): bool
    {
        if ($agendamento->getStatus() !== self::STATUS_ATIVO) {
            return false;
        }

        $data = trim((string) $sessao->getData());
        $hora = substr(trim((string) $sessao->getHora()), 0, 8);
        if ($data === '' || $hora === '') {
            return false;
        }

        try {
            $fimSessao = new DateTime($data . ' ' . $hora);
        } catch (Throwable $e) {
            return false;
        }

        if (preg_match('/^(\d+):([0-5]\d)/', trim($duracao), $partes) === 1) {
            $fimSessao->modify('+' . (int) $partes[1] . ' hours +' . (int) $partes[2] . ' minutes');
        }

        return $fimSessao <= new DateTime();
    }

    private function podeAlterar(Agendamento $agendamento, Sessao $sessao): bool
    {
        if (!in_array($agendamento->getStatus(), [self::STATUS_PENDENTE, self::STATUS_ATIVO], true)) {
            return false;
        }

        $data = trim((string) $sessao->getData());
        $hora = substr(trim((string) $sessao->getHora()), 0, 8);
        if ($data === '' || $hora === '') {
            return false;
        }

        try {
            return new DateTime($data . ' ' . $hora) > new DateTime();
        } catch (Throwable $e) {
            return false;
        }
    }
}