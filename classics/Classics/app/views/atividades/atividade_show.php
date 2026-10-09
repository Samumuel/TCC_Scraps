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
        <?php
        $atividade = $atividade ?? [];
        $sessoes = $sessoes ?? [];
        $abaAtiva = $aba_ativa ?? 'dados';
        $ativaDados = $abaAtiva === 'dados' ? 'active' : '';
        $ativaSessoes = $abaAtiva === 'sessoes' ? 'active' : '';
        $paneDados = $abaAtiva === 'dados' ? 'show active' : '';
        $paneSessoes = $abaAtiva === 'sessoes' ? 'show active' : '';
        ?>

        <?php if (!empty($atividade)): ?>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Detalhes da Atividade</h2>
                <a href="<?= URL_BASE ?>/atividade/listar" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Voltar para Lista
                </a>
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <ul class="nav nav-tabs mb-4" id="atividadeShowTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?= $ativaDados ?>" id="dados-tab" data-bs-toggle="tab" data-bs-target="#dados-pane" type="button" role="tab" aria-controls="dados-pane" aria-selected="<?= $abaAtiva === 'dados' ? 'true' : 'false' ?>">
                                Dados da atividade
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?= $ativaSessoes ?>" id="sessoes-tab" data-bs-toggle="tab" data-bs-target="#sessoes-pane" type="button" role="tab" aria-controls="sessoes-pane" aria-selected="<?= $abaAtiva === 'sessoes' ? 'true' : 'false' ?>">
                                Sessões
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="atividadeShowTabsContent">
                        <div class="tab-pane fade <?= $paneDados ?>" id="dados-pane" role="tabpanel" aria-labelledby="dados-tab" tabindex="0">
                            <div class="row g-4">
                                <div class="col-md-4 text-center">
                                    <?php if (!empty($atividade['Fotos'])): ?>
                                        <img src="<?= htmlspecialchars(URL_BASE . '/' . ltrim($atividade['Fotos'], '/'), ENT_QUOTES, 'UTF-8') ?>" alt="Foto da atividade" class="img-fluid rounded" style="max-height: 280px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 280px;">
                                            <i class="bi bi-image text-muted" style="font-size: 80px;" aria-label="Sem foto"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="col-md-8">
                                    <h3 class="card-title mb-3"><?= htmlspecialchars($atividade['Nome'] ?? '-', ENT_QUOTES, 'UTF-8') ?></h3>
                                    <p class="mb-2"><strong>Descrição:</strong> <?= htmlspecialchars($atividade['Descricao'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                                    <p class="mb-2"><strong>Categoria:</strong> <?= htmlspecialchars($atividade['Categoria_Nome'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                                    <p class="mb-2"><strong>Duração:</strong> <?= htmlspecialchars($atividade['Duracao'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                                    <p class="mb-2"><strong>Localização:</strong> <?= htmlspecialchars($atividade['Localizacao'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                                    <p class="mb-2"><strong>CEP:</strong> <?= htmlspecialchars($atividade['Localizacao_CEP'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                                    <p class="mb-2"><strong>Complemento:</strong> <?= htmlspecialchars($atividade['Localizacao_Complemento'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                                    <p class="mb-2"><strong>Valor:</strong> R$ <?= number_format((float) ($atividade['Valor'] ?? 0), 2, ',', '.') ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade <?= $paneSessoes ?>" id="sessoes-pane" role="tabpanel" aria-labelledby="sessoes-tab" tabindex="0">
                            <h5 class="mb-3">Sessões cadastradas</h5>

                            <?php if (empty($sessoes)): ?>
                                <div class="alert alert-info mb-0" role="alert">
                                    Esta atividade ainda não possui sessões cadastradas.
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Data</th>
                                                <th>Hora incial</th>
                                                <th>Quantidade mínima</th>
                                                <th>Quantidade máxima</th>
                                                <th>Quantidade disponível</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($sessoes as $sessao): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars((string) ($sessao['Data'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                                                    <td><?= htmlspecialchars(substr((string) ($sessao['Hora'] ?? '-'), 0, 5), ENT_QUOTES, 'UTF-8') ?></td>
                                                    <td><?= (int) ($sessao['Qtd_min'] ?? 0) ?></td>
                                                    <td><?= (int) ($sessao['Qtd_max'] ?? 0) ?></td>
                                                    <td><?= (int) ($sessao['Qtd_disponivel'] ?? 0) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
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
