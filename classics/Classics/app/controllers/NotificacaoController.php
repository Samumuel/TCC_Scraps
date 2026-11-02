<?php

namespace app\controllers;

use app\core\Controller;
use app\services\NotificacaoService;

class NotificacaoController extends Controller
{
    private NotificacaoService $service;

    public function __construct()
    {
        $this->service = new NotificacaoService();
    }

    public function abrir()
    {
        $destinatario = $this->getDestinatarioLogado();
        if ($destinatario === null) {
            $this->redirect(URL_BASE);
        }

        $notificacaoId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $link = $this->service->abrir($destinatario['tipo'], $destinatario['id'], $notificacaoId);

        $this->redirect(URL_BASE . ($link ?? '/'));
    }

    public function marcarTodasLidas()
    {
        $destinatario = $this->getDestinatarioLogado();
        if ($destinatario === null) {
            $this->redirect(URL_BASE);
        }

        $this->service->marcarTodasComoLidas($destinatario['tipo'], $destinatario['id']);

        // Volta para a página em que o usuário estava (somente caminhos internos)
        $retorno = (string) ($_POST['retorno'] ?? '/');
        if (!str_starts_with($retorno, '/') || str_starts_with($retorno, '//')) {
            $retorno = '/';
        }

        $this->redirect(URL_BASE . $retorno);
    }

    private function getDestinatarioLogado(): ?array
    {
        if (isset($_SESSION['usuario_logado']) && is_object($_SESSION['usuario_logado'])) {
            return ['tipo' => NotificacaoService::DESTINATARIO_USUARIO, 'id' => (int) $_SESSION['usuario_logado']->getIdUsuario()];
        }

        if (isset($_SESSION['empresa_logada']) && is_object($_SESSION['empresa_logada'])) {
            return ['tipo' => NotificacaoService::DESTINATARIO_EMPRESA, 'id' => (int) $_SESSION['empresa_logada']->getIdEmpresa()];
        }

        return null;
    }
}
