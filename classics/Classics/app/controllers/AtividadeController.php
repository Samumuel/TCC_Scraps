<?php 

namespace app\controllers;

use app\core\Controller;
use app\helpers\UploadImagem;
use app\models\Atividade;
use app\models\Localizacao;
use app\models\Sessao;
use app\services\AtividadeService;
use app\services\AvaliacaoService;
use app\services\CategoriaService;
use app\services\EmpresaService;
use app\services\FavoritoService;
use app\services\LocalizacaoService;
use app\services\SessaoService;
use DateTime;

class AtividadeController extends Controller {

	private AtividadeService $service;
	private CategoriaService $categoriaService;
	private LocalizacaoService $localizacaoService;
	private SessaoService $sessaoService;
	private FavoritoService $favoritoService;
	private AvaliacaoService $avaliacaoService;

	public function __construct()
	{
		$this->service = new AtividadeService();
		$this->categoriaService = new CategoriaService();
		$this->localizacaoService = new LocalizacaoService();
		$this->sessaoService = new SessaoService();
		$this->favoritoService = new FavoritoService();
		$this->avaliacaoService = new AvaliacaoService();
	}

	public function listarTodos() {
		$resultado = $this->service->filtrarAtividades(
			(string) ($_GET['busca'] ?? ''),
			(string) ($_GET['cidade'] ?? ''),
			(string) ($_GET['categoria'] ?? ''),
			(string) ($_GET['data'] ?? '')
		);

		$data['lista'] = $resultado['atividades'];
		$data['filtros'] = $resultado['filtros'];
		$data['erros'] = $resultado['erros'];
		$data['categorias_filtro'] = $this->categoriaService->getNomesCategoriasComAtividades();
		$this->view('atividades/atividade_list', $data);
	}

	public function criar() {
		$empresaId = $this->getEmpresaParaNovaAtividade();

		if ($empresaId === null) {
			$this->redirect(URL_BASE);
		}

		$data['categorias'] = $this->categoriaService->getCategorias((int) $empresaId);
		$this->view('atividades/atividade_create', $data);
	}

	public function salvar() {
		$empresaId = $this->getEmpresaParaNovaAtividade();
		if ($empresaId === null) {
			$this->redirect(URL_BASE);
		}

		$nome = trim((string) ($_POST['nome'] ?? ''));
		$duracaoHoras = trim((string) ($_POST['duracao_horas'] ?? ''));
		$duracaoMinutos = trim((string) ($_POST['duracao_minutos'] ?? ''));
		$descricao = trim((string) ($_POST['descricao'] ?? ''));
		$localizacao = trim((string) ($_POST['localizacao'] ?? ''));
		$valorInput = trim((string) ($_POST['valor'] ?? ''));
		$categoriaInput = trim((string) ($_POST['categoria_id'] ?? ($_POST['categoria'] ?? '')));
		$categorias = $this->categoriaService->getCategorias($empresaId);
		// O administrador pode criar uma categoria nova para a empresa no próprio formulário
		$novaCategoria = $this->isAdminLogado() ? mb_substr(trim((string) ($_POST['nova_categoria'] ?? '')), 0, 100) : '';

		$erros = [];
		$duracaoNormalizada = null;
		if ($nome === '') {
			$erros['nome'] = 'Informe o nome da atividade.';
		}
		if ($descricao === '') {
			$erros['descricao'] = 'Informe a descricao da atividade.';
		}
		$validacaoDuracao = $this->validarDuracao($duracaoHoras, $duracaoMinutos);
		if ($validacaoDuracao['erro'] !== null) {
			$erros['duracao'] = $validacaoDuracao['erro'];
		} else {
			$duracaoNormalizada = $validacaoDuracao['duracao'];
		}
		if ($valorInput === '') {
			$erros['valor'] = 'Informe o valor da atividade.';
		} elseif (!$this->isValidDecimalInput($valorInput)) {
			$erros['valor'] = 'Informe um valor valido usando apenas numeros e ponto (ex: 10.50).';
		}
		if ($novaCategoria !== '') {
			// A categoria nova tem prioridade sobre a selecionada
			$categoriaInput = '';
		} elseif ($categoriaInput === '') {
			$erros['categoria_id'] = $this->isAdminLogado() ? 'Selecione uma categoria ou informe uma nova.' : 'Selecione uma categoria.';
		}

		$categoriaId = null;
		if ($categoriaInput !== '') {
			$categoriaId = (int) $categoriaInput;
			$categoriaValida = false;

			foreach ($categorias as $categoria) {
				if ((int) ($categoria['ID_Categoria'] ?? 0) === $categoriaId) {
					$categoriaValida = true;
					break;
				}
			}

			if (!$categoriaValida) {
				$erros['categoria_id'] = 'Selecione uma categoria valida.';
			}
		}

		if (!empty($erros)) {
			$data['atividade'] = [
				'Nome' => $nome,
				'Duracao_Horas' => $duracaoHoras,
				'Duracao_Minutos' => $duracaoMinutos,
				'Descricao' => $descricao,
				'Localizacao' => $localizacao,
				'Valor' => $valorInput,
				'Categoria_ID' => $categoriaId ?? 0,
				'Nova_Categoria' => $novaCategoria
			];
			$data['categorias'] = $categorias;
			$data['erros'] = $erros;
			$this->view('atividades/atividade_create', $data);
			return;
		}

		$valor = (float) $valorInput;
		$resultadoUpload = UploadImagem::salvar($_FILES['fotos'] ?? null, 'atividades', 'atividade');
		if (!$resultadoUpload['sucesso']) {
			$data['atividade'] = [
				'Nome' => $nome,
				'Duracao_Horas' => $duracaoHoras,
				'Duracao_Minutos' => $duracaoMinutos,
				'Descricao' => $descricao,
				'Localizacao' => $localizacao,
				'Valor' => $valorInput,
				'Categoria_ID' => $categoriaId,
				'Nova_Categoria' => $novaCategoria
			];
			$data['categorias'] = $categorias;
			$data['erros'] = ['fotos' => $resultadoUpload['erro']];
			$this->view('atividades/atividade_create', $data);
			return;
		}

		if ($novaCategoria !== '') {
			$categoriaId = $this->categoriaService->saveCategoriaRetornandoId($novaCategoria, $empresaId);
			if ($categoriaId === null) {
				UploadImagem::remover((string) ($resultadoUpload['foto'] ?? ''), 'atividades');
				echo 'Erro ao salvar a categoria.';
				return;
			}
		}

		$atividade = new Atividade();
		$atividade->setNome($nome);
		$atividade->setDuracao($duracaoNormalizada);
		$atividade->setDescricao($descricao);
		$atividade->setLocalizacao($localizacao);
		$atividade->setValor($valor);
		$atividade->setFotos($resultadoUpload['foto']);
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
		$empresaId = $this->getEmpresaDaTela();
		if ($empresaId === null) {
			$this->redirect(URL_BASE);
		}

		$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
		$data['atividade'] = $this->service->getAtividade($id);
		if (!$data['atividade'] || (int) ($data['atividade']['Empresa_ID'] ?? 0) !== $empresaId) {
			$this->redirect($this->urlListaAtividades());
		}

		$data['categorias'] = $this->categoriaService->getCategorias($empresaId);
		$data['localizacao'] = $this->localizacaoService->getLocalizacaoPorAtividade($id);
		$data['sessoes'] = $this->sessaoService->getSessoesPorAtividade($id);
		$data['sessao_form'] = [];
		$data['sessao_editando_id'] = 0;
		$data['atividade'] = $this->prepararDuracaoParaFormulario($data['atividade']);
		$data['aba_ativa'] = $_GET['tab'] ?? 'atividade';
		$this->view('atividades/atividade_edit', $data);
	}

