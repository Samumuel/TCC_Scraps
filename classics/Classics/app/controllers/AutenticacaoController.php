<?php

namespace app\controllers;

use app\core\Controller;
use app\helpers\Validador;
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

        $email = trim((string) ($_POST['email'] ?? ''));
        $senha = (string) ($_POST['senha'] ?? '');

        // Campos em branco ou e-mail fora do formato: avisa no próprio campo, sem consultar o banco
        $validador = new Validador();
        $validador->obrigatorio('email', $email, 'Informe o e-mail.');
        if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $validador->obrigatorio('email', '', 'Informe um e-mail válido, no formato nome@dominio.com.');
        }
        $validador->obrigatorio('senha', $senha, 'Informe a senha.');

        if ($validador->temErros()) {
            $this->view('autenticacao/login', ['erros' => $validador->getErros(), 'email' => $email]);
            return;
        }

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

            // Mensagem única para e-mail não cadastrado ou senha errada, para não revelar quais e-mails têm conta
            $this->view('autenticacao/login', [
                'erros' => ['geral' => 'E-mail ou senha incorretos.'],
                'email' => $email
            ]);
        }
    }

    public function logout()
    {
        $this->autenticacaoService->logout();
        $this->redirect(URL_BASE);
    }
}