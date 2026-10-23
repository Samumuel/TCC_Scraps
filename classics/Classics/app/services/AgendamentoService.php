<?php

namespace app\services;

use app\models\Agendamento;
use app\models\Sessao;
use app\repositories\AgendamentoRepository;
use DateTime;
use Throwable;

class AgendamentoService
{
    private const STATUS_ATIVO = 'aprovado';
    private const STATUS_CANCELADO = 'cancelado';

    private AgendamentoRepository $repository;
    private SessaoService $sessaoService;

    public function __construct()
    {
        $this->repository = new AgendamentoRepository();
        $this->sessaoService = new SessaoService();
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

    public function getAgendamentosPorUsuario(int $usuarioId): array
    {
        if ($usuarioId <= 0) {
            return [];
        }

        $agendamentos = $this->repository->getAgendamentosPorUsuario($usuarioId);

        foreach ($agendamentos as $indice => $item) {
            $agendamentos[$indice]['pode_alterar'] = $this->podeAlterar($item['agendamento'], $item['sessao']);
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

        if (!$this->repository->atualizarQuantidadePessoas($agendamento, $novaQuantidade)) {
            return [
                'sucesso' => false,
                'erro' => 'falha'
            ];
        }

        return [
            'sucesso' => true
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

        // TODO RF08: notificar o cliente sobre o cancelamento feito pela empresa
        if (!$this->repository->cancelarAgendamento($carregado['agendamento'], self::STATUS_CANCELADO)) {
            return [
                'sucesso' => false,
                'erro' => 'falha'
            ];
        }

        return [
            'sucesso' => true
        ];
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

    private function podeAlterar(Agendamento $agendamento, Sessao $sessao): bool
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
            return new DateTime($data . ' ' . $hora) > new DateTime();
        } catch (Throwable $e) {
            return false;
        }
    }
}