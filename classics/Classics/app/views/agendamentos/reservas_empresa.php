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
        $mensagensSucesso = [
            'cancelado' => 'Reserva cancelada com sucesso! As vagas foram devolvidas para a sessão.',
            'aprovado' => 'Reserva aceita! O agendamento do cliente está confirmado.',
            'recusado' => 'Reserva recusada. As vagas foram devolvidas para a sessão.'
        ];
        if (isset($_GET['sucesso']) && isset($mensagensSucesso[(string) $_GET['sucesso']])) {
            $mensagem = $mensagensSucesso[(string) $_GET['sucesso']];
            $tipoMensagem = 'success';
        } elseif (isset($_GET['erro'])) {
            $erro = (string) $_GET['erro'];
            if ($erro === 'agendamento') {
                $mensagem = 'Reserva não encontrada.';
            } elseif ($erro === 'sessao') {
                $mensagem = 'Sessão não encontrada.';
            } elseif ($erro === 'bloqueado') {
                $mensagem = 'Esta reserva não pode mais ser alterada.';
            } elseif ($erro === 'status') {
                $mensagem = 'Esta reserva não está mais aguardando aprovação.';
            } else {
                $mensagem = 'Não foi possível concluir a operação. Tente novamente.';
            }
            $tipoMensagem = 'danger';
        }

        $statusLabels = [
            'pendente' => ['Aguardando aprovação', 'warning', 'bi-hourglass-split'],
            'aprovado' => ['Confirmado', 'success', 'bi-check-circle'],
            'cancelado' => ['Cancelado', 'danger', 'bi-x-circle'],
            'recusado' => ['Recusado', 'danger', 'bi-slash-circle']
        ];

        $pendentes = array_values(array_filter($agendamentos, fn($item) => !empty($item['pode_alterar']) && $item['agendamento']->getStatus() === 'pendente'));
        $proximos = array_values(array_filter($agendamentos, fn($item) => !empty($item['pode_alterar']) && $item['agendamento']->getStatus() !== 'pendente'));
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
            <div class="col-6 col-lg-3">
                <div class="card h-100">
                    <div class="card-body p-4 d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Aguardando aprovação</p>
                            <p class="h2 mb-0"><?= count($pendentes) ?></p>
                        </div>
                        <i class="bi bi-hourglass-split" style="font-size: 40px; color: var(--cl-warning);"></i>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card h-100">
                    <div class="card-body p-4 d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Reservas confirmadas</p>
                            <p class="h2 mb-0"><?= count($proximos) ?></p>
                        </div>
                        <i class="bi bi-calendar-check text-primary" style="font-size: 40px;"></i>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
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
            <div class="col-6 col-lg-3">
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
            <h2 class="mb-3">Aguardando aprovação</h2>

            <?php if (!empty($pendentes)): ?>
                <div class="d-flex flex-column gap-3">
                    <?php foreach ($pendentes as $item): ?>
                        <?php $acoesCard = 'pendente'; ?>
                        <?php require __DIR__ . '/_reserva_card.php'; ?>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="card text-center py-4">
                    <div class="card-body">
                        <i class="bi bi-hourglass text-muted" style="font-size: 40px;"></i>
                        <p class="text-muted mt-2 mb-0">Nenhuma reserva aguardando aprovação.</p>
                    </div>
                </div>
            <?php endif; ?>
        </section>

        <section class="mb-5">
            <h2 class="mb-3">Próximas reservas confirmadas</h2>

            <?php if (!empty($proximos)): ?>
                <div class="d-flex flex-column gap-3">
                    <?php foreach ($proximos as $item): ?>
                        <?php $acoesCard = 'confirmada'; ?>
                        <?php require __DIR__ . '/_reserva_card.php'; ?>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="card text-center py-5">
                    <div class="card-body">
                        <i class="bi bi-calendar3 text-muted" style="font-size: 56px;"></i>
                        <h3 class="h5 mt-3 mb-2">Nenhuma reserva confirmada</h3>
                        <p class="text-muted mb-0">As reservas que você aceitar aparecerão aqui.</p>
                    </div>
                </div>
            <?php endif; ?>
        </section>

        <?php if (!empty($anteriores)): ?>
            <section>
                <h2 class="mb-3">Reservas anteriores, canceladas e recusadas</h2>

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
                                    $statusLabel = $agendamento->getStatus() === 'pendente'
                                        ? ['Não confirmado', 'neutral', 'bi-clock-history']
                                        : ($statusLabels[(string) $agendamento->getStatus()] ?? [ucfirst((string) $agendamento->getStatus()), 'neutral', 'bi-info-circle']);
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
