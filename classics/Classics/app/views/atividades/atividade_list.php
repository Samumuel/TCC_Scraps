<!--Feito pelo chat, tem que validar-->
<!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta charset="UTF-8">
	<title>Projeto Integrador • Lista de Atividades</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
	<div class="container py-4">
		<?php $empresaLogada = $_SESSION['empresa_logada'] ?? null; ?>
		<div class="d-flex justify-content-between align-items-center mb-4">
			<h2 class="mb-0">Lista de Atividades</h2>
			<?php if(isset($_SESSION['empresa_logada'])): ?>
				<a href="<?= URL_BASE ?>/atividade/cadastrar" class="btn btn-primary">
					<i class="bi bi-plus-circle"></i> Nova Atividade
				</a>
			<?php endif; ?>
		</div>

		<div class="card shadow-sm">
			<div class="card-body p-0">
				<div class="table-responsive">
					<table class="table table-hover mb-0">
						<thead class="table-light">
							<tr>
								<th class="px-4 py-3">Foto</th>
								<th class="px-4 py-3">Nome</th>
								<th class="px-4 py-3">Duração</th>
								<th class="px-4 py-3">Localização</th>
								<th class="px-4 py-3">Valor</th>
								<th class="px-4 py-3">Categoria</th>
								<th class="px-4 py-3 text-end">Ações</th>
							</tr>
						</thead>
						<tbody>
							<?php if (!empty($lista)): ?>
								<?php foreach ($lista as $atividade): ?>
									<tr>
										<td class="px-4 py-3 align-middle">
											<?php if (!empty($atividade['Fotos'])): ?>
												<img src="<?= htmlspecialchars(URL_BASE . '/' . ltrim($atividade['Fotos'], '/'), ENT_QUOTES, 'UTF-8') ?>" alt="Foto da atividade" style="width: 72px; height: 52px; object-fit: cover;" class="rounded">
											<?php else: ?>
												<i class="bi bi-image text-muted" style="font-size: 28px;" aria-label="Sem foto"></i>
											<?php endif; ?>
										</td>
										<td class="px-4 py-3 align-middle"><?= htmlspecialchars($atividade['Nome'] ?? '-') ?></td>
										<td class="px-4 py-3 align-middle"><?= htmlspecialchars($atividade['Duracao'] ?? '-') ?></td>
										<td class="px-4 py-3 align-middle"><?= htmlspecialchars($atividade['Localizacao'] ?? '-') ?></td>
										<td class="px-4 py-3 align-middle">R$ <?= number_format((float) ($atividade['Valor'] ?? 0), 2, ',', '.') ?></td>
										<td class="px-4 py-3 align-middle"><?= htmlspecialchars($atividade['Categoria_Nome'] ?? '-') ?></td>
										<td class="px-4 py-3 align-middle text-end">
											<a href="<?= URL_BASE ?>/atividade/ver?id=<?= (int) ($atividade['ID_Atividade'] ?? 0) ?>" class="btn btn-sm btn-outline-primary">
												<i class="bi bi-eye"></i> Ver
											</a>
											<?php if ($empresaLogada && (int) ($atividade['Empresa_ID'] ?? 0) === (int) $empresaLogada->getIdEmpresa()): ?>
												<a href="<?= URL_BASE ?>/atividade/editar?id=<?= (int) ($atividade['ID_Atividade'] ?? 0) ?>" class="btn btn-sm btn-outline-primary">
													<i class="bi bi-pencil"></i> Editar
												</a>
												<a href="<?= URL_BASE ?>/atividade/excluir?id=<?= (int) ($atividade['ID_Atividade'] ?? 0) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Deseja excluir esta atividade?')">
													<i class="bi bi-trash"></i> Excluir
												</a>
											<?php endif; ?>
										</td>
									</tr>
								<?php endforeach; ?>
							<?php else: ?>
								<tr>
									<td class="px-4 py-4 text-center text-muted" colspan="8">Nenhuma atividade cadastrada.</td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div class="mt-4">
			<a href="<?= URL_BASE ?>/empresa" class="btn btn-link">Ver Empresas</a>
			<a href="<?= URL_BASE ?>/usuarios" class="btn btn-link">Ver Usuários</a>
			<a href="<?= URL_BASE ?>/categoria" class="btn btn-link">Ver Categorias</a>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
