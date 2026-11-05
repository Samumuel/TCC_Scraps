<?php
// Faixa exibida ao administrador quando ele cadastra ou edita atividades de uma empresa
$faixaEmpresaAdmin = isset($_SESSION['admin_logado']) ? ($_SESSION['admin_empresa_alvo'] ?? null) : null;
?>
<?php if (!empty($faixaEmpresaAdmin['id'])): ?>
    <div class="cl-faixa-admin">
        <div class="container d-flex flex-wrap align-items-center justify-content-between gap-2">
            <span><i class="bi bi-shield-lock"></i> Você está gerenciando as atividades de <strong><?= htmlspecialchars((string) $faixaEmpresaAdmin['nome'], ENT_QUOTES, 'UTF-8') ?></strong></span>
            <a href="<?= URL_BASE ?>/empresa/atividades" class="small fw-semibold">Ver atividades da empresa</a>
        </div>
    </div>
<?php endif; ?>
