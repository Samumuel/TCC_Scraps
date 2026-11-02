<?php 

namespace app\controllers;

use app\core\Controller;
use app\helpers\Validador;
use app\models\Empresa;
use app\services\EmpresaService;

class EmpresaController extends Controller {
    
    private EmpresaService $service;

    public function __construct()
    {
        $this->service = new EmpresaService();
    }

    // Lista e cadastro pelo painel ficam restritos ao administrador (ainda não implementado)
    public function listarTodos() {
        $this->redirect(URL_BASE);
    }

    public function criar() {
        $this->redirect(URL_BASE);
    }

    public function salvar() {
        $nome = $_POST['nome'];
        $email = trim((string) ($_POST['email'] ?? ''));
        $cnpj = $_POST['cnpj'] ?? null;
        $localizacao = $_POST['localizacao'] ?? null;
        $telefone = $_POST['telefone'] ?? null;
        $senha = $_POST['senha'];
        $cadastroPublico = isset($_POST['cadastro_publico']);

        // Somente o cadastro público está disponível
        if (!$cadastroPublico) {
            $this->redirect(URL_BASE);
        }

        $validador = new Validador();
        $validador->obrigatorio('email', $email, 'O e-mail da empresa é obrigatório.');
        $validador->email('email', $email);
        $validador->cnpj('cnpj', $cnpj);
        $validador->telefone('telefone', $telefone);
        if ($validador->temErros()) {
            $data['empresa'] = [
                'nome' => $nome,
                'email' => $email,
                'cnpj' => $cnpj,
                'localizacao' => $localizacao,
                'telefone' => $telefone,
                'status' => 'pendente'
            ];
            $data['erros'] = $validador->getErros();
            $data['tipo_cadastro'] = 'empresa';
            $this->view($cadastroPublico ? 'autenticacao/cadastrar' : 'empresas/empresa_create', $data);
            return;
        }

        $empresa = new Empresa();
        $empresa->setNome($nome);
        $empresa->setEmail($email);
        $empresa->setCnpj($cnpj);
        $empresa->setLocalizacao($localizacao);
        $empresa->setTelefone($telefone);
        $empresa->setSenha($senha);
        // RN02: toda empresa nova começa pendente de aprovação
        $empresa->setStatus('pendente');

        if ($this->service->saveEmpresa($empresa)) {
            $this->redirect(URL_BASE);
        } else {
            $data['empresa'] = [
                'nome' => $nome,
                'email' => $email,
                'cnpj' => $cnpj,
                'localizacao' => $localizacao,
                'telefone' => $telefone,
                'status' => 'pendente'
            ];
            $data['erros']['email'] = "Este e-mail já está cadastrado.";
            $data['tipo_cadastro'] = 'empresa';
            $this->view($cadastroPublico ? 'autenticacao/cadastrar' : 'empresas/empresa_create', $data);
        }
    }

    public function editar() {
        $empresaId = $this->getEmpresaLogadaId();
        if ($empresaId === null) {
            $this->redirect(URL_BASE);
        }

        // A empresa só pode editar o próprio cadastro
        $data['empresa'] = $this->service->getEmpresa($empresaId);
        if (!$data['empresa']) {
            $this->redirect(URL_BASE);
        }

        $this->view('empresas/empresa_edit', $data);
    }

    public function atualizar() {
        $empresaId = $this->getEmpresaLogadaId();
        if ($empresaId === null) {
            $this->redirect(URL_BASE);
        }

        $validador = new Validador();
        $email = trim((string) ($_POST['email'] ?? ''));
        $validador->obrigatorio('email', $email, 'O e-mail da empresa é obrigatório.');
        $validador->email('email', $email);
        $validador->cnpj('cnpj', $_POST['cnpj'] ?? '');
        $validador->telefone('telefone', $_POST['telefone'] ?? '');
        if ($validador->temErros()) {
            $data['empresa'] = $this->montarDadosEmpresaFormulario($empresaId);
            $data['erros'] = $validador->getErros();
            $this->view('empresas/empresa_edit', $data);
            return;
        }

        // O id vem da sessão; status e senha vazia são mantidos pelo service
        $empresa = new Empresa(
            $empresaId,
            (string) ($_POST['nome'] ?? ''),
            $email,
            $_POST['cnpj'] ?? '',
            $_POST['localizacao'] ?? '',
            $_POST['telefone'] ?? '',
            (string) ($_POST['senha'] ?? '')
        );

        if ($this->service->updateEmpresa($empresa)) {
            $empresaAtualizada = $this->service->getEmpresa($empresaId);
            if ($empresaAtualizada) {
                $_SESSION['empresa_logada'] = Empresa::arrayParaObjeto($empresaAtualizada);
            }
            $this->redirect(URL_BASE . '/empresa/ver?id=' . $empresaId);
        } else {
            $data['empresa'] = $this->montarDadosEmpresaFormulario($empresaId);
            $data['erros']['email'] = "Este e-mail já está cadastrado.";
            $this->view('empresas/empresa_edit', $data);
        }
    }

    public function verEmpresa() {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id <= 0) {
            $this->redirect(URL_BASE);
        }

        $data['empresa'] = $this->service->getEmpresa($id);
        $data['empresa_propria'] = $id === $this->getEmpresaLogadaId();
        $this->view('empresas/empresa_show', $data);
    }

    // Exclusão de empresas ficará restrita ao administrador (ainda não implementado)
    public function excluir() {
        $this->redirect(URL_BASE);
    }

    private function montarDadosEmpresaFormulario(int $empresaId): array
    {
        return [
            'ID_Empresa' => $empresaId,
            'nome' => $_POST['nome'] ?? '',
            'email' => trim((string) ($_POST['email'] ?? '')),
            'cnpj' => $_POST['cnpj'] ?? '',
            'localizacao' => $_POST['localizacao'] ?? '',
            'telefone' => $_POST['telefone'] ?? ''
        ];
    }

    private function getEmpresaLogadaId(): ?int
    {
        if (!isset($_SESSION['empresa_logada']) || !is_object($_SESSION['empresa_logada'])) {
            return null;
        }

        return (int) $_SESSION['empresa_logada']->getIdEmpresa();
    }
}