<?php 
if(!isset($_SESSION['empresa_logada'])){
    header('Location: ' . URL_BASE);
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Projeto Integrador • Lista de Categorias</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>

    <div class="container py-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Lista de Categorias</h2>
            <a href="<?= URL_BASE ?>/categoria/cadastrar" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nova Categoria
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3">ID</th>
                                <th class="px-4 py-3">Nome da Categoria</th>
                                <th class="px-4 py-3 text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($lista)): ?>
                                <?php foreach ($lista as $categoria): ?>
                                    <tr>
                                        <td class="px-4 py-3 align-middle"><?= $categoria['ID_Categoria'] ?></td>
                                        <td class="px-4 py-3 align-middle"><?= htmlspecialchars($categoria['Nome_Categoria'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td class="px-4 py-3 align-middle text-end">
                                            <a href="<?= URL_BASE ?>/categoria/editar?id=<?= $categoria['ID_Categoria'] ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i> Editar
                                            </a>
                                            <a href="<?= URL_BASE ?>/categoria/excluir?id=<?= $categoria['ID_Categoria'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Deseja excluir esta categoria?')">
                                                <i class="bi bi-trash"></i> Excluir
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td class="px-4 py-4 text-center text-muted" colspan="3">Nenhuma categoria cadastrada.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <a href="<?= URL_BASE ?>/empresa" class="btn btn-link">Ver Empresas</a>
            <a href="<?= URL_BASE ?>/usuarios" class="btn btn-link">Ver Usuários</a>
            <a href="<?= URL_BASE ?>/atividade" class="btn btn-link">Ver Atividades</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
