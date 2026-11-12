<?php

namespace app\controllers;

use app\core\Controller;
use app\services\AtividadeService;

class AgendamentoController extends Controller
{
    private $service;
    private AtividadeService $atividadeService;

    public function __construct()
    {
        $serviceClass = 'app\\services\\AgendamentoService';
        $this->service = new $serviceClass();
        $this->atividadeService = new AtividadeService();
    }

    public function salvar()
    {
        // Só o cliente (não empresa nem administrador) faz reservas
        $usuarioId = $this->getClienteLogadoId();
        $atividadeIdFallback = isset($_POST['atividade_id']) ? (int) $_POST['atividade_id'] : 0;

        if ($usuarioId === null) {
            $this->redirect($this->montarUrlRetorno($atividadeIdFallback, 'erro_inscricao=acesso'));
        }

        $sessaoId = isset($_POST['sessao_id']) ? (int) $_POST['sessao_id'] : 0;
        $quantidadePessoasInput = trim((string) ($_POST['quantidade_pessoas'] ?? ''));

        if ($sessaoId <= 0) {
            $this->redirect($this->montarUrlRetorno($atividadeIdFallback, 'erro_inscricao=sessao'));
        }

        if (!preg_match('/^\d+$/', $quantidadePessoasInput) || (int) $quantidadePessoasInput <= 0) {
            $this->redirect($this->montarUrlRetorno($atividadeIdFallback, 'erro_inscricao=quantidade'));
        }

        $resultado = $this->service->registrarInscricao($usuarioId, $sessaoId, (int) $quantidadePessoasInput);
        $atividadeId = (int) ($resultado['atividade_id'] ?? $atividadeIdFallback);

        if (!empty($resultado['sucesso'])) {
            $this->redirect($this->montarUrlRetorno($atividadeId, 'sucesso_inscricao=1'));
        }

        $erro = (string) ($resultado['erro'] ?? 'falha');
        $vagas = isset($resultado['vagas']) ? '&vagas=' . (int) $resultado['vagas'] : '';
        $minimo = isset($resultado['minimo']) ? '&minimo=' . (int) $resultado['minimo'] : '';
        $this->redirect($this->montarUrlRetorno($atividadeId, 'erro_inscricao=' . rawurlencode($erro) . $vagas . $minimo));
    }

    public function meusAgendamentos()
    {
        $usuarioId = $this->getUsuarioLogadoId();
        if ($usuarioId === null) {
            $this->redirect(URL_BASE);
        }

        $data['agendamentos'] = $this->service->getAgendamentosPorUsuario($usuarioId);
        $this->view('agendamentos/meus_agendamentos', $data);
    }

    public function atualizar()
    {
        $usuarioId = $this->getUsuarioLogadoId();
        if ($usuarioId === null) {
            $this->redirect(URL_BASE);
        }

        $agendamentoId = isset($_POST['agendamento_id']) ? (int) $_POST['agendamento_id'] : 0;
        $quantidadePessoasInput = trim((string) ($_POST['quantidade_pessoas'] ?? ''));

        if ($agendamentoId <= 0) {
            $this->redirect(URL_BASE . '/agendamento/meus?erro=agendamento');
        }

        if (!preg_match('/^\d+$/', $quantidadePessoasInput) || (int) $quantidadePessoasInput <= 0) {
            $this->redirect(URL_BASE . '/agendamento/meus?erro=quantidade');
        }

        $resultado = $this->service->atualizarQuantidadePessoas($usuarioId, $agendamentoId, (int) $quantidadePessoasInput);

        if (!empty($resultado['sucesso'])) {
            $this->redirect(URL_BASE . '/agendamento/meus?sucesso=' . (!empty($resultado['pendente']) ? 'atualizado_pendente' : 'atualizado'));
        }

        $this->redirect($this->urlErroMeusAgendamentos($resultado));
    }

    // UC07 FA02: o cliente troca a sessão (data e horário) da própria reserva
    public function trocarSessao()
    {
        $usuarioId = $this->getUsuarioLogadoId();
        if ($usuarioId === null) {
            $this->redirect(URL_BASE);
        }

        $agendamentoId = isset($_POST['agendamento_id']) ? (int) $_POST['agendamento_id'] : 0;
        $novaSessaoId = isset($_POST['sessao_id']) ? (int) $_POST['sessao_id'] : 0;

        if ($agendamentoId <= 0) {
            $this->redirect(URL_BASE . '/agendamento/meus?erro=agendamento');
        }

        if ($novaSessaoId <= 0) {
            $this->redirect(URL_BASE . '/agendamento/meus?erro=sessao');
        }

        $resultado = $this->service->trocarSessao($usuarioId, $agendamentoId, $novaSessaoId);

        if (!empty($resultado['sucesso'])) {
            $this->redirect(URL_BASE . '/agendamento/meus?sucesso=sessao_trocada');
        }

        $this->redirect($this->urlErroMeusAgendamentos($resultado));
    }

