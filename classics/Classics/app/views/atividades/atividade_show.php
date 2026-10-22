<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Projeto Integrador • Detalhes da Atividade</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <div class="container py-5">
        <?php
        $atividade = $atividade ?? [];
        $sessoes = $sessoes ?? [];
        $abaAtiva = $aba_ativa ?? 'dados';
        $ativaDados = $abaAtiva === 'dados' ? 'active' : '';
        $ativaSessoes = $abaAtiva === 'sessoes' ? 'active' : '';
        $paneDados = $abaAtiva === 'dados' ? 'show active' : '';
        $paneSessoes = $abaAtiva === 'sessoes' ? 'show active' : '';
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
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Detalhes da Atividade</h2>
                <a href="<?= URL_BASE ?>/atividade/listar" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Voltar para Lista
                </a>
            </div>

            <?php if (!empty($mensagemInscricao)): ?>
                <div class="alert alert-<?= htmlspecialchars($tipoMensagemInscricao ?? 'info', ENT_QUOTES, 'UTF-8') ?> shadow-sm" role="alert">
                    <?= htmlspecialchars($mensagemInscricao, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <ul class="nav nav-tabs mb-4" id="atividadeShowTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?= $ativaDados ?>" id="dados-tab" data-bs-toggle="tab" data-bs-target="#dados-pane" type="button" role="tab" aria-controls="dados-pane" aria-selected="<?= $abaAtiva === 'dados' ? 'true' : 'false' ?>">
                                Dados da atividade
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?= $ativaSessoes ?>" id="sessoes-tab" data-bs-toggle="tab" data-bs-target="#sessoes-pane" type="button" role="tab" aria-controls="sessoes-pane" aria-selected="<?= $abaAtiva === 'sessoes' ? 'true' : 'false' ?>">
                                Sessões
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="atividadeShowTabsContent">
                        <div class="tab-pane fade <?= $paneDados ?>" id="dados-pane" role="tabpanel" aria-labelledby="dados-tab" tabindex="0">
                            <div class="row g-4">
                                <div class="col-md-4 text-center">
                                    <?php if (!empty($atividade['Fotos'])): ?>
                                        <img src="<?= htmlspecialchars(URL_BASE . '/' . ltrim($atividade['Fotos'], '/'), ENT_QUOTES, 'UTF-8') ?>" alt="Foto da atividade" class="img-fluid rounded" style="max-height: 280px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 280px;">
                                            <i class="bi bi-image text-muted" style="font-size: 80px;" aria-label="Sem foto"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="col-md-8">
                                    <h3 class="card-title mb-3"><?= htmlspecialchars($atividade['Nome'] ?? '-', ENT_QUOTES, 'UTF-8') ?></h3>
                                    <p class="mb-2"><strong>Descrição:</strong> <?= htmlspecialchars($atividade['Descricao'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                                    <p class="mb-2"><strong>Categoria:</strong> <?= htmlspecialchars($atividade['Categoria_Nome'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                                    <p class="mb-2"><strong>Duração:</strong> <?= htmlspecialchars($atividade['Duracao'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                                    <p class="mb-2"><strong>Localização:</strong> <?= htmlspecialchars($atividade['Localizacao'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                                    <p class="mb-2"><strong>CEP:</strong> <?= htmlspecialchars($atividade['Localizacao_CEP'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                                    <p class="mb-2"><strong>Complemento:</strong> <?= htmlspecialchars($atividade['Localizacao_Complemento'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                                    <p class="mb-2"><strong>Valor:</strong> R$ <?= number_format((float) ($atividade['Valor'] ?? 0), 2, ',', '.') ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade <?= $paneSessoes ?>" id="sessoes-pane" role="tabpanel" aria-labelledby="sessoes-tab" tabindex="0">
                            <h5 class="mb-3">Sessões cadastradas</h5>

                            <?php if (empty($sessoes)): ?>
                                <div class="alert alert-info mb-0" role="alert">
                                    Esta atividade ainda não possui sessões cadastradas.
                                </div>
                            <?php else: ?>
                                <?php if (!$usuarioPodeSeInscrever): ?>
                                    <div class="alert alert-secondary mb-3" role="alert">
                                        Apenas clientes autenticados podem se inscrever em sessões.
                                    </div>
                                <?php endif; ?>
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Data</th>
                                                <th>Hora incial</th>
                                                <th>Quantidade mínima</th>
                                                <th>Quantidade máxima</th>
                                                <th>Quantidade disponível</th>
                                                <th class="text-end">Ação</th>
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
                                                    <td><?= htmlspecialchars($dataSessao, ENT_QUOTES, 'UTF-8') ?></td>
                                                    <td><?= htmlspecialchars($horaSessao, ENT_QUOTES, 'UTF-8') ?></td>
                                                    <td><?= (int) ($sessao['Qtd_min'] ?? 0) ?></td>
                                                    <td><?= (int) ($sessao['Qtd_max'] ?? 0) ?></td>
                                                    <td><?= $qtdDisponivel ?></td>
                                                    <td class="text-end">
                                                        <?php if ($usuarioPodeSeInscrever): ?>
                                                            <?php if ($qtdDisponivel > 0): ?>
                                                                <button type="button"
                                                                    class="btn btn-sm btn-primary"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#inscricaoModal"
                                                                    data-sessao-id="<?= $sessaoId ?>"
                                                                    data-sessao-data="<?= htmlspecialchars($dataSessao, ENT_QUOTES, 'UTF-8') ?>"
                                                                    data-sessao-hora="<?= htmlspecialchars($horaSessao, ENT_QUOTES, 'UTF-8') ?>">
                                                                    <i class="bi bi-check-circle"></i> Inscrever-se
                                                                </button>
                                                            <?php else: ?>
                                                                <span class="badge bg-secondary">Esgotada</span>
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            <span class="text-muted small">Entre como cliente para se inscrever.</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
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
            <div class="alert alert-warning shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i> Atividade não encontrada.
                <div class="mt-3">
                    <a href="<?= URL_BASE ?>/atividade/listar" class="btn btn-warning">Voltar para Lista</a>
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
    </script>
</body>

</html>
