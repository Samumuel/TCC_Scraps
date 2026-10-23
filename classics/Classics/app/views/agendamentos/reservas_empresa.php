<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Classics • Reservas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
<?php require __DIR__ . '/../partials/head.php'; ?>
</head>

<body>
<?php require __DIR__ . '/../partials/navbar.php'; ?>
    <div class="container cl-page">
        <?php
        $agendamentos = $agendamentos ?? [];
        $atividades = $atividades ?? [];
        $atividadeSelecionada = (int) ($atividade_selecionada ?? 0);

        $mensagem = null;
        $tipoMensagem = null;
        if (isset($_GET['sucesso']) && (string) $_GET['sucesso'] === 'cancelado') {
            $mensagem = 'Reserva cancelada com sucesso! As vagas foram devolvidas para a sessão.';
            $tipoMensagem = 'success';
        } elseif (isset($_GET['erro'])) {
            $erro = (string) $_GET['erro'];
            if ($erro === 'agendamento') {
                $mensagem = 'Reserva não encontrada.';
            } elseif ($erro === 'sessao') {
                $mensagem = 'Sessão não encontrada.';
            } elseif ($erro === 'bloqueado') {
                $mensagem = 'Esta reserva não pode mais ser cancelada.';
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
        $pessoasConfirmadas = array_sum(array_map(fn($item) => (int) $item['agendamento']->getQuantidadePessoas(), $proximos));

        $formatarData = function (?string $data): string {
            $data = trim((string) $data);
            $partes = explode('-', $data);

            return count($partes) === 3 ? $partes[2] . '/' . $partes[1] . '/' . $partes[0] : ($data !== '' ? $data : '-');
        };
        ?>

        <div class="cl-page-header">
            <h1 class="mb-1">Reservas</h1>
            <p>Visualize e gerencie os agendamentos das suas atividades</p>
        </div>

        <?php if (!empty($mensagem)): ?>
            <div class="alert alert-<?= htmlspecialchars($tipoMensagem ?? 'info', ENT_QUOTES, 'UTF-8') ?>" role="alert">
                <?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body p-4 d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Reservas ativas</p>
                            <p class="h2 mb-0"><?= count($proximos) ?></p>
                        </div>
                        <i class="bi bi-calendar-check text-primary" style="font-size: 40px;"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body p-4 d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Pessoas confirmadas</p>
                            <p class="h2 mb-0"><?= $pessoasConfirmadas ?></p>
                        </div>
                        <i class="bi bi-people text-secondary-brand" style="font-size: 40px;"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body p-4 d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Total de reservas</p>
                            <p class="h2 mb-0"><?= count($agendamentos) ?></p>
                        </div>
                        <i class="bi bi-journal-text" style="font-size: 40px; color: var(--cl-accent);"></i>
                    </div>
                </div>
            </div>
        </div>

        <form action="<?= URL_BASE ?>/agendamento/empresa" method="GET" class="row g-2 align-items-end mb-5">
            <div class="col-sm-8 col-md-5">
                <label for="atividade" class="form-label">Filtrar por atividade</label>
                <select id="atividade" name="atividade" class="form-select">
                    <option value="0">Todas as atividades</option>
                    <?php foreach ($atividades as $atividade): ?>
                        <option value="<?= (int) $atividade->getIdAtividade() ?>" <?= (int) $atividade->getIdAtividade() === $atividadeSelecionada ? 'selected' : '' ?>>
                            <?= htmlspecialchars((string) $atividade->getNome(), ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-sm-4 col-md-3">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="bi bi-funnel"></i> Filtrar
                </button>
            </div>
        </form>

        <section class="mb-5">
            <h2 class="mb-3">Próximas reservas</h2>

            <?php if (!empty($proximos)): ?>
                <div class="d-flex flex-column gap-3">
                    <?php foreach ($proximos as $item): ?>
                        <?php
                        $agendamento = $item['agendamento'];
                        $sessao = $item['sessao'];
                        $quantidade = (int) $agendamento->getQuantidadePessoas();
                        $statusLabel = $statusLabels[(string) $agendamento->getStatus()] ?? [ucfirst((string) $agendamento->getStatus()), 'neutral', 'bi-info-circle'];
                        ?>
                        <div class="card">
                            <div class="card-body p-4">
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

                                <div class="row g-3 pb-3 mb-3 border-bottom">
                                    <div class="col-12 col-md-4">
                                        <div class="cl-info-tile">
                                            <i class="bi bi-person"></i>
                                            <div>
                                                <small>Cliente</small>
                                                <?= htmlspecialchars($item['cliente_nome'] !== '' ? $item['cliente_nome'] : '-', ENT_QUOTES, 'UTF-8') ?>
                                                <?php if ($item['cliente_email'] !== ''): ?>
                                                    <small><?= htmlspecialchars($item['cliente_email'], ENT_QUOTES, 'UTF-8') ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="cl-info-tile">
                                            <i class="bi bi-calendar3"></i>
                                            <div><small>Data</small><?= htmlspecialchars($formatarData($sessao->getData()), ENT_QUOTES, 'UTF-8') ?></div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-2">
                                        <div class="cl-info-tile">
                                            <i class="bi bi-clock"></i>
                                            <div><small>Horário</small><?= htmlspecialchars(substr((string) ($sessao->getHora() ?? '-'), 0, 5), ENT_QUOTES, 'UTF-8') ?></div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="cl-info-tile">
                                            <i class="bi bi-people"></i>
                                            <div><small>Participantes</small><?= $quantidade ?> <?= $quantidade > 1 ? 'pessoas' : 'pessoa' ?></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <form action="<?= URL_BASE ?>/agendamento/empresa/cancelar" method="POST" class="m-0" onsubmit="return confirm('Deseja cancelar esta reserva? As vagas serão devolvidas para a sessão.')">
                                        <input type="hidden" name="agendamento_id" value="<?= (int) $agendamento->getIdAgendamento() ?>">
                                        <input type="hidden" name="atividade" value="<?= $atividadeSelecionada ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-x-circle"></i> Cancelar reserva
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="card text-center py-5">
                    <div class="card-body">
                        <i class="bi bi-calendar3 text-muted" style="font-size: 56px;"></i>
                        <h3 class="h5 mt-3 mb-2">Nenhuma reserva futura</h3>
                        <p class="text-muted mb-0">Quando clientes agendarem suas atividades, as reservas aparecerão aqui.</p>
                    </div>
                </div>
            <?php endif; ?>
        </section>

        <?php if (!empty($anteriores)): ?>
            <section>
                <h2 class="mb-3">Reservas anteriores e canceladas</h2>

                <div class="card">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3">Atividade</th>
                                    <th class="px-4 py-3">Cliente</th>
                                    <th class="px-4 py-3">Data</th>
                                    <th class="px-4 py-3">Horário</th>
                                    <th class="px-4 py-3">Pessoas</th>
                                    <th class="px-4 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($anteriores as $item): ?>
                                    <?php
                                    $agendamento = $item['agendamento'];
                                    $sessao = $item['sessao'];
                                    $statusLabel = $statusLabels[(string) $agendamento->getStatus()] ?? [ucfirst((string) $agendamento->getStatus()), 'neutral', 'bi-info-circle'];
                                    ?>
                                    <tr>
                                        <td class="px-4 py-3"><?= htmlspecialchars($item['atividade_nome'] !== '' ? $item['atividade_nome'] : '-', ENT_QUOTES, 'UTF-8') ?></td>
                                        <td class="px-4 py-3"><?= htmlspecialchars($item['cliente_nome'] !== '' ? $item['cliente_nome'] : '-', ENT_QUOTES, 'UTF-8') ?></td>
                                        <td class="px-4 py-3"><?= htmlspecialchars($formatarData($sessao->getData()), ENT_QUOTES, 'UTF-8') ?></td>
                                        <td class="px-4 py-3"><?= htmlspecialchars(substr((string) ($sessao->getHora() ?? '-'), 0, 5), ENT_QUOTES, 'UTF-8') ?></td>
                                        <td class="px-4 py-3"><?= (int) $agendamento->getQuantidadePessoas() ?></td>
                                        <td class="px-4 py-3">
                                            <span class="cl-badge cl-badge-<?= $statusLabel[1] ?>">
                                                <i class="bi <?= $statusLabel[2] ?>"></i> <?= htmlspecialchars($statusLabel[0], ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
