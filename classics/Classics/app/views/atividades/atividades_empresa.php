<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Classics • Atividades da empresa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
<?php require __DIR__ . '/../partials/head.php'; ?>
</head>

<body>
<?php require __DIR__ . '/../partials/navbar.php'; ?>
    <div class="container cl-page">
        <?php
        $atividades = $atividades ?? [];
        $empresaLista = $empresa_lista ?? [];
        $podeCriar = !empty($pode_criar);
        $visaoAdmin = isset($admin_logado);
        $nomeEmpresa = (string) ($empresaLista['Name'] ?? '');

        $mensagem = null;
        $tipoMensagem = null;
        $mensagensSucesso = [
            'criada' => 'Atividade cadastrada com sucesso!',
            'editada' => 'Atividade atualizada com sucesso!',
            'excluida' => 'Atividade excluída. Os clientes com reserva futura foram avisados.'
        ];
        $mensagensErro = [
            'atividade' => 'Atividade não encontrada.',
            'excluir' => 'Não foi possível excluir a atividade. Tente novamente.'
        ];
        if (isset($_GET['sucesso']) && isset($mensagensSucesso[(string) $_GET['sucesso']])) {
            $mensagem = $mensagensSucesso[(string) $_GET['sucesso']];
            $tipoMensagem = 'success';
        } elseif (isset($_GET['erro'])) {
            $mensagem = $mensagensErro[(string) $_GET['erro']] ?? 'Não foi possível concluir a operação. Tente novamente.';
            $tipoMensagem = 'danger';
        }
        ?>

        <?php if ($visaoAdmin): ?>
            <a href="<?= URL_BASE ?>/admin" class="cl-back-link">
                <i class="bi bi-arrow-left"></i> Voltar ao painel
            </a>
        <?php endif; ?>

        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 cl-page-header">
            <div>
                <h1 class="mb-1"><?= $visaoAdmin ? 'Atividades da empresa' : 'Minhas Atividades' ?></h1>
                <p class="mb-0">
                    <?php if ($visaoAdmin): ?>
                        <?= htmlspecialchars($nomeEmpresa, ENT_QUOTES, 'UTF-8') ?> •
                    <?php endif; ?>
                    <?= count($atividades) ?> atividade(s) cadastrada(s)
                </p>
            </div>
            <?php if ($podeCriar): ?>
                <a href="<?= URL_BASE ?>/atividade/cadastrar" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Nova atividade
                </a>
            <?php endif; ?>
        </div>

        <?php if (!empty($mensagem)): ?>
            <div class="alert alert-<?= $tipoMensagem ?>" role="alert">
                <?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <?php if ($visaoAdmin && !$podeCriar): ?>
            <div class="alert alert-warning" role="alert">
                <i class="bi bi-info-circle"></i> Esta empresa não está aprovada: as atividades dela não aparecem no catálogo e não é possível criar novas.
            </div>
        <?php endif; ?>

        <?php if (empty($atividades)): ?>
            <div class="card text-center py-5">
                <div class="card-body">
                    <i class="bi bi-grid text-muted" style="font-size: 56px;"></i>
                    <h3 class="mt-3 mb-2">Nenhuma atividade cadastrada</h3>
                    <p class="text-muted mb-<?= $podeCriar ? '4' : '0' ?>">As atividades cadastradas aparecem aqui para ver, editar ou excluir.</p>
                    <?php if ($podeCriar): ?>
                        <a href="<?= URL_BASE ?>/atividade/cadastrar" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Cadastrar a primeira atividade
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($atividades as $atividade): ?>
                    <?php
                    $atividadeId = (int) $atividade['ID_Atividade'];
                    $sessoesFuturas = (int) ($atividade['Sessoes_Futuras'] ?? 0);
                    $noCatalogo = (int) ($atividade['Sessoes_Com_Vaga'] ?? 0) > 0 && $podeCriar;
                    ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card cl-activity-card h-100">
                            <?php if (!empty($atividade['Fotos'])): ?>
                                <img src="<?= htmlspecialchars(URL_BASE . '/' . ltrim((string) $atividade['Fotos'], '/'), ENT_QUOTES, 'UTF-8') ?>" alt="Foto da atividade" class="cl-activity-img">
                            <?php else: ?>
                                <div class="cl-activity-img cl-placeholder-img">
                                    <i class="bi bi-image" style="font-size: 48px;" aria-label="Sem foto"></i>
                                </div>
                            <?php endif; ?>
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex flex-wrap gap-2 mb-2">
                                    <?php if (!empty($atividade['Categoria_Nome'])): ?>
                                        <span class="cl-badge cl-badge-category"><?= htmlspecialchars((string) $atividade['Categoria_Nome'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <?php endif; ?>
                                    <?php if ($noCatalogo): ?>
                                        <span class="cl-badge cl-badge-success"><i class="bi bi-eye"></i> No catálogo</span>
                                    <?php else: ?>
                                        <span class="cl-badge cl-badge-neutral" title="Sem sessão futura com vaga<?= $podeCriar ? '' : ' ou empresa não aprovada' ?>"><i class="bi bi-eye-slash"></i> Fora do catálogo</span>
                                    <?php endif; ?>
                                </div>
                                <h3 class="card-title mb-2"><?= htmlspecialchars((string) ($atividade['Nome'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></h3>
                                <div class="cl-meta mb-1">
                                    <i class="bi bi-geo-alt"></i>
                                    <span><?= htmlspecialchars((string) (($atividade['Localizacao'] ?? '') !== '' ? $atividade['Localizacao'] : '-'), ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                                <div class="cl-meta mb-1">
                                    <i class="bi bi-calendar3"></i>
                                    <span><?= $sessoesFuturas ?> sessão(ões) futura(s)</span>
                                </div>
                                <div class="cl-price mt-auto mb-3">
                                    R$ <?= number_format((float) ($atividade['Valor'] ?? 0), 2, ',', '.') ?>
                                    <span class="text-muted small fw-normal">/pessoa</span>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="<?= URL_BASE ?>/atividade/ver?id=<?= $atividadeId ?>" class="btn btn-sm btn-outline-secondary flex-fill">
                                        <i class="bi bi-eye"></i> Ver
                                    </a>
                                    <a href="<?= URL_BASE ?>/atividade/editar?id=<?= $atividadeId ?>" class="btn btn-sm btn-outline-primary flex-fill">
                                        <i class="bi bi-pencil"></i> Editar
                                    </a>
                                    <form action="<?= URL_BASE ?>/atividade/excluir" method="POST" class="m-0 flex-fill"
                                        onsubmit="return confirm('Excluir esta atividade? Sessões, reservas, avaliações e favoritos dela também serão apagados e os clientes com reserva futura serão avisados.')">
                                        <input type="hidden" name="id" value="<?= $atividadeId ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                            <i class="bi bi-trash"></i> Excluir
                                        </button>
                                    </form>
                                </div>
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