	public function atualizar() {
		$empresaId = $this->getEmpresaDaTela();
		if ($empresaId === null) {
			$this->redirect(URL_BASE);
		}

		$id = isset($_POST['id']) ? (int) $_POST['id'] : (int) ($_POST['id_atividade'] ?? 0);
		$atividadeAtual = $this->service->getAtividade($id);
		if (!$atividadeAtual || (int) ($atividadeAtual['Empresa_ID'] ?? 0) !== $empresaId) {
			$this->redirect($this->urlListaAtividades());
		}

		$nome = trim((string) ($_POST['nome'] ?? ''));
		$localizacao = trim((string) ($_POST['localizacao'] ?? ''));
		$duracaoHoras = trim((string) ($_POST['duracao_horas'] ?? ''));
		$duracaoMinutos = trim((string) ($_POST['duracao_minutos'] ?? ''));
		$descricao = trim((string) ($_POST['descricao'] ?? ''));
		$valorInput = trim((string) ($_POST['valor'] ?? ''));
		$categoriaInput = trim((string) ($_POST['categoria_id'] ?? ($_POST['categoria'] ?? '')));
		$categorias = $this->categoriaService->getCategorias($empresaId);

		$erros = [];
		$duracaoNormalizada = null;
		if ($nome === '') {
			$erros['nome'] = 'Informe o nome da atividade.';
		}
		if ($descricao === '') {
			$erros['descricao'] = 'Informe a descricao da atividade.';
		}
		$validacaoDuracao = $this->validarDuracao($duracaoHoras, $duracaoMinutos);
		if ($validacaoDuracao['erro'] !== null) {
			$erros['duracao'] = $validacaoDuracao['erro'];
		} else {
			$duracaoNormalizada = $validacaoDuracao['duracao'];
		}
		if ($valorInput === '') {
			$erros['valor'] = 'Informe o valor da atividade.';
		} elseif (!$this->isValidDecimalInput($valorInput)) {
			$erros['valor'] = 'Informe um valor valido usando apenas numeros e ponto (ex: 10.50).';
		}
		if ($categoriaInput === '') {
			$erros['categoria_id'] = 'Selecione uma categoria.';
		}

		$categoriaId = null;
		if ($categoriaInput !== '') {
			$categoriaId = (int) $categoriaInput;
			$categoriaValida = false;

			foreach ($categorias as $categoria) {
				if ((int) ($categoria['ID_Categoria'] ?? 0) === $categoriaId) {
					$categoriaValida = true;
					break;
				}
			}

			if (!$categoriaValida) {
				$erros['categoria_id'] = 'Selecione uma categoria valida.';
			}
		}

		if (!empty($erros)) {
			$data['atividade'] = [
				'ID_Atividade' => $id,
				'Nome' => $nome,
				'Duracao_Horas' => $duracaoHoras,
				'Duracao_Minutos' => $duracaoMinutos,
				'Descricao' => $descricao,
				'Localizacao' => $localizacao,
				'Valor' => $valorInput,
				'Fotos' => $atividadeAtual['Fotos'] ?? null,
				'Categoria_ID' => $categoriaId ?? 0,
				'Empresa_ID' => $empresaId,
				'Categoria_Nome' => $atividadeAtual['Categoria_Nome'] ?? null
			];
			$data['categorias'] = $categorias;
			$data['localizacao'] = $this->localizacaoService->getLocalizacaoPorAtividade($id);
			$data['sessoes'] = $this->sessaoService->getSessoesPorAtividade($id);
			$data['sessao_form'] = [];
			$data['sessao_editando_id'] = 0;
			$data['aba_ativa'] = 'atividade';
			$data['erros'] = $erros;
			$this->view('atividades/atividade_edit', $data);
			return;
		}

		$valor = (float) $valorInput;
		$fotoAtual = (string) ($atividadeAtual['Fotos'] ?? '');
		$resultadoUpload = UploadImagem::salvar($_FILES['fotos'] ?? null, 'atividades', 'atividade');
		if (!$resultadoUpload['sucesso']) {
			$data['atividade'] = [
				'ID_Atividade' => $id,
				'Nome' => $nome,
				'Duracao_Horas' => $duracaoHoras,
				'Duracao_Minutos' => $duracaoMinutos,
				'Descricao' => $descricao,
				'Localizacao' => $localizacao,
				'Valor' => $valorInput,
				'Fotos' => $fotoAtual,
				'Categoria_ID' => $categoriaId ?? 0,
				'Empresa_ID' => $empresaId,
				'Categoria_Nome' => $atividadeAtual['Categoria_Nome'] ?? null
			];
			$data['categorias'] = $categorias;
			$data['localizacao'] = $this->localizacaoService->getLocalizacaoPorAtividade($id);
			$data['sessoes'] = $this->sessaoService->getSessoesPorAtividade($id);
			$data['sessao_form'] = [];
			$data['sessao_editando_id'] = 0;
			$data['aba_ativa'] = 'atividade';
			$data['erros'] = ['fotos' => $resultadoUpload['erro']];
			$this->view('atividades/atividade_edit', $data);
			return;
		}

		$fotoNova = $resultadoUpload['foto'] ?? null;
		$fotos = $fotoNova ?? ($fotoAtual !== '' ? $fotoAtual : null);

		$atividade = new Atividade(
			$id,
			$nome,
			$duracaoNormalizada,
			$descricao,
			$localizacao !== '' ? $localizacao : null,
			$valor,
			$fotos,
			$categoriaId,
			$empresaId
		);

		if (!$this->service->updateAtividade($atividade)) {
			if ($fotoNova !== null) {
				UploadImagem::remover($fotoNova, 'atividades');
			}
			echo 'Erro ao atualizar a atividade.';
			return;
		}

		if ($fotoNova !== null && $fotoAtual !== '') {
			UploadImagem::remover($fotoAtual, 'atividades');
		}

		$this->redirect($this->urlListaAtividades('sucesso=editada'));
	}

