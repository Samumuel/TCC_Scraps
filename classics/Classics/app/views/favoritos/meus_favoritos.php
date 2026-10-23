<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Classics • Favoritos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
<?php require __DIR__ . '/../partials/head.php'; ?>
</head>

<body>
<?php require __DIR__ . '/../partials/navbar.php'; ?>
    <div class="container cl-page">
        <?php
        $favoritos = $favoritos ?? [];

        $mensagem = null;
        $tipoMensagem = null;
        if (isset($_GET['sucesso'])) {
            $sucesso = (string) $_GET['sucesso'];
            if ($sucesso === 'removido') {
                $mensagem = 'Atividade removida dos favoritos.';
            } elseif ($sucesso === 'adicionado') {
                $mensagem = 'Atividade adicionada aos favoritos.';
            }
            $tipoMensagem = 'success';
        } elseif (isset($_GET['erro'])) {
            $erro = (string) $_GET['erro'];
            if ($erro === 'atividade') {
                $mensagem = 'Atividade não encontrada.';
            } else {
                $mensagem = 'Não foi possível concluir a operação. Tente novamente.';
            }
            $tipoMensagem = 'danger';
        }
        ?>

        <div class="cl-page-header">
            <h1 class="mb-1">Favoritos</h1>
            <p>As atividades que você salvou para conferir depois</p>
        </div>

        <?php if (!empty($mensagem)): ?>
            <div class="alert alert-<?= htmlspecialchars($tipoMensagem ?? 'info', ENT_QUOTES, 'UTF-8') ?>" role="alert">
                <?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($favoritos)): ?>
            <div class="row g-4">
                <?php foreach ($favoritos as $item): ?>
                    <?php
                    $atividade = $item['atividade'];
                    $atividadeId = (int) $atividade->getIdAtividade();
                    ?>
                    <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                        <div class="card cl-activity-card cl-card-hover">
                            <div class="position-relative">
                                <a href="<?= URL_BASE ?>/atividade/ver?id=<?= $atividadeId ?>">
                                    <?php if (!empty($atividade->getFotos())): ?>
                                        <img src="<?= htmlspecialchars(URL_BASE . '/' . ltrim((string) $atividade->getFotos(), '/'), ENT_QUOTES, 'UTF-8') ?>" alt="Foto da atividade" class="cl-activity-img">
                                    <?php else: ?>
                                        <div class="cl-activity-img cl-placeholder-img">
                                            <i class="bi bi-image" style="font-size: 48px;" aria-label="Sem foto"></i>
                                        </div>
                                    <?php endif; ?>
                                </a>
                                <form action="<?= URL_BASE ?>/favorito/alternar" method="POST" class="cl-heart-overlay">
                                    <input type="hidden" name="atividade_id" value="<?= $atividadeId ?>">
                                    <input type="hidden" name="origem" value="favoritos">
                                    <button type="submit" class="cl-heart-btn ativo" title="Remover dos favoritos" aria-label="Remover dos favoritos" aria-pressed="true">
                                        <i class="bi bi-heart-fill"></i>
                                    </button>
                                </form>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <?php if ($item['categoria_nome'] !== ''): ?>
                                    <div class="mb-2">
                                        <span class="cl-badge cl-badge-category"><?= htmlspecialchars($item['categoria_nome'], ENT_QUOTES, 'UTF-8') ?></span>
                                    </div>
                                <?php endif; ?>
                                <h3 class="card-title mb-2"><?= htmlspecialchars((string) ($atividade->getNome() ?? '-'), ENT_QUOTES, 'UTF-8') ?></h3>
                                <div class="cl-meta mb-1">
                                    <i class="bi bi-geo-alt"></i>
                                    <span><?= htmlspecialchars((string) ($atividade->getLocalizacao() ?? '-'), ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                                <div class="cl-meta mb-3">
                                    <i class="bi bi-clock"></i>
                                    <span><?= htmlspecialchars(\app\helpers\Formatador::duracao($atividade->getDuracao()), ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                                <div class="cl-price mt-auto mb-3">
                                    R$ <?= number_format((float) ($atividade->getValor() ?? 0), 2, ',', '.') ?>
                                    <span class="text-muted small fw-normal">/pessoa</span>
                                </div>
                                <a href="<?= URL_BASE ?>/atividade/ver?id=<?= $atividadeId ?>" class="btn btn-primary btn-sm w-100">
                                    Ver atividade
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="card text-center py-5">
                <div class="card-body">
                    <i class="bi bi-heart text-muted" style="font-size: 56px;"></i>
                    <h3 class="h5 mt-3 mb-2">Nenhuma atividade favoritada</h3>
                    <p class="text-muted mb-4">Toque no coração de uma atividade para salvá-la aqui.</p>
                    <a href="<?= URL_BASE ?>/" class="btn btn-primary">Explorar atividades</a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
