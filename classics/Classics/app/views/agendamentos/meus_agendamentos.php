<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Projeto Integrador • Meus Agendamentos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <div class="container py-4">
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
            'aprovado' => ['Ativo', 'success'],
            'cancelado' => ['Cancelado', 'secondary']
        ];
        ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Meus Agendamentos</h2>
            <a href="<?= URL_BASE ?>/atividade/listar" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar para Atividades
            </a>
        </div>

        <?php if (!empty($mensagem)): ?>
            <div class="alert alert-<?= htmlspecialchars($tipoMensagem ?? 'info', ENT_QUOTES, 'UTF-8') ?> shadow-sm" role="alert">
                <?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <div class="alert alert-info small" role="alert">
            Para trocar de sessão, data ou hora, cancele o agendamento e faça uma nova inscrição na página da atividade.
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3">Atividade</th>
                                <th class="px-4 py-3">Data</th>
                                <th class="px-4 py-3">Hora</th>
                                <th class="px-4 py-3">Pessoas</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($agendamentos)): ?>
                                <?php foreach ($agendamentos as $item): ?>
                                    <?php
                                    $agendamento = $item['agendamento'];
                                    $sessao = $item['sessao'];
                                    $agendamentoId = (int) $agendamento->getIdAgendamento();
                                    $status = (string) $agendamento->getStatus();
                                    $statusLabel = $statusLabels[$status] ?? [ucfirst($status !== '' ? $status : '-'), 'light text-dark'];
                                    $qtdMinima = max(1, (int) $sessao->getQtdMin());
                                    $qtdLivre = (int) $sessao->getQtdDisponivel() + (int) $agendamento->getQuantidadePessoas();
                                    ?>
                                    <tr>
                                        <td class="px-4 py-3">
                                            <a href="<?= URL_BASE ?>/atividade/ver?id=<?= (int) $agendamento->getIdAtividade() ?>">
                                                <?= htmlspecialchars($item['atividade_nome'] !== '' ? $item['atividade_nome'] : '-', ENT_QUOTES, 'UTF-8') ?>
                                            </a>
                                        </td>
                                        <td class="px-4 py-3"><?= htmlspecialchars((string) ($sessao->getData() ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                                        <td class="px-4 py-3"><?= htmlspecialchars(substr((string) ($sessao->getHora() ?? '-'), 0, 5), ENT_QUOTES, 'UTF-8') ?></td>
                                        <td class="px-4 py-3"><?= (int) $agendamento->getQuantidadePessoas() ?></td>
                                        <td class="px-4 py-3">
                                            <span class="badge bg-<?= htmlspecialchars($statusLabel[1], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($statusLabel[0], ENT_QUOTES, 'UTF-8') ?></span>
                                        </td>
                                        <td class="px-4 py-3 text-end">
                                            <?php if (!empty($item['pode_alterar'])): ?>
                                                <form action="<?= URL_BASE ?>/agendamento/atualizar" method="POST" class="d-inline-flex gap-1 align-items-center">
                                                    <input type="hidden" name="agendamento_id" value="<?= $agendamentoId ?>">
                                                    <input type="number" name="quantidade_pessoas" class="form-control form-control-sm" style="width: 90px;"
                                                        value="<?= (int) $agendamento->getQuantidadePessoas() ?>" min="<?= $qtdMinima ?>" max="<?= $qtdLivre ?>" step="1" required
                                                        aria-label="Quantidade de pessoas">
                                                    <button type="submit" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-pencil"></i> Salvar
                                                    </button>
                                                </form>
                                                <form action="<?= URL_BASE ?>/agendamento/cancelar" method="POST" class="d-inline" onsubmit="return confirm('Deseja cancelar este agendamento?')">
                                                    <input type="hidden" name="agendamento_id" value="<?= $agendamentoId ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-x-circle"></i> Cancelar
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <span class="text-muted small">Sem ações disponíveis</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td class="px-4 py-4 text-center text-muted" colspan="6">Você ainda não possui agendamentos.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