	public function localizacaoCadastrar()
	{
		$empresaId = $this->getEmpresaDaTela();
		if ($empresaId === null) {
			$this->redirect(URL_BASE);
		}

		$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
		$atividade = $this->getAtividadeDaEmpresa($id, $empresaId);
		if (!$atividade) {
			$this->redirect($this->urlListaAtividades());
		}

		$data['atividade'] = $atividade;
		$data['localizacao'] = $this->localizacaoService->getLocalizacaoPorAtividade($id);
		$data['origem'] = 'create';
		$this->view('atividades/localizacao_create', $data);
	}

	public function salvarLocalizacao()
	{
		$empresaId = $this->getEmpresaDaTela();
		if ($empresaId === null) {
			$this->redirect(URL_BASE);
		}

		$idAtividade = isset($_POST['id_atividade']) ? (int) $_POST['id_atividade'] : 0;
		$atividade = $this->getAtividadeDaEmpresa($idAtividade, $empresaId);
		if (!$atividade) {
			$this->redirect($this->urlListaAtividades());
		}

		$origem = $_POST['origem'] ?? 'create';
		$cep = trim((string) ($_POST['cep'] ?? ''));
		$complemento = trim((string) ($_POST['complemento'] ?? ''));
		$erros = [];

		if ($cep === '') {
			$erros['cep'] = 'Informe o CEP.';
		} elseif (!$this->isDigitsOnly($cep)) {
			$erros['cep'] = 'O CEP deve conter apenas numeros.';
		} elseif (strlen($cep) !== 8) {
			$erros['cep'] = 'O CEP deve conter exatamente 8 digitos.';
		}
		if ($complemento === '') {
			$erros['complemento'] = 'Informe o complemento.';
		}

		if (!empty($erros)) {
			$data['atividade'] = $atividade;
			// Valores digitados voltam para o formulário; a mensagem depende do que existe no banco
			$data['localizacao'] = [
				'CEP' => $cep,
				'Complemento' => $complemento
			];
			$data['possui_localizacao'] = !empty($this->localizacaoService->getLocalizacaoPorAtividade($idAtividade));
			$data['erros'] = $erros;

			if ($origem === 'editar') {
				$data['categorias'] = $this->categoriaService->getCategorias($empresaId);
				$data['sessoes'] = $this->sessaoService->getSessoesPorAtividade($idAtividade);
				$data['sessao_form'] = [];
				$data['sessao_editando_id'] = 0;
				$data['aba_ativa'] = 'localizacao';
				$this->view('atividades/atividade_edit', $data);
				return;
			}

			$data['origem'] = 'create';
			$this->view('atividades/localizacao_create', $data);
			return;
		}

		$localizacao = new Localizacao();
		$localizacao->setIdAtividade($idAtividade);
		$localizacao->setCep($cep);
		$localizacao->setComplemento($complemento);

		$this->localizacaoService->saveOrUpdateLocalizacao($localizacao);

		if ($origem === 'editar') {
			$this->redirect(URL_BASE . '/atividade/editar?id=' . $idAtividade . '&tab=localizacao');
		}

		$this->redirect(URL_BASE . '/atividade/sessao/cadastrar?id=' . $idAtividade);
	}

