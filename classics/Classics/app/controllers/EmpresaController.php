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

    public function listarTodos() {
        $data['lista'] = $this->service->getEmpresas();
        $this->view('empresas/empresa_list', $data);
    }

    public function criar() {
        $this->view('empresas/empresa_create', []);
    }

    public function salvar() {
        $nome = $_POST['nome'];
        $email = trim((string) ($_POST['email'] ?? ''));
        $cnpj = $_POST['cnpj'] ?? null;
        $localizacao = $_POST['localizacao'] ?? null;
        $telefone = $_POST['telefone'] ?? null;
        $senha = $_POST['senha'];
        $cadastroPublico = isset($_POST['cadastro_publico']);

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
                'status' => $_POST['status'] ?? 'pendente'
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
        // Garantir que status seja 'pendente' por padrão ao cadastrar
        $empresa->setStatus($_POST['status'] ?? 'pendente');

        if ($this->service->saveEmpresa($empresa)) {
            $this->redirect(URL_BASE . '/empresa');
        } else {
            $data['empresa'] = [
                'nome' => $nome,
                'email' => $email,
                'cnpj' => $cnpj,
                'localizacao' => $localizacao,
                'telefone' => $telefone,
                'status' => $_POST['status'] ?? 'pendente'
            ];
            $data['erros']['email'] = "Este e-mail já está cadastrado.";
            $data['tipo_cadastro'] = 'empresa';
            $this->view($cadastroPublico ? 'autenticacao/cadastrar' : 'empresas/empresa_create', $data);
        }
    }

    public function editar() {
        $id = $_GET['id'];
        $data['empresa'] = $this->service->getEmpresa($id);
        $this->view('empresas/empresa_edit', $data);
    }

    public function atualizar() {

        $validador = new Validador();
        $email = trim((string) ($_POST['email'] ?? ''));
        $validador->obrigatorio('email', $email, 'O e-mail da empresa é obrigatório.');
        $validador->email('email', $email);
        $validador->cnpj('cnpj', $_POST['cnpj'] ?? '');
        $validador->telefone('telefone', $_POST['telefone'] ?? '');
        if ($validador->temErros()) {
            $data['empresa'] = [
                'ID_Empresa' => (int) ($_POST['id'] ?? 0),
                'nome' => $_POST['nome'] ?? '',
                'email' => trim((string) ($_POST['email'] ?? '')),
                'cnpj' => $_POST['cnpj'] ?? '',
                'localizacao' => $_POST['localizacao'] ?? '',
                'telefone' => $_POST['telefone'] ?? '',
                'status' => $_POST['status'] ?? 'pendente'
            ];
            $data['erros'] = $validador->getErros();
            $this->view('empresas/empresa_edit', $data);
            return;
        }

        $empresa = new Empresa(
            $_POST['id'],
            $_POST['nome'],
            trim((string) ($_POST['email'] ?? '')),
            $_POST['cnpj'],
            $_POST['localizacao'],
            $_POST['telefone'],
            $_POST['senha'],
            $_POST['status'] ?? 'pendente'
        );

        if ($this->service->updateEmpresa($empresa)) {
            $this->redirect(URL_BASE . '/empresa');
        } else {
            $data['empresa'] = [
                'ID_Empresa' => (int) ($_POST['id'] ?? 0),
                'nome' => $_POST['nome'] ?? '',
                'email' => trim((string) ($_POST['email'] ?? '')),
                'cnpj' => $_POST['cnpj'] ?? '',
                'localizacao' => $_POST['localizacao'] ?? '',
                'telefone' => $_POST['telefone'] ?? '',
                'status' => $_POST['status'] ?? 'pendente'
            ];
            $data['erros']['email'] = "Este e-mail já está cadastrado.";
            $this->view('empresas/empresa_edit', $data);
        }
    }

    public function verEmpresa() {
        if (!isset($_GET['id'])) {
            $this->redirect(URL_BASE . '/empresa');
        }

        $id = $_GET['id'];
        $data['empresa'] = $this->service->getEmpresa($id);
        $this->view('empresas/empresa_show', $data);
    }
    
    public function excluir() {
        $id = $_GET['id'];
        if ($this->service->deleteEmpresa($id)) {
            $this->redirect(URL_BASE . '/empresa');
        } else {
            echo "Erro ao excluir a empresa.";
        }
    }
}