<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Classics • Nova empresa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
<?php require __DIR__ . '/../partials/head.php'; ?>
</head>

<body>
<?php require __DIR__ . '/../partials/navbar.php'; ?>
    <div class="container cl-page">
        <?php $empresa = $empresa ?? []; $erros = $erros ?? []; ?>
        <a href="<?= URL_BASE ?>/admin?aba=<?= htmlspecialchars((string) ($empresa['status'] ?? 'pendente'), ENT_QUOTES, 'UTF-8') ?>" class="cl-back-link">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>

        <div class="cl-page-header">
            <h1 class="mb-1">Nova empresa</h1>
            <p>A empresa entra como pendente e é aprovada pelo painel, como no cadastro público</p>
        </div>

        <div class="card shadow-sm col-lg-8">
            <div class="card-body p-4">
                <form action="<?= URL_BASE ?>/admin/empresa/salvar" method="POST" novalidate>

                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome da empresa</label>
                        <input type="text" class="form-control<?= isset($erros['nome']) ? ' is-invalid' : '' ?>" id="nome" name="nome" maxlength="255" value="<?= htmlspecialchars((string) ($empresa['nome'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        <?php if (isset($erros['nome'])): ?>
                            <div class="invalid-feedback d-block"><?= htmlspecialchars((string) $erros['nome'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control<?= isset($erros['email']) ? ' is-invalid' : '' ?>" id="email" name="email" maxlength="255" value="<?= htmlspecialchars((string) ($empresa['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        <?php if (isset($erros['email'])): ?>
                            <div class="invalid-feedback d-block"><?= htmlspecialchars((string) $erros['email'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                    <div>
                        <label for="cnpj" class="form-label">CNPJ</label>
                        <input type="text" class="form-control somente-numeros<?= isset($erros['cnpj']) ? ' is-invalid' : '' ?>" id="cnpj" name="cnpj" inputmode="numeric" pattern="[0-9]*" maxlength="14" data-maxlength="14" value="<?= htmlspecialchars((string) ($empresa['cnpj'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        <div class="form-text">Apenas números (14 dígitos).</div>
                        <?php if (isset($erros['cnpj'])): ?>
                            <div class="invalid-feedback d-block"><?= htmlspecialchars((string) $erros['cnpj'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                    </div>
                        </div>
                        <div class="col-md-6">
                    <div>
                        <label for="telefone" class="form-label">Telefone</label>
                        <input type="text" class="form-control somente-numeros<?= isset($erros['telefone']) ? ' is-invalid' : '' ?>" id="telefone" name="telefone" inputmode="numeric" pattern="[0-9]*" maxlength="11" data-maxlength="11" value="<?= htmlspecialchars((string) ($empresa['telefone'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        <div class="form-text">Apenas números (10 ou 11 dígitos).</div>
                        <?php if (isset($erros['telefone'])): ?>
                            <div class="invalid-feedback d-block"><?= htmlspecialchars((string) $erros['telefone'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                    </div>
                        </div>
                    </div>
                    <div class="mb-3"></div>
                    <div class="mb-3">
                        <label for="localizacao" class="form-label">Localização</label>
                        <input type="text" class="form-control<?= isset($erros['localizacao']) ? ' is-invalid' : '' ?>" id="localizacao" name="localizacao" maxlength="255" value="<?= htmlspecialchars((string) ($empresa['localizacao'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        <?php if (isset($erros['localizacao'])): ?>
                            <div class="invalid-feedback d-block"><?= htmlspecialchars((string) $erros['localizacao'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-4">
                        <label for="senha" class="form-label">Senha</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="senha" name="senha" autocomplete="new-password"<?= isset($erros['senha']) ? ' aria-invalid="true"' : '' ?>>
                            <button class="btn btn-outline-secondary" type="button" data-alternar-senha="senha" aria-label="Mostrar senha">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <?php if (isset($erros['senha'])): ?>
                            <div class="invalid-feedback d-block"><?= htmlspecialchars((string) $erros['senha'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= URL_BASE ?>/admin?aba=<?= htmlspecialchars((string) ($empresa['status'] ?? 'pendente'), ENT_QUOTES, 'UTF-8') ?>" class="btn btn-outline-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-building-add"></i> Cadastrar empresa
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= URL_BASE ?>/js/validacao-numerica.js"></script>
<?php require __DIR__ . '/_alternar_senha.php'; ?>
</body>

</html>