	public function salvarSessaoEdicao()
	{
		$empresaId = $this->getEmpresaDaTela();
		if ($empresaId === null) {
			$this->redirect(URL_BASE);
		}

		$idAtividade = isset($_POST['id_atividade']) ? (int) $_POST['id_atividade'] : 0;
		$atividade = $this->getAtividadeDaEmpresa($idAtividade, $empresaId);
		if (!$atividade) {
			$this->redirect($this->urlListaAtividades());
		}

		$validacao = $this->validarCamposSessao($_POST);
		if (!empty($validacao['erros'])) {
			$data['atividade'] = $atividade;
			$data['categorias'] = $this->categoriaService->getCategorias($empresaId);
			$data['localizacao'] = $this->localizacaoService->getLocalizacaoPorAtividade($idAtividade);
			$data['sessoes'] = $this->sessaoService->getSessoesPorAtividade($idAtividade);
			$data['sessao_form'] = $validacao['form'];
			$data['sessao_editando_id'] = 0;
			$data['atividade'] = $this->prepararDuracaoParaFormulario($data['atividade']);
			$data['aba_ativa'] = 'sessoes';
			$data['erros'] = $validacao['erros'];
			$this->view('atividades/atividade_edit', $data);
			return;
		}

		$sessao = new Sessao();
		$sessao->setIdAtividade($idAtividade);
		$sessao->setData($validacao['data']);
		$sessao->setHora($validacao['horaDb']);
		$sessao->setQtdMin($validacao['qtdMin']);
		$sessao->setQtdMax($validacao['qtdMax']);
		$sessao->setQtdDisponivel($validacao['qtdDisponivel']);

		if (!$this->sessaoService->saveSessao($sessao)) {
			$data['atividade'] = $atividade;
			$data['categorias'] = $this->categoriaService->getCategorias($empresaId);
			$data['localizacao'] = $this->localizacaoService->getLocalizacaoPorAtividade($idAtividade);
			$data['sessoes'] = $this->sessaoService->getSessoesPorAtividade($idAtividade);
			$data['sessao_form'] = $validacao['form'];
			$data['sessao_editando_id'] = 0;
			$data['atividade'] = $this->prepararDuracaoParaFormulario($data['atividade']);
			$data['aba_ativa'] = 'sessoes';
			$data['erros'] = ['geral' => 'Erro ao salvar a sessao. Tente novamente.'];
			$this->view('atividades/atividade_edit', $data);
			return;
		}

		$this->redirect(URL_BASE . '/atividade/editar?id=' . $idAtividade . '&tab=sessoes&sucesso_sessao=1');
	}

	public function atualizarSessaoEdicao()
	{
		$empresaId = $this->getEmpresaDaTela();
		if ($empresaId === null) {
			$this->redirect(URL_BASE);
		}

		$idAtividade = isset($_POST['id_atividade']) ? (int) $_POST['id_atividade'] : 0;
		$idSessao = isset($_POST['id_sessao']) ? (int) $_POST['id_sessao'] : 0;
		$atividade = $this->getAtividadeDaEmpresa($idAtividade, $empresaId);
		if (!$atividade) {
			$this->redirect($this->urlListaAtividades());
		}

		$sessaoAtual = $this->sessaoService->getSessaoPorAtividade($idSessao, $idAtividade);
		if (!$sessaoAtual) {
			$this->redirect(URL_BASE . '/atividade/editar?id=' . $idAtividade . '&tab=sessoes');
		}

		$validacao = $this->validarCamposSessao($_POST, true);
		if (empty($validacao['erros']['qtd_max']) && $validacao['qtdMax'] !== null) {
			$erroLimite = $this->sessaoService->validarLimiteReservas($idSessao, $validacao['qtdMax']);
			if ($erroLimite !== null) {
				$validacao['erros']['qtd_max'] = $erroLimite;
			}
		}
		if (!empty($validacao['erros'])) {
			$data['atividade'] = $atividade;
			$data['categorias'] = $this->categoriaService->getCategorias($empresaId);
			$data['localizacao'] = $this->localizacaoService->getLocalizacaoPorAtividade($idAtividade);
			$data['sessoes'] = $this->sessaoService->getSessoesPorAtividade($idAtividade);
			$data['sessao_form'] = $validacao['form'];
			$data['sessao_editando_id'] = $idSessao;
			$data['atividade'] = $this->prepararDuracaoParaFormulario($data['atividade']);
			$data['aba_ativa'] = 'sessoes';
			$data['erros'] = $validacao['erros'];
			$this->view('atividades/atividade_edit', $data);
			return;
		}

		$sessao = new Sessao();
		$sessao->setIdSessao($idSessao);
		$sessao->setIdAtividade($idAtividade);
		$sessao->setData($validacao['data']);
		$sessao->setHora($validacao['horaDb']);
		$sessao->setQtdMin($validacao['qtdMin']);
		$sessao->setQtdMax($validacao['qtdMax']);
		$sessao->setQtdDisponivel($validacao['qtdDisponivel']);

		if (!$this->sessaoService->updateSessao($sessao)) {
			$data['atividade'] = $atividade;
			$data['categorias'] = $this->categoriaService->getCategorias($empresaId);
			$data['localizacao'] = $this->localizacaoService->getLocalizacaoPorAtividade($idAtividade);
			$data['sessoes'] = $this->sessaoService->getSessoesPorAtividade($idAtividade);
			$data['sessao_form'] = $validacao['form'];
			$data['sessao_editando_id'] = $idSessao;
			$data['atividade'] = $this->prepararDuracaoParaFormulario($data['atividade']);
			$data['aba_ativa'] = 'sessoes';
			$data['erros'] = ['geral' => 'Erro ao atualizar a sessao. Tente novamente.'];
			$this->view('atividades/atividade_edit', $data);
			return;
		}

		$this->redirect(URL_BASE . '/atividade/editar?id=' . $idAtividade . '&tab=sessoes&sucesso_sessao=2');
	}

