<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Projeto Integrador • <?= isset($animal['id']) ? 'Editar' : 'Novo' ?> Animal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0"><?= isset($animal['id']) ? 'Editar' : 'Novo' ?> Animal</h2>
            <a href="<?= URL_BASE ?>/animais" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>

        <div class="card shadow-sm col-md-8 mx-auto">
            <div class="card-body p-4">
                <form action="<?= URL_BASE ?>/animais/<?= isset($animal['id']) ? 'atualizar' : 'salvar' ?>" method="post">
                    <?php if (isset($animal['id'])): ?>
                        <input type="hidden" name="id" value="<?= $animal['id'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="nome" name="nome" value="<?= $animal['nome'] ?? '' ?>">
                        <?php if (isset($erros['nome'])): ?>
                            <div class="text-danger small"><?= $erros['nome'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="especie" class="form-label">Espécie</label>
                        <input type="text" class="form-control" id="especie" name="especie" value="<?= $animal['especie'] ?? '' ?>">
                        <?php if (isset($erros['especie'])): ?>
                            <div class="text-danger small"><?= $erros['especie'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="dono" class="form-label">Dono</label>
                        <input type="text" class="form-control" id="dono" name="dono" value="<?= $animal['dono'] ?? '' ?>">
                        <?php if (isset($erros['dono'])): ?>
                            <div class="text-danger small"><?= $erros['dono'] ?></div>
                        <?php endif; ?>
                    </div>
                        <div class="mb-3">
                        <label for="idade" class="form-label">Idade</label>
                        <input type="text" class="form-control" id="idade" name="idade" value="<?= $animal['idade'] ?? '' ?>">
                        <?php if (isset($erros['idade'])): ?>
                            <div class="text-danger small"><?= $erros['idade'] ?></div>
                        <?php endif; ?>
                    </div>

                    

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-circle"></i> <?= isset($animal['id']) ? 'Atualizar' : 'Salvar' ?>
                        </button>
                    </div>
                </form>
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
