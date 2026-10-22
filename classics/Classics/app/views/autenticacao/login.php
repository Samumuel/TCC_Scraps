<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classics • Entrar</title>
<?php require __DIR__ . '/../partials/head.php'; ?>
</head>

<body>
    <div class="cl-auth">
        <?php require __DIR__ . '/_auth_hero.php'; ?>

        <div class="cl-auth-panel">
            <div class="d-flex justify-content-between align-items-center px-4 pt-4">
                <a href="<?= URL_BASE ?>/" class="cl-brand">
                    <span class="cl-logo">C</span>
                    <span class="cl-brand-name">Classics</span>
                </a>
                <a href="<?= URL_BASE ?>/" class="text-muted small fw-semibold text-decoration-none">
                    <i class="bi bi-x-lg"></i> <span class="d-none d-sm-inline">Continuar sem entrar</span>
                </a>
            </div>

            <div class="cl-auth-form">
                <div class="cl-tab-switcher mb-4">
                    <a href="<?= URL_BASE ?>/login" class="ativo">Entrar</a>
                    <a href="<?= URL_BASE ?>/cadastrar">Criar conta</a>
                </div>

                <h2 class="h3 mb-1">Bem-vindo de volta</h2>
                <p class="text-muted small mb-4">Entre na sua conta para continuar explorando</p>

                <?php if (isset($erros)): ?>
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <div>
                            <?= is_array($erros) ? implode('<br>', $erros) : $erros ?>
                        </div>
                    </div>
                <?php endif; ?>

                <form action="<?= URL_BASE ?>/logar" method="post">
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <div class="cl-input-icon">
                            <i class="bi bi-envelope"></i>
                            <input type="email" class="form-control" id="email" name="email" placeholder="seu@email.com" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="senha" class="form-label">Senha</label>
                        <div class="input-group">
                            <div class="cl-input-icon flex-grow-1">
                                <i class="bi bi-lock"></i>
                                <input type="password" class="form-control rounded-end-0" id="senha" name="senha" placeholder="Sua senha" required>
                            </div>
                            <button class="btn btn-outline-secondary" type="button" id="toggleSenha" aria-label="Mostrar senha">
                                <i class="bi bi-eye" id="iconSenha"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary py-2">
                            Entrar <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>
                </form>

                <p class="mt-4 text-center small text-muted">
                    Não tem uma conta?
                    <a href="<?= URL_BASE ?>/cadastrar" class="fw-bold">Criar conta grátis</a>
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const toggleSenha = document.querySelector('#toggleSenha');
        const senhaInput = document.querySelector('#senha');
        const iconSenha = document.querySelector('#iconSenha');

        if (toggleSenha) {
            toggleSenha.addEventListener('click', function() {
                const type = senhaInput.getAttribute('type') === 'password' ? 'text' : 'password';
                senhaInput.setAttribute('type', type);

                iconSenha.classList.toggle('bi-eye');
                iconSenha.classList.toggle('bi-eye-slash');
            });
        }
    </script>
</body>

</html>