	public function excluirSessaoEdicao()
	{
		$empresaId = $this->getEmpresaDaTela();
		if ($empresaId === null) {
			$this->redirect(URL_BASE);
		}

		$idAtividade = isset($_POST['id_atividade']) ? (int) $_POST['id_atividade'] : 0;
		$idSessao = isset($_POST['id_sessao']) ? (int) $_POST['id_sessao'] : 0;
		$atividade = $this->getAtividadeDaEmpresa($idAtividade, $empresaId);
		if (!$atividade) {
			$this->redirect($this->urlListaAtividades());
		}

		$this->sessaoService->deleteSessao($idSessao, $idAtividade);

		$this->redirect(URL_BASE . '/atividade/editar?id=' . $idAtividade . '&tab=sessoes&sucesso_sessao=3');
	}

	public function sessaoCadastrar()
	{
		$empresaId = $this->getEmpresaDaTela();
		if ($empresaId === null) {
			$this->redirect(URL_BASE);
		}

		$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
		$atividade = $this->getAtividadeDaEmpresa($id, $empresaId);
		if (!$atividade) {
			$this->redirect($this->urlListaAtividades());
		}

		$data['atividade'] = $atividade;
		$data['sessoes'] = $this->sessaoService->getSessoesPorAtividade($id);
		$data['totalSessoes'] = $this->sessaoService->countSessoesPorAtividade($id);
		$data['sessao'] = [];
		$this->view('atividades/sessao_create', $data);
	}

