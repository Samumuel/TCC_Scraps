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
		if (!$this->getEmpresaLogadaId()) {
			$this->redirect(URL_BASE);
		}

		$this->view('categorias/categoria_create', []);
	}

	public function listarTodos()
	{
		$empresaId = $this->getEmpresaLogadaId();
		if (!$empresaId) {
			$this->redirect(URL_BASE);
		}

		$data['lista'] = $this->service->getCategorias($empresaId);
		$this->view('categorias/categoria_list', $data);
	}

	public function salvar()
	{
		$empresaId = $this->getEmpresaLogadaId();
		if (!$empresaId) {
			$this->redirect(URL_BASE);
		}

		$nomeCategoria = $_POST['nome_categoria'] ?? '';

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
		$empresaId = $this->getEmpresaLogadaId();
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
		$empresaId = $this->getEmpresaLogadaId();
		if (!$empresaId) {
			$this->redirect(URL_BASE);
		}

		$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
		$nomeCategoria = $_POST['nome_categoria'] ?? '';

		$categoria = new Categoria($id, $nomeCategoria, $empresaId);

		if ($this->service->updateCategoria($categoria, $empresaId)) {
			$this->redirect(URL_BASE . '/categoria');
		} else {
			echo 'Erro ao atualizar a categoria.';
		}
	}

	public function excluir()
	{
		$empresaId = $this->getEmpresaLogadaId();
		if (!$empresaId) {
			$this->redirect(URL_BASE);
		}

		if (!isset($_GET['id'])) {
			$this->redirect(URL_BASE . '/categoria');
		}

		$id = (int) $_GET['id'];
		if ($this->service->deleteCategoria($id, $empresaId)) {
			$this->redirect(URL_BASE . '/categoria');
		} else {
			echo 'Erro ao excluir a categoria.';
		}
	}

	private function getEmpresaLogadaId(): int
	{
		if (!isset($_SESSION['empresa_logada'])) {
			return 0;
		}

		$empresa = $_SESSION['empresa_logada'];
		if (is_object($empresa) && method_exists($empresa, 'getIdEmpresa')) {
			return (int) $empresa->getIdEmpresa();
		}

		if (is_array($empresa) && isset($empresa['ID_Empresa'])) {
			return (int) $empresa['ID_Empresa'];
		}

		return 0;
	}
}
