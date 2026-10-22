<!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta charset="UTF-8">
	<title>Classics • Atividades</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
<?php require __DIR__ . '/../partials/head.php'; ?>
</head>

<body>
<?php require __DIR__ . '/../partials/navbar.php'; ?>
	<?php $empresaLogada = $_SESSION['empresa_logada'] ?? null; ?>

	<section class="cl-hero">
		<div class="container">
			<div class="col-lg-7">
				<h1 class="mb-3">Descubra atividades de lazer incríveis</h1>
				<p class="mb-0">Conecte-se com as melhores empresas de entretenimento e lazer</p>
				<?php if ($empresaLogada): ?>
					<a href="<?= URL_BASE ?>/atividade/cadastrar" class="btn btn-primary mt-4">
						<i class="bi bi-plus-circle"></i> Nova Atividade
					</a>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<div class="container cl-page">
		<div class="d-flex justify-content-between align-items-center mb-4">
			<h2 class="mb-0">Atividades</h2>
			<span class="text-muted small"><?= count($lista ?? []) ?> atividade(s)</span>
		</div>

		<?php if (!empty($lista)): ?>
			<div class="row g-4">
				<?php foreach ($lista as $atividade): ?>
					<?php $atividadeId = (int) ($atividade['ID_Atividade'] ?? 0); ?>
					<div class="col-12 col-md-6 col-lg-4 col-xl-3">
						<div class="card cl-activity-card cl-card-hover">
							<a href="<?= URL_BASE ?>/atividade/ver?id=<?= $atividadeId ?>">
								<?php if (!empty($atividade['Fotos'])): ?>
									<img src="<?= htmlspecialchars(URL_BASE . '/' . ltrim($atividade['Fotos'], '/'), ENT_QUOTES, 'UTF-8') ?>" alt="Foto da atividade" class="cl-activity-img">
								<?php else: ?>
									<div class="cl-activity-img cl-placeholder-img">
										<i class="bi bi-image" style="font-size: 48px;" aria-label="Sem foto"></i>
									</div>
								<?php endif; ?>
							</a>
							<div class="card-body d-flex flex-column">
								<?php if (!empty($atividade['Categoria_Nome'])): ?>
									<div class="mb-2">
										<span class="cl-badge cl-badge-category"><?= htmlspecialchars($atividade['Categoria_Nome']) ?></span>
									</div>
								<?php endif; ?>
								<h3 class="card-title mb-2"><?= htmlspecialchars($atividade['Nome'] ?? '-') ?></h3>
								<div class="cl-meta mb-1">
									<i class="bi bi-geo-alt"></i>
									<span><?= htmlspecialchars($atividade['Localizacao'] ?? '-') ?></span>
								</div>
								<div class="cl-meta mb-3">
									<i class="bi bi-clock"></i>
									<span><?= htmlspecialchars($atividade['Duracao'] ?? '-') ?></span>
								</div>
								<div class="cl-price mt-auto mb-3">
									R$ <?= number_format((float) ($atividade['Valor'] ?? 0), 2, ',', '.') ?>
									<span class="text-muted small fw-normal">/pessoa</span>
								</div>
								<a href="<?= URL_BASE ?>/atividade/ver?id=<?= $atividadeId ?>" class="btn btn-primary btn-sm w-100">
									Ver detalhes
								</a>
								<?php if ($empresaLogada && (int) ($atividade['Empresa_ID'] ?? 0) === (int) $empresaLogada->getIdEmpresa()): ?>
									<div class="d-flex gap-2 mt-2">
										<a href="<?= URL_BASE ?>/atividade/editar?id=<?= $atividadeId ?>" class="btn btn-sm btn-outline-primary flex-fill">
											<i class="bi bi-pencil"></i> Editar
										</a>
										<a href="<?= URL_BASE ?>/atividade/excluir?id=<?= $atividadeId ?>" class="btn btn-sm btn-outline-danger flex-fill" onclick="return confirm('Deseja excluir esta atividade?')">
											<i class="bi bi-trash"></i> Excluir
										</a>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php else: ?>
			<div class="card text-center py-5">
				<div class="card-body">
					<i class="bi bi-compass text-muted" style="font-size: 56px;"></i>
					<h3 class="mt-3 mb-2">Nenhuma atividade cadastrada</h3>
					<p class="text-muted mb-0">Volte em breve para conferir as novidades.</p>
				</div>
			</div>
		<?php endif; ?>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
