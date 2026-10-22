<?php
$navUsuario = isset($_SESSION['usuario_logado']) && is_object($_SESSION['usuario_logado']) ? $_SESSION['usuario_logado'] : null;
$navEmpresa = isset($_SESSION['empresa_logada']) && is_object($_SESSION['empresa_logada']) ? $_SESSION['empresa_logada'] : null;
$navUri = (string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

$navLinks = [
    ['Explorar', '/', ['/', '/atividade', '/atividade/listar', '/atividade/ver']]
];

if ($navUsuario !== null) {
    $navLinks[] = ['Meus Agendamentos', '/agendamento/meus', ['/agendamento/meus']];
    $navLinks[] = ['Usuários', '/usuarios', ['/usuarios', '/usuarios/cadastrar', '/usuarios/editar', '/usuarios/ver']];
    $navLinks[] = ['Empresas', '/empresa', ['/empresa', '/empresa/ver']];
} elseif ($navEmpresa !== null) {
    $navLinks[] = ['Nova Atividade', '/atividade/cadastrar', ['/atividade/cadastrar', '/atividade/localizacao/cadastrar', '/atividade/sessao/cadastrar']];
    $navLinks[] = ['Categorias', '/categoria', ['/categoria', '/categoria/listar', '/categoria/cadastrar', '/categoria/editar']];
    $navLinks[] = ['Empresas', '/empresa', ['/empresa', '/empresa/ver', '/empresa/editar']];
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
        <a class="cl-brand me-4" href="<?= URL_BASE ?>/">
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
                <?php if ($navPerfilUrl !== null): ?>
                    <a href="<?= htmlspecialchars($navPerfilUrl, ENT_QUOTES, 'UTF-8') ?>" class="cl-icon-btn" title="<?= htmlspecialchars($navPerfilNome, ENT_QUOTES, 'UTF-8') ?>">
                        <i class="bi bi-person"></i>
                    </a>
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