	public function salvarSessao()
	{
		$empresaId = $this->getEmpresaDaTela();
		if ($empresaId === null) {
			$this->redirect(URL_BASE);
		}

		$idAtividade = isset($_POST['id_atividade']) ? (int) $_POST['id_atividade'] : 0;
		$atividade = $this->getAtividadeDaEmpresa($idAtividade, $empresaId);
		if (!$atividade) {
			$this->redirect($this->urlListaAtividades());
		}

		$dataSessao = trim((string) ($_POST['data'] ?? ''));
		$hora = trim((string) ($_POST['hora'] ?? ''));
		$qtdMinInput = trim((string) ($_POST['qtd_min'] ?? ''));
		$qtdMaxInput = trim((string) ($_POST['qtd_max'] ?? ''));
		$qtdDisponivelInput = trim((string) ($_POST['qtd_disponivel'] ?? ''));

		$erros = [];

		if ($dataSessao === '') {
			$erros['data'] = 'Informe a data da sessao.';
		}
		if ($hora === '') {
			$erros['hora'] = 'Informe a hora inicial da sessao.';
		} elseif (!$this->isValidTimeInput($hora)) {
			$erros['hora'] = 'Informe a hora inicial no formato HH:MM.';
		}

		$erroDataHora = $this->validarDataHoraSessao($dataSessao, $hora);
		if ($erroDataHora !== null && !isset($erros['data']) && !isset($erros['hora'])) {
			$erros['data'] = $erroDataHora;
		}

		if ($qtdMinInput === '') {
			$erros['qtd_min'] = 'Informe a quantidade minima.';
		} elseif (!$this->isDigitsOnly($qtdMinInput)) {
			$erros['qtd_min'] = 'A quantidade minima deve conter apenas numeros.';
		}

		if ($qtdMaxInput === '') {
			$erros['qtd_max'] = 'Informe a quantidade maxima.';
		} elseif (!$this->isDigitsOnly($qtdMaxInput)) {
			$erros['qtd_max'] = 'A quantidade maxima deve conter apenas numeros.';
		}

		if ($qtdDisponivelInput === '') {
			$erros['qtd_disponivel'] = 'Informe a quantidade disponivel.';
		} elseif (!$this->isDigitsOnly($qtdDisponivelInput)) {
			$erros['qtd_disponivel'] = 'A quantidade disponivel deve conter apenas numeros.';
		}

		$qtdMin = $qtdMinInput !== '' && $this->isDigitsOnly($qtdMinInput) ? (int) $qtdMinInput : null;
		$qtdMax = $qtdMaxInput !== '' && $this->isDigitsOnly($qtdMaxInput) ? (int) $qtdMaxInput : null;
		$qtdDisponivel = $qtdDisponivelInput !== '' && $this->isDigitsOnly($qtdDisponivelInput) ? (int) $qtdDisponivelInput : null;

		if ($qtdMin !== null && $qtdMax !== null && $qtdMin > $qtdMax) {
			$erros['qtd_min'] = 'A quantidade minima nao pode ser maior que a maxima.';
		}
		if ($qtdMax !== null && $qtdDisponivel !== null && $qtdDisponivel > $qtdMax) {
			$erros['qtd_disponivel'] = 'A quantidade disponivel nao pode ser maior que a maxima.';
		}

		if (!empty($erros)) {
			$data['atividade'] = $atividade;
			$data['sessoes'] = $this->sessaoService->getSessoesPorAtividade($idAtividade);
			$data['totalSessoes'] = $this->sessaoService->countSessoesPorAtividade($idAtividade);
			$data['sessao'] = [
				'Data' => $dataSessao,
				'Hora' => $hora,
				'Qtd_min' => $qtdMinInput,
				'Qtd_max' => $qtdMaxInput,
				'Qtd_disponivel' => $qtdDisponivelInput
			];
			$data['erros'] = $erros;
			$this->view('atividades/sessao_create', $data);
			return;
		}

		$sessao = new Sessao();
		$sessao->setIdAtividade($idAtividade);
		$sessao->setData($dataSessao);
		$sessao->setHora($this->normalizeTimeInputForDb($hora));
		$sessao->setQtdMin($qtdMin);
		$sessao->setQtdMax($qtdMax);
		$sessao->setQtdDisponivel($qtdDisponivel);

		if (!$this->sessaoService->saveSessao($sessao)) {
			$data['atividade'] = $atividade;
			$data['sessoes'] = $this->sessaoService->getSessoesPorAtividade($idAtividade);
			$data['totalSessoes'] = $this->sessaoService->countSessoesPorAtividade($idAtividade);
			$data['sessao'] = [
				'Data' => $dataSessao,
				'Hora' => $hora,
				'Qtd_min' => $qtdMinInput,
				'Qtd_max' => $qtdMaxInput,
				'Qtd_disponivel' => $qtdDisponivelInput
			];
			$data['erros'] = ['geral' => 'Erro ao salvar a sessao. Tente novamente.'];
			$this->view('atividades/sessao_create', $data);
			return;
		}

		$this->redirect(URL_BASE . '/atividade/sessao/cadastrar?id=' . $idAtividade . '&sucesso=1');
	}

	public function concluirCadastro()
	{
		$empresaId = $this->getEmpresaDaTela();
		if ($empresaId === null) {
			$this->redirect(URL_BASE);
		}

		$idAtividade = isset($_POST['id_atividade']) ? (int) $_POST['id_atividade'] : 0;
		$atividade = $this->getAtividadeDaEmpresa($idAtividade, $empresaId);
		if (!$atividade) {
			$this->redirect($this->urlListaAtividades());
		}

		$totalSessoes = $this->sessaoService->countSessoesPorAtividade($idAtividade);
		if ($totalSessoes < 1) {
			$data['atividade'] = $atividade;
			$data['sessoes'] = $this->sessaoService->getSessoesPorAtividade($idAtividade);
			$data['totalSessoes'] = $totalSessoes;
			$data['sessao'] = [];
			$data['erros'] = ['geral' => 'Cadastre pelo menos 1 sessao para concluir a atividade.'];
			$this->view('atividades/sessao_create', $data);
			return;
		}

		$this->redirect($this->urlListaAtividades('sucesso=criada'));
	}

	public function verAtividade() {
		if (!isset($_GET['id'])) {
			$this->redirect(URL_BASE . '/');
		}

		$id = (int) $_GET['id'];
		$data['atividade'] = $this->service->getAtividade($id);

		// RN02: atividade de empresa não aprovada só é visível para a própria empresa e para o administrador
		if ($data['atividade'] && strtolower(trim((string) ($data['atividade']['Empresa_Status'] ?? ''))) !== 'aprovada') {
			$empresaLogada = $_SESSION['empresa_logada'] ?? null;
			$propria = is_object($empresaLogada) && (int) $empresaLogada->getIdEmpresa() === (int) ($data['atividade']['Empresa_ID'] ?? 0);
			if (!$propria && !$this->isAdminLogado()) {
				$this->redirect(URL_BASE . '/');
			}
		}

		$data['sessoes'] = $this->sessaoService->getSessoesDisponiveisPorAtividade($id);
		$data['avaliacoes'] = $this->avaliacaoService->getAvaliacoesPorAtividade($id);
		$data['resumo_avaliacoes'] = $this->avaliacaoService->getResumoPorAtividade($id);
		$data['aba_ativa'] = $_GET['tab'] ?? 'dados';

		$usuarioLogado = $_SESSION['usuario_logado'] ?? null;
		$clienteId = is_object($usuarioLogado) && method_exists($usuarioLogado, 'getIdUsuario') ? (int) $usuarioLogado->getIdUsuario() : 0;
		$data['favoritado'] = $clienteId > 0 ? $this->favoritoService->isFavorito($clienteId, $id) : false;

		// RF12: o cliente avalia, edita ou exclui a própria avaliação pela página da atividade
		$data['minha_avaliacao'] = $clienteId > 0 ? $this->avaliacaoService->getAvaliacaoDoUsuario($clienteId, $id) : null;
		$data['pode_avaliar'] = $clienteId > 0 && $this->avaliacaoService->podeAvaliar($clienteId, $id);

		$this->view('atividades/atividade_show', $data);
	}