    private function urlErroMeusAgendamentos(array $resultado): string
    {
        $erro = (string) ($resultado['erro'] ?? 'falha');
        $vagas = isset($resultado['vagas']) ? '&vagas=' . (int) $resultado['vagas'] : '';

        return URL_BASE . '/agendamento/meus?erro=' . rawurlencode($erro) . $vagas;
    }

    public function cancelar()
    {
        $usuarioId = $this->getUsuarioLogadoId();
        if ($usuarioId === null) {
            $this->redirect(URL_BASE);
        }

        $agendamentoId = isset($_POST['agendamento_id']) ? (int) $_POST['agendamento_id'] : 0;
        if ($agendamentoId <= 0) {
            $this->redirect(URL_BASE . '/agendamento/meus?erro=agendamento');
        }

        $resultado = $this->service->cancelarAgendamento($usuarioId, $agendamentoId);

        if (!empty($resultado['sucesso'])) {
            $this->redirect(URL_BASE . '/agendamento/meus?sucesso=cancelado');
        }

        $erro = (string) ($resultado['erro'] ?? 'falha');
        $this->redirect(URL_BASE . '/agendamento/meus?erro=' . rawurlencode($erro));
    }

    public function reservasEmpresa()
    {
        $empresaId = $this->getEmpresaAprovadaId();
        if ($empresaId === null) {
            $this->redirect(URL_BASE);
        }

        $atividadeId = isset($_GET['atividade']) ? (int) $_GET['atividade'] : 0;

        $data['atividades'] = $this->atividadeService->getAtividadesPorEmpresa($empresaId);
        $data['atividade_selecionada'] = $atividadeId;
        $data['agendamentos'] = $this->service->getAgendamentosPorEmpresa($empresaId, $atividadeId);
        $this->view('agendamentos/reservas_empresa', $data);
    }

    public function cancelarReservaEmpresa()
    {
        $this->executarAcaoReservaEmpresa('cancelar');
    }

    public function aprovarReservaEmpresa()
    {
        $this->executarAcaoReservaEmpresa('aprovar');
    }

    public function recusarReservaEmpresa()
    {
        $this->executarAcaoReservaEmpresa('recusar');
    }

    private function executarAcaoReservaEmpresa(string $acao)
    {
        $empresaId = $this->getEmpresaAprovadaId();
        if ($empresaId === null) {
            $this->redirect(URL_BASE);
        }

        $agendamentoId = isset($_POST['agendamento_id']) ? (int) $_POST['agendamento_id'] : 0;
        $atividadeId = isset($_POST['atividade']) ? (int) $_POST['atividade'] : 0;
        $filtro = $atividadeId > 0 ? 'atividade=' . $atividadeId . '&' : '';

        if ($agendamentoId <= 0) {
            $this->redirect(URL_BASE . '/agendamento/empresa?' . $filtro . 'erro=agendamento');
        }

        if ($acao === 'aprovar') {
            $resultado = $this->service->aprovarAgendamentoDaEmpresa($empresaId, $agendamentoId);
            $sucesso = 'aprovado';
        } elseif ($acao === 'recusar') {
            $resultado = $this->service->recusarAgendamentoDaEmpresa($empresaId, $agendamentoId);
            $sucesso = 'recusado';
        } else {
            $resultado = $this->service->cancelarAgendamentoDaEmpresa($empresaId, $agendamentoId);
            $sucesso = 'cancelado';
        }

        if (!empty($resultado['sucesso'])) {
            $this->redirect(URL_BASE . '/agendamento/empresa?' . $filtro . 'sucesso=' . $sucesso);
        }

        $erro = (string) ($resultado['erro'] ?? 'falha');
        $this->redirect(URL_BASE . '/agendamento/empresa?' . $filtro . 'erro=' . rawurlencode($erro));
    }




    private function montarUrlRetorno(int $atividadeId, string $query): string
    {
        if ($atividadeId <= 0) {
            return URL_BASE . '/?' . $query;
        }

        return URL_BASE . '/atividade/ver?id=' . $atividadeId . '&tab=sessoes&' . $query;
    }
}