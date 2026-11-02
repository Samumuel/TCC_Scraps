<?php

namespace app\controllers;

use app\core\Controller;
use app\services\AvaliacaoService;

class AvaliacaoController extends Controller
{
    private AvaliacaoService $service;

    public function __construct()
    {
        $this->service = new AvaliacaoService();
    }

    public function salvar()
    {
        $usuarioId = $this->getClienteLogadoId();
        if ($usuarioId === null) {
            $this->redirect(URL_BASE);
        }

        $atividadeId = isset($_POST['atividade_id']) ? (int) $_POST['atividade_id'] : 0;
        $notaInput = trim((string) ($_POST['nota'] ?? ''));
        $comentario = (string) ($_POST['comentario'] ?? '');

        if (!preg_match('/^[1-5]$/', $notaInput)) {
            $this->redirect(URL_BASE . '/agendamento/meus?erro=nota');
        }

        $resultado = $this->service->salvarAvaliacao($usuarioId, $atividadeId, (int) $notaInput, $comentario);

        if (!empty($resultado['sucesso'])) {
            $this->redirect(URL_BASE . '/agendamento/meus?sucesso=avaliado');
        }

        $erro = (string) ($resultado['erro'] ?? 'falha');
        $this->redirect(URL_BASE . '/agendamento/meus?erro=' . rawurlencode($erro));
    }

    private function getClienteLogadoId(): ?int
    {
        if (!isset($_SESSION['usuario_logado']) || !is_object($_SESSION['usuario_logado']) || isset($_SESSION['empresa_logada'])) {
            return null;
        }

        $usuario = $_SESSION['usuario_logado'];
        if (!method_exists($usuario, 'getIdUsuario') || !method_exists($usuario, 'getTipoUsuario')) {
            return null;
        }

        $tipo = strtolower(trim((string) $usuario->getTipoUsuario()));
        if (!in_array($tipo, ['user', 'cliente'], true)) {
            return null;
        }

        return (int) $usuario->getIdUsuario();
    }
}
