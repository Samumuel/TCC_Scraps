<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Classics • Novo usuário</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
<?php require __DIR__ . '/../partials/head.php'; ?>
</head>

<body>
<?php require __DIR__ . '/../partials/navbar.php'; ?>
    <div class="container cl-page">
        <?php $usuario = $usuario ?? []; $erros = $erros ?? []; ?>
        <a href="<?= URL_BASE ?>/admin/usuarios" class="cl-back-link">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>

        <div class="cl-page-header">
            <h1 class="mb-1">Novo usuário</h1>
            <p>Cadastre um cliente ou outro administrador da plataforma</p>
        </div>

        <div class="card shadow-sm col-lg-8">
            <div class="card-body p-4">
                <form action="<?= URL_BASE ?>/admin/usuario/salvar" method="POST" novalidate>

                    <div class="mb-3">
                        <label for="nomeUsuario" class="form-label">Nome</label>
                        <input type="text" class="form-control<?= isset($erros['nomeUsuario']) ? ' is-invalid' : '' ?>" id="nomeUsuario" name="nomeUsuario" maxlength="255" value="<?= htmlspecialchars((string) ($usuario['nomeUsuario'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        <?php if (isset($erros['nomeUsuario'])): ?>
                            <div class="invalid-feedback d-block"><?= htmlspecialchars((string) $erros['nomeUsuario'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control<?= isset($erros['email']) ? ' is-invalid' : '' ?>" id="email" name="email" maxlength="255" value="<?= htmlspecialchars((string) ($usuario['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        <?php if (isset($erros['email'])): ?>
                            <div class="invalid-feedback d-block"><?= htmlspecialchars((string) $erros['email'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-5">
                    <div>
                        <label for="cpf" class="form-label">CPF</label>
                        <input type="text" class="form-control somente-numeros<?= isset($erros['cpf']) ? ' is-invalid' : '' ?>" id="cpf" name="cpf" inputmode="numeric" pattern="[0-9]*" maxlength="11" data-maxlength="11" value="<?= htmlspecialchars((string) ($usuario['cpf'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        <div class="form-text">Apenas números (11 dígitos).</div>
                        <?php if (isset($erros['cpf'])): ?>
                            <div class="invalid-feedback d-block"><?= htmlspecialchars((string) $erros['cpf'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                    </div>
                        </div>
                        <div class="col-md-4">
                    <div>
                        <label for="telefone" class="form-label">Telefone</label>
                        <input type="text" class="form-control somente-numeros<?= isset($erros['telefone']) ? ' is-invalid' : '' ?>" id="telefone" name="telefone" inputmode="numeric" pattern="[0-9]*" maxlength="11" data-maxlength="11" value="<?= htmlspecialchars((string) ($usuario['telefone'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        <div class="form-text">Apenas números.</div>
                        <?php if (isset($erros['telefone'])): ?>
                            <div class="invalid-feedback d-block"><?= htmlspecialchars((string) $erros['telefone'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                    </div>
                        </div>
                        <div class="col-md-3">
                    <div>
                        <label for="idade" class="form-label">Idade</label>
                        <input type="number" class="form-control<?= isset($erros['idade']) ? ' is-invalid' : '' ?>" id="idade" name="idade" min="0" max="120" value="<?= htmlspecialchars((string) ($usuario['idade'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        <?php if (isset($erros['idade'])): ?>
                            <div class="invalid-feedback d-block"><?= htmlspecialchars((string) $erros['idade'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                    </div>
                        </div>
                    </div>
                    <div class="mb-3"></div>
                    <div class="mb-3">
                        <label for="tipo_usuario" class="form-label">Tipo de conta</label>
                        <select class="form-select" id="tipo_usuario" name="tipo_usuario">
                            <option value="user" <?= ($usuario['tipo_usuario'] ?? 'user') !== 'admin' ? 'selected' : '' ?>>Cliente</option>
                            <option value="admin" <?= ($usuario['tipo_usuario'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrador</option>
                        </select>
                        <div class="form-text">O administrador só acessa o painel. O tipo não pode ser alterado depois.</div>
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
                        <a href="<?= URL_BASE ?>/admin/usuarios" class="btn btn-outline-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-person-plus"></i> Cadastrar usuário
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