	// Exclusão por POST (o formulário pede confirmação); vale para a empresa e para o administrador
	public function excluir() {
		$empresaId = $this->getEmpresaDaTela();
		if ($empresaId === null) {
			$this->redirect(URL_BASE);
		}

		$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
		$atividade = $this->service->getAtividade($id);
		if (!$atividade || (int) ($atividade['Empresa_ID'] ?? 0) !== $empresaId) {
			$this->redirect($this->urlListaAtividades('erro=atividade'));
		}

		if ($this->service->deleteAtividade($id, $empresaId)) {
			$this->redirect($this->urlListaAtividades('sucesso=excluida'));
		}

		$this->redirect($this->urlListaAtividades('erro=excluir'));
	}

	// Lista de atividades de uma empresa: a própria empresa aprovada ou o administrador (?empresa=ID)
	public function listarDaEmpresa() {
		if ($this->isAdminLogado() && isset($_GET['empresa'])) {
			$empresa = (new EmpresaService())->getEmpresa((int) $_GET['empresa']);
			if (!$empresa) {
				$this->redirect(URL_BASE . '/admin?erro=empresa');
			}
			$_SESSION['admin_empresa_alvo'] = ['id' => (int) $empresa['ID_Empresa'], 'nome' => (string) ($empresa['Name'] ?? '')];
		}

		$empresaId = $this->getEmpresaDaTela();
		if ($empresaId === null) {
			$this->redirect($this->isAdminLogado() ? URL_BASE . '/admin' : URL_BASE);
		}

		$empresa = (new EmpresaService())->getEmpresa($empresaId);
		if (!$empresa) {
			unset($_SESSION['admin_empresa_alvo']);
			$this->redirect($this->isAdminLogado() ? URL_BASE . '/admin?erro=empresa' : URL_BASE);
		}

		$agora = new DateTime();
		$data['empresa_lista'] = $empresa;
		$data['pode_criar'] = strtolower(trim((string) ($empresa['Status'] ?? ''))) === EmpresaService::STATUS_APROVADA;
		$data['atividades'] = $this->service->getAtividadesDaEmpresaComResumo($empresaId, $agora->format('Y-m-d'), $agora->format('H:i:s'));
		$this->view('atividades/atividades_empresa', $data);
	}

	// Empresa dona das atividades nesta tela:
	// - empresa logada: a própria, se aprovada (RN02)
	// - administrador: a empresa escolhida no painel (botão "Atividades" ou "Criar atividade")
	private function getEmpresaDaTela(): ?int
	{
		if ($this->isAdminLogado()) {
			$alvo = (int) ($_SESSION['admin_empresa_alvo']['id'] ?? 0);

			return $alvo > 0 ? $alvo : null;
		}

		return $this->getEmpresaAprovadaId();
	}

	// RN02: o administrador só cria atividade para empresa aprovada
	private function getEmpresaParaNovaAtividade(): ?int
	{
		$empresaId = $this->getEmpresaDaTela();
		if ($empresaId !== null && $this->isAdminLogado() && !(new EmpresaService())->isAprovada($empresaId)) {
			return null;
		}

		return $empresaId;
	}


	// Volta para a lista de atividades da empresa (do administrador, a da empresa escolhida)
	private function urlListaAtividades(string $query = ''): string
	{
		return URL_BASE . '/empresa/atividades' . ($query !== '' ? '?' . $query : '');
	}

	private function getAtividadeDaEmpresa(int $id, int $empresaId): ?array
	{
		$atividade = $this->service->getAtividade($id);
		if (!$atividade || (int) ($atividade['Empresa_ID'] ?? 0) !== $empresaId) {
			return null;
		}

		return $atividade;
	}

	private function isDigitsOnly(string $value): bool
	{
		return preg_match('/^\d+$/', $value) === 1;
	}

