<?php
if (!isset($_SESSION['empresa_logada'])) {
    header('Location: ' . URL_BASE);
    exit();
}

$atividade = $atividade ?? [];
$sessao = $sessao ?? [];
$sessoes = $sessoes ?? [];
$totalSessoes = (int) ($totalSessoes ?? 0);
$erros = $erros ?? [];
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Projeto Integrador • Sessões da Atividade</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">Configurar sessões</h2>
                <p class="text-muted mb-0">Etapa 3 de 3 para a atividade <?= htmlspecialchars($atividade['Nome'] ?? '', ENT_QUOTES, 'UTF-8') ?>.</p>
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
                        <p class="mb-3"><strong>Duração:</strong> <?= htmlspecialchars($atividade['Duracao'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                        <div class="alert <?= $totalSessoes > 0 ? 'alert-success' : 'alert-warning' ?> mb-0" role="alert">
                            Sessões cadastradas: <strong><?= $totalSessoes ?></strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card shadow-sm mb-3">
                    <div class="card-body p-4">
                        <?php if (isset($_GET['sucesso']) && (string) $_GET['sucesso'] === '1'): ?>
                            <div class="alert alert-success" role="alert">
                                Sessão cadastrada com sucesso. Você pode adicionar outra sessão ou concluir a atividade.
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($erros)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?= htmlspecialchars($erros['geral'] ?? 'Corrija os campos obrigatorios da sessao.', ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        <?php endif; ?>

                        <form action="<?= URL_BASE ?>/atividade/sessao/salvar" method="POST">
                            <input type="hidden" name="id_atividade" value="<?= (int) ($atividade['ID_Atividade'] ?? 0) ?>">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="data" class="form-label">Data</label>
                                    <input type="date" class="form-control <?= isset($erros['data']) ? 'is-invalid' : '' ?>" id="data" name="data" value="<?= htmlspecialchars($sessao['Data'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                                    <?php if (isset($erros['data'])): ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($erros['data'], ENT_QUOTES, 'UTF-8') ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <label for="hora" class="form-label">Hora incial</label>
                                    <input type="time" class="form-control <?= isset($erros['hora']) ? 'is-invalid' : '' ?>" id="hora" name="hora" step="60" value="<?= htmlspecialchars($sessao['Hora'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                                    <?php if (isset($erros['hora'])): ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($erros['hora'], ENT_QUOTES, 'UTF-8') ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-4">
                                    <label for="qtd_min" class="form-label">Quantidade minima</label>
                                    <input type="number" min="0" class="form-control <?= isset($erros['qtd_min']) ? 'is-invalid' : '' ?>" id="qtd_min" name="qtd_min" value="<?= htmlspecialchars((string) ($sessao['Qtd_min'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
                                    <?php if (isset($erros['qtd_min'])): ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($erros['qtd_min'], ENT_QUOTES, 'UTF-8') ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-4">
                                    <label for="qtd_max" class="form-label">Quantidade maxima</label>
                                    <input type="number" min="0" class="form-control <?= isset($erros['qtd_max']) ? 'is-invalid' : '' ?>" id="qtd_max" name="qtd_max" value="<?= htmlspecialchars((string) ($sessao['Qtd_max'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
                                    <?php if (isset($erros['qtd_max'])): ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($erros['qtd_max'], ENT_QUOTES, 'UTF-8') ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-4">
                                    <label for="qtd_disponivel" class="form-label">Quantidade disponivel</label>
                                    <input type="number" min="0" class="form-control <?= isset($erros['qtd_disponivel']) ? 'is-invalid' : '' ?>" id="qtd_disponivel" name="qtd_disponivel" value="<?= htmlspecialchars((string) ($sessao['Qtd_disponivel'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
                                    <?php if (isset($erros['qtd_disponivel'])): ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($erros['qtd_disponivel'], ENT_QUOTES, 'UTF-8') ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary">Salvar sessão</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="mb-3">Sessões já cadastradas</h5>
                        <?php if (empty($sessoes)): ?>
                            <p class="text-muted mb-3">Nenhuma sessão cadastrada até o momento.</p>
                        <?php else: ?>
                            <div class="table-responsive mb-3">
                                <table class="table table-sm align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Data</th>
                                            <th>Hora incial</th>
                                            <th>Qtd. mínima</th>
                                            <th>Qtd. máxima</th>
                                            <th>Disponível</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($sessoes as $item): ?>
                                            <tr>
                                                <td><?= htmlspecialchars((string) ($item['Data'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                                                <td><?= htmlspecialchars(substr((string) ($item['Hora'] ?? '-'), 0, 5), ENT_QUOTES, 'UTF-8') ?></td>
                                                <td><?= (int) ($item['Qtd_min'] ?? 0) ?></td>
                                                <td><?= (int) ($item['Qtd_max'] ?? 0) ?></td>
                                                <td><?= (int) ($item['Qtd_disponivel'] ?? 0) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>

                        <form action="<?= URL_BASE ?>/atividade/cadastro/concluir" method="POST" class="d-flex justify-content-end">
                            <input type="hidden" name="id_atividade" value="<?= (int) ($atividade['ID_Atividade'] ?? 0) ?>">
                            <button type="submit" class="btn btn-success">Concluir atividade</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
