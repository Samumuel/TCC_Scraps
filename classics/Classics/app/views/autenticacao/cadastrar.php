<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Classics • Criar conta</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
            <a href="<?= URL_BASE ?>/login">Entrar</a>
            <a href="<?= URL_BASE ?>/cadastrar" class="ativo">Criar conta</a>
        </div>

        <h2 class="h3 mb-1">Criar sua conta</h2>
        <p class="text-muted small mb-4">Comece a explorar experiências incríveis hoje</p>

        <div class="tipo-cadastro cl-account-type mb-4">
            <button type="button" id="btnUsuario" class="ativo">
                <i class="bi bi-person"></i>
                <strong>Cliente</strong>
                <small>Busco e reservo atividades</small>
            </button>

            <button type="button" id="btnEmpresa">
                <i class="bi bi-building"></i>
                <strong>Empresa</strong>
                <small>Ofereço atividades e serviços</small>
            </button>
        </div>

        <input type="hidden" name="tipo" id="tipo" value="usuario">

        <?php if (isset($tipo_cadastro) && $tipo_cadastro === 'empresa'): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const tipo = document.getElementById('tipo');
                tipo.value = 'empresa';
                document.getElementById('formUsuario').style.display = 'none';
                document.getElementById('formEmpresa').style.display = 'block';
                document.getElementById('btnUsuario').classList.remove('ativo');
                document.getElementById('btnEmpresa').classList.add('ativo');
            });
        </script>
        <?php endif; ?>

        <!-- Formulário para cadastro de usuário -->
        <div id="formUsuario">
            <div>
                <form action="<?= URL_BASE ?>/usuarios/salvar" method="post">
                    <input type="hidden" name="cadastro_publico" value="1">
                    <?php if (isset($usuario['id'])): ?>
                    <input type="hidden" name="id" value="<?= $usuario['id'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="nomeUsuario" class="form-label">Nome de Usuário</label>
                        <input type="text" class="form-control" id="nomeUsuario" name="nomeUsuario"
                            value="<?= $usuario['nomeUsuario'] ?? '' ?>">
                        <?php if (isset($erros['nomeUsuario'])): ?>
                        <div class="text-danger small"><?= $erros['nomeUsuario'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control email-validacao" id="emailUsuario" name="email"
                            aria-describedby="emailUsuarioAjuda emailUsuarioErro"
                            value="<?= $usuario['email'] ?? '' ?>">
                        <div id="emailUsuarioAjuda" class="form-text">Digite um e-mail válido.</div>
                        <?php if (isset($erros['email'])): ?>
                        <div id="emailUsuarioErro" class="text-danger small"><?= htmlspecialchars($erros['email'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="cpf" class="form-label">CPF</label>
                        <input type="text" class="form-control somente-numeros" id="cpf" name="cpf"
                            inputmode="numeric" pattern="[0-9]*" maxlength="11" data-maxlength="11"
                            value="<?= $usuario['cpf'] ?? '' ?>">
                        <div class="form-text">Digite apenas números (11 dígitos).</div>
                        <?php if (isset($erros['cpf'])): ?>
                        <div class="text-danger small"><?= htmlspecialchars($erros['cpf'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="telefone" class="form-label">Telefone</label>
                        <input type="text" class="form-control somente-numeros" id="telefone" name="telefone"
                            inputmode="numeric" pattern="[0-9]*" maxlength="11" data-maxlength="11"
                            value="<?= $usuario['telefone'] ?? '' ?>">
                        <div class="form-text">Digite apenas números (10 ou 11 dígitos).</div>
                        <?php if (isset($erros['telefone'])): ?>
                        <div class="text-danger small"><?= htmlspecialchars($erros['telefone'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="idade" class="form-label">Idade</label>
                        <input type="number" class="form-control" id="idade" name="idade"
                            value="<?= $usuario['idade'] ?? '' ?>">
                    </div>

                    <input type="hidden" name="id" value="user">

                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="senha" name="senha">
                            <button class="btn btn-outline-secondary" type="button" id="toggleSenha">
                                <i class="bi bi-eye" id="iconSenha"></i>
                            </button>
                        </div>
                        <?php if (isset($erros['senha'])): ?>
                        <div class="text-danger small"><?= $erros['senha'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary py-2">
                            <?= isset($usuario['id']) ? 'Atualizar' : 'Criar conta' ?> <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Formulário para cadastro de empresa -->
        <div id="formEmpresa" style="display: none;">
            <div>
                <form action="<?= URL_BASE ?>/empresa/salvar" method="POST">
                    <input type="hidden" name="cadastro_publico" value="1">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome da empresa</label>
                        <input type="text" class="form-control" id="nome" name="nome" value="<?= htmlspecialchars($empresa['nome'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control email-validacao" id="emailEmpresa" name="email"
                            aria-describedby="emailEmpresaAjuda emailEmpresaErro" value="<?= htmlspecialchars($empresa['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                        <div id="emailEmpresaAjuda" class="form-text">Digite um e-mail válido. Este campo é obrigatório.</div>
                        <?php if (isset($erros['email'])): ?>
                        <div id="emailEmpresaErro" class="text-danger small"><?= htmlspecialchars($erros['email'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="cnpj" class="form-label">CNPJ</label>
                        <input type="text" class="form-control somente-numeros" id="cnpj" name="cnpj"
                            inputmode="numeric" pattern="[0-9]*" maxlength="14" data-maxlength="14" value="<?= htmlspecialchars($empresa['cnpj'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
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
                        <input type="text" class="form-control somente-numeros" id="telefone" name="telefone"
                            inputmode="numeric" pattern="[0-9]*" maxlength="11" data-maxlength="11" value="<?= htmlspecialchars($empresa['telefone'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <div class="form-text">Digite apenas números (10 ou 11 dígitos).</div>
                        <?php if (isset($erros['telefone'])): ?>
                        <div class="text-danger small"><?= htmlspecialchars($erros['telefone'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="senha" name="senha" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary py-2">
                            Criar conta <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <p class="mt-4 text-center small text-muted">
            Já tem uma conta?
            <a href="<?= URL_BASE ?>/login" class="fw-bold">Entrar</a>
        </p>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= URL_BASE ?>/js/cadastro.js"></script>
    <script src="<?= URL_BASE ?>/js/validacao-numerica.js"></script>
    <script src="<?= URL_BASE ?>/js/validacao-email.js"></script>
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