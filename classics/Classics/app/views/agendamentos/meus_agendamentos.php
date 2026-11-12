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
            } elseif ($sucesso === 'atualizado_pendente') {
                $mensagem = 'Agendamento atualizado! Como a quantidade de pessoas aumentou, ele voltou a aguardar a aprovação da empresa.';
            } elseif ($sucesso === 'cancelado') {
                $mensagem = 'Agendamento cancelado com sucesso!';
            } elseif ($sucesso === 'sessao_trocada') {
                $mensagem = 'Sessão trocada! O agendamento voltou a aguardar a aprovação da empresa.';
            } elseif ($sucesso === 'avaliado') {
                $mensagem = 'Avaliação salva! Obrigado pela sua opinião.';
            }
            $tipoMensagem = 'success';
        } elseif (isset($_GET['erro'])) {
            $erro = (string) $_GET['erro'];
            if ($erro === 'quantidade') {
                $mensagem = 'Informe uma quantidade de pessoas válida.';
            } elseif ($erro === 'minimo') {
                $mensagem = 'A quantidade de pessoas é menor que o mínimo exigido pela sessão.';
            } elseif ($erro === 'lotacao') {
                // FE01: informa quantas vagas restam para esta reserva
                $mensagem = isset($_GET['vagas'])
                    ? 'Não há vagas suficientes. Vagas disponíveis nesta sessão para a sua reserva: ' . max(0, (int) $_GET['vagas']) . '.'
                    : 'Não há vagas suficientes nesta sessão.';
            } elseif ($erro === 'mesma_sessao') {
                $mensagem = 'Escolha uma sessão diferente da atual.';
            } elseif ($erro === 'encerrada') {
                $mensagem = 'A sessão escolhida não está mais disponível.';
            } elseif ($erro === 'indisponivel') {
                $mensagem = 'Esta atividade não está recebendo agendamentos no momento.';
            } elseif ($erro === 'agendamento') {
                $mensagem = 'Agendamento não encontrado.';
            } elseif ($erro === 'sessao') {
                $mensagem = 'Sessão não encontrada.';
            } elseif ($erro === 'bloqueado') {
                $mensagem = 'Este agendamento não pode mais ser alterado.';
            } elseif ($erro === 'nota') {
                $mensagem = 'Escolha uma nota de 1 a 5 estrelas.';
            } elseif ($erro === 'comentario') {
                $mensagem = 'O comentário deve ter no máximo 500 caracteres.';
            } elseif ($erro === 'nao_permitido') {
                $mensagem = 'Só é possível avaliar atividades das quais você participou.';
            } else {
                $mensagem = 'Não foi possível concluir a operação. Tente novamente.';
            }
            $tipoMensagem = 'danger';
        }

        $statusLabels = \app\helpers\Formatador::STATUS_AGENDAMENTO;

        $proximos = array_values(array_filter($agendamentos, fn($item) => !empty($item['pode_alterar'])));
        $anteriores = array_values(array_filter($agendamentos, fn($item) => empty($item['pode_alterar'])));

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
                                    <?php if ($item['atividade_foto'] !== ''): ?>
                                        <img src="<?= htmlspecialchars(URL_BASE . '/' . ltrim($item['atividade_foto'], '/'), ENT_QUOTES, 'UTF-8') ?>" alt="Foto da atividade" class="rounded-3 flex-shrink-0 d-none d-md-block" style="width: 10rem; height: 8rem; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="cl-placeholder-img rounded-3 flex-shrink-0 d-none d-md-flex" style="width: 100%; max-width: 10rem; height: 8rem;">
                                            <i class="bi bi-calendar-event" style="font-size: 40px;"></i>
                                        </div>
                                    <?php endif; ?>

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
                                            <span class="cl-meta"><i class="bi bi-calendar3"></i> <?= htmlspecialchars(\app\helpers\Formatador::data($sessao->getData()), ENT_QUOTES, 'UTF-8') ?></span>
                                            <span class="cl-meta"><i class="bi bi-clock"></i> <?= htmlspecialchars(substr((string) ($sessao->getHora() ?? '-'), 0, 5), ENT_QUOTES, 'UTF-8') ?></span>
                                            <span class="cl-meta"><i class="bi bi-people"></i> <?= $quantidade ?> <?= $quantidade > 1 ? 'pessoas' : 'pessoa' ?></span>
                                        </div>

                                        <div class="d-flex flex-wrap justify-content-end gap-2 pt-3 border-top">
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#editar<?= $agendamentoId ?>" aria-expanded="false" aria-controls="editar<?= $agendamentoId ?>">
                                                <i class="bi bi-pencil"></i> Editar
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#trocar<?= $agendamentoId ?>" aria-expanded="false" aria-controls="trocar<?= $agendamentoId ?>">
                                                <i class="bi bi-arrow-left-right"></i> Trocar sessão
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
                                                    * Até <?= $qtdLivre ?> pessoa(s) nesta sessão.<?= $agendamento->getStatus() === 'aprovado' ? ' Se aumentar a quantidade, o agendamento voltará a aguardar a aprovação da empresa.' : '' ?> Para mudar a data ou o horário, use "Trocar sessão".
                                                </p>
                                            </div>
                                        </div>

                                        <!-- UC07 FA02: trocar a sessão (data e horário) da reserva -->
                                        <div class="collapse" id="trocar<?= $agendamentoId ?>">
                                            <div class="rounded-3 p-3 mt-3" style="background: var(--cl-muted);">
                                                <h4 class="h6 mb-3">Trocar sessão</h4>
                                                <?php if (empty($item['sessoes_troca'])): ?>
                                                    <p class="text-muted small mb-0">Não há outra sessão desta atividade disponível para <?= $quantidade ?> <?= $quantidade > 1 ? 'pessoas' : 'pessoa' ?> no momento (com vagas suficientes e dentro do mínimo e máximo de participantes).</p>
                                                <?php else: ?>
                                                    <form action="<?= URL_BASE ?>/agendamento/trocar-sessao" method="POST" class="row g-2 align-items-end"
                                                        onsubmit="return confirm('Trocar a sessão desta reserva? Ela voltará a aguardar a aprovação da empresa.')">
                                                        <input type="hidden" name="agendamento_id" value="<?= $agendamentoId ?>">
                                                        <div class="col-md-8">
                                                            <label for="sessao<?= $agendamentoId ?>" class="form-label">Nova sessão</label>
                                                            <select id="sessao<?= $agendamentoId ?>" name="sessao_id" class="form-select" required>
                                                                <option value="">Escolha uma sessão</option>
                                                                <?php foreach ($item['sessoes_troca'] as $opcaoSessao): ?>
                                                                    <option value="<?= (int) $opcaoSessao['ID_Sessao'] ?>">
                                                                        <?= htmlspecialchars(\app\helpers\Formatador::data($opcaoSessao['Data'] ?? '') . ' às ' . substr((string) ($opcaoSessao['Hora'] ?? ''), 0, 5), ENT_QUOTES, 'UTF-8') ?>
                                                                        (<?= (int) $opcaoSessao['Qtd_disponivel'] ?> vagas)
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4 d-flex gap-2">
                                                            <button type="submit" class="btn btn-primary">Trocar</button>
                                                            <button type="button" class="btn btn-outline-secondary border-0" data-bs-toggle="collapse" data-bs-target="#trocar<?= $agendamentoId ?>">Cancelar</button>
                                                        </div>
                                                    </form>
                                                    <p class="text-muted small mt-3 mb-0">* Só aparecem sessões futuras com vagas e limites de participantes que comportam <?= $quantidade ?> <?= $quantidade > 1 ? 'pessoas' : 'pessoa' ?>. Após a troca, a reserva volta a aguardar a aprovação da empresa.</p>
                                                <?php endif; ?>
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
                <h2 class="mb-3">Atividades anteriores, canceladas e recusadas</h2>

                <div class="d-flex flex-column gap-3">
                    <?php foreach ($anteriores as $item): ?>
                        <?php
                        $agendamento = $item['agendamento'];
                        $sessao = $item['sessao'];
                        $quantidade = (int) $agendamento->getQuantidadePessoas();
                        $statusLabel = $agendamento->getStatus() === 'pendente'
                            ? ['Não confirmado', 'neutral', 'bi-clock-history']
                            : ($statusLabels[(string) $agendamento->getStatus()] ?? [ucfirst((string) $agendamento->getStatus()), 'neutral', 'bi-info-circle']);
                        ?>
                        <div class="card">
                            <div class="card-body p-4 d-flex flex-column flex-md-row gap-4 align-items-md-center">
                                <?php if ($item['atividade_foto'] !== ''): ?>
                                    <img src="<?= htmlspecialchars(URL_BASE . '/' . ltrim($item['atividade_foto'], '/'), ENT_QUOTES, 'UTF-8') ?>" alt="Foto da atividade" class="rounded-3 flex-shrink-0 d-none d-md-block" style="width: 5rem; height: 5rem; object-fit: cover;">
                                <?php else: ?>
                                    <div class="cl-placeholder-img rounded-3 flex-shrink-0 d-none d-md-flex" style="width: 5rem; height: 5rem;">
                                        <i class="bi bi-calendar-event" style="font-size: 28px;"></i>
                                    </div>
                                <?php endif; ?>
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
                                        <span class="cl-meta"><i class="bi bi-calendar3"></i> <?= htmlspecialchars(\app\helpers\Formatador::data($sessao->getData()), ENT_QUOTES, 'UTF-8') ?></span>
                                        <span class="cl-meta"><i class="bi bi-clock"></i> <?= htmlspecialchars(substr((string) ($sessao->getHora() ?? '-'), 0, 5), ENT_QUOTES, 'UTF-8') ?></span>
                                        <span class="cl-meta"><i class="bi bi-people"></i> <?= $quantidade ?> <?= $quantidade > 1 ? 'pessoas' : 'pessoa' ?></span>
                                    </div>

                                    <?php if (!empty($item['pode_avaliar'])): ?>
                                        <?php $avaliacaoFeita = $item['avaliacao']; ?>
                                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pt-3 mt-3 border-top">
                                            <?php if ($avaliacaoFeita): ?>
                                                <span class="d-inline-flex align-items-center gap-2 small text-muted">
                                                    Sua avaliação:
                                                    <?php $notaEstrelas = (int) $avaliacaoFeita->getNota(); require __DIR__ . '/../partials/estrelas.php'; ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="small text-muted">Como foi esta atividade?</span>
                                            <?php endif; ?>
                                            <button type="button" class="btn btn-sm <?= $avaliacaoFeita ? 'btn-outline-primary' : 'btn-primary' ?>"
                                                data-bs-toggle="modal" data-bs-target="#avaliacaoModal"
                                                data-atividade-id="<?= (int) $agendamento->getIdAtividade() ?>"
                                                data-atividade-nome="<?= htmlspecialchars($item['atividade_nome'], ENT_QUOTES, 'UTF-8') ?>"
                                                data-nota="<?= $avaliacaoFeita ? (int) $avaliacaoFeita->getNota() : 0 ?>"
                                                data-comentario="<?= $avaliacaoFeita ? htmlspecialchars((string) $avaliacaoFeita->getComentario(), ENT_QUOTES, 'UTF-8') : '' ?>">
                                                <i class="bi <?= $avaliacaoFeita ? 'bi-pencil' : 'bi-star' ?>"></i> <?= $avaliacaoFeita ? 'Editar avaliação' : 'Avaliar' ?>
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    </div>

    <div class="modal fade" id="avaliacaoModal" tabindex="-1" aria-labelledby="avaliacaoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="avaliacaoModalLabel">Avaliar atividade</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <form action="<?= URL_BASE ?>/avaliacao/salvar" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="atividade_id" id="avaliacaoAtividadeId" value="">
                        <p class="fw-semibold mb-3" id="avaliacaoAtividadeNome">-</p>

                        <fieldset class="mb-3">
                            <legend class="form-label fs-6">Sua nota</legend>
                            <div class="cl-estrelas-input">
                                <?php for ($nota = 5; $nota >= 1; $nota--): ?>
                                    <input type="radio" name="nota" id="nota<?= $nota ?>" value="<?= $nota ?>" required>
                                    <label for="nota<?= $nota ?>" title="<?= $nota ?> <?= $nota > 1 ? 'estrelas' : 'estrela' ?>"><i class="bi bi-star-fill"></i></label>
                                <?php endfor; ?>
                            </div>
                        </fieldset>

                        <div class="mb-1">
                            <label for="avaliacaoComentario" class="form-label">Comentário <span class="text-muted fw-normal">(opcional)</span></label>
                            <textarea class="form-control" id="avaliacaoComentario" name="comentario" rows="4" maxlength="500" placeholder="Conte como foi a sua experiência"></textarea>
                            <div class="form-text">Até 500 caracteres.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Salvar avaliação
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Preenche a janela de avaliação com a atividade escolhida (e a avaliação anterior, se houver)
        const avaliacaoModal = document.getElementById('avaliacaoModal');
        if (avaliacaoModal) {
            avaliacaoModal.addEventListener('show.bs.modal', function(event) {
                const botao = event.relatedTarget;
                if (!botao) {
                    return;
                }

                const nota = botao.getAttribute('data-nota') || '0';
                document.getElementById('avaliacaoAtividadeId').value = botao.getAttribute('data-atividade-id') || '';
                document.getElementById('avaliacaoAtividadeNome').textContent = botao.getAttribute('data-atividade-nome') || '-';
                document.getElementById('avaliacaoComentario').value = botao.getAttribute('data-comentario') || '';
                document.querySelectorAll('#avaliacaoModal input[name="nota"]').forEach(function(campo) {
                    campo.checked = campo.value === nota;
                });
            });
        }
    </script>
</body>

</html>
