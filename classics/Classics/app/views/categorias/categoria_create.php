<?php 
if(!isset($_SESSION['empresa_logada'])){
	header('Location: ' . URL_BASE);
	exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta charset="UTF-8">
	<title>Projeto Integrador • Nova Categoria</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- Bootstrap 5 -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

	<!-- Bootstrap Icons -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

</head>

<body>

	<div class="container">

		<div class="d-flex mt-5">

			<!-- CONTEUDO -->
			<main class="flex-fill content">

				<!-- TITULO + VOLTAR -->
				<div class="d-flex justify-content-between align-items-center mb-4">
					<h2 class="mb-0">Nova Categoria</h2>
					<a href="<?= URL_BASE ?>/categoria" class="btn btn-outline-secondary">
						<i class="bi bi-arrow-left"></i> Voltar
					</a>
				</div>

				<!-- CARD COM FORMULARIO -->
				<div class="card shadow-sm">
					<div class="card-body">
						<form action="<?= URL_BASE ?>/categoria/salvar" method="POST">
							<div class="mb-3">
								<label for="nome_categoria" class="form-label">Nome da categoria</label>
								<input type="text" class="form-control" id="nome_categoria" name="nome_categoria" required>
							</div>
							<button type="submit" class="btn btn-primary">Salvar</button>
						</form>
					</div>
				</div>

			</main>
		</div>

	</div>


	<!-- Bootstrap JS -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
