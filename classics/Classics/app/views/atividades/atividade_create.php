<?php 
if(!isset($_SESSION['empresa_logada'])){
    header('Location: ' . URL_BASE);
    exit();
}

$categorias = $categorias ?? [];
$atividade = $atividade ?? [];
$erros = $erros ?? [];
$atividadeCategoriaId = (int) ($atividade['Categoria_ID'] ?? 0);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Classics • Nova Atividade</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
<?php require __DIR__ . '/../partials/head.php'; ?>
</head>

<body>
<?php require __DIR__ . '/../partials/navbar.php'; ?>

    <div class="container cl-page">



        <div class="d-flex">

            <!-- CONTEÚDO -->
            <main class="flex-fill content">

                <!-- TÍTULO + VOLTAR -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">Nova Atividade</h2>
                    <a href="<?= URL_BASE ?>/atividade" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>

                <!-- CARD COM FORMULÁRIO -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form action="<?= URL_BASE ?>/atividade/salvar" method="POST" enctype="multipart/form-data">
                            <?php if (!empty($erros)): ?>
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
                                    Nao ha categorias cadastradas. <a href="<?= URL_BASE ?>/categoria/cadastrar" class="alert-link">Cadastre uma categoria</a> para criar atividades.
                                </div>
                            <?php endif; ?>
                            <div class="mb-3">
                                <label for="duracao_horas" class="form-label">Duração</label>
                                <div class="row g-2">
                                    <div class="col-6 col-sm-4">
                                        <div class="input-group">
                                            <input type="number" class="form-control <?= isset($erros['duracao']) ? 'is-invalid' : '' ?>" id="duracao_horas" name="duracao_horas" min="0" max="838" step="1" placeholder="0" aria-label="Horas de duração" value="<?= htmlspecialchars((string) ($atividade['Duracao_Horas'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                            <span class="input-group-text">h</span>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-4">
                                        <div class="input-group">
                                            <input type="number" class="form-control <?= isset($erros['duracao']) ? 'is-invalid' : '' ?>" id="duracao_minutos" name="duracao_minutos" min="0" max="59" step="1" placeholder="0" aria-label="Minutos de duração" value="<?= htmlspecialchars((string) ($atividade['Duracao_Minutos'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                            <span class="input-group-text">min</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-text">Quanto tempo a atividade dura. Ex.: 1 h e 30 min.</div>
                                <?php if (isset($erros['duracao'])): ?>
                                    <div class="invalid-feedback d-block"><?= htmlspecialchars($erros['duracao'], ENT_QUOTES, 'UTF-8') ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <label for="localizacao" class="form-label">Localização</label>
                                <input type="text" class="form-control" id="localizacao" name="localizacao" value="<?= htmlspecialchars($atividade['Localizacao'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            </div>
                            <div class="mb-3">
                                <label for="valor" class="form-label">Valor</label>
                                <input type="text" class="form-control <?= isset($erros['valor']) ? 'is-invalid' : '' ?>" id="valor" name="valor" value="<?= htmlspecialchars((string) ($atividade['Valor'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                <?php if (isset($erros['valor'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($erros['valor'], ENT_QUOTES, 'UTF-8') ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <label for="fotos" class="form-label">Fotos</label>
                                <input type="file" class="form-control" id="fotos" name="fotos" accept="image/*"
                                    >
                                <?php if (isset($erros['fotos'])): ?>
                                    <div class="invalid-feedback d-block"><?= htmlspecialchars($erros['fotos'], ENT_QUOTES, 'UTF-8') ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="alert alert-info mb-3" role="alert">
                                A localização será cadastrada na próxima etapa e, em seguida, será necessário cadastrar pelo menos 1 sessão.
                            </div>
                            <button type="submit" class="btn btn-primary" <?= empty($categorias) ? 'disabled' : '' ?>>Confirmar</button>
                        </form>
                    </div>
                </div>

            </main>
        </div>

    </div>


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>