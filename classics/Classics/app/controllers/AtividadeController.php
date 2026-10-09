<?php 

namespace app\controllers;

use app\core\Controller;
use app\models\Atividade;
use app\models\Localizacao;
use app\models\Sessao;
use app\services\AtividadeService;
use app\services\CategoriaService;
use app\services\LocalizacaoService;
use app\services\SessaoService;

class AtividadeController extends Controller {
	private const TAMANHO_MAX_FOTO = 5_242_880; // 5mb de tamanha maximo;

	private AtividadeService $service;
	private CategoriaService $categoriaService;
	private LocalizacaoService $localizacaoService;
	private SessaoService $sessaoService;

	public function __construct()
	{
		$this->service = new AtividadeService();
		$this->categoriaService = new CategoriaService();
		$this->localizacaoService = new LocalizacaoService();
		$this->sessaoService = new SessaoService();
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
		$empresaId = $this->getEmpresaLogadaId();
		if ($empresaId === null) {
			$this->redirect(URL_BASE);
		}

		$nome = trim((string) ($_POST['nome'] ?? ''));
		$duracao = trim((string) ($_POST['duracao'] ?? ''));
		$descricao = trim((string) ($_POST['descricao'] ?? ''));
		$localizacao = trim((string) ($_POST['localizacao'] ?? ''));
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
		if ($duracao === '') {
			$erros['duracao'] = 'Informe a duracao da atividade.';
		} elseif (!$this->isValidTimeInput($duracao)) {
			$erros['duracao'] = 'Informe a duracao no formato HH:MM.';
		} else {
			$duracaoNormalizada = $this->normalizeTimeInputForDb($duracao);
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
				'Nome' => $nome,
				'Duracao' => $duracao,
				'Descricao' => $descricao,
				'Localizacao' => $localizacao,
				'Valor' => $valorInput,
				'Categoria_ID' => $categoriaId ?? 0
			];
			$data['categorias'] = $categorias;
			$data['erros'] = $erros;
			$this->view('atividades/atividade_create', $data);
			return;
		}

		$valor = (float) $valorInput;
		$resultadoUpload = $this->processarUploadFoto($_FILES['fotos'] ?? null);
		if (!$resultadoUpload['sucesso']) {
			$data['atividade'] = [
				'Nome' => $nome,
				'Duracao' => $duracao,
				'Descricao' => $descricao,
				'Localizacao' => $localizacao,
				'Valor' => $valorInput,
				'Categoria_ID' => $categoriaId
			];
			$data['categorias'] = $categorias;
			$data['erros'] = ['fotos' => $resultadoUpload['erro']];
			$this->view('atividades/atividade_create', $data);
			return;
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
		$data['sessoes'] = $this->sessaoService->getSessoesPorAtividade($id);
		$data['sessao_form'] = [];
		$data['sessao_editando_id'] = 0;
		$data['atividade']['Duracao'] = $this->formatTimeForInput((string) ($data['atividade']['Duracao'] ?? ''));
		$data['aba_ativa'] = $_GET['tab'] ?? 'atividade';
		$this->view('atividades/atividade_edit', $data);
	}

