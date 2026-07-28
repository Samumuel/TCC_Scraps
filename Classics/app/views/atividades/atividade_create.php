<?php 
if(!isset($_SESSION['empresa_logada'])){
    header('Location: ' . URL_BASE);
    exit();
}

$categorias = $categorias ?? [];
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Projeto Integrador • Nova Atividade</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

</head>

<body>

    <div class="container">



        <div class="d-flex mt-5">

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
                        <form action="<?= URL_BASE ?>/atividade/salvar" method="POST">
                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome da atividade</label>
                                <input type="text" class="form-control" id="nome" name="nome" required>
                            </div>
                            <div class="mb-3">
                                <label for="descricao" class="form-label">Descrição</label>
                                <textarea class="form-control" id="descricao" name="descricao" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="categoria_id" class="form-label">Categoria</label>
                                <select class="form-select" id="categoria_id" name="categoria_id" required <?= empty($categorias) ? 'disabled' : '' ?>>
                                    <option value="">Selecione uma categoria</option>
                                    <?php foreach ($categorias as $categoria): ?>
                                        <option value="<?= (int) $categoria['ID_Categoria'] ?>">
                                            <?= htmlspecialchars($categoria['Nome_Categoria'], ENT_QUOTES, 'UTF-8') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <?php if (empty($categorias)): ?>
                                <div class="alert alert-warning" role="alert">
                                    Nao ha categorias cadastradas. <a href="<?= URL_BASE ?>/categoria/cadastrar" class="alert-link">Cadastre uma categoria</a> para criar atividades.
                                </div>
                            <?php endif; ?>
                            <div class="mb-3">
                                <label for="duracao" class="form-label">Duração</label>
                                <input type="text" class="form-control" id="duracao" name="duracao">
                            </div>
                            <div class="mb-3">
                                <label for="localizacao" class="form-label">Localização</label>
                                <input type="text" class="form-control" id="localizacao" name="localizacao">
                            </div>
                            <div class="mb-3">
                                <label for="vagas" class="form-label">Número de vagas</label>
                                <input type="number" class="form-control" id="vagas" name="vagas" min="1">
                            </div>
                            <div class="mb-3">
                                <label for="valor" class="form-label">Valor</label>
                                <input type="text" class="form-control" id="valor" name="valor">
                            </div>
                            <div class="mb-3">
                                <label for="fotos" class="form-label">Fotos</label>
                                <input type="file" class="form-control" id="fotos" name="fotos" accept="image/*"
                                    multiple>
                            </div>
                            <button type="submit" class="btn btn-primary" <?= empty($categorias) ? 'disabled' : '' ?>>Salvar</button>
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