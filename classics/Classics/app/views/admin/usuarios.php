<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Classics • Usuários</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
<?php require __DIR__ . '/../partials/head.php'; ?>
</head>

<body>
<?php require __DIR__ . '/../partials/navbar.php'; ?>
    <div class="container cl-page">
        <?php
        $usuarios = $usuarios ?? [];
        $adminId = (int) ($admin_id ?? 0);

        $mensagem = null;
        $tipoMensagem = null;
        $mensagensSucesso = [
            'editado' => 'Dados do usuário atualizados.',
            'excluido' => 'Usuário excluído com todos os seus dados.'
        ];
        $mensagensErro = [
            'usuario' => 'Usuário não encontrado.',
            'proprio' => 'Você não pode excluir a sua própria conta.',
            'excluir' => 'Não foi possível excluir o usuário. Tente novamente.'
        ];
        if (isset($_GET['sucesso']) && isset($mensagensSucesso[(string) $_GET['sucesso']])) {
            $mensagem = $mensagensSucesso[(string) $_GET['sucesso']];
            $tipoMensagem = 'success';
        } elseif (isset($_GET['erro'])) {
            $mensagem = $mensagensErro[(string) $_GET['erro']] ?? 'Não foi possível concluir a operação. Tente novamente.';
            $tipoMensagem = 'danger';
        }
        ?>

        <div class="cl-page-header">
            <h1 class="mb-1">Usuários</h1>
            <p><?= count($usuarios) ?> usuário(s) cadastrado(s) na plataforma</p>
        </div>

        <?php if (!empty($mensagem)): ?>
            <div class="alert alert-<?= $tipoMensagem ?>" role="alert">
                <?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <?php if (empty($usuarios)): ?>
            <div class="card text-center py-5">
                <div class="card-body">
                    <i class="bi bi-people text-muted" style="font-size: 56px;"></i>
                    <h3 class="mt-3 mb-0">Nenhum usuário cadastrado</h3>
                </div>
            </div>
        <?php else: ?>
            <!-- Telas médias e grandes: tabela -->
            <div class="card d-none d-md-block">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Nome</th>
                                <th>E-mail</th>
                                <th>CPF</th>
                                <th>Telefone</th>
                                <th>Tipo</th>
                                <th class="text-end pe-4">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $usuario): ?>
                                <?php
                                $usuarioId = (int) $usuario['ID_Usuario'];
                                $ehAdmin = strtolower(trim((string) ($usuario['Tipo_usuario'] ?? ''))) === 'admin';
                                ?>
                                <tr>
                                    <td class="ps-4 fw-semibold">
                                        <?= htmlspecialchars((string) ($usuario['Nome'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                                        <?php if ($usuarioId === $adminId): ?>
                                            <span class="text-muted small fw-normal">(você)</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars((string) ($usuario['Email'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="text-nowrap"><?= htmlspecialchars(\app\helpers\Formatador::cpf($usuario['CPF'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="text-nowrap"><?= htmlspecialchars(\app\helpers\Formatador::telefone($usuario['Telefone'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                    <td>
                                        <span class="cl-badge cl-badge-<?= $ehAdmin ? 'info' : 'neutral' ?>"><?= $ehAdmin ? 'Administrador' : 'Cliente' ?></span>
                                    </td>
                                    <td class="text-end pe-4 text-nowrap">
                                        <a href="<?= URL_BASE ?>/admin/usuario/editar?id=<?= $usuarioId ?>" class="btn btn-sm btn-outline-secondary" title="Editar">
                                            <i class="bi bi-pencil"></i> Editar
                                        </a>
                                        <?php if ($usuarioId !== $adminId): ?>
                                            <form action="<?= URL_BASE ?>/admin/usuario/excluir" method="POST" class="d-inline m-0"
                                                onsubmit="return confirm('Excluir este usuário? Os agendamentos, avaliações e favoritos dele também serão apagados. Esta ação não pode ser desfeita.')">
                                                <input type="hidden" name="usuario_id" value="<?= $usuarioId ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir">
                                                    <i class="bi bi-trash"></i> Excluir
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Celular: um cartão por usuário -->
            <div class="d-flex d-md-none flex-column gap-3">
                <?php foreach ($usuarios as $usuario): ?>
                    <?php
                    $usuarioId = (int) $usuario['ID_Usuario'];
                    $ehAdmin = strtolower(trim((string) ($usuario['Tipo_usuario'] ?? ''))) === 'admin';
                    ?>
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <strong>
                                    <?= htmlspecialchars((string) ($usuario['Nome'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                                    <?php if ($usuarioId === $adminId): ?>
                                        <span class="text-muted small fw-normal">(você)</span>
                                    <?php endif; ?>
                                </strong>
                                <span class="cl-badge cl-badge-<?= $ehAdmin ? 'info' : 'neutral' ?>"><?= $ehAdmin ? 'Administrador' : 'Cliente' ?></span>
                            </div>
                            <div class="cl-meta small mb-1 text-break"><i class="bi bi-envelope"></i> <span><?= htmlspecialchars((string) ($usuario['Email'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span></div>
                            <div class="cl-meta small mb-1"><i class="bi bi-person-vcard"></i> <span>CPF: <?= htmlspecialchars(\app\helpers\Formatador::cpf($usuario['CPF'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span></div>
                            <div class="cl-meta small mb-3"><i class="bi bi-telephone"></i> <span><?= htmlspecialchars(\app\helpers\Formatador::telefone($usuario['Telefone'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span></div>
                            <div class="d-flex gap-2">
                                <a href="<?= URL_BASE ?>/admin/usuario/editar?id=<?= $usuarioId ?>" class="btn btn-sm btn-outline-secondary flex-fill">
                                    <i class="bi bi-pencil"></i> Editar
                                </a>
                                <?php if ($usuarioId !== $adminId): ?>
                                    <form action="<?= URL_BASE ?>/admin/usuario/excluir" method="POST" class="m-0 flex-fill"
                                        onsubmit="return confirm('Excluir este usuário? Os agendamentos, avaliações e favoritos dele também serão apagados. Esta ação não pode ser desfeita.')">
                                        <input type="hidden" name="usuario_id" value="<?= $usuarioId ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                            <i class="bi bi-trash"></i> Excluir
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