	public function atualizar() {
		$empresaId = $this->getEmpresaLogadaId();
		if ($empresaId === null) {
			$this->redirect(URL_BASE);
		}

		$id = isset($_POST['id']) ? (int) $_POST['id'] : (int) ($_POST['id_atividade'] ?? 0);
		$atividadeAtual = $this->service->getAtividade($id);
		if (!$atividadeAtual || (int) ($atividadeAtual['Empresa_ID'] ?? 0) !== $empresaId) {
			$this->redirect(URL_BASE . '/atividade/listar');
		}

		$nome = trim((string) ($_POST['nome'] ?? ''));
		$duracao = trim((string) ($_POST['duracao'] ?? ''));
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
		if ($duracao === '') {
			$erros['duracao'] = 'Informe a duracao da atividade.';
		} elseif (!$this->isValidTimeInput($duracao)) {
			$erros['duracao'] = 'Informe a duracao no formato HH:MM.';
		} else {
			$duracaoNormalizada = $this->normalizeTimeInputForDb($duracao);
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
				'Duracao' => $duracao,
				'Descricao' => $descricao,
				'Localizacao' => $atividadeAtual['Localizacao'] ?? null,
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
		$resultadoUpload = $this->processarUploadFoto($_FILES['fotos'] ?? null);
		if (!$resultadoUpload['sucesso']) {
			$data['atividade'] = [
				'ID_Atividade' => $id,
				'Nome' => $nome,
				'Duracao' => $duracao,
				'Descricao' => $descricao,
				'Localizacao' => $atividadeAtual['Localizacao'] ?? null,
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
			$atividadeAtual['Localizacao'] ?? null,
			$valor,
			$fotos,
			$categoriaId,
			$empresaId
		);

		if (!$this->service->updateAtividade($atividade)) {
			if ($fotoNova !== null) {
				$this->removerFotoLocal($fotoNova);
			}
			echo 'Erro ao atualizar a atividade.';
			return;
		}

		if ($fotoNova !== null && $fotoAtual !== '') {
			$this->removerFotoLocal($fotoAtual);
		}

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
			$data['localizacao'] = [
				'CEP' => $cep,
				'Complemento' => $complemento
			];
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
		$empresaId = $this->getEmpresaLogadaId();
		if ($empresaId === null) {
			$this->redirect(URL_BASE);
		}

		$idAtividade = isset($_POST['id_atividade']) ? (int) $_POST['id_atividade'] : 0;
		$atividade = $this->getAtividadeDaEmpresa($idAtividade, $empresaId);
		if (!$atividade) {
			$this->redirect(URL_BASE . '/atividade/listar');
		}

		$validacao = $this->validarCamposSessao($_POST);
		if (!empty($validacao['erros'])) {
			$data['atividade'] = $atividade;
			$data['categorias'] = $this->categoriaService->getCategorias($empresaId);
			$data['localizacao'] = $this->localizacaoService->getLocalizacaoPorAtividade($idAtividade);
			$data['sessoes'] = $this->sessaoService->getSessoesPorAtividade($idAtividade);
			$data['sessao_form'] = $validacao['form'];
			$data['sessao_editando_id'] = 0;
			$data['atividade']['Duracao'] = $this->formatTimeForInput((string) ($data['atividade']['Duracao'] ?? ''));
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
			$data['atividade']['Duracao'] = $this->formatTimeForInput((string) ($data['atividade']['Duracao'] ?? ''));
			$data['aba_ativa'] = 'sessoes';
			$data['erros'] = ['geral' => 'Erro ao salvar a sessao. Tente novamente.'];
			$this->view('atividades/atividade_edit', $data);
			return;
		}

		$this->redirect(URL_BASE . '/atividade/editar?id=' . $idAtividade . '&tab=sessoes&sucesso_sessao=1');
	}

	public function atualizarSessaoEdicao()
	{
		$empresaId = $this->getEmpresaLogadaId();
		if ($empresaId === null) {
			$this->redirect(URL_BASE);
		}

		$idAtividade = isset($_POST['id_atividade']) ? (int) $_POST['id_atividade'] : 0;
		$idSessao = isset($_POST['id_sessao']) ? (int) $_POST['id_sessao'] : 0;
		$atividade = $this->getAtividadeDaEmpresa($idAtividade, $empresaId);
		if (!$atividade) {
			$this->redirect(URL_BASE . '/atividade/listar');
		}

		$sessaoAtual = $this->sessaoService->getSessaoPorAtividade($idSessao, $idAtividade);
		if (!$sessaoAtual) {
			$this->redirect(URL_BASE . '/atividade/editar?id=' . $idAtividade . '&tab=sessoes');
		}

		$validacao = $this->validarCamposSessao($_POST);
		if (!empty($validacao['erros'])) {
			$data['atividade'] = $atividade;
			$data['categorias'] = $this->categoriaService->getCategorias($empresaId);
			$data['localizacao'] = $this->localizacaoService->getLocalizacaoPorAtividade($idAtividade);
			$data['sessoes'] = $this->sessaoService->getSessoesPorAtividade($idAtividade);
			$data['sessao_form'] = $validacao['form'];
			$data['sessao_editando_id'] = $idSessao;
			$data['atividade']['Duracao'] = $this->formatTimeForInput((string) ($data['atividade']['Duracao'] ?? ''));
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
			$data['atividade']['Duracao'] = $this->formatTimeForInput((string) ($data['atividade']['Duracao'] ?? ''));
			$data['aba_ativa'] = 'sessoes';
			$data['erros'] = ['geral' => 'Erro ao atualizar a sessao. Tente novamente.'];
			$this->view('atividades/atividade_edit', $data);
			return;
		}

		$this->redirect(URL_BASE . '/atividade/editar?id=' . $idAtividade . '&tab=sessoes&sucesso_sessao=2');
	}

	public function excluirSessaoEdicao()
	{
		$empresaId = $this->getEmpresaLogadaId();
		if ($empresaId === null) {
			$this->redirect(URL_BASE);
		}

		$idAtividade = isset($_POST['id_atividade']) ? (int) $_POST['id_atividade'] : 0;
		$idSessao = isset($_POST['id_sessao']) ? (int) $_POST['id_sessao'] : 0;
		$atividade = $this->getAtividadeDaEmpresa($idAtividade, $empresaId);
		if (!$atividade) {
			$this->redirect(URL_BASE . '/atividade/listar');
		}

		$this->sessaoService->deleteSessao($idSessao, $idAtividade);

		$this->redirect(URL_BASE . '/atividade/editar?id=' . $idAtividade . '&tab=sessoes&sucesso_sessao=3');
	}

	public function sessaoCadastrar()
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
		$data['sessoes'] = $this->sessaoService->getSessoesPorAtividade($id);
		$data['totalSessoes'] = $this->sessaoService->countSessoesPorAtividade($id);
		$data['sessao'] = [];
		$this->view('atividades/sessao_create', $data);
	}

	public function salvarSessao()
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
		$empresaId = $this->getEmpresaLogadaId();
		if ($empresaId === null) {
			$this->redirect(URL_BASE);
		}

		$idAtividade = isset($_POST['id_atividade']) ? (int) $_POST['id_atividade'] : 0;
		$atividade = $this->getAtividadeDaEmpresa($idAtividade, $empresaId);
		if (!$atividade) {
			$this->redirect(URL_BASE . '/atividade/listar');
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

		$this->redirect(URL_BASE . '/atividade/listar');
	}

	public function verAtividade() {
		if (!isset($_GET['id'])) {
			$this->redirect(URL_BASE . '/atividade/listar');
		}

		$id = (int) $_GET['id'];
		$data['atividade'] = $this->service->getAtividade($id);
		if (!empty($data['atividade'])) {
			$data['atividade']['Duracao'] = $this->formatTimeForInput((string) ($data['atividade']['Duracao'] ?? ''));
		}
		$data['sessoes'] = $this->sessaoService->getSessoesPorAtividade($id);
		$data['aba_ativa'] = $_GET['tab'] ?? 'dados';
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

	private function formatTimeForInput(string $value): string
	{
		if (preg_match('/^([01]\d|2[0-3]):[0-5]\d:[0-5]\d$/', $value) === 1) {
			return substr($value, 0, 5);
		}

		return $value;
	}

	private function isValidDecimalInput(string $value): bool
	{
		return preg_match('/^\d+(\.\d{1,2})?$/', $value) === 1;
	}

	private function validarCamposSessao(array $input): array
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

	private function processarUploadFoto(?array $arquivo): array
	{
		if ($arquivo === null || !isset($arquivo['error']) || (int) $arquivo['error'] === UPLOAD_ERR_NO_FILE) {
			return ['sucesso' => true, 'foto' => null, 'erro' => null];
		}

		if ((int) $arquivo['error'] !== UPLOAD_ERR_OK) {
			return ['sucesso' => false, 'foto' => null, 'erro' => 'Falha no upload da imagem.'];
		}

		if ((int) ($arquivo['size'] ?? 0) > self::TAMANHO_MAX_FOTO) {
			return ['sucesso' => false, 'foto' => null, 'erro' => 'A imagem deve ter no máximo 5MB.'];
		}

		$tmp = (string) ($arquivo['tmp_name'] ?? '');
		if ($tmp === '' || !is_uploaded_file($tmp)) {
			return ['sucesso' => false, 'foto' => null, 'erro' => 'Arquivo de imagem inválido.'];
		}

		$finfo = finfo_open(FILEINFO_MIME_TYPE); //Valida o tipo do arquivo verdadeiro
		if ($finfo === false) {
			return ['sucesso' => false, 'foto' => null, 'erro' => 'Não foi possível validar o arquivo enviado.'];
		}

		$mime = finfo_file($finfo, $tmp); 
		finfo_close($finfo);
		$extensoesPermitidas = [
			'image/jpeg' => 'jpg',
			'image/png' => 'png',
			'image/webp' => 'webp'
		];

		if (!isset($extensoesPermitidas[$mime]) || getimagesize($tmp) === false) {
			return ['sucesso' => false, 'foto' => null, 'erro' => 'Formato de imagem inválido. Use JPG, PNG ou WEBP.'];
		}

		try {
			$identificador = bin2hex(random_bytes(8)); // Gera um identificador único para o nome do arquivo.
		} catch (\Exception $e) {
			return ['sucesso' => false, 'foto' => null, 'erro' => 'Não foi possível gerar o nome da imagem.'];
		}

		$nomeArquivo = 'atividade_' . $identificador . '.' . $extensoesPermitidas[$mime]; // Gera o nome da foto.
		$caminhoRelativo = 'uploads/atividades/' . $nomeArquivo;
		$diretorioUpload = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'atividades';

		if (!is_dir($diretorioUpload) && !mkdir($diretorioUpload, 0775, true) && !is_dir($diretorioUpload)) {
			return ['sucesso' => false, 'foto' => null, 'erro' => 'Não foi possível preparar o diretório de upload.'];
		}

		$destino = $diretorioUpload . DIRECTORY_SEPARATOR . $nomeArquivo;
		if (!move_uploaded_file($tmp, $destino)) {
			return ['sucesso' => false, 'foto' => null, 'erro' => 'Não foi possível salvar a imagem enviada.'];
		}

		return ['sucesso' => true, 'foto' => $caminhoRelativo, 'erro' => null];
	}

	private function removerFotoLocal(string $caminhoRelativo): void
	{
		$prefixo = 'uploads/atividades/';
		if ($caminhoRelativo === '' || strpos($caminhoRelativo, $prefixo) !== 0) {
			return;
		}

		$caminhoCompleto = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $caminhoRelativo);
		if (is_file($caminhoCompleto)) {
			unlink($caminhoCompleto);
		}
	}

}
