<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Projeto Integrador • Detalhes da Atividade</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <div class="container py-5">
        <?php if (!empty($atividade)): ?>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Detalhes da Atividade</h2>
                <a href="<?= URL_BASE ?>/atividade/listar" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Voltar para Lista
                </a>
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-8">
                            <h3 class="card-title mb-3"><?= htmlspecialchars($atividade['Nome'] ?? '-', ENT_QUOTES, 'UTF-8') ?></h3>
                            <p class="mb-2"><strong>Descrição:</strong> <?= htmlspecialchars($atividade['Descricao'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                            <p class="mb-2"><strong>Categoria:</strong> <?= htmlspecialchars($atividade['Categoria_Nome'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                            <p class="mb-2"><strong>Duração:</strong> <?= htmlspecialchars($atividade['Duracao'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                            <p class="mb-2"><strong>Localização:</strong> <?= htmlspecialchars($atividade['Localizacao'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                            <?php if (!empty($atividade['Localizacao_CEP']) || !empty($atividade['Localizacao_Complemento'])): ?>
                                <p class="mb-2"><strong>CEP:</strong> <?= htmlspecialchars($atividade['Localizacao_CEP'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                                <p class="mb-2"><strong>Complemento:</strong> <?= htmlspecialchars($atividade['Localizacao_Complemento'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                            <?php endif; ?>
                            <p class="mb-2"><strong>Valor:</strong> R$ <?= number_format((float) ($atividade['Valor'] ?? 0), 2, ',', '.') ?></p>
                            <p class="mb-2"><strong>Empresa:</strong> <?= htmlspecialchars((string) ($atividade['Empresa_ID'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i> Atividade não encontrada.
                <div class="mt-3">
                    <a href="<?= URL_BASE ?>/atividade/listar" class="btn btn-warning">Voltar para Lista</a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
