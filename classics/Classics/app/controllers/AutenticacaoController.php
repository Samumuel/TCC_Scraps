<?php

namespace app\controllers;

use app\core\Controller;
use app\services\AutenticacaoService;

class AutenticacaoController extends Controller
{

    private AutenticacaoService $autenticacaoService;

    public function __construct()
    {
        $this->autenticacaoService = new AutenticacaoService();
    }

    public function login()
    {

        $this->view('autenticacao/login');
    }

    public function cadastrar()
    {

        $this->view('autenticacao/cadastrar');
    }


    public function logar()
    { 

        $email = $_POST['email'];
        $senha = $_POST['senha'];

        $resultado = $this->autenticacaoService->logar($email, $senha);

        if ($resultado && isset($_SESSION['admin_logado'])) {
            $this->redirect(URL_BASE . '/admin');
        } else if ($resultado && isset($_SESSION['usuario_logado'])) {
            $this->redirect(URL_BASE . '/');
        } else if (!isset($_SESSION['usuario_logado']) && $resultado && isset($_SESSION['empresa_logada'])) {
            // RN02: empresa ainda não aprovada vai para o perfil, onde vê a situação do cadastro
            if ($this->empresaLogadaAprovada()) {
                $this->redirect(URL_BASE . '/atividade/listar');
            }
            $this->redirect(URL_BASE . '/empresa/ver?id=' . (int) $_SESSION['empresa_logada']->getIdEmpresa());
        } else {

            $dados['erros'] = "Algo deu errado no login";

            $this->view('autenticacao/login', $dados);
        }
    }

    public function logout()
    {
        $this->autenticacaoService->logout();
        $this->redirect(URL_BASE);
    }
}