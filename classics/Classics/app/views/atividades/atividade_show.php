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
            $mensagemInscricao = 'Solicitação enviada! Seu agendamento está aguardando a aprovação da empresa. Acompanhe em Meus Agendamentos.';
            $tipoMensagemInscricao = 'success';
        } elseif (isset($_GET['erro_inscricao'])) {
            $erroInscricao = (string) $_GET['erro_inscricao'];
            if ($erroInscricao === 'acesso') {
                $mensagemInscricao = 'Faça login como cliente para se inscrever em uma sessão.';
            } elseif ($erroInscricao === 'quantidade') {
                $mensagemInscricao = 'Informe uma quantidade de pessoas válida.';
            } elseif ($erroInscricao === 'sessao') {
                $mensagemInscricao = 'Sessão não encontrada.';
            } elseif ($erroInscricao === 'encerrada') {
                $mensagemInscricao = 'Esta sessão não está mais disponível para inscrição.';
            } elseif ($erroInscricao === 'lotacao') {
                // FE01: informa quantas vagas restam na sessão
                $mensagemInscricao = isset($_GET['vagas'])
                    ? 'Não há vagas suficientes. Vagas disponíveis nesta sessão: ' . max(0, (int) $_GET['vagas']) . '.'
                    : 'Não há vagas suficientes nesta sessão.';
            } elseif ($erroInscricao === 'minimo') {
                $mensagemInscricao = isset($_GET['minimo'])
                    ? 'Esta sessão exige no mínimo ' . max(1, (int) $_GET['minimo']) . ' participantes por reserva.'
                    : 'A quantidade de pessoas é menor que o mínimo exigido pela sessão.';
            } elseif ($erroInscricao === 'indisponivel') {
                $mensagemInscricao = 'Esta atividade não está recebendo agendamentos no momento.';
            } else {
                $mensagemInscricao = 'Não foi possível realizar a inscrição. Tente novamente.';
            }
            $tipoMensagemInscricao = 'danger';
        }

        $mensagensAvaliacao = [
            'avaliado' => ['Avaliação salva! Obrigado pela sua opinião.', 'success'],
            'excluida' => ['Sua avaliação foi excluída.', 'success'],
            'nota' => ['Escolha uma nota de 1 a 5 estrelas.', 'danger'],
            'comentario' => ['O comentário deve ter no máximo 500 caracteres.', 'danger'],
            'nao_permitido' => ['Só é possível avaliar atividades das quais você participou.', 'danger']
        ];
        $codigoAvaliacao = (string) ($_GET['avaliacao_sucesso'] ?? ($_GET['avaliacao_erro'] ?? ''));
        $mensagemAvaliacao = $codigoAvaliacao !== ''
            ? ($mensagensAvaliacao[$codigoAvaliacao] ?? ['Não foi possível concluir a operação. Tente novamente.', 'danger'])
            : null;
        $minhaAvaliacao = $minha_avaliacao ?? null;
        $podeAvaliar = !empty($pode_avaliar);
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

                    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                        <h1 class="mb-0"><?= htmlspecialchars($atividade['Nome'] ?? '-', ENT_QUOTES, 'UTF-8') ?></h1>

                        <?php if ($usuarioPodeSeInscrever): ?>
                            <?php $favoritado = !empty($favoritado); ?>
                            <form action="<?= URL_BASE ?>/favorito/alternar" method="POST" class="m-0">
                                <input type="hidden" name="atividade_id" value="<?= (int) ($atividade['ID_Atividade'] ?? 0) ?>">
                                <input type="hidden" name="origem" value="atividade">
                                <button type="submit" class="cl-heart-btn <?= $favoritado ? 'ativo' : '' ?>"
                                    title="<?= $favoritado ? 'Remover dos favoritos' : 'Adicionar aos favoritos' ?>"
                                    aria-label="<?= $favoritado ? 'Remover dos favoritos' : 'Adicionar aos favoritos' ?>"
                                    aria-pressed="<?= $favoritado ? 'true' : 'false' ?>">
                                    <i class="bi <?= $favoritado ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
                                </button>
                            </form>
                        <?php elseif (!isset($usuario_logado) && !isset($empresa_logada) && !isset($admin_logado)): ?>
                            <a href="<?= URL_BASE ?>/login" class="cl-heart-btn" title="Entre para favoritar" aria-label="Entre para favoritar">
                                <i class="bi bi-heart"></i>
                            </a>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($atividade['Empresa_Nome'])): ?>
                        <p class="text-muted mb-3">
                            <i class="bi bi-building"></i> Oferecido por
                            <a href="<?= URL_BASE ?>/empresa/ver?id=<?= (int) ($atividade['Empresa_ID'] ?? 0) ?>" class="fw-semibold"><?= htmlspecialchars((string) $atividade['Empresa_Nome'], ENT_QUOTES, 'UTF-8') ?></a>
                        </p>
                    <?php endif; ?>

                    <?php
                    $avaliacoes = $avaliacoes ?? [];
                    $resumoAvaliacoes = $resumo_avaliacoes ?? ['media' => 0.0, 'total' => 0];
                    ?>
                    <div class="d-flex flex-wrap align-items-center gap-3 mb-4">
                        <?php if ($resumoAvaliacoes['total'] > 0): ?>
                            <a href="#avaliacoes" class="cl-nota-media text-reset text-decoration-none">
                                <i class="bi bi-star-fill"></i>
                                <?= \app\helpers\Formatador::media($resumoAvaliacoes['media']) ?>
                                <small>(<?= $resumoAvaliacoes['total'] ?> <?= $resumoAvaliacoes['total'] > 1 ? 'avaliações' : 'avaliação' ?>)</small>
                            </a>
                        <?php endif; ?>
                        <div class="cl-meta">
                            <i class="bi bi-geo-alt" title="Cidade" aria-label="Cidade"></i>
                            <span><?= htmlspecialchars($atividade['Localizacao'] ?? '-', ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
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
                                    <?= htmlspecialchars(\app\helpers\Formatador::duracao($atividade['Duracao'] ?? null), ENT_QUOTES, 'UTF-8') ?>
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
                                Esta atividade não possui sessões disponíveis no momento.
                            </div>
                        <?php else: ?>
                            <?php if (empty(array_filter($sessoes, fn($sessao) => (int) ($sessao['Qtd_disponivel'] ?? 0) > 0))): ?>
                                <div class="alert alert-info mb-3" role="alert">
                                    Esta atividade não possui sessões com vagas disponíveis no momento.
                                </div>
                            <?php endif; ?>
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
                                                                    data-sessao-hora="<?= htmlspecialchars($horaSessao, ENT_QUOTES, 'UTF-8') ?>"
                                                                    data-sessao-vagas="<?= $qtdDisponivel ?>"
                                                                    data-sessao-minimo="<?= max(1, (int) ($sessao['Qtd_min'] ?? 0)) ?>">
                                                                    Reservar
                                                                </button>
                                                            <?php else: ?>
                                                                <span class="cl-badge cl-badge-neutral">Esgotada</span>
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            <?php if (isset($admin_logado) || isset($empresa_logada)): ?>
                                                                <span class="text-muted small">-</span>
                                                            <?php else: ?>
                                                                <a href="<?= URL_BASE ?>/login" class="small fw-semibold">Entre para reservar</a>
                                                            <?php endif; ?>
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

                    <section id="avaliacoes" class="mt-5">
                        <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
                            <h2 class="mb-0">Avaliações</h2>
                            <?php if ($resumoAvaliacoes['total'] > 0): ?>
                                <span class="cl-nota-media fs-6">
                                    <i class="bi bi-star-fill"></i>
                                    <?= \app\helpers\Formatador::media($resumoAvaliacoes['media']) ?>
                                    <small>· <?= $resumoAvaliacoes['total'] ?> <?= $resumoAvaliacoes['total'] > 1 ? 'avaliações' : 'avaliação' ?></small>
                                </span>
                            <?php endif; ?>
                        </div>

                        <?php if ($mensagemAvaliacao !== null): ?>
                            <div class="alert alert-<?= $mensagemAvaliacao[1] ?>" role="alert">
                                <?= htmlspecialchars($mensagemAvaliacao[0], ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($minhaAvaliacao !== null || $podeAvaliar): ?>
                            <?php $notaAtual = $minhaAvaliacao !== null ? (int) $minhaAvaliacao->getNota() : 0; ?>
                            <div class="card mb-3 cl-minha-avaliacao">
                                <div class="card-body p-4">
                                    <?php if ($minhaAvaliacao !== null): ?>
                                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-2">
                                            <div>
                                                <strong class="d-block mb-1">Sua avaliação</strong>
                                                <?php $notaEstrelas = $notaAtual; require __DIR__ . '/../partials/estrelas.php'; ?>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <?php if ($podeAvaliar): ?>
                                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#formAvaliacao" aria-expanded="false" aria-controls="formAvaliacao">
                                                        <i class="bi bi-pencil"></i> Editar
                                                    </button>
                                                <?php endif; ?>
                                                <form action="<?= URL_BASE ?>/avaliacao/excluir" method="POST" class="m-0" onsubmit="return confirm('Deseja excluir a sua avaliação?')">
                                                    <input type="hidden" name="atividade_id" value="<?= (int) ($atividade['ID_Atividade'] ?? 0) ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-trash"></i> Excluir
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        <?php if (trim((string) $minhaAvaliacao->getComentario()) !== ''): ?>
                                            <p class="text-muted mb-0"><?= nl2br(htmlspecialchars((string) $minhaAvaliacao->getComentario(), ENT_QUOTES, 'UTF-8')) ?></p>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <strong class="d-block mb-1">Você participou desta atividade</strong>
                                        <p class="text-muted small mb-0">Conte como foi a sua experiência e ajude outros clientes.</p>
                                    <?php endif; ?>

                                    <?php if ($podeAvaliar): ?>
                                        <div class="collapse <?= $minhaAvaliacao === null ? 'show' : '' ?>" id="formAvaliacao">
                                            <form action="<?= URL_BASE ?>/avaliacao/salvar" method="POST" class="mt-3 pt-3 border-top">
                                                <input type="hidden" name="atividade_id" value="<?= (int) ($atividade['ID_Atividade'] ?? 0) ?>">
                                                <input type="hidden" name="origem" value="atividade">
                                                <fieldset class="mb-3">
                                                    <legend class="form-label fs-6">Sua nota</legend>
                                                    <div class="cl-estrelas-input">
                                                        <?php for ($nota = 5; $nota >= 1; $nota--): ?>
                                                            <input type="radio" name="nota" id="avaliarNota<?= $nota ?>" value="<?= $nota ?>" <?= $nota === $notaAtual ? 'checked' : '' ?> required>
                                                            <label for="avaliarNota<?= $nota ?>" title="<?= $nota ?> <?= $nota > 1 ? 'estrelas' : 'estrela' ?>"><i class="bi bi-star-fill"></i></label>
                                                        <?php endfor; ?>
                                                    </div>
                                                </fieldset>
                                                <div class="mb-3">
                                                    <label for="avaliarComentario" class="form-label">Comentário <span class="text-muted fw-normal">(opcional)</span></label>
                                                    <textarea class="form-control" id="avaliarComentario" name="comentario" rows="3" maxlength="500" placeholder="Conte como foi a sua experiência"><?= htmlspecialchars((string) ($minhaAvaliacao !== null ? $minhaAvaliacao->getComentario() : ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                                                    <div class="form-text">Até 500 caracteres.</div>
                                                </div>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="bi bi-check-circle"></i> Salvar avaliação
                                                </button>
                                            </form>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (empty($avaliacoes)): ?>
                            <div class="alert alert-info mb-0" role="alert">
                                Esta atividade ainda não recebeu avaliações.
                            </div>
                        <?php else: ?>
                            <div class="card">
                                <div class="card-body py-1 px-4">
                                    <?php foreach ($avaliacoes as $itemAvaliacao): ?>
                                        <?php $avaliacaoAtual = $itemAvaliacao['avaliacao']; ?>
                                        <div class="cl-avaliacao-item py-3">
                                            <div class="d-flex justify-content-between align-items-start gap-3 mb-1">
                                                <strong><?= htmlspecialchars($itemAvaliacao['usuario_nome'] !== '' ? $itemAvaliacao['usuario_nome'] : 'Usuário', ENT_QUOTES, 'UTF-8') ?></strong>
                                                <?php $notaEstrelas = (int) $avaliacaoAtual->getNota(); require __DIR__ . '/../partials/estrelas.php'; ?>
                                            </div>
                                            <?php if (trim((string) $avaliacaoAtual->getComentario()) !== ''): ?>
                                                <p class="text-muted mb-0"><?= nl2br(htmlspecialchars((string) $avaliacaoAtual->getComentario(), ENT_QUOTES, 'UTF-8')) ?></p>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
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
                                    <div class="form-text" id="inscricaoVagasTexto">Informe a quantidade total de participantes dessa inscrição.</div>
                                </div>
                                <!-- UC07 passo 6: resumo do agendamento antes da confirmação -->
                                <div class="rounded-3 p-3 cl-resumo-reserva" data-valor-pessoa="<?= htmlspecialchars((string) (float) ($atividade['Valor'] ?? 0), ENT_QUOTES, 'UTF-8') ?>">
                                    <div class="fw-semibold mb-2">Resumo da reserva</div>
                                    <div class="d-flex justify-content-between small mb-1">
                                        <span class="text-muted">Atividade</span>
                                        <span class="text-end"><?= htmlspecialchars((string) ($atividade['Nome'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between small mb-1">
                                        <span class="text-muted">Valor</span>
                                        <span id="inscricaoCalculo">R$ <?= number_format((float) ($atividade['Valor'] ?? 0), 2, ',', '.') ?> × 0 pessoas</span>
                                    </div>
                                    <div class="d-flex justify-content-between border-top pt-2 mt-2">
                                        <span class="fw-semibold">Total</span>
                                        <span class="fw-bold text-primary" id="inscricaoTotal">R$ 0,00</span>
                                    </div>
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

                // Limites da sessão: a quantidade não pode passar das vagas disponíveis (RN05)
                const vagas = parseInt(button.getAttribute('data-sessao-vagas') || '0', 10);
                const minimo = parseInt(button.getAttribute('data-sessao-minimo') || '1', 10);
                const campoQuantidade = document.getElementById('quantidade_pessoas');
                campoQuantidade.min = minimo;
                campoQuantidade.max = vagas;
                campoQuantidade.value = minimo;
                document.getElementById('inscricaoVagasTexto').textContent = `Vagas disponíveis nesta sessão: ${vagas}.`;
                atualizarResumo();
            });

            // Valor total = valor por pessoa × quantidade de pessoas
            const resumo = inscricaoModal.querySelector('.cl-resumo-reserva');
            const valorPessoa = parseFloat(resumo.dataset.valorPessoa || '0');
            const moeda = (valor) => valor.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
            const atualizarResumo = () => {
                const quantidade = Math.max(0, parseInt(document.getElementById('quantidade_pessoas').value || '0', 10) || 0);
                document.getElementById('inscricaoCalculo').textContent = `${moeda(valorPessoa)} × ${quantidade} ${quantidade === 1 ? 'pessoa' : 'pessoas'}`;
                document.getElementById('inscricaoTotal').textContent = moeda(valorPessoa * quantidade);
            };
            document.getElementById('quantidade_pessoas').addEventListener('input', atualizarResumo);
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
