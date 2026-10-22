<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Projeto Integrador • Dados da Empresa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>

    <div class="container py-5">

        <?php if (isset($empresa)): ?>
            <!-- TÍTULO + VOLTAR -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Detalhes da Empresa</h2>
                <a href="<?= URL_BASE ?>/empresa" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Voltar para Lista
                </a>
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <div class="row g-4">

                        <!-- INFO -->
                        <div class="col-md-8">
                            <h3 class="card-title mb-3"><?= $empresa['Nome'] ?? $empresa['nome'] ?></h3>
                            <p class="mb-2"><strong>Email:</strong> <?= $empresa['Email'] ?? $empresa['email'] ?></p>
                            <p class="mb-2"><strong>CNPJ:</strong> <?= $empresa['CNPJ'] ?? $empresa['cnpj'] ?></p>
                            <p class="mb-2"><strong>Localização:</strong> <?= $empresa['Localizacao'] ?? $empresa['localizacao'] ?></p>
                            <p class="mb-2"><strong>Telefone:</strong> <?= $empresa['Telefone'] ?? $empresa['telefone'] ?></p>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i> Empresa não encontrada.
                <div class="mt-3">
                    <a href="<?= URL_BASE ?>/empresa" class="btn btn-warning">Voltar para Lista</a>
                </div>
            </div>
        <?php endif; ?>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>