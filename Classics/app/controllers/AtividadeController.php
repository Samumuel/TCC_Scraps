<?php 

namespace app\controllers;

use app\core\Controller;
use app\models\Atividade;
use app\models\Localizacao;
use app\services\AtividadeService;
use app\services\CategoriaService;
use app\services\LocalizacaoService;

class AtividadeController extends Controller {

	private AtividadeService $service;
	private CategoriaService $categoriaService;
	private LocalizacaoService $localizacaoService;

	public function __construct()
	{
		$this->service = new AtividadeService();
		$this->categoriaService = new CategoriaService();
		$this->localizacaoService = new LocalizacaoService();
	}

	public function listarTodos() {
		$data['lista'] = $this->service->getAtividades();
		$this->view('atividades/atividade_list', $data);
	}

	public function criar() {
		$empresaId = $this->getEmpresaLogadaId();

		if ($empresaId === null) {
			$this->redirect(URL_BASE);
		}

		$data['categorias'] = $this->categoriaService->getCategorias((int) $empresaId);
		$this->view('atividades/atividade_create', $data);
	}

	public function salvar() {
		$nome = $_POST['nome'] ?? '';
		$duracao = $_POST['duracao'] ?? null;
		$descricao = $_POST['descricao'] ?? null;
		$localizacao = $_POST['localizacao'] ?? null;
		$valor = isset($_POST['valor']) && $_POST['valor'] !== '' ? (float) $_POST['valor'] : null;
		$fotos = $_POST['fotos'] ?? null;
		$categoriaId = isset($_POST['categoria_id']) ? (int) $_POST['categoria_id'] : (isset($_POST['categoria']) && $_POST['categoria'] !== '' ? (int) $_POST['categoria'] : null);
		$empresaId = isset($_POST['empresa_id']) ? (int) $_POST['empresa_id'] : null;

		if ($empresaId === null && isset($_SESSION['empresa_logada'])) {
			$empresaId = $_SESSION['empresa_logada']->getIdEmpresa();
		}

		$atividade = new Atividade();
		$atividade->setNome($nome);
		$atividade->setDuracao($duracao);
		$atividade->setDescricao($descricao);
		$atividade->setLocalizacao($localizacao);
		$atividade->setValor($valor);
		$atividade->setFotos($fotos);
		$atividade->setCategoriaId($categoriaId);
		$atividade->setEmpresaId($empresaId);

		$atividadeId = $this->service->saveAtividade($atividade);
		if ($atividadeId) {
			$this->redirect(URL_BASE . '/atividade/localizacao/cadastrar?id=' . (int) $atividadeId);
		} else {
			echo 'Erro ao salvar a atividade.';
		}
	}

	public function editar() {
		$empresaId = $this->getEmpresaLogadaId();
		if ($empresaId === null) {
			$this->redirect(URL_BASE);
		}

		$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
		$data['atividade'] = $this->service->getAtividade($id);
		if (!$data['atividade'] || (int) ($data['atividade']['Empresa_ID'] ?? 0) !== $empresaId) {
			$this->redirect(URL_BASE . '/atividade/listar');
		}

		$data['categorias'] = $this->categoriaService->getCategorias($empresaId);
		$data['localizacao'] = $this->localizacaoService->getLocalizacaoPorAtividade($id);
		$data['aba_ativa'] = $_GET['tab'] ?? 'atividade';
		$this->view('atividades/atividade_edit', $data);
	}

