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

            <div class="d-flex flex-wrap justify-content-end gap-2">
                <?php if ($acoesCard === 'pendente'): ?>
                    <form action="<?= URL_BASE ?>/agendamento/empresa/recusar" method="POST" class="m-0" onsubmit="return confirm('Deseja recusar esta reserva? As vagas serão devolvidas para a sessão.')">
                        <input type="hidden" name="agendamento_id" value="<?= (int) $agendamento->getIdAgendamento() ?>">
                        <input type="hidden" name="atividade" value="<?= $atividadeSelecionada ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-slash-circle"></i> Recusar
                        </button>
                    </form>
                    <form action="<?= URL_BASE ?>/agendamento/empresa/aprovar" method="POST" class="m-0">
                        <input type="hidden" name="agendamento_id" value="<?= (int) $agendamento->getIdAgendamento() ?>">
                        <input type="hidden" name="atividade" value="<?= $atividadeSelecionada ?>">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="bi bi-check-circle"></i> Aceitar
                        </button>
                    </form>
                <?php else: ?>
                    <form action="<?= URL_BASE ?>/agendamento/empresa/cancelar" method="POST" class="m-0" onsubmit="return confirm('Deseja cancelar esta reserva? As vagas serão devolvidas para a sessão.')">
                        <input type="hidden" name="agendamento_id" value="<?= (int) $agendamento->getIdAgendamento() ?>">
                        <input type="hidden" name="atividade" value="<?= $atividadeSelecionada ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-x-circle"></i> Cancelar reserva
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
