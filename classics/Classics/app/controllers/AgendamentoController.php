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
        $usuarioId = $this->getUsuarioLogadoId();
        $atividadeIdFallback = isset($_POST['atividade_id']) ? (int) $_POST['atividade_id'] : 0;

        if (!$this->usuarioPodeSeInscrever()) {
            $this->redirect($this->montarUrlRetorno($atividadeIdFallback, 'erro_inscricao=acesso'));
        }

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
        $this->redirect($this->montarUrlRetorno($atividadeId, 'erro_inscricao=' . rawurlencode($erro)));
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
            $this->redirect(URL_BASE . '/agendamento/meus?sucesso=atualizado');
        }

        $erro = (string) ($resultado['erro'] ?? 'falha');
        $this->redirect(URL_BASE . '/agendamento/meus?erro=' . rawurlencode($erro));
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
        $empresaId = $this->getEmpresaLogadaId();
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
        $empresaId = $this->getEmpresaLogadaId();
        if ($empresaId === null) {
            $this->redirect(URL_BASE);
        }

        $agendamentoId = isset($_POST['agendamento_id']) ? (int) $_POST['agendamento_id'] : 0;
        $atividadeId = isset($_POST['atividade']) ? (int) $_POST['atividade'] : 0;
        $filtro = $atividadeId > 0 ? 'atividade=' . $atividadeId . '&' : '';

        if ($agendamentoId <= 0) {
            $this->redirect(URL_BASE . '/agendamento/empresa?' . $filtro . 'erro=agendamento');
        }

        $resultado = $this->service->cancelarAgendamentoDaEmpresa($empresaId, $agendamentoId);

        if (!empty($resultado['sucesso'])) {
            $this->redirect(URL_BASE . '/agendamento/empresa?' . $filtro . 'sucesso=cancelado');
        }

        $erro = (string) ($resultado['erro'] ?? 'falha');
        $this->redirect(URL_BASE . '/agendamento/empresa?' . $filtro . 'erro=' . rawurlencode($erro));
    }

    private function getUsuarioLogadoId(): ?int
    {
        if (!isset($_SESSION['usuario_logado']) || !is_object($_SESSION['usuario_logado'])) {
            return null;
        }

        if (!method_exists($_SESSION['usuario_logado'], 'getIdUsuario')) {
            return null;
        }

        return (int) $_SESSION['usuario_logado']->getIdUsuario();
    }

    private function getEmpresaLogadaId(): ?int
    {
        if (!isset($_SESSION['empresa_logada']) || !is_object($_SESSION['empresa_logada'])) {
            return null;
        }

        return (int) $_SESSION['empresa_logada']->getIdEmpresa();
    }

    private function usuarioPodeSeInscrever(): bool
    {
        if (!isset($_SESSION['usuario_logado']) || isset($_SESSION['empresa_logada'])) {
            return false;
        }

        $usuario = $_SESSION['usuario_logado'];
        if (!is_object($usuario) || !method_exists($usuario, 'getTipoUsuario')) {
            return false;
        }

        $tipo = strtolower(trim((string) $usuario->getTipoUsuario()));

        return in_array($tipo, ['user', 'cliente'], true);
    }

    private function montarUrlRetorno(int $atividadeId, string $query): string
    {
        if ($atividadeId <= 0) {
            return URL_BASE . '/atividade/listar?' . $query;
        }

        return URL_BASE . '/atividade/ver?id=' . $atividadeId . '&tab=sessoes&' . $query;
    }
}