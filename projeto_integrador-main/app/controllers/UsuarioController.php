<?php

namespace app\controllers;

use app\core\Controller;
use app\helpers\Validador;
use app\models\Usuario;
use app\services\UsuarioService;

class UsuarioController extends Controller
{
    private UsuarioService $service;

    public function __construct()
    {
        $this->service = new UsuarioService();
    }

    public function index()
    {
        $data['usuarios'] = $this->service->getUsuarios();
        $this->view('usuarios/usuario_list', $data);
    }

    public function cadastrar()
    {
        $this->view('usuarios/usuario_create');
    }

    public function salvar()
    {
        $validador = new Validador();

        //Sanitizar
        $nomeUsuario = filter_input(INPUT_POST, 'nomeUsuario', FILTER_SANITIZE_SPECIAL_CHARS);
        $email  = $_POST['email'];
        $cpf   = $_POST['cpf'] ?? '';
        $telefone = $_POST['telefone'] ?? '';
        $idade = $_POST['idade'] ?? '';
        $tipo_usuario = $_POST['tipo_usuario'] ?? 'user';
        $senha  = $_POST['senha'];

        //Validar
        $validador->obrigatorio('nomeUsuario', $nomeUsuario);
        $validador->obrigatorio('email', $email);

        if ($validador->temErros()) {

            $data['usuario'] = $_POST;
            $data['erros'] = $validador->getErros();

            $this->view('usuarios/usuario_create', $data);

            return;
        }


        //Salvar
        // Ordem do construtor: id, nome, email, cpf, telefone, idade, tipo_usuario, foto, senha
        $usuario = new Usuario(0, $nomeUsuario, $email, $cpf, $telefone, $idade, $tipo_usuario, '', $senha);

        if ($this->service->saveUsuario($usuario)) {
            $this->redirect(URL_BASE . '/usuarios');
        } else {

            $data["usuario"] = $_POST;
            $data['erros']['email'] = "Erro: Este e-mail já está cadastrado!";

            $this->view('usuarios/usuario_create', $data);
        }
    }

    public function editar()
    {
        $id = $_GET['id'];
        $data['usuario'] = $this->service->getUsuarioById($id);
        $this->view('usuarios/usuario_edit', $data);
    }

    public function atualizar()
    {
        // Ordem do construtor: id, nome, email, cpf, telefone, idade, tipo_usuario, foto, senha
        $usuario = new Usuario(
            $_POST['id'],
            $_POST['nomeUsuario'],
            $_POST['email'],
            $_POST['cpf'] ?? '',
            $_POST['telefone'] ?? '',
            $_POST['idade'] ?? 0,
            $_POST['tipo_usuario'] ?? 'user',
            '',
            $_POST['senha']
        );
        $this->service->updateUsuario($usuario);
        $this->redirect(URL_BASE . '/usuarios');
    }

    public function verUsuario() {
        if (!isset($_GET['id'])) {
            $this->redirect(URL_BASE . '/usuarios');
        }

        $id = $_GET['id'];
        $data['usuario'] = $this->service->getUsuarioById($id);
        $this->view('usuarios/usuario_show', $data);
    }

    public function excluir()
    {
        $id = $_GET['id'];
        $this->service->deleteUsuario($id);
        $this->redirect(URL_BASE . '/usuarios');
    }
}
