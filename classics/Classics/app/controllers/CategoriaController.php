<?php 

namespace app\controllers;

use app\core\Controller;
use app\models\Categoria;
use app\services\CategoriaService;

class CategoriaController extends Controller
{
	private CategoriaService $service;

	public function __construct()
	{
		$this->service = new CategoriaService();
	}

	public function criar()
	{
		if (!$this->getEmpresaAprovadaId()) {
			$this->redirect(URL_BASE);
		}

		$this->view('categorias/categoria_create', []);
	}

	public function listarTodos()
	{
		$empresaId = $this->getEmpresaAprovadaId();
		if (!$empresaId) {
			$this->redirect(URL_BASE);
		}

		$data['lista'] = $this->service->getCategorias($empresaId);
		$this->view('categorias/categoria_list', $data);
	}

	public function salvar()
	{
		$empresaId = $this->getEmpresaAprovadaId();
		if (!$empresaId) {
			$this->redirect(URL_BASE);
		}

		$nomeCategoria = trim((string) ($_POST['nome_categoria'] ?? ''));
		$erro = $this->validarNome($nomeCategoria);
		if ($erro !== null) {
			$this->view('categorias/categoria_create', ['nome_categoria' => $nomeCategoria, 'erro' => $erro]);
			return;
		}

		$categoria = new Categoria();
		$categoria->setNomeCategoria($nomeCategoria);
		$categoria->setEmpresaId($empresaId);

		if ($this->service->saveCategoria($categoria, $empresaId)) {
			$this->redirect(URL_BASE . '/categoria');
		} else {
			echo 'Erro ao salvar a categoria.';
		}
	}

	public function editar()
	{
		$empresaId = $this->getEmpresaAprovadaId();
		if (!$empresaId) {
			$this->redirect(URL_BASE);
		}

		if (!isset($_GET['id'])) {
			$this->redirect(URL_BASE . '/categoria');
		}

		$id = (int) $_GET['id'];
		$data['categoria'] = $this->service->getCategoria($id, $empresaId);
		if (!$data['categoria']) {
			$this->redirect(URL_BASE . '/categoria');
		}

		$this->view('categorias/categoria_edit', $data);
	}

	public function atualizar()
	{
		$empresaId = $this->getEmpresaAprovadaId();
		if (!$empresaId) {
			$this->redirect(URL_BASE);
		}

		$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
		$nomeCategoria = trim((string) ($_POST['nome_categoria'] ?? ''));
		$erro = $this->validarNome($nomeCategoria);
		if ($erro !== null) {
			$this->view('categorias/categoria_edit', [
				'categoria' => ['ID_Categoria' => $id, 'Nome_Categoria' => $nomeCategoria],
				'erro' => $erro
			]);
			return;
		}

		$categoria = new Categoria($id, $nomeCategoria, $empresaId);

		if ($this->service->updateCategoria($categoria, $empresaId)) {
			$this->redirect(URL_BASE . '/categoria');
		} else {
			echo 'Erro ao atualizar a categoria.';
		}
	}

	public function excluir()
	{
		$empresaId = $this->getEmpresaAprovadaId();
		if (!$empresaId) {
			$this->redirect(URL_BASE);
		}

		// Exclusão por POST (o formulário pede confirmação)
		$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
		$resultado = $this->service->deleteCategoria($id, $empresaId);

		if (!empty($resultado['sucesso'])) {
			$this->redirect(URL_BASE . '/categoria?sucesso=excluida');
		}

		$this->redirect(URL_BASE . '/categoria?erro=' . rawurlencode((string) ($resultado['erro'] ?? 'falha')));
	}

	private function validarNome(string $nomeCategoria): ?string
	{
		if ($nomeCategoria === '') {
			return 'Informe o nome da categoria.';
		}

		if (mb_strlen($nomeCategoria) > 100) {
			return 'O nome da categoria deve ter no máximo 100 caracteres.';
		}

		return null;
	}

}
