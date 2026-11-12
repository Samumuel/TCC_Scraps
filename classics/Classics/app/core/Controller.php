<?php

namespace app\core;

use app\services\EmpresaService;
use app\services\NotificacaoService;

class Controller
{

    public function view(string $view, ?array $data = null)
    {
        $data = $data ?? [];
        $data['usuario_logado'] = $data['usuario_logado'] ?? ($_SESSION['usuario_logado'] ?? null);
        $data['empresa_logada'] = $data['empresa_logada'] ?? ($_SESSION['empresa_logada'] ?? null);
        $data['admin_logado'] = $data['admin_logado'] ?? ($_SESSION['admin_logado'] ?? null);
        $data['status_empresa_logada'] = $data['status_empresa_logada'] ?? $this->getStatusEmpresaLogada();
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

        // O administrador também é um registro da tabela Usuario
        if (isset($_SESSION['admin_logado']) && is_object($_SESSION['admin_logado'])) {
            return (new NotificacaoService())->getResumo(NotificacaoService::DESTINATARIO_USUARIO, (int) $_SESSION['admin_logado']->getIdUsuario());
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
        if (!isset($_SESSION['admin_logado']) || !is_object($_SESSION['admin_logado'])) {
            // Cliente ou empresa logados voltam para o início; visitante vai para o login
            $logado = isset($_SESSION['usuario_logado']) || isset($_SESSION['empresa_logada']);
            $this->redirect(URL_BASE . ($logado ? '/' : '/login'));
        }

        return true;
    }

    // Status atual da empresa logada, lido do banco (o administrador pode alterá-lo a qualquer momento)
    protected function getStatusEmpresaLogada(): ?string
    {
        if (!isset($_SESSION['empresa_logada']) || !is_object($_SESSION['empresa_logada'])) {
            return null;
        }

        return (new EmpresaService())->getStatus((int) $_SESSION['empresa_logada']->getIdEmpresa());
    }

    // RN02: apenas empresa aprovada cadastra atividades e categorias e gerencia reservas
    protected function empresaLogadaAprovada(): bool
    {
        return $this->getStatusEmpresaLogada() === EmpresaService::STATUS_APROVADA;
    }

    // ---- Quem está logado (usados por todos os controllers) ----

    protected function getUsuarioLogadoId(): ?int
    {
        if (!isset($_SESSION['usuario_logado']) || !is_object($_SESSION['usuario_logado'])) {
            return null;
        }

        return (int) $_SESSION['usuario_logado']->getIdUsuario();
    }

    // Cliente: usuário comum (não administrador) logado
    protected function getClienteLogadoId(): ?int
    {
        if (isset($_SESSION['empresa_logada']) || !isset($_SESSION['usuario_logado']) || !is_object($_SESSION['usuario_logado'])) {
            return null;
        }

        $tipo = strtolower(trim((string) $_SESSION['usuario_logado']->getTipoUsuario()));

        return in_array($tipo, ['user', 'cliente'], true) ? (int) $_SESSION['usuario_logado']->getIdUsuario() : null;
    }

    protected function getEmpresaLogadaId(): ?int
    {
        if (!isset($_SESSION['empresa_logada']) || !is_object($_SESSION['empresa_logada'])) {
            return null;
        }

        return (int) $_SESSION['empresa_logada']->getIdEmpresa();
    }

    // RN02: id da empresa logada somente se ela estiver aprovada
    protected function getEmpresaAprovadaId(): ?int
    {
        $empresaId = $this->getEmpresaLogadaId();

        return $empresaId !== null && $this->empresaLogadaAprovada() ? $empresaId : null;
    }

    protected function isAdminLogado(): bool
    {
        return isset($_SESSION['admin_logado']) && is_object($_SESSION['admin_logado']);
    }
}
