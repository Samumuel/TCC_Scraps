<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Projeto Integrador • <?= isset($usuario['id']) ? 'Editar' : 'Novo' ?> Usuário</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0"><?= isset($usuario['id']) ? 'Editar' : 'Novo' ?> Usuário</h2>
            <a href="<?= URL_BASE ?>/usuarios" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>

        <div class="card shadow-sm col-md-8 mx-auto">
            <div class="card-body p-4">
                <form action="<?= URL_BASE ?>/usuarios/<?= isset($usuario['id']) ? 'atualizar' : 'salvar' ?>" method="post" enctype="multipart/form-data">
                    <?php if (isset($usuario['id'])): ?>
                        <input type="hidden" name="id" value="<?= $usuario['id'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="nomeUsuario" class="form-label">Nome de Usuário</label>
                        <input type="text" class="form-control" id="nomeUsuario" name="nomeUsuario" value="<?= $usuario['nomeUsuario'] ?? '' ?>">
                        <?php if (isset($erros['nomeUsuario'])): ?>
                            <div class="text-danger small"><?= $erros['nomeUsuario'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control email-validacao" id="email" name="email" aria-describedby="emailAjuda emailErro" value="<?= $usuario['email'] ?? '' ?>">
                        <div id="emailAjuda" class="form-text">Digite um e-mail válido.</div>
                        <?php if (isset($erros['email'])): ?>
                            <div id="emailErro" class="text-danger small"><?= htmlspecialchars($erros['email'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="cpf" class="form-label">CPF</label>
                        <input type="text" class="form-control somente-numeros" id="cpf" name="cpf" inputmode="numeric" pattern="[0-9]*" maxlength="11" data-maxlength="11" value="<?= $usuario['cpf'] ?? '' ?>">
                        <div class="form-text">Digite apenas números (11 dígitos).</div>
                        <?php if (isset($erros['cpf'])): ?>
                            <div class="text-danger small"><?= htmlspecialchars($erros['cpf'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="telefone" class="form-label">Telefone</label>
                        <input type="text" class="form-control somente-numeros" id="telefone" name="telefone" inputmode="numeric" pattern="[0-9]*" maxlength="11" data-maxlength="11" value="<?= $usuario['telefone'] ?? '' ?>">
                        <div class="form-text">Digite apenas números (10 ou 11 dígitos).</div>
                        <?php if (isset($erros['telefone'])): ?>
                            <div class="text-danger small"><?= htmlspecialchars($erros['telefone'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="idade" class="form-label">Idade</label>
                        <input type="number" class="form-control" id="idade" name="idade" value="<?= $usuario['idade'] ?? '' ?>">
                    </div>

                    <div class="mb-3">
                        <label for="foto" class="form-label">Foto de Perfil</label>
                        <input type="file" class="form-control" id="foto" name="foto" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                        <div class="form-text">Formatos permitidos: JPG, PNG e WEBP. Tamanho máximo: 5MB.</div>
                        <?php if (isset($erros['foto'])): ?>
                            <div class="text-danger small"><?= htmlspecialchars($erros['foto'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <?php if (!empty($usuario['foto'])): ?>
                            <?php
                            $fotoAtual = (string) $usuario['foto'];
                            $fotoSrc = preg_match('#^https?://#i', $fotoAtual)
                                ? $fotoAtual
                                : URL_BASE . '/' . ltrim($fotoAtual, '/');
                            ?>
                            <img src="<?= htmlspecialchars($fotoSrc, ENT_QUOTES, 'UTF-8') ?>" alt="Foto atual do usuário" class="rounded-circle" style="width:120px; height:120px; object-fit:cover;">
                        <?php else: ?>
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 120px; height: 120px;">
                                <i class="bi bi-person-circle" style="font-size: 48px; color: #999;"></i>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha <?= isset($usuario['id']) ? '(Preencha apenas se quiser alterar)' : '' ?></label>
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
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-circle"></i> <?= isset($usuario['id']) ? 'Atualizar' : 'Salvar' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
