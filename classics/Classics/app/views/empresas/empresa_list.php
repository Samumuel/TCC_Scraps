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
    <title>Projeto Integrador • Lista de Animais</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>

    <div class="container">


        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Lista de Empresas</h2>
            <a href="<?= URL_BASE ?>/empresa/cadastrar" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nova Empresa
            </a>
        </div>

        <!-- CARD COM TABELA -->
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3">Nome</th>
                                <th class="px-4 py-3">Email</th>
                                <th class="px-4 py-3">CNPJ</th>
                                <th class="px-4 py-3">Localização</th>
                                <th class="px-4 py-3">Telefone</th>
                                <th class="px-4 py-3 text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lista as $empresa): ?>
                            <tr>
                                <td class="px-4 py-3 align-middle"><?= $empresa['Nome'] ?></td>
                                <td class="px-4 py-3 align-middle"><?= $empresa['Email'] ?></td>
                                <td class="px-4 py-3 align-middle"><?= $empresa['CNPJ'] ?></td>
                                <td class="px-4 py-3 align-middle"><?= $empresa['Localizacao'] ?></td>
                                <td class="px-4 py-3 align-middle"><?= $empresa['Telefone'] ?></td>
                                <td class="px-4 py-3 align-middle text-end">
                                    <a href="<?= URL_BASE ?>/empresa/editar?id=<?= $empresa['ID_Empresa'] ?>"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Editar
                                    </a>
                                    <a href="<?= URL_BASE ?>/empresa/ver?id=<?= $empresa['ID_Empresa'] ?>"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Ver
                                    </a>
                                    <a href="<?= URL_BASE ?>/empresa/excluir?id=<?= $empresa['ID_Empresa'] ?>"
                                        class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Deseja excluir esta empresa?')">
                                        <i class="bi bi-trash"></i> Excluir
                                    </a>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="mt-4">
            <a href="<?= URL_BASE ?>/usuarios" class="btn btn-link">Ver Usuários</a>
            <a href="<?= URL_BASE ?>/atividade" class="btn btn-link">Ver Atividades</a>
            <a href="<?= URL_BASE ?>/categoria" class="btn btn-link">Ver Categorias</a>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>