<?php

namespace app\core;

use app\services\NotificacaoService;

class Controller
{

    public function view(string $view, ?array $data = null)
    {
        $data = $data ?? [];
        $data['usuario_logado'] = $data['usuario_logado'] ?? ($_SESSION['usuario_logado'] ?? null);
        $data['empresa_logada'] = $data['empresa_logada'] ?? ($_SESSION['empresa_logada'] ?? null);
        $data['notificacoes'] = $data['notificacoes'] ?? $this->getResumoNotificacoes();
        extract($data);

        $path = __DIR__ . "/../views/$view.php";

        if (file_exists($path)) {
            require_once $path;
        } else {
            print 'A view solicitada não foi encontrada: ' . $view;
        }
    }

    // Notificações do sino da navbar para quem está logado
    private function getResumoNotificacoes(): array
    {
        $resumoVazio = ['nao_lidas' => 0, 'recentes' => []];

        if (isset($_SESSION['usuario_logado']) && is_object($_SESSION['usuario_logado'])) {
            return (new NotificacaoService())->getResumo(NotificacaoService::DESTINATARIO_USUARIO, (int) $_SESSION['usuario_logado']->getIdUsuario());
        }

        if (isset($_SESSION['empresa_logada']) && is_object($_SESSION['empresa_logada'])) {
            return (new NotificacaoService())->getResumo(NotificacaoService::DESTINATARIO_EMPRESA, (int) $_SESSION['empresa_logada']->getIdEmpresa());
        }

        return $resumoVazio;
    }

    public function redirect(string $url)
    {
        header('location: ' . $url);
        exit();
    }

    public function autenticacaoRequired()
    {
        if (!isset($_SESSION['usuario_logado'])) {
            $this->redirect(URL_BASE . '/login');
        }

        return true;
    }

    public function adminRequired()
    {
        if (!isset($_SESSION['usuario_logado']) || $_SESSION['usuario_logado']->getPerfil() !== 'admin') {
            $this->redirect(URL_BASE . '/login');
        }

        return true;
    }
}
