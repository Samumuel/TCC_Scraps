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

        if ($resultado && isset($_SESSION['usuario_logado'])) {
            $this->redirect(URL_BASE . '/usuarios');
        } else if (!isset($_SESSION['usuario_logado']) && $resultado && isset($_SESSION['empresa_logada'])) {
            $this->redirect(URL_BASE . '/atividade/listar');
        } else {

            $dados['erros'] = "Uma linda mensagem de erro";

            $this->view('autenticacao/login', $dados);
        }
    }
}