	private function isValidTimeInput(string $value): bool
	{
		return preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $value) === 1;
	}

	private function normalizeTimeInputForDb(string $value): string
	{
		return $value . ':00';
	}

	private function validarDuracao(string $horas, string $minutos): array
	{
		if ($horas === '' && $minutos === '') {
			return ['duracao' => null, 'erro' => 'Informe a duracao da atividade.'];
		}

		$horas = $horas === '' ? '0' : $horas;
		$minutos = $minutos === '' ? '0' : $minutos;

		if (!$this->isDigitsOnly($horas) || !$this->isDigitsOnly($minutos)) {
			return ['duracao' => null, 'erro' => 'Informe horas e minutos usando apenas numeros inteiros.'];
		}

		$horasInt = (int) $horas;
		$minutosInt = (int) $minutos;

		if ($minutosInt > 59) {
			return ['duracao' => null, 'erro' => 'Os minutos devem estar entre 0 e 59.'];
		}

		// Limite do tipo TIME do MySQL
		if ($horasInt > 838) {
			return ['duracao' => null, 'erro' => 'A duracao maxima e de 838 horas.'];
		}

		if ($horasInt === 0 && $minutosInt === 0) {
			return ['duracao' => null, 'erro' => 'A duracao deve ser maior que zero.'];
		}

		return ['duracao' => sprintf('%02d:%02d:00', $horasInt, $minutosInt), 'erro' => null];
	}

	private function prepararDuracaoParaFormulario(array $atividade): array
	{
		$duracao = trim((string) ($atividade['Duracao'] ?? ''));

		if (preg_match('/^(\d+):([0-5]\d)(:[0-5]\d)?$/', $duracao, $partes) === 1) {
			$atividade['Duracao_Horas'] = (int) $partes[1];
			$atividade['Duracao_Minutos'] = (int) $partes[2];
		}

		return $atividade;
	}

	private function isValidDecimalInput(string $value): bool
	{
		return preg_match('/^\d+(\.\d{1,2})?$/', $value) === 1;
	}

	// RN04: a sessao deve ter data valida e acontecer em data e horario futuros
	private function validarDataHoraSessao(string $dataSessao, string $hora): ?string
	{
		if ($dataSessao === '' || !$this->isValidTimeInput($hora)) {
			return null;
		}

		$data = DateTime::createFromFormat('!Y-m-d', $dataSessao);
		if ($data === false || $data->format('Y-m-d') !== $dataSessao) {
			return 'Informe uma data valida.';
		}

		if (new DateTime($dataSessao . ' ' . $hora) <= new DateTime()) {
			return 'A sessao deve ser em uma data e horario futuros.';
		}

		return null;
	}

	// Na edição ($edicao = true) a quantidade disponível é calculada pelo sistema e não é validada aqui
	private function validarCamposSessao(array $input, bool $edicao = false): array
	{
		$dataSessao = trim((string) ($input['data'] ?? ''));
		$hora = trim((string) ($input['hora'] ?? ''));
		$qtdMinInput = trim((string) ($input['qtd_min'] ?? ''));
		$qtdMaxInput = trim((string) ($input['qtd_max'] ?? ''));
		$qtdDisponivelInput = trim((string) ($input['qtd_disponivel'] ?? ''));

		$erros = [];

		if ($dataSessao === '') {
			$erros['data'] = 'Informe a data da sessao.';
		}
		if ($hora === '') {
			$erros['hora'] = 'Informe a hora inicial da sessao.';
		} elseif (!$this->isValidTimeInput($hora)) {
			$erros['hora'] = 'Informe a hora inicial no formato HH:MM.';
		}

		$erroDataHora = $this->validarDataHoraSessao($dataSessao, $hora);
		if ($erroDataHora !== null && !isset($erros['data']) && !isset($erros['hora'])) {
			$erros['data'] = $erroDataHora;
		}

		if ($qtdMinInput === '') {
			$erros['qtd_min'] = 'Informe a quantidade minima.';
		} elseif (!$this->isDigitsOnly($qtdMinInput)) {
			$erros['qtd_min'] = 'A quantidade minima deve conter apenas numeros.';
		}

		if ($qtdMaxInput === '') {
			$erros['qtd_max'] = 'Informe a quantidade maxima.';
		} elseif (!$this->isDigitsOnly($qtdMaxInput)) {
			$erros['qtd_max'] = 'A quantidade maxima deve conter apenas numeros.';
		}

		if (!$edicao) {
			if ($qtdDisponivelInput === '') {
				$erros['qtd_disponivel'] = 'Informe a quantidade disponivel.';
			} elseif (!$this->isDigitsOnly($qtdDisponivelInput)) {
				$erros['qtd_disponivel'] = 'A quantidade disponivel deve conter apenas numeros.';
			}
		}

		$qtdMin = $qtdMinInput !== '' && $this->isDigitsOnly($qtdMinInput) ? (int) $qtdMinInput : null;
		$qtdMax = $qtdMaxInput !== '' && $this->isDigitsOnly($qtdMaxInput) ? (int) $qtdMaxInput : null;
		$qtdDisponivel = $qtdDisponivelInput !== '' && $this->isDigitsOnly($qtdDisponivelInput) ? (int) $qtdDisponivelInput : null;

		if ($qtdMin !== null && $qtdMax !== null && $qtdMin > $qtdMax) {
			$erros['qtd_min'] = 'A quantidade minima nao pode ser maior que a maxima.';
		}
		if (!$edicao && $qtdMax !== null && $qtdDisponivel !== null && $qtdDisponivel > $qtdMax) {
			$erros['qtd_disponivel'] = 'A quantidade disponivel nao pode ser maior que a maxima.';
		}

		return [
			'erros' => $erros,
			'form' => [
				'Data' => $dataSessao,
				'Hora' => $hora,
				'Qtd_min' => $qtdMinInput,
				'Qtd_max' => $qtdMaxInput,
				'Qtd_disponivel' => $qtdDisponivelInput
			],
			'data' => $dataSessao,
			'horaDb' => $this->isValidTimeInput($hora) ? $this->normalizeTimeInputForDb($hora) : $hora,
			'qtdMin' => $qtdMin,
			'qtdMax' => $qtdMax,
			'qtdDisponivel' => $qtdDisponivel
		];
	}



}
