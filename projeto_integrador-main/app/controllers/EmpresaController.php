<?php 

namespace app\controllers;

use app\core\Controller;
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
        $email = $_POST['email'] ?? null;
        $cnpj = $_POST['cnpj'] ?? null;
        $localizacao = $_POST['localizacao'] ?? null;
        $telefone = $_POST['telefone'] ?? null;
        $senha = $_POST['senha'];

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
            echo "Erro ao salvar a empresa.";
        }
    }

    public function editar() {
        $id = $_GET['id'];
        $data['empresa'] = $this->service->getEmpresa($id);
        $this->view('empresas/empresa_edit', $data);
    }

    public function atualizar() {

        $empresa = new Empresa(
            $_POST['id'],
            $_POST['nome'],
            $_POST['email'],
            $_POST['cnpj'],
            $_POST['localizacao'],
            $_POST['telefone'],
            $_POST['senha'],
            $_POST['status'] ?? 'pendente'
        );

        $this->service->updateEmpresa($empresa);
        $this->redirect(URL_BASE . '/empresa');
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