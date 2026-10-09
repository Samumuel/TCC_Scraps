<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Projeto Integrador • <?= isset($empresa['ID_Empresa']) ? 'Editar' : 'Nova' ?> Empresa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0"><?= isset($empresa['ID_Empresa']) ? 'Editar' : 'Nova' ?> Empresa</h2>
            <a href="<?= URL_BASE ?>/empresa" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>

        <div class="card shadow-sm col-md-8 mx-auto">
            <div class="card-body p-4">
                <form action="<?= URL_BASE ?>/empresa/<?= isset($empresa['ID_Empresa']) ? 'atualizar' : 'salvar' ?>" method="post">
                    <?php if (isset($empresa['ID_Empresa'])): ?>
                        <input type="hidden" name="id" value="<?= $empresa['ID_Empresa'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome da Empresa</label>
                        <input type="text" class="form-control" id="nome" name="nome" value="<?= $empresa['Name'] ?? $empresa['nome'] ?? '' ?>">
                        <?php if (isset($erros['nome'])): ?>
                            <div class="text-danger small"><?= $erros['nome'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control email-validacao" id="email" name="email" aria-describedby="emailAjuda emailErro" value="<?= $empresa['Email'] ?? $empresa['email'] ?? '' ?>" required>
                        <div id="emailAjuda" class="form-text">Digite um e-mail válido. Este campo é obrigatório.</div>
                        <?php if (isset($erros['email'])): ?>
                            <div id="emailErro" class="text-danger small"><?= htmlspecialchars($erros['email'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="cnpj" class="form-label">CNPJ</label>
                        <input type="text" class="form-control somente-numeros" id="cnpj" name="cnpj" inputmode="numeric" pattern="[0-9]*" maxlength="14" data-maxlength="14" value="<?= $empresa['CNPJ'] ?? $empresa['cnpj'] ?? '' ?>">
                        <div class="form-text">Digite apenas números (14 dígitos).</div>
                        <?php if (isset($erros['cnpj'])): ?>
                            <div class="text-danger small"><?= $erros['cnpj'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="localizacao" class="form-label">Localização</label>
                        <input type="text" class="form-control" id="localizacao" name="localizacao" value="<?= $empresa['Localizacao'] ?? $empresa['localizacao'] ?? '' ?>">
                        <?php if (isset($erros['localizacao'])): ?>
                            <div class="text-danger small"><?= $erros['localizacao'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="telefone" class="form-label">Telefone</label>
                        <input type="text" class="form-control somente-numeros" id="telefone" name="telefone" inputmode="numeric" pattern="[0-9]*" maxlength="11" data-maxlength="11" value="<?= $empresa['Telefone'] ?? $empresa['telefone'] ?? '' ?>">
                        <div class="form-text">Digite apenas números (10 ou 11 dígitos).</div>
                        <?php if (isset($erros['telefone'])): ?>
                            <div class="text-danger small"><?= $erros['telefone'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha <?= isset($empresa['ID_Empresa']) ? '(Preencha apenas se quiser alterar)' : '' ?></label>
                        <input type="password" class="form-control" id="senha" name="senha">
                        <?php if (isset($erros['senha'])): ?>
                            <div class="text-danger small"><?= $erros['senha'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-circle"></i> <?= isset($empresa['ID_Empresa']) ? 'Atualizar' : 'Salvar' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= URL_BASE ?>/js/validacao-numerica.js"></script>
    <script src="<?= URL_BASE ?>/js/validacao-email.js"></script>
</body>

</html>
