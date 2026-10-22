<?php
if (!isset($_SESSION['empresa_logada'])) {
    header('Location: ' . URL_BASE);
    exit();
}

$atividade = $atividade ?? [];
$localizacao = $localizacao ?? [];
$erros = $erros ?? [];
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Projeto Integrador • Localização da Atividade</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">Configurar localização</h2>
                <p class="text-muted mb-0">Etapa 2 de 3 para a atividade <?= htmlspecialchars($atividade['Nome'] ?? '', ENT_QUOTES, 'UTF-8') ?>.</p>
            </div>
            <a href="<?= URL_BASE ?>/atividade/listar" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card shadow-sm h-100 border-0 bg-light">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Resumo da atividade</h5>
                        <p class="mb-2"><strong>Nome:</strong> <?= htmlspecialchars($atividade['Nome'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                        <p class="mb-2"><strong>Categoria:</strong> <?= htmlspecialchars($atividade['Categoria_Nome'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                        <p class="mb-0"><strong>Duração:</strong> <?= htmlspecialchars($atividade['Duracao'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <?php if (!empty($localizacao)): ?>
                            <div class="alert alert-info" role="alert">
                                Esta atividade já possui uma localização cadastrada. Os dados abaixo podem ser alterados.
                            </div>
                        <?php endif; ?>
                        <?php if (isset($erros['cep']) || isset($erros['complemento'])): ?>
                            <div class="alert alert-danger" role="alert">
                                Corrija os campos obrigatorios da localizacao.
                            </div>
                        <?php endif; ?>

                        <form action="<?= URL_BASE ?>/atividade/localizacao/salvar" method="POST">
                            <?php $origem = 'create'; include __DIR__ . '/_localizacao_form.php'; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