	public function atualizar() {
		$empresaId = $this->getEmpresaLogadaId();
		if ($empresaId === null) {
			$this->redirect(URL_BASE);
		}

		$id = isset($_POST['id']) ? (int) $_POST['id'] : (int) ($_POST['id_atividade'] ?? 0);
		$nome = $_POST['nome'] ?? '';
		$duracao = $_POST['duracao'] ?? null;
		$descricao = $_POST['descricao'] ?? null;
		$valor = isset($_POST['valor']) && $_POST['valor'] !== '' ? (float) $_POST['valor'] : null;
		$fotos = $_POST['fotos'] ?? null;
		$categoriaId = isset($_POST['categoria_id']) ? (int) $_POST['categoria_id'] : (isset($_POST['categoria']) && $_POST['categoria'] !== '' ? (int) $_POST['categoria'] : null);
		$atividadeAtual = $this->service->getAtividade($id);
		if (!$atividadeAtual || (int) ($atividadeAtual['Empresa_ID'] ?? 0) !== $empresaId) {
			$this->redirect(URL_BASE . '/atividade/listar');
		}

		$atividade = new Atividade(
			$id,
			$nome,
			$duracao,
			$descricao,
			$atividadeAtual['Localizacao'] ?? null,
			$valor,
			$fotos,
			$categoriaId,
			$empresaId
		);

		$this->service->updateAtividade($atividade);
		$this->redirect(URL_BASE . '/atividade/listar');
	}

	public function localizacaoCadastrar()
	{
		$empresaId = $this->getEmpresaLogadaId();
		if ($empresaId === null) {
			$this->redirect(URL_BASE);
		}

		$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
		$atividade = $this->getAtividadeDaEmpresa($id, $empresaId);
		if (!$atividade) {
			$this->redirect(URL_BASE . '/atividade/listar');
		}

		$data['atividade'] = $atividade;
		$data['localizacao'] = $this->localizacaoService->getLocalizacaoPorAtividade($id);
		$data['origem'] = 'create';
		$this->view('atividades/localizacao_create', $data);
	}

	public function salvarLocalizacao()
	{
		$empresaId = $this->getEmpresaLogadaId();
		if ($empresaId === null) {
			$this->redirect(URL_BASE);
		}

		$idAtividade = isset($_POST['id_atividade']) ? (int) $_POST['id_atividade'] : 0;
		$atividade = $this->getAtividadeDaEmpresa($idAtividade, $empresaId);
		if (!$atividade) {
			$this->redirect(URL_BASE . '/atividade/listar');
		}

		$localizacao = new Localizacao();
		$localizacao->setIdAtividade($idAtividade);
		$localizacao->setCep(trim((string) ($_POST['cep'] ?? '')));
		$localizacao->setComplemento(trim((string) ($_POST['complemento'] ?? '')));

		$this->localizacaoService->saveOrUpdateLocalizacao($localizacao);

		$origem = $_POST['origem'] ?? 'create';
		if ($origem === 'editar') {
			$this->redirect(URL_BASE . '/atividade/editar?id=' . $idAtividade . '&tab=localizacao');
		}

		$this->redirect(URL_BASE . '/atividade/listar');
	}

	public function verAtividade() {
		if (!isset($_GET['id'])) {
			$this->redirect(URL_BASE . '/atividade/listar');
		}

		$id = (int) $_GET['id'];
		$data['atividade'] = $this->service->getAtividade($id);
		$this->view('atividades/atividade_show', $data);
	}

	public function excluir() {
		$empresaId = $this->getEmpresaLogadaId();
		if ($empresaId === null) {
			$this->redirect(URL_BASE);
		}

		$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
		$atividade = $this->service->getAtividade($id);
		if (!$atividade || (int) ($atividade['Empresa_ID'] ?? 0) !== $empresaId) {
			$this->redirect(URL_BASE);
		}

		if ($this->service->deleteAtividade($id, $empresaId)) {
			$this->redirect(URL_BASE . '/atividade/listar');
		} else {
			echo 'Erro ao excluir a atividade.';
		}
	}

	private function getEmpresaLogadaId(): ?int
	{
		if (!isset($_SESSION['empresa_logada']) || !is_object($_SESSION['empresa_logada'])) {
			return null;
		}

		return (int) $_SESSION['empresa_logada']->getIdEmpresa();
	}

	private function getAtividadeDaEmpresa(int $id, int $empresaId): ?array
	{
		$atividade = $this->service->getAtividade($id);
		if (!$atividade || (int) ($atividade['Empresa_ID'] ?? 0) !== $empresaId) {
			return null;
		}

		return $atividade;
	}
}
