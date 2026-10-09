<?php
if (!isset($_SESSION['empresa_logada'])) {
    header('Location: ' . URL_BASE);
    exit();
}

$atividade = $atividade ?? [];
$categorias = $categorias ?? [];
$localizacao = $localizacao ?? [];
$sessoes = $sessoes ?? [];
$sessaoForm = $sessao_form ?? [];
$sessaoEditandoId = (int) ($sessao_editando_id ?? 0);
$erros = $erros ?? [];
$atividadeCategoriaId = (int) ($atividade['Categoria_ID'] ?? 0);
$abaAtiva = $aba_ativa ?? 'atividade';
$ativaAtividade = $abaAtiva === 'atividade' ? 'active' : '';
$ativaLocalizacao = $abaAtiva === 'localizacao' ? 'active' : '';
$ativaSessoes = $abaAtiva === 'sessoes' ? 'active' : '';
$paneAtividade = $abaAtiva === 'atividade' ? 'show active' : '';
$paneLocalizacao = $abaAtiva === 'localizacao' ? 'show active' : '';
$paneSessoes = $abaAtiva === 'sessoes' ? 'show active' : '';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Projeto Integrador • Editar Atividade</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Editar Atividade</h2>
            <a href="<?= URL_BASE ?>/atividade/listar" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-4">
                <ul class="nav nav-tabs mb-4" id="atividadeTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= $ativaAtividade ?>" id="atividade-tab" data-bs-toggle="tab" data-bs-target="#atividade-pane" type="button" role="tab" aria-controls="atividade-pane" aria-selected="<?= $abaAtiva === 'atividade' ? 'true' : 'false' ?>">
                            Dados da atividade
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= $ativaLocalizacao ?>" id="localizacao-tab" data-bs-toggle="tab" data-bs-target="#localizacao-pane" type="button" role="tab" aria-controls="localizacao-pane" aria-selected="<?= $abaAtiva === 'localizacao' ? 'true' : 'false' ?>">
                            Localização
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= $ativaSessoes ?>" id="sessoes-tab" data-bs-toggle="tab" data-bs-target="#sessoes-pane" type="button" role="tab" aria-controls="sessoes-pane" aria-selected="<?= $abaAtiva === 'sessoes' ? 'true' : 'false' ?>">
                            Sessões
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="atividadeTabsContent">
                    <div class="tab-pane fade <?= $paneAtividade ?>" id="atividade-pane" role="tabpanel" aria-labelledby="atividade-tab" tabindex="0">
                        <form action="<?= URL_BASE ?>/atividade/atualizar" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?= (int) ($atividade['ID_Atividade'] ?? 0) ?>">

                            <?php if (isset($erros['nome']) || isset($erros['descricao']) || isset($erros['categoria_id']) || isset($erros['duracao']) || isset($erros['valor'])): ?>
                                <div class="alert alert-danger" role="alert">
                                    Corrija os campos obrigatorios antes de salvar.
                                </div>
                            <?php endif; ?>

                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome da atividade</label>
                                <input type="text" class="form-control <?= isset($erros['nome']) ? 'is-invalid' : '' ?>" id="nome" name="nome" value="<?= htmlspecialchars($atividade['Nome'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                                <?php if (isset($erros['nome'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($erros['nome'], ENT_QUOTES, 'UTF-8') ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="descricao" class="form-label">Descrição</label>
                                <textarea class="form-control <?= isset($erros['descricao']) ? 'is-invalid' : '' ?>" id="descricao" name="descricao" rows="3"><?= htmlspecialchars($atividade['Descricao'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                                <?php if (isset($erros['descricao'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($erros['descricao'], ENT_QUOTES, 'UTF-8') ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="categoria_id" class="form-label">Categoria</label>
                                <select class="form-select <?= isset($erros['categoria_id']) ? 'is-invalid' : '' ?>" id="categoria_id" name="categoria_id" required <?= empty($categorias) ? 'disabled' : '' ?>>
                                    <option value="">Selecione uma categoria</option>
                                    <?php foreach ($categorias as $categoria): ?>
                                        <option value="<?= (int) $categoria['ID_Categoria'] ?>" <?= (int) $categoria['ID_Categoria'] === $atividadeCategoriaId ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($categoria['Nome_Categoria'], ENT_QUOTES, 'UTF-8') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($erros['categoria_id'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($erros['categoria_id'], ENT_QUOTES, 'UTF-8') ?></div>
                                <?php endif; ?>
                            </div>

                            <?php if (empty($categorias)): ?>
                                <div class="alert alert-warning" role="alert">
                                    Nao ha categorias cadastradas para esta empresa.
                                </div>
                            <?php endif; ?>

                            <div class="mb-3">
                                <label for="duracao" class="form-label">Duração</label>
                                <input type="time" class="form-control <?= isset($erros['duracao']) ? 'is-invalid' : '' ?>" id="duracao" name="duracao" value="<?= htmlspecialchars($atividade['Duracao'] ?? '', ENT_QUOTES, 'UTF-8') ?>" step="60">
                                <?php if (isset($erros['duracao'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($erros['duracao'], ENT_QUOTES, 'UTF-8') ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="valor" class="form-label">Valor</label>
                                <input type="text" class="form-control <?= isset($erros['valor']) ? 'is-invalid' : '' ?>" id="valor" name="valor" value="<?= htmlspecialchars((string) ($atividade['Valor'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                <?php if (isset($erros['valor'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($erros['valor'], ENT_QUOTES, 'UTF-8') ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="fotos" class="form-label">Foto da atividade</label>
                                <input type="file" class="form-control <?= isset($erros['fotos']) ? 'is-invalid' : '' ?>" id="fotos" name="fotos" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                                <div class="form-text">Selecione uma nova foto para substituir a atual. Formatos permitidos: JPG, PNG e WEBP. Tamanho máximo: 5MB.</div>
                                <?php if (isset($erros['fotos'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($erros['fotos'], ENT_QUOTES, 'UTF-8') ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <?php if (!empty($atividade['Fotos'])): ?>
                                    <img src="<?= htmlspecialchars(URL_BASE . '/' . ltrim($atividade['Fotos'], '/'), ENT_QUOTES, 'UTF-8') ?>" alt="Foto atual da atividade" class="rounded" style="width: 180px; height: 120px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 180px; height: 120px;">
                                        <i class="bi bi-image text-muted" style="font-size: 42px;" aria-label="Sem foto"></i>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="<?= URL_BASE ?>/atividade/listar" class="btn btn-outline-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-primary" <?= empty($categorias) ? 'disabled' : '' ?>>Atualizar</button>
                            </div>
                        </form>
                    </div>

                    <div class="tab-pane fade <?= $paneLocalizacao ?>" id="localizacao-pane" role="tabpanel" aria-labelledby="localizacao-tab" tabindex="0">
                        <div class="row g-4">
                            <div class="col-lg-4">
                                <div class="card h-100 border-0 bg-light">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">Resumo da atividade</h5>
                                        <p class="mb-2"><strong>Nome:</strong> <?= htmlspecialchars($atividade['Nome'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                                        <p class="mb-2"><strong>Categoria:</strong> <?= htmlspecialchars($atividade['Categoria_Nome'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                                        <p class="mb-0"><strong>Localização atual:</strong> <?= htmlspecialchars($atividade['Localizacao'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-8">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <form action="<?= URL_BASE ?>/atividade/localizacao/salvar" method="POST">
                                            <input type="hidden" name="id_atividade" value="<?= (int) ($atividade['ID_Atividade'] ?? 0) ?>">
                                            <input type="hidden" name="origem" value="editar">

                                            <?php if (isset($erros['cep']) || isset($erros['complemento'])): ?>
                                                <div class="alert alert-danger" role="alert">
                                                    Corrija os campos obrigatorios da localizacao.
                                                </div>
                                            <?php endif; ?>

                                            <div class="mb-3">
                                                <label for="cep" class="form-label">CEP</label>
                                                <input type="text" class="form-control <?= isset($erros['cep']) ? 'is-invalid' : '' ?>" id="cep" name="cep" value="<?= htmlspecialchars($localizacao['CEP'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                                                <?php if (isset($erros['cep'])): ?>
                                                    <div class="invalid-feedback"><?= htmlspecialchars($erros['cep'], ENT_QUOTES, 'UTF-8') ?></div>
                                                <?php endif; ?>
                                            </div>

                                            <div class="mb-3">
                                                <label for="complemento" class="form-label">Complemento</label>
                                                <input type="text" class="form-control <?= isset($erros['complemento']) ? 'is-invalid' : '' ?>" id="complemento" name="complemento" value="<?= htmlspecialchars($localizacao['Complemento'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                                                <?php if (isset($erros['complemento'])): ?>
                                                    <div class="invalid-feedback"><?= htmlspecialchars($erros['complemento'], ENT_QUOTES, 'UTF-8') ?></div>
                                                <?php endif; ?>
                                            </div>

                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="<?= URL_BASE ?>/atividade/listar" class="btn btn-outline-secondary">Cancelar</a>
                                                <button type="submit" class="btn btn-primary">
                                                    <?= !empty($localizacao) ? 'Salvar alterações' : 'Cadastrar localização' ?>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade <?= $paneSessoes ?>" id="sessoes-pane" role="tabpanel" aria-labelledby="sessoes-tab" tabindex="0">
                        <div class="row g-4">
                            <div class="col-lg-4">
                                <div class="card h-100 border-0 bg-light">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">Resumo da atividade</h5>
                                        <p class="mb-2"><strong>Nome:</strong> <?= htmlspecialchars($atividade['Nome'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                                        <p class="mb-2"><strong>Categoria:</strong> <?= htmlspecialchars($atividade['Categoria_Nome'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                                        <p class="mb-0"><strong>Total de sessões:</strong> <?= count($sessoes) ?></p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-8">
                                <div class="card shadow-sm mb-3">
                                    <div class="card-body">
                                        <h5 class="mb-3">Nova sessão</h5>

                                        <?php if (isset($_GET['sucesso_sessao']) && (string) $_GET['sucesso_sessao'] === '1'): ?>
                                            <div class="alert alert-success" role="alert">
                                                Sessão cadastrada com sucesso.
                                            </div>
                                        <?php endif; ?>

                                        <?php if (isset($_GET['sucesso_sessao']) && (string) $_GET['sucesso_sessao'] === '2'): ?>
                                            <div class="alert alert-success" role="alert">
                                                Sessão atualizada com sucesso.
                                            </div>
                                        <?php endif; ?>

                                        <?php if (isset($_GET['sucesso_sessao']) && (string) $_GET['sucesso_sessao'] === '3'): ?>
                                            <div class="alert alert-success" role="alert">
                                                Sessão excluída com sucesso.
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($abaAtiva === 'sessoes' && !empty($erros)): ?>
                                            <div class="alert alert-danger" role="alert">
                                                <?= htmlspecialchars($erros['geral'] ?? 'Corrija os campos obrigatorios da sessao.', ENT_QUOTES, 'UTF-8') ?>
                                            </div>
                                        <?php endif; ?>

                                        <form action="<?= URL_BASE ?>/atividade/sessao/edicao/salvar" method="POST">
                                            <input type="hidden" name="id_atividade" value="<?= (int) ($atividade['ID_Atividade'] ?? 0) ?>">

                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label for="sessao_data" class="form-label">Data</label>
                                                    <input type="date" class="form-control <?= isset($erros['data']) && $sessaoEditandoId === 0 ? 'is-invalid' : '' ?>" id="sessao_data" name="data" value="<?= htmlspecialchars($sessaoForm['Data'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                                                    <?php if (isset($erros['data']) && $sessaoEditandoId === 0): ?>
                                                        <div class="invalid-feedback"><?= htmlspecialchars($erros['data'], ENT_QUOTES, 'UTF-8') ?></div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="sessao_hora" class="form-label">Hora incial</label>
                                                    <input type="time" class="form-control <?= isset($erros['hora']) && $sessaoEditandoId === 0 ? 'is-invalid' : '' ?>" id="sessao_hora" name="hora" step="60" value="<?= htmlspecialchars($sessaoForm['Hora'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                                                    <?php if (isset($erros['hora']) && $sessaoEditandoId === 0): ?>
                                                        <div class="invalid-feedback"><?= htmlspecialchars($erros['hora'], ENT_QUOTES, 'UTF-8') ?></div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="sessao_qtd_min" class="form-label">Quantidade minima</label>
                                                    <input type="number" min="0" class="form-control <?= isset($erros['qtd_min']) && $sessaoEditandoId === 0 ? 'is-invalid' : '' ?>" id="sessao_qtd_min" name="qtd_min" value="<?= htmlspecialchars((string) ($sessaoForm['Qtd_min'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
                                                    <?php if (isset($erros['qtd_min']) && $sessaoEditandoId === 0): ?>
                                                        <div class="invalid-feedback"><?= htmlspecialchars($erros['qtd_min'], ENT_QUOTES, 'UTF-8') ?></div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="sessao_qtd_max" class="form-label">Quantidade maxima</label>
                                                    <input type="number" min="0" class="form-control <?= isset($erros['qtd_max']) && $sessaoEditandoId === 0 ? 'is-invalid' : '' ?>" id="sessao_qtd_max" name="qtd_max" value="<?= htmlspecialchars((string) ($sessaoForm['Qtd_max'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
                                                    <?php if (isset($erros['qtd_max']) && $sessaoEditandoId === 0): ?>
                                                        <div class="invalid-feedback"><?= htmlspecialchars($erros['qtd_max'], ENT_QUOTES, 'UTF-8') ?></div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="sessao_qtd_disponivel" class="form-label">Quantidade disponivel</label>
                                                    <input type="number" min="0" class="form-control <?= isset($erros['qtd_disponivel']) && $sessaoEditandoId === 0 ? 'is-invalid' : '' ?>" id="sessao_qtd_disponivel" name="qtd_disponivel" value="<?= htmlspecialchars((string) ($sessaoForm['Qtd_disponivel'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
                                                    <?php if (isset($erros['qtd_disponivel']) && $sessaoEditandoId === 0): ?>
                                                        <div class="invalid-feedback"><?= htmlspecialchars($erros['qtd_disponivel'], ENT_QUOTES, 'UTF-8') ?></div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-end mt-3">
                                                <button type="submit" class="btn btn-primary">Cadastrar sessão</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="mb-3">Sessões cadastradas</h5>
                                        <?php if (empty($sessoes)): ?>
                                            <p class="text-muted mb-0">Nenhuma sessão cadastrada para esta atividade.</p>
                                        <?php else: ?>
                                            <div class="table-responsive">
                                                <table class="table table-sm align-middle mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Data</th>
                                                            <th>Hora incial</th>
                                                            <th>Qtd. mínima</th>
                                                            <th>Qtd. máxima</th>
                                                            <th>Disponível</th>
                                                            <th class="text-end">Ações</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($sessoes as $item): ?>
                                                            <?php
                                                            $idSessaoLinha = (int) ($item['ID_Sessao'] ?? 0);
                                                            $editandoLinha = $sessaoEditandoId === $idSessaoLinha;
                                                            $dataLinha = $editandoLinha ? ($sessaoForm['Data'] ?? '') : (string) ($item['Data'] ?? '');
                                                            $horaLinha = $editandoLinha ? ($sessaoForm['Hora'] ?? '') : substr((string) ($item['Hora'] ?? ''), 0, 5);
                                                            $qtdMinLinha = $editandoLinha ? (string) ($sessaoForm['Qtd_min'] ?? '') : (string) ($item['Qtd_min'] ?? '');
                                                            $qtdMaxLinha = $editandoLinha ? (string) ($sessaoForm['Qtd_max'] ?? '') : (string) ($item['Qtd_max'] ?? '');
                                                            $qtdDisponivelLinha = $editandoLinha ? (string) ($sessaoForm['Qtd_disponivel'] ?? '') : (string) ($item['Qtd_disponivel'] ?? '');
                                                            $updateFormId = 'sessao-update-' . $idSessaoLinha;
                                                            ?>
                                                            <tr>
                                                                <td>
                                                                    <input type="date" form="<?= htmlspecialchars($updateFormId, ENT_QUOTES, 'UTF-8') ?>" class="form-control form-control-sm <?= isset($erros['data']) && $editandoLinha ? 'is-invalid' : '' ?>" name="data" value="<?= htmlspecialchars($dataLinha, ENT_QUOTES, 'UTF-8') ?>" required>
                                                                    <?php if (isset($erros['data']) && $editandoLinha): ?>
                                                                        <div class="invalid-feedback d-block"><?= htmlspecialchars($erros['data'], ENT_QUOTES, 'UTF-8') ?></div>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td>
                                                                    <input type="time" form="<?= htmlspecialchars($updateFormId, ENT_QUOTES, 'UTF-8') ?>" class="form-control form-control-sm <?= isset($erros['hora']) && $editandoLinha ? 'is-invalid' : '' ?>" name="hora" step="60" value="<?= htmlspecialchars($horaLinha, ENT_QUOTES, 'UTF-8') ?>" required>
                                                                    <?php if (isset($erros['hora']) && $editandoLinha): ?>
                                                                        <div class="invalid-feedback d-block"><?= htmlspecialchars($erros['hora'], ENT_QUOTES, 'UTF-8') ?></div>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td>
                                                                    <input type="number" form="<?= htmlspecialchars($updateFormId, ENT_QUOTES, 'UTF-8') ?>" min="0" class="form-control form-control-sm <?= isset($erros['qtd_min']) && $editandoLinha ? 'is-invalid' : '' ?>" name="qtd_min" value="<?= htmlspecialchars($qtdMinLinha, ENT_QUOTES, 'UTF-8') ?>" required>
                                                                    <?php if (isset($erros['qtd_min']) && $editandoLinha): ?>
                                                                        <div class="invalid-feedback d-block"><?= htmlspecialchars($erros['qtd_min'], ENT_QUOTES, 'UTF-8') ?></div>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td>
                                                                    <input type="number" form="<?= htmlspecialchars($updateFormId, ENT_QUOTES, 'UTF-8') ?>" min="0" class="form-control form-control-sm <?= isset($erros['qtd_max']) && $editandoLinha ? 'is-invalid' : '' ?>" name="qtd_max" value="<?= htmlspecialchars($qtdMaxLinha, ENT_QUOTES, 'UTF-8') ?>" required>
                                                                    <?php if (isset($erros['qtd_max']) && $editandoLinha): ?>
                                                                        <div class="invalid-feedback d-block"><?= htmlspecialchars($erros['qtd_max'], ENT_QUOTES, 'UTF-8') ?></div>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td>
                                                                    <input type="number" form="<?= htmlspecialchars($updateFormId, ENT_QUOTES, 'UTF-8') ?>" min="0" class="form-control form-control-sm <?= isset($erros['qtd_disponivel']) && $editandoLinha ? 'is-invalid' : '' ?>" name="qtd_disponivel" value="<?= htmlspecialchars($qtdDisponivelLinha, ENT_QUOTES, 'UTF-8') ?>" required>
                                                                    <?php if (isset($erros['qtd_disponivel']) && $editandoLinha): ?>
                                                                        <div class="invalid-feedback d-block"><?= htmlspecialchars($erros['qtd_disponivel'], ENT_QUOTES, 'UTF-8') ?></div>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td class="text-end">
                                                                    <div class="d-inline-flex gap-2">
                                                                        <form id="<?= htmlspecialchars($updateFormId, ENT_QUOTES, 'UTF-8') ?>" action="<?= URL_BASE ?>/atividade/sessao/edicao/atualizar" method="POST" class="d-inline">
                                                                            <input type="hidden" name="id_atividade" value="<?= (int) ($atividade['ID_Atividade'] ?? 0) ?>">
                                                                            <input type="hidden" name="id_sessao" value="<?= $idSessaoLinha ?>">
                                                                            <button type="submit" class="btn btn-sm btn-outline-primary">Salvar</button>
                                                                        </form>
                                                                        <form action="<?= URL_BASE ?>/atividade/sessao/edicao/excluir" method="POST" class="d-inline" onsubmit="return confirm('Deseja excluir esta sessão?');">
                                                                            <input type="hidden" name="id_atividade" value="<?= (int) ($atividade['ID_Atividade'] ?? 0) ?>">
                                                                            <input type="hidden" name="id_sessao" value="<?= $idSessaoLinha ?>">
                                                                            <button type="submit" class="btn btn-sm btn-outline-danger">Excluir</button>
                                                                        </form>
                                                                    </div>
                                                                </td>
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
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>