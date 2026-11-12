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
    <title>Classics • Lista de Categorias</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
<?php require __DIR__ . '/../partials/head.php'; ?>
</head>

<body>
<?php require __DIR__ . '/../partials/navbar.php'; ?>

    <div class="container cl-page">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Lista de Categorias</h2>
            <a href="<?= URL_BASE ?>/categoria/cadastrar" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nova Categoria
            </a>
        </div>

        <?php
        $mensagensCategoria = [
            'excluida' => ['Categoria excluída.', 'success'],
            'em_uso' => ['Esta categoria está sendo usada por uma ou mais atividades e não pode ser excluída. Troque a categoria dessas atividades antes.', 'danger'],
            'categoria' => ['Categoria não encontrada.', 'danger']
        ];
        $codigoCategoria = (string) ($_GET['sucesso'] ?? ($_GET['erro'] ?? ''));
        $mensagemCategoria = $codigoCategoria !== '' ? ($mensagensCategoria[$codigoCategoria] ?? ['Não foi possível concluir a operação.', 'danger']) : null;
        ?>
        <?php if ($mensagemCategoria !== null): ?>
            <div class="alert alert-<?= $mensagemCategoria[1] ?>" role="alert"><?= htmlspecialchars($mensagemCategoria[0], ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

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
                                            <form action="<?= URL_BASE ?>/categoria/excluir" method="POST" class="d-inline m-0" onsubmit="return confirm('Deseja excluir esta categoria?')">
                                                <input type="hidden" name="id" value="<?= (int) $categoria['ID_Categoria'] ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i> Excluir
                                                </button>
                                            </form>
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

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
