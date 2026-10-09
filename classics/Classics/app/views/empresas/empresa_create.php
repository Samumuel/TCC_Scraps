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
    <title>Projeto Integrador • Nova Empresa</title>
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
                    <h2 class="mb-0">Nova Empresa</h2>
                    <a href="<?= URL_BASE ?>/empresa" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>

                <!-- CARD COM FORMULÁRIO -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form action="<?= URL_BASE ?>/empresa/salvar" method="POST">
                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome da empresa</label>
                                <input type="text" class="form-control" id="nome" name="nome" value="<?= htmlspecialchars($empresa['nome'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">E-mail</label>
                                <input type="email" class="form-control email-validacao" id="email" name="email" aria-describedby="emailAjuda emailErro" value="<?= htmlspecialchars($empresa['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                                <div id="emailAjuda" class="form-text">Digite um e-mail válido. Este campo é obrigatório.</div>
                                <?php if (isset($erros['email'])): ?>
                                    <div id="emailErro" class="text-danger small"><?= htmlspecialchars($erros['email'], ENT_QUOTES, 'UTF-8') ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <label for="cnpj" class="form-label">CNPJ</label>
                                <input type="text" class="form-control somente-numeros" id="cnpj" name="cnpj" inputmode="numeric" pattern="[0-9]*" maxlength="14" data-maxlength="14" value="<?= htmlspecialchars($empresa['cnpj'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                <div class="form-text">Digite apenas números (14 dígitos).</div>
                                <?php if (isset($erros['cnpj'])): ?>
                                    <div class="text-danger small"><?= htmlspecialchars($erros['cnpj'], ENT_QUOTES, 'UTF-8') ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <label for="localizacao" class="form-label">Localização</label>
                                <input type="text" class="form-control" id="localizacao" name="localizacao" value="<?= htmlspecialchars($empresa['localizacao'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            </div>
                            <div class="mb-3">
                                <label for="telefone" class="form-label">Telefone</label>
                                <input type="text" class="form-control somente-numeros" id="telefone" name="telefone" inputmode="numeric" pattern="[0-9]*" maxlength="11" data-maxlength="11" value="<?= htmlspecialchars($empresa['telefone'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                <div class="form-text">Digite apenas números (10 ou 11 dígitos).</div>
                                <?php if (isset($erros['telefone'])): ?>
                                    <div class="text-danger small"><?= htmlspecialchars($erros['telefone'], ENT_QUOTES, 'UTF-8') ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <label for="senha" class="form-label">Senha</label>
                                <input type="password" class="form-control" id="senha" name="senha" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Salvar</button>
                        </form>
                    </div>
                </div>

            </main>
        </div>

    </div>


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= URL_BASE ?>/js/validacao-numerica.js"></script>
    <script src="<?= URL_BASE ?>/js/validacao-email.js"></script>

</body>

</html>