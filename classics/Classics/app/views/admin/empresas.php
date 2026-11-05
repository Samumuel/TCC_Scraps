<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Classics • Painel Administrativo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
<?php require __DIR__ . '/../partials/head.php'; ?>
</head>

<body>
<?php require __DIR__ . '/../partials/navbar.php'; ?>
    <div class="container cl-page">
        <?php
        $aba = $aba ?? 'pendente';
        $empresas = $empresas ?? [];
        $contagem = $contagem ?? ['pendente' => 0, 'aprovada' => 0, 'suspensa' => 0, 'rejeitada' => 0];

        $abas = [
            'pendente' => 'Pendentes',
            'aprovada' => 'Aprovadas',
            'suspensa' => 'Suspensas',
            'rejeitada' => 'Rejeitadas'
        ];
        $statusLabels = [
            'pendente' => ['Aguardando aprovação', 'warning'],
            'aprovada' => ['Aprovada', 'success'],
            'suspensa' => ['Suspensa', 'neutral'],
            'rejeitada' => ['Rejeitada', 'danger']
        ];

        $mensagem = null;
        $tipoMensagem = null;
        $mensagensSucesso = [
            'aprovar' => 'Empresa aprovada! Ela já pode cadastrar atividades e receber agendamentos.',
            'rejeitar' => 'Cadastro da empresa rejeitado.',
            'suspender' => 'Empresa suspensa. As atividades dela saíram do catálogo.',
            'reativar' => 'Empresa reativada. As atividades dela voltaram ao catálogo.',
            'editada' => 'Dados da empresa atualizados.',
            'excluida' => 'Empresa excluída com todos os seus dados.'
        ];
        $mensagensErro = [
            'empresa' => 'Empresa não encontrada.',
            'status' => 'Esta ação não é permitida para a situação atual da empresa.',
            'excluir' => 'Não foi possível excluir a empresa. Tente novamente.'
        ];
        if (isset($_GET['sucesso']) && isset($mensagensSucesso[(string) $_GET['sucesso']])) {
            $mensagem = $mensagensSucesso[(string) $_GET['sucesso']];
            $tipoMensagem = 'success';
        } elseif (isset($_GET['erro'])) {
            $mensagem = $mensagensErro[(string) $_GET['erro']] ?? 'Não foi possível concluir a operação. Tente novamente.';
            $tipoMensagem = 'danger';
        }
        ?>

        <div class="cl-page-header">
            <h1 class="mb-1">Painel Administrativo</h1>
            <p>Gerencie as empresas cadastradas na plataforma</p>
        </div>

        <?php if (!empty($mensagem)): ?>
            <div class="alert alert-<?= $tipoMensagem ?>" role="alert">
                <?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="card h-100">
                    <div class="card-body p-4 d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Aguardando aprovação</p>
                            <p class="h2 mb-0"><?= (int) $contagem['pendente'] ?></p>
                        </div>
                        <i class="bi bi-clock" style="font-size: 40px; color: var(--cl-warning);"></i>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card h-100">
                    <div class="card-body p-4 d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Empresas aprovadas</p>
                            <p class="h2 mb-0"><?= (int) $contagem['aprovada'] ?></p>
                        </div>
                        <i class="bi bi-check-circle" style="font-size: 40px; color: var(--cl-success);"></i>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card h-100">
                    <div class="card-body p-4 d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Total de atividades</p>
                            <p class="h2 mb-0"><?= (int) ($total_atividades ?? 0) ?></p>
                        </div>
                        <i class="bi bi-building text-primary" style="font-size: 40px;"></i>
                    </div>
                </div>
            </div>
        </div>

        <ul class="nav cl-admin-abas mb-4">
            <?php foreach ($abas as $chaveAba => $rotuloAba): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $chaveAba === $aba ? 'active' : '' ?>" href="<?= URL_BASE ?>/admin?aba=<?= $chaveAba ?>">
                        <?= $rotuloAba ?> (<?= (int) ($contagem[$chaveAba] ?? 0) ?>)
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <?php if (empty($empresas)): ?>
            <div class="card text-center py-5">
                <div class="card-body">
                    <i class="bi bi-building text-muted" style="font-size: 56px;"></i>
                    <?php
                    $vazio = [
                        'pendente' => 'Nenhuma empresa aguardando aprovação',
                        'aprovada' => 'Nenhuma empresa aprovada',
                        'suspensa' => 'Nenhuma empresa suspensa',
                        'rejeitada' => 'Nenhuma empresa rejeitada'
                    ];
                    ?>
                    <h3 class="mt-3 mb-2"><?= $vazio[$aba] ?? 'Nenhuma empresa encontrada' ?></h3>
                    <p class="text-muted mb-0">Quando houver empresas nesta situação, elas aparecem aqui.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="d-flex flex-column gap-3">
                <?php foreach ($empresas as $empresa): ?>
                    <?php
                    $empresaId = (int) $empresa['ID_Empresa'];
                    $status = strtolower(trim((string) ($empresa['Status'] ?? '')));
                    $statusLabel = $statusLabels[$status] ?? [$status, 'neutral'];
                    $nomeEmpresa = (string) ($empresa['Name'] ?? '');
                    ?>
                    <div class="card">
                        <div class="card-body p-4">
                            <div class="d-flex gap-3 align-items-start">
                                <div class="cl-admin-icone"><i class="bi bi-building"></i></div>
                                <div class="flex-grow-1 min-w-0">
                                    <h3 class="h5 mb-1"><?= htmlspecialchars($nomeEmpresa !== '' ? $nomeEmpresa : 'Empresa sem nome', ENT_QUOTES, 'UTF-8') ?></h3>
                                    <p class="text-muted small mb-2">CNPJ: <?= htmlspecialchars(\app\helpers\Formatador::cnpj($empresa['CNPJ'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                                    <span class="cl-badge cl-badge-<?= $statusLabel[1] ?>"><?= htmlspecialchars($statusLabel[0], ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                            </div>

                            <div class="row g-2 mt-3 small">
                                <div class="col-12 col-md-4 cl-meta text-break">
                                    <i class="bi bi-envelope"></i>
                                    <span><?= htmlspecialchars((string) ($empresa['Email'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                                <div class="col-12 col-md-4 cl-meta">
                                    <i class="bi bi-telephone"></i>
                                    <span><?= htmlspecialchars(\app\helpers\Formatador::telefone($empresa['Telefone'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                                <div class="col-12 col-md-4 cl-meta">
                                    <i class="bi bi-geo-alt"></i>
                                    <span><?= htmlspecialchars((string) (($empresa['Localizacao'] ?? '') !== '' ? $empresa['Localizacao'] : '-'), ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                            </div>

                            <hr>

                            <div class="d-flex flex-wrap gap-2">
                                <?php
                                $acoes = [];
                                if (in_array($status, ['pendente', 'rejeitada'], true)) {
                                    $acoes[] = ['aprovar', 'Aprovar empresa', 'btn-primary', 'bi-check-circle', null];
                                }
                                if ($status === 'pendente') {
                                    $acoes[] = ['rejeitar', 'Rejeitar', 'btn-outline-primary', 'bi-x-circle', 'Deseja rejeitar o cadastro desta empresa?'];
                                }
                                if ($status === 'aprovada') {
                                    $acoes[] = ['suspender', 'Suspender', 'btn-outline-primary', 'bi-pause-circle', 'Deseja suspender esta empresa? As atividades dela sairão do catálogo.'];
                                }
                                if ($status === 'suspensa') {
                                    $acoes[] = ['reativar', 'Reativar', 'btn-primary', 'bi-play-circle', null];
                                }
                                ?>
                                <?php foreach ($acoes as [$acao, $rotulo, $classe, $icone, $confirmacao]): ?>
                                    <form action="<?= URL_BASE ?>/admin/empresa/status" method="POST" class="m-0"
                                        <?= $confirmacao ? 'onsubmit="return confirm(\'' . htmlspecialchars($confirmacao, ENT_QUOTES, 'UTF-8') . '\')"' : '' ?>>
                                        <input type="hidden" name="empresa_id" value="<?= $empresaId ?>">
                                        <input type="hidden" name="acao" value="<?= $acao ?>">
                                        <input type="hidden" name="aba" value="<?= $aba ?>">
                                        <button type="submit" class="btn <?= $classe ?>"><i class="bi <?= $icone ?>"></i> <?= $rotulo ?></button>
                                    </form>
                                <?php endforeach; ?>

                                <a href="<?= URL_BASE ?>/admin/empresa/editar?id=<?= $empresaId ?>" class="btn btn-outline-secondary">
                                    <i class="bi bi-pencil"></i> Editar
                                </a>
                                <form action="<?= URL_BASE ?>/admin/empresa/excluir" method="POST" class="m-0 ms-md-auto"
                                    onsubmit="return confirm('Excluir esta empresa? Todas as atividades, sessões, reservas, avaliações e categorias dela também serão apagadas. Esta ação não pode ser desfeita.')">
                                    <input type="hidden" name="empresa_id" value="<?= $empresaId ?>">
                                    <input type="hidden" name="aba" value="<?= $aba ?>">
                                    <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash"></i> Excluir</button>
                                </form>
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
