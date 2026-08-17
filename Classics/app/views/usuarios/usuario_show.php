<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Projeto Integrador • Dados do Usuário</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>

    <div class="container py-5">

        <?php if (isset($usuario)): ?>
            <!-- TÍTULO + VOLTAR -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Detalhes do Usuário</h2>
                <a href="<?= URL_BASE ?>/usuarios" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Voltar para Lista
                </a>
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <div class="row g-4">

                        <!-- FOTO -->
                        <div class="col-md-4 text-center">
                            <?php if (!empty($usuario['foto'])): ?>
                                    <?php
                                    $fotoUsuario = (string) $usuario['foto'];
                                    $fotoSrc = preg_match('#^https?://#i', $fotoUsuario)
                                        ? $fotoUsuario
                                        : URL_BASE . '/' . ltrim($fotoUsuario, '/');
                                    ?>
                                    <img src="<?= htmlspecialchars($fotoSrc, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($usuario['nomeUsuario'] ?? $usuario['nome'] ?? '', ENT_QUOTES) ?>" class="rounded-circle" style="width:250px; height:250px; object-fit:cover; max-width:100%;">
                            <?php else: ?>
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 250px; height: 250px; margin: 0 auto;">
                                    <i class="bi bi-person-circle" style="font-size: 80px; color: #999;"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- INFO -->
                        <div class="col-md-8">
                            <h3 class="card-title mb-3"><?= $usuario['nomeUsuario'] ?></h3>
                            <p class="mb-2"><strong>Email:</strong> <?= $usuario['email'] ?></p>
                            <p class="mb-2"><strong>CPF:</strong> <?= $usuario['cpf'] ?></p>
                            <p class="mb-2"><strong>Telefone:</strong> <?= $usuario['telefone'] ?></p>
                            <p class="mb-2"><strong>Idade:</strong> <?= $usuario['idade'] ?> anos</p>
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