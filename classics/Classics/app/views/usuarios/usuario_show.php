<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Classics • Dados do Usuário</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
<?php require __DIR__ . '/../partials/head.php'; ?>
</head>

<body>
<?php require __DIR__ . '/../partials/navbar.php'; ?>

    <div class="container cl-page">

        <?php if (isset($usuario)): ?>
            <?php $usuarioLogado = $_SESSION['usuario_logado'] ?? null; ?>
            <a href="<?= URL_BASE ?>/usuarios" class="cl-back-link">
                <i class="bi bi-arrow-left"></i> Voltar para Lista
            </a>

            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card text-center">
                        <div class="card-body p-4">
                            <?php if (!empty($usuario['foto'])): ?>
                                <?php
                                $fotoUsuario = (string) $usuario['foto'];
                                $fotoSrc = preg_match('#^https?://#i', $fotoUsuario)
                                    ? $fotoUsuario
                                    : URL_BASE . '/' . ltrim($fotoUsuario, '/');
                                ?>
                                <img src="<?= htmlspecialchars($fotoSrc, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($usuario['nomeUsuario'] ?? $usuario['nome'] ?? '', ENT_QUOTES) ?>" class="rounded-circle mb-3" style="width:160px; height:160px; object-fit:cover; max-width:100%;">
                            <?php else: ?>
                                <div class="cl-placeholder-img rounded-circle mx-auto mb-3" style="width: 160px; height: 160px;">
                                    <i class="bi bi-person" style="font-size: 72px;"></i>
                                </div>
                            <?php endif; ?>
                            <h1 class="h3 mb-1"><?= htmlspecialchars((string) ($usuario['nomeUsuario'] ?? ''), ENT_QUOTES, 'UTF-8') ?></h1>
                            <p class="text-muted small mb-0"><?= htmlspecialchars((string) ($usuario['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>

                            <?php if ($usuarioLogado && isset($usuario['id']) && (int) $usuario['id'] === (int) $usuarioLogado->getIdUsuario()): ?>
                                <a href="<?= URL_BASE ?>/usuarios/editar?id=<?= (int) $usuario['id'] ?>" class="btn btn-outline-primary btn-sm mt-3">
                                    <i class="bi bi-pencil"></i> Editar perfil
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body p-4">
                            <h2 class="h4 mb-4">Informações da conta</h2>
                            <div class="row g-4">
                                <div class="col-sm-6">
                                    <div class="cl-info-tile">
                                        <i class="bi bi-envelope"></i>
                                        <div><small>E-mail</small><?= htmlspecialchars((string) ($usuario['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="cl-info-tile">
                                        <i class="bi bi-person-vcard"></i>
                                        <div><small>CPF</small><?= htmlspecialchars((string) ($usuario['cpf'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="cl-info-tile">
                                        <i class="bi bi-telephone"></i>
                                        <div><small>Telefone</small><?= htmlspecialchars((string) ($usuario['telefone'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="cl-info-tile">
                                        <i class="bi bi-cake2"></i>
                                        <div><small>Idade</small><?= htmlspecialchars((string) ($usuario['idade'] ?? ''), ENT_QUOTES, 'UTF-8') ?> anos</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i> Usuário não encontrado.
                <div class="mt-3">
                    <a href="<?= URL_BASE ?>/usuarios" class="btn btn-warning">Voltar para Lista</a>
                </div>
            </div>
        <?php endif; ?>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>