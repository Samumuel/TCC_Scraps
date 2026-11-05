<?php
$navUsuario = isset($_SESSION['usuario_logado']) && is_object($_SESSION['usuario_logado']) ? $_SESSION['usuario_logado'] : null;
$navEmpresa = isset($_SESSION['empresa_logada']) && is_object($_SESSION['empresa_logada']) ? $_SESSION['empresa_logada'] : null;
$navAdmin = isset($_SESSION['admin_logado']) && is_object($_SESSION['admin_logado']) ? $_SESSION['admin_logado'] : null;
// RN02: só a empresa aprovada vê os menus de gestão
$navEmpresaAprovada = $navEmpresa !== null && ($status_empresa_logada ?? null) === 'aprovada';
$navUri = (string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

if ($navAdmin !== null) {
    // O administrador não tem os acessos comuns (Explorar, Reservas, Nova Atividade, Categorias)
    $navLinks = [
        ['Empresas', '/admin', ['/admin', '/admin/empresa/editar']],
        ['Usuários', '/admin/usuarios', ['/admin/usuarios', '/admin/usuario/editar']]
    ];
} else {
    $navLinks = [
        ['Explorar', '/', ['/', '/atividade', '/atividade/listar', '/atividade/ver']]
    ];
}

if ($navUsuario !== null) {
    $navLinks[] = ['Meus Agendamentos', '/agendamento/meus', ['/agendamento/meus']];
    $navLinks[] = ['Favoritos', '/favorito/meus', ['/favorito/meus']];
} elseif ($navEmpresaAprovada) {
    $navLinks[] = ['Reservas', '/agendamento/empresa', ['/agendamento/empresa']];
    $navLinks[] = ['Nova Atividade', '/atividade/cadastrar', ['/atividade/cadastrar', '/atividade/localizacao/cadastrar', '/atividade/sessao/cadastrar']];
    $navLinks[] = ['Categorias', '/categoria', ['/categoria', '/categoria/listar', '/categoria/cadastrar', '/categoria/editar']];
}

$navPerfilUrl = null;
$navPerfilNome = '';
if ($navUsuario !== null) {
    $navPerfilUrl = URL_BASE . '/usuarios/ver?id=' . (int) $navUsuario->getIdUsuario();
    $navPerfilNome = (string) $navUsuario->getNome();
} elseif ($navEmpresa !== null) {
    $navPerfilUrl = URL_BASE . '/empresa/ver?id=' . (int) $navEmpresa->getIdEmpresa();
    $navPerfilNome = (string) $navEmpresa->getNome();
}
?>
<nav class="navbar navbar-expand-md cl-navbar sticky-top py-3">
    <div class="container">
        <a class="cl-brand me-4" href="<?= URL_BASE ?><?= $navAdmin !== null ? '/admin' : '/' ?>">
            <span class="cl-logo">C</span>
            <span class="cl-brand-name">Classics</span>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarClassics" aria-controls="navbarClassics" aria-expanded="false" aria-label="Abrir menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarClassics">
            <ul class="navbar-nav me-auto gap-md-3">
                <?php foreach ($navLinks as [$navRotulo, $navRota, $navRotasAtivas]): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= in_array($navUri, $navRotasAtivas, true) ? 'active' : '' ?>" href="<?= URL_BASE . $navRota ?>">
                            <?= htmlspecialchars($navRotulo, ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <div class="d-flex align-items-center gap-2 mt-3 mt-md-0">
                <?php if ($navPerfilUrl !== null || $navAdmin !== null): ?>
                    <?php
                    $navNotificacoes = $notificacoes ?? ['nao_lidas' => 0, 'recentes' => []];
                    $navNaoLidas = (int) $navNotificacoes['nao_lidas'];
                    ?>
                    <div class="dropdown">
                        <button type="button" class="cl-icon-btn position-relative" data-bs-toggle="dropdown" data-bs-auto-close="outside" data-bs-display="static" aria-expanded="false"
                            title="Notificações" aria-label="Notificações<?= $navNaoLidas > 0 ? ' (' . $navNaoLidas . ' não lidas)' : '' ?>">
                            <i class="bi bi-bell"></i>
                            <?php if ($navNaoLidas > 0): ?>
                                <span class="cl-sino-contador"><?= $navNaoLidas > 9 ? '9+' : $navNaoLidas ?></span>
                            <?php endif; ?>
                        </button>
                        <div class="dropdown-menu dropdown-menu-start dropdown-menu-md-end cl-notificacoes p-0">
                            <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                                <strong>Notificações</strong>
                                <?php if ($navNaoLidas > 0): ?>
                                    <form action="<?= URL_BASE ?>/notificacao/lidas" method="POST" class="m-0">
                                        <input type="hidden" name="retorno" value="<?= htmlspecialchars((string) ($_SERVER['REQUEST_URI'] ?? '/'), ENT_QUOTES, 'UTF-8') ?>">
                                        <button type="submit" class="btn btn-link btn-sm p-0">Marcar todas como lidas</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                            <?php if (empty($navNotificacoes['recentes'])): ?>
                                <p class="text-muted small text-center mb-0 py-4">Você não tem notificações.</p>
                            <?php else: ?>
                                <?php foreach ($navNotificacoes['recentes'] as $navNotificacao): ?>
                                    <?php
                                    $navDataHora = DateTime::createFromFormat('Y-m-d H:i:s', (string) $navNotificacao->getDataHora());
                                    ?>
                                    <a href="<?= URL_BASE ?>/notificacao/abrir?id=<?= (int) $navNotificacao->getIdNotificacao() ?>" class="cl-notificacao <?= $navNotificacao->isLida() ? '' : 'nao-lida' ?>">
                                        <span class="cl-notificacao-ponto" aria-hidden="true"></span>
                                        <span>
                                            <span class="d-block"><?= htmlspecialchars((string) $navNotificacao->getMensagem(), ENT_QUOTES, 'UTF-8') ?></span>
                                            <small class="text-muted"><?= $navDataHora ? $navDataHora->format('d/m/Y \à\s H:i') : '' ?></small>
                                        </span>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if ($navPerfilUrl !== null): ?>
                        <a href="<?= htmlspecialchars($navPerfilUrl, ENT_QUOTES, 'UTF-8') ?>" class="cl-icon-btn" title="<?= htmlspecialchars($navPerfilNome, ENT_QUOTES, 'UTF-8') ?>">
                            <i class="bi bi-person"></i>
                        </a>
                    <?php else: ?>
                        <span class="cl-badge cl-badge-info" title="<?= htmlspecialchars((string) $navAdmin->getNome(), ENT_QUOTES, 'UTF-8') ?>">
                            <i class="bi bi-shield-lock"></i> Admin
                        </span>
                    <?php endif; ?>
                    <form action="<?= URL_BASE ?>/logout" method="POST" class="m-0">
                        <button type="submit" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-box-arrow-right"></i> Sair
                        </button>
                    </form>
                <?php else: ?>
                    <a href="<?= URL_BASE ?>/login" class="btn btn-sm btn-outline-secondary border-0">
                        <i class="bi bi-box-arrow-in-right"></i> Entrar
                    </a>
                    <a href="<?= URL_BASE ?>/cadastrar" class="btn btn-sm btn-primary">
                        <i class="bi bi-person-plus"></i> Cadastrar
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
