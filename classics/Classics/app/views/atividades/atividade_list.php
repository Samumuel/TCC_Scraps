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
	<?php
	$empresaLogada = $_SESSION['empresa_logada'] ?? null;
	$filtros = $filtros ?? ['busca' => '', 'categoria' => '', 'data' => ''];
	$categoriasFiltro = $categorias_filtro ?? [];
	$erros = $erros ?? [];
	$filtrando = ($filtros['busca'] ?? '') !== '' || ($filtros['categoria'] ?? '') !== '' || ($filtros['data'] ?? '') !== '';
	?>

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

			<form action="<?= URL_BASE ?>/" method="GET" class="cl-search-bar mt-4" role="search">
				<div class="cl-input-icon cl-search-busca">
					<i class="bi bi-search"></i>
					<input type="search" class="form-control" name="busca" maxlength="100" placeholder="O que você quer fazer?" aria-label="Buscar atividade pelo nome" value="<?= htmlspecialchars($filtros['busca'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
				</div>
				<select class="form-select cl-search-categoria" name="categoria" aria-label="Filtrar por categoria">
					<option value="">Todas as categorias</option>
					<?php foreach ($categoriasFiltro as $nomeCategoria): ?>
						<option value="<?= htmlspecialchars((string) $nomeCategoria, ENT_QUOTES, 'UTF-8') ?>" <?= (string) $nomeCategoria === ($filtros['categoria'] ?? '') ? 'selected' : '' ?>>
							<?= htmlspecialchars((string) $nomeCategoria, ENT_QUOTES, 'UTF-8') ?>
						</option>
					<?php endforeach; ?>
				</select>
				<div class="cl-input-icon cl-search-data">
					<i class="bi bi-calendar3"></i>
					<input type="date" class="form-control <?= isset($erros['data']) ? 'is-invalid' : '' ?>" name="data" min="<?= date('Y-m-d') ?>" aria-label="Filtrar por data" value="<?= htmlspecialchars($filtros['data'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
				</div>
				<button type="submit" class="btn btn-primary px-4">
					<i class="bi bi-search"></i> Buscar
				</button>
			</form>
			<?php if (isset($erros['data'])): ?>
				<div class="text-danger small mt-2"><?= htmlspecialchars($erros['data'], ENT_QUOTES, 'UTF-8') ?></div>
			<?php endif; ?>
		</div>
	</section>

	<div class="container cl-page">
		<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
			<h2 class="mb-0"><?= $filtrando ? 'Resultados da busca' : 'Atividades' ?></h2>
			<div class="d-flex align-items-center gap-3">
				<span class="text-muted small"><?= count($lista ?? []) ?> atividade(s)<?= $filtrando ? ' encontrada(s)' : '' ?></span>
				<?php if ($filtrando): ?>
					<a href="<?= URL_BASE ?>/" class="small fw-semibold"><i class="bi bi-x-circle"></i> Limpar filtros</a>
				<?php endif; ?>
			</div>
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
									<span><?= htmlspecialchars(\app\helpers\Formatador::duracao($atividade['Duracao'] ?? null)) ?></span>
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
					<?php if ($filtrando): ?>
						<i class="bi bi-search text-muted" style="font-size: 56px;"></i>
						<h3 class="mt-3 mb-2">Nenhuma atividade encontrada</h3>
						<p class="text-muted mb-4">Tente outro nome, categoria ou data.</p>
						<a href="<?= URL_BASE ?>/" class="btn btn-primary">Ver todas as atividades</a>
					<?php else: ?>
						<i class="bi bi-compass text-muted" style="font-size: 56px;"></i>
						<h3 class="mt-3 mb-2">Nenhuma atividade cadastrada</h3>
						<p class="text-muted mb-0">Volte em breve para conferir as novidades.</p>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
