<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Classics • Detalhes da Atividade</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
<?php require __DIR__ . '/../partials/head.php'; ?>
</head>

<body>
<?php require __DIR__ . '/../partials/navbar.php'; ?>
    <div class="container cl-page">
        <?php
        $atividade = $atividade ?? [];
        $sessoes = $sessoes ?? [];
        $abaAtiva = $aba_ativa ?? 'dados';
        $usuarioPodeSeInscrever = isset($usuario_logado) && is_object($usuario_logado) && method_exists($usuario_logado, 'getTipoUsuario') && !isset($empresa_logada) && in_array(strtolower(trim((string) $usuario_logado->getTipoUsuario())), ['user', 'cliente'], true);

        $mensagemInscricao = null;
        $tipoMensagemInscricao = null;
        if (isset($_GET['sucesso_inscricao']) && (string) $_GET['sucesso_inscricao'] === '1') {
            $mensagemInscricao = 'Inscrição realizada com sucesso!';
            $tipoMensagemInscricao = 'success';
        } elseif (isset($_GET['erro_inscricao'])) {
            $erroInscricao = (string) $_GET['erro_inscricao'];
            if ($erroInscricao === 'acesso') {
                $mensagemInscricao = 'Faça login como cliente para se inscrever em uma sessão.';
            } elseif ($erroInscricao === 'quantidade') {
                $mensagemInscricao = 'Informe uma quantidade de pessoas válida.';
            } elseif ($erroInscricao === 'sessao') {
                $mensagemInscricao = 'Sessão não encontrada.';
            } elseif ($erroInscricao === 'lotacao') {
                $mensagemInscricao = 'Não há disponibilidade suficiente para esta inscrição.';
            } else {
                $mensagemInscricao = 'Não foi possível realizar a inscrição. Tente novamente.';
            }
            $tipoMensagemInscricao = 'danger';
        }
        ?>

        <?php if (!empty($atividade)): ?>
            <a href="<?= URL_BASE ?>/" class="cl-back-link">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>

            <?php if (!empty($mensagemInscricao)): ?>
                <div class="alert alert-<?= htmlspecialchars($tipoMensagemInscricao ?? 'info', ENT_QUOTES, 'UTF-8') ?>" role="alert">
                    <?= htmlspecialchars($mensagemInscricao, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <div class="row g-4 g-lg-5">
                <div class="col-lg-8">
                    <?php if (!empty($atividade['Categoria_Nome'])): ?>
                        <span class="cl-badge cl-badge-category mb-3"><?= htmlspecialchars($atividade['Categoria_Nome'], ENT_QUOTES, 'UTF-8') ?></span>
                    <?php endif; ?>

                    <h1 class="mb-3"><?= htmlspecialchars($atividade['Nome'] ?? '-', ENT_QUOTES, 'UTF-8') ?></h1>

                    <div class="cl-meta mb-4">
                        <i class="bi bi-geo-alt"></i>
                        <span><?= htmlspecialchars($atividade['Localizacao'] ?? '-', ENT_QUOTES, 'UTF-8') ?></span>
                    </div>

                    <?php if (!empty($atividade['Fotos'])): ?>
                        <img src="<?= htmlspecialchars(URL_BASE . '/' . ltrim($atividade['Fotos'], '/'), ENT_QUOTES, 'UTF-8') ?>" alt="Foto da atividade" class="w-100 rounded-4 mb-5" style="max-height: 420px; object-fit: cover;">
                    <?php else: ?>
                        <div class="cl-placeholder-img rounded-4 mb-5" style="height: 320px;">
                            <i class="bi bi-image" style="font-size: 80px;" aria-label="Sem foto"></i>
                        </div>
                    <?php endif; ?>

                    <h2 class="mb-3">Sobre esta experiência</h2>
                    <p class="text-muted mb-4" style="line-height: 1.7;"><?= nl2br(htmlspecialchars($atividade['Descricao'] ?? '-', ENT_QUOTES, 'UTF-8')) ?></p>

                    <div class="row g-4 mb-5">
                        <div class="col-sm-6 col-md-4">
                            <div class="cl-info-tile">
                                <i class="bi bi-clock"></i>
                                <div>
                                    <small>Duração</small>
                                    <?= htmlspecialchars($atividade['Duracao'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="cl-info-tile">
                                <i class="bi bi-mailbox"></i>
                                <div>
                                    <small>CEP</small>
                                    <?= htmlspecialchars($atividade['Localizacao_CEP'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="cl-info-tile">
                                <i class="bi bi-signpost"></i>
                                <div>
                                    <small>Complemento</small>
                                    <?= htmlspecialchars($atividade['Localizacao_Complemento'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <section id="sessoes">
                        <h2 class="mb-3">Sessões disponíveis</h2>

                        <?php if (empty($sessoes)): ?>
                            <div class="alert alert-info mb-0" role="alert">
                                Esta atividade ainda não possui sessões cadastradas.
                            </div>
                        <?php else: ?>
                            <?php if (!$usuarioPodeSeInscrever): ?>
                                <div class="alert alert-info mb-3" role="alert">
                                    Apenas clientes autenticados podem se inscrever em sessões.
                                </div>
                            <?php endif; ?>
                            <div class="card">
                                <div class="table-responsive">
                                    <table class="table align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th class="px-4 py-3">Data</th>
                                                <th class="px-4 py-3">Hora inicial</th>
                                                <th class="px-4 py-3">Mín.</th>
                                                <th class="px-4 py-3">Máx.</th>
                                                <th class="px-4 py-3">Vagas</th>
                                                <th class="px-4 py-3 text-end">Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($sessoes as $sessao): ?>
                                                <?php
                                                $sessaoId = (int) ($sessao['ID_Sessao'] ?? 0);
                                                $dataSessao = (string) ($sessao['Data'] ?? '-');
                                                $horaSessao = substr((string) ($sessao['Hora'] ?? '-'), 0, 5);
                                                $qtdDisponivel = (int) ($sessao['Qtd_disponivel'] ?? 0);
                                                ?>
                                                <tr>
                                                    <td class="px-4 py-3"><?= htmlspecialchars($dataSessao, ENT_QUOTES, 'UTF-8') ?></td>
                                                    <td class="px-4 py-3"><?= htmlspecialchars($horaSessao, ENT_QUOTES, 'UTF-8') ?></td>
                                                    <td class="px-4 py-3"><?= (int) ($sessao['Qtd_min'] ?? 0) ?></td>
                                                    <td class="px-4 py-3"><?= (int) ($sessao['Qtd_max'] ?? 0) ?></td>
                                                    <td class="px-4 py-3 <?= $qtdDisponivel > 0 && $qtdDisponivel < 5 ? 'text-danger fw-semibold' : '' ?>"><?= $qtdDisponivel ?></td>
                                                    <td class="px-4 py-3 text-end">
                                                        <?php if ($usuarioPodeSeInscrever): ?>
                                                            <?php if ($qtdDisponivel > 0): ?>
                                                                <button type="button"
                                                                    class="btn btn-sm btn-primary"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#inscricaoModal"
                                                                    data-sessao-id="<?= $sessaoId ?>"
                                                                    data-sessao-data="<?= htmlspecialchars($dataSessao, ENT_QUOTES, 'UTF-8') ?>"
                                                                    data-sessao-hora="<?= htmlspecialchars($horaSessao, ENT_QUOTES, 'UTF-8') ?>">
                                                                    Reservar
                                                                </button>
                                                            <?php else: ?>
                                                                <span class="cl-badge cl-badge-neutral">Esgotada</span>
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            <a href="<?= URL_BASE ?>/login" class="small fw-semibold">Entre para reservar</a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>
                    </section>
                </div>

                <div class="col-lg-4">
                    <div class="card cl-sticky-sidebar">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-baseline gap-2 mb-3">
                                <span class="cl-price fs-2">R$ <?= number_format((float) ($atividade['Valor'] ?? 0), 2, ',', '.') ?></span>
                                <span class="text-muted">por pessoa</span>
                            </div>
                            <a href="#sessoes" class="btn btn-primary w-100 mb-3">
                                <i class="bi bi-calendar-check"></i> Escolher sessão
                            </a>
                            <p class="text-muted small text-center mb-0">
                                Escolha uma sessão na lista e informe a quantidade de pessoas.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="inscricaoModal" tabindex="-1" aria-labelledby="inscricaoModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="inscricaoModalLabel">Inscrever-se na sessão</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                        </div>
                        <form action="<?= URL_BASE ?>/agendamento/salvar" method="POST">
                            <div class="modal-body">
                                <input type="hidden" name="atividade_id" value="<?= (int) ($atividade['ID_Atividade'] ?? 0) ?>">
                                <input type="hidden" name="sessao_id" id="inscricaoSessaoId" value="">

                                <div class="mb-3">
                                    <label class="form-label">Sessão selecionada</label>
                                    <div class="form-control-plaintext fw-semibold" id="inscricaoSessaoDescricao">-</div>
                                </div>

                                <div class="mb-3">
                                    <label for="quantidade_pessoas" class="form-label">Quantidade de pessoas</label>
                                    <input type="number" class="form-control" id="quantidade_pessoas" name="quantidade_pessoas" min="1" required>
                                    <div class="form-text">Informe a quantidade total de participantes dessa inscrição.</div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> Confirmar inscrição
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i> Atividade não encontrada.
                <div class="mt-3">
                    <a href="<?= URL_BASE ?>/" class="btn btn-primary">Voltar para Atividades</a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const inscricaoModal = document.getElementById('inscricaoModal');
        if (inscricaoModal) {
            inscricaoModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                if (!button) {
                    return;
                }

                const sessaoId = button.getAttribute('data-sessao-id') || '';
                const dataSessao = button.getAttribute('data-sessao-data') || '-';
                const horaSessao = button.getAttribute('data-sessao-hora') || '-';

                const campoSessaoId = document.getElementById('inscricaoSessaoId');
                const campoDescricao = document.getElementById('inscricaoSessaoDescricao');

                if (campoSessaoId) {
                    campoSessaoId.value = sessaoId;
                }

                if (campoDescricao) {
                    campoDescricao.textContent = `${dataSessao} às ${horaSessao}`;
                }
            });
        }
        if (<?= json_encode($abaAtiva === 'sessoes') ?>) {
            const secaoSessoes = document.getElementById('sessoes');
            if (secaoSessoes) {
                secaoSessoes.scrollIntoView();
            }
        }
    </script>
</body>

</html>
