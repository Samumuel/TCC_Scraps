<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Classics • Meus Agendamentos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
<?php require __DIR__ . '/../partials/head.php'; ?>
</head>

<body>
<?php require __DIR__ . '/../partials/navbar.php'; ?>
    <div class="container cl-page">
        <?php
        $agendamentos = $agendamentos ?? [];

        $mensagem = null;
        $tipoMensagem = null;
        if (isset($_GET['sucesso'])) {
            $sucesso = (string) $_GET['sucesso'];
            if ($sucesso === 'atualizado') {
                $mensagem = 'Agendamento atualizado com sucesso!';
            } elseif ($sucesso === 'cancelado') {
                $mensagem = 'Agendamento cancelado com sucesso!';
            }
            $tipoMensagem = 'success';
        } elseif (isset($_GET['erro'])) {
            $erro = (string) $_GET['erro'];
            if ($erro === 'quantidade') {
                $mensagem = 'Informe uma quantidade de pessoas válida.';
            } elseif ($erro === 'minimo') {
                $mensagem = 'A quantidade de pessoas é menor que o mínimo exigido pela sessão.';
            } elseif ($erro === 'lotacao') {
                $mensagem = 'Não há disponibilidade suficiente para esta quantidade de pessoas.';
            } elseif ($erro === 'agendamento') {
                $mensagem = 'Agendamento não encontrado.';
            } elseif ($erro === 'sessao') {
                $mensagem = 'Sessão não encontrada.';
            } elseif ($erro === 'bloqueado') {
                $mensagem = 'Este agendamento não pode mais ser alterado.';
            } else {
                $mensagem = 'Não foi possível concluir a operação. Tente novamente.';
            }
            $tipoMensagem = 'danger';
        }

        $statusLabels = [
            'aprovado' => ['Confirmado', 'success', 'bi-check-circle'],
            'cancelado' => ['Cancelado', 'danger', 'bi-x-circle']
        ];

        $proximos = array_values(array_filter($agendamentos, fn($item) => !empty($item['pode_alterar'])));
        $anteriores = array_values(array_filter($agendamentos, fn($item) => empty($item['pode_alterar'])));

        $formatarData = function (?string $data): string {
            $data = trim((string) $data);
            $partes = explode('-', $data);

            return count($partes) === 3 ? $partes[2] . '/' . $partes[1] . '/' . $partes[0] : ($data !== '' ? $data : '-');
        };
        ?>

        <div class="cl-page-header">
            <h1 class="mb-1">Meus Agendamentos</h1>
            <p>Gerencie todas as suas atividades agendadas</p>
        </div>

        <?php if (!empty($mensagem)): ?>
            <div class="alert alert-<?= htmlspecialchars($tipoMensagem ?? 'info', ENT_QUOTES, 'UTF-8') ?>" role="alert">
                <?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <section class="mb-5">
            <h2 class="mb-3">Próximas atividades</h2>

            <?php if (!empty($proximos)): ?>
                <div class="d-flex flex-column gap-3">
                    <?php foreach ($proximos as $item): ?>
                        <?php
                        $agendamento = $item['agendamento'];
                        $sessao = $item['sessao'];
                        $agendamentoId = (int) $agendamento->getIdAgendamento();
                        $quantidade = (int) $agendamento->getQuantidadePessoas();
                        $statusLabel = $statusLabels[(string) $agendamento->getStatus()] ?? [ucfirst((string) $agendamento->getStatus()), 'neutral', 'bi-info-circle'];
                        $qtdMinima = max(1, (int) $sessao->getQtdMin());
                        $qtdLivre = (int) $sessao->getQtdDisponivel() + $quantidade;
                        ?>
                        <div class="card cl-card-hover">
                            <div class="card-body p-4">
                                <div class="d-flex flex-column flex-md-row gap-4">
                                    <div class="cl-placeholder-img rounded-3 flex-shrink-0 d-none d-md-flex" style="width: 100%; max-width: 10rem; height: 8rem;">
                                        <i class="bi bi-calendar-event" style="font-size: 40px;"></i>
                                    </div>

                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                            <h3 class="h5 mb-0">
                                                <a href="<?= URL_BASE ?>/atividade/ver?id=<?= (int) $agendamento->getIdAtividade() ?>" class="text-reset text-decoration-none">
                                                    <?= htmlspecialchars($item['atividade_nome'] !== '' ? $item['atividade_nome'] : '-', ENT_QUOTES, 'UTF-8') ?>
                                                </a>
                                            </h3>
                                            <span class="cl-badge cl-badge-<?= $statusLabel[1] ?>">
                                                <i class="bi <?= $statusLabel[2] ?>"></i> <?= htmlspecialchars($statusLabel[0], ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                        </div>

                                        <div class="d-flex flex-wrap gap-4 mb-3">
                                            <span class="cl-meta"><i class="bi bi-calendar3"></i> <?= htmlspecialchars($formatarData($sessao->getData()), ENT_QUOTES, 'UTF-8') ?></span>
                                            <span class="cl-meta"><i class="bi bi-clock"></i> <?= htmlspecialchars(substr((string) ($sessao->getHora() ?? '-'), 0, 5), ENT_QUOTES, 'UTF-8') ?></span>
                                            <span class="cl-meta"><i class="bi bi-people"></i> <?= $quantidade ?> <?= $quantidade > 1 ? 'pessoas' : 'pessoa' ?></span>
                                        </div>

                                        <div class="d-flex flex-wrap justify-content-end gap-2 pt-3 border-top">
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#editar<?= $agendamentoId ?>" aria-expanded="false" aria-controls="editar<?= $agendamentoId ?>">
                                                <i class="bi bi-pencil"></i> Editar
                                            </button>
                                            <form action="<?= URL_BASE ?>/agendamento/cancelar" method="POST" class="m-0" onsubmit="return confirm('Deseja cancelar este agendamento?')">
                                                <input type="hidden" name="agendamento_id" value="<?= $agendamentoId ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-x-circle"></i> Cancelar agendamento
                                                </button>
                                            </form>
                                            <a href="<?= URL_BASE ?>/atividade/ver?id=<?= (int) $agendamento->getIdAtividade() ?>" class="btn btn-sm btn-primary">
                                                Ver atividade
                                            </a>
                                        </div>

                                        <div class="collapse" id="editar<?= $agendamentoId ?>">
                                            <div class="rounded-3 p-3 mt-3" style="background: var(--cl-muted);">
                                                <h4 class="h6 mb-3">Editar agendamento</h4>
                                                <form action="<?= URL_BASE ?>/agendamento/atualizar" method="POST" class="row g-2 align-items-end">
                                                    <input type="hidden" name="agendamento_id" value="<?= $agendamentoId ?>">
                                                    <div class="col-sm-6 col-md-4">
                                                        <label for="quantidade<?= $agendamentoId ?>" class="form-label">Número de pessoas</label>
                                                        <input type="number" id="quantidade<?= $agendamentoId ?>" name="quantidade_pessoas" class="form-control"
                                                            value="<?= $quantidade ?>" min="<?= $qtdMinima ?>" max="<?= $qtdLivre ?>" step="1" required>
                                                    </div>
                                                    <div class="col-sm-6 col-md-8 d-flex gap-2">
                                                        <button type="submit" class="btn btn-primary">Salvar alterações</button>
                                                        <button type="button" class="btn btn-outline-secondary border-0" data-bs-toggle="collapse" data-bs-target="#editar<?= $agendamentoId ?>">Cancelar</button>
                                                    </div>
                                                </form>
                                                <p class="text-muted small mt-3 mb-0">
                                                    * Até <?= $qtdLivre ?> pessoa(s) nesta sessão. Para trocar de sessão, data ou hora, cancele este agendamento e faça uma nova inscrição na página da atividade.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="card text-center py-5">
                    <div class="card-body">
                        <i class="bi bi-calendar3 text-muted" style="font-size: 56px;"></i>
                        <h3 class="h5 mt-3 mb-2">Nenhum agendamento futuro</h3>
                        <p class="text-muted mb-4">Explore atividades incríveis e faça seu próximo agendamento</p>
                        <a href="<?= URL_BASE ?>/" class="btn btn-primary">Explorar atividades</a>
                    </div>
                </div>
            <?php endif; ?>
        </section>

        <?php if (!empty($anteriores)): ?>
            <section>
                <h2 class="mb-3">Atividades anteriores e canceladas</h2>

                <div class="d-flex flex-column gap-3">
                    <?php foreach ($anteriores as $item): ?>
                        <?php
                        $agendamento = $item['agendamento'];
                        $sessao = $item['sessao'];
                        $quantidade = (int) $agendamento->getQuantidadePessoas();
                        $statusLabel = $statusLabels[(string) $agendamento->getStatus()] ?? [ucfirst((string) $agendamento->getStatus()), 'neutral', 'bi-info-circle'];
                        ?>
                        <div class="card">
                            <div class="card-body p-4 d-flex flex-column flex-md-row gap-4 align-items-md-center">
                                <div class="cl-placeholder-img rounded-3 flex-shrink-0 d-none d-md-flex" style="width: 5rem; height: 5rem;">
                                    <i class="bi bi-calendar-event" style="font-size: 28px;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
                                        <h3 class="h6 mb-0">
                                            <a href="<?= URL_BASE ?>/atividade/ver?id=<?= (int) $agendamento->getIdAtividade() ?>" class="text-reset text-decoration-none">
                                                <?= htmlspecialchars($item['atividade_nome'] !== '' ? $item['atividade_nome'] : '-', ENT_QUOTES, 'UTF-8') ?>
                                            </a>
                                        </h3>
                                        <span class="cl-badge cl-badge-<?= $statusLabel[1] ?>">
                                            <i class="bi <?= $statusLabel[2] ?>"></i> <?= htmlspecialchars($statusLabel[0], ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </div>
                                    <div class="d-flex flex-wrap gap-4">
                                        <span class="cl-meta"><i class="bi bi-calendar3"></i> <?= htmlspecialchars($formatarData($sessao->getData()), ENT_QUOTES, 'UTF-8') ?></span>
                                        <span class="cl-meta"><i class="bi bi-clock"></i> <?= htmlspecialchars(substr((string) ($sessao->getHora() ?? '-'), 0, 5), ENT_QUOTES, 'UTF-8') ?></span>
                                        <span class="cl-meta"><i class="bi bi-people"></i> <?= $quantidade ?> <?= $quantidade > 1 ? 'pessoas' : 'pessoa' ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
