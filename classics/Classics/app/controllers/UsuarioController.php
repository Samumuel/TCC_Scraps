<?php

namespace app\controllers;

use app\core\Controller;
use app\helpers\UploadImagem;
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

    public function salvar()
    {
        $validador = new Validador();

        //Sanitizar
        $nomeUsuario = filter_input(INPUT_POST, 'nomeUsuario', FILTER_SANITIZE_SPECIAL_CHARS);
        $email  = trim((string) ($_POST['email'] ?? ''));
        $cpf   = $_POST['cpf'] ?? '';
        $telefone = $_POST['telefone'] ?? '';
        $idade = $_POST['idade'] ?? '';
        $senha  = (string) ($_POST['senha'] ?? '');
        $cadastroPublico = isset($_POST['cadastro_publico']);

        // Somente o cadastro público está disponível; o perfil é sempre de cliente
        if (!$cadastroPublico) {
            $this->redirect(URL_BASE);
        }
        $tipo_usuario = 'user';

        //Validar
        $validador->obrigatorio('nomeUsuario', $nomeUsuario);
        $validador->obrigatorio('email', $email);
        $validador->email('email', $email);
        $validador->cpf('cpf', $cpf);
        $validador->telefone('telefone', $telefone);
        $validador->senha('senha', $senha);
        $validador->confirmacaoSenha('confirmar_senha', $senha, $_POST['confirmar_senha'] ?? '');

        if ($validador->temErros()) {

            $data['usuario'] = $_POST;
            $data['erros'] = $validador->getErros();

            $this->view('autenticacao/cadastrar', $data);

            return;
        }


        //Salvar
        // Ordem do construtor: id, nome, email, cpf, telefone, idade, tipo_usuario, foto, senha
        $usuario = new Usuario(0, $nomeUsuario, $email, $cpf, $telefone, $idade, $tipo_usuario, '', $senha);

        if ($this->service->saveUsuario($usuario)) {
            $this->redirect(URL_BASE);
        } else {

            $data["usuario"] = $_POST;
            $data['erros']['email'] = "Este e-mail já está cadastrado.";

            $this->view('autenticacao/cadastrar', $data);
        }
    }

    public function editar()
    {
        $usuarioLogadoId = $this->getUsuarioLogadoId();
        if ($usuarioLogadoId === null) {
            $this->redirect(URL_BASE);
        }

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id !== $usuarioLogadoId) {
            $this->redirect($this->urlPerfil($usuarioLogadoId));
        }

        $data['usuario'] = $this->service->getUsuarioById($id);
        if (!$data['usuario']) {
            $this->redirect(URL_BASE);
        }

        $this->view('usuarios/usuario_edit', $data);
    }

    public function atualizar()
    {
        $usuarioLogadoId = $this->getUsuarioLogadoId();
        if ($usuarioLogadoId === null) {
            $this->redirect(URL_BASE);
        }

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if ($id !== $usuarioLogadoId) {
            $this->redirect($this->urlPerfil($usuarioLogadoId));
        }

        $usuarioAtual = $this->service->getUsuarioById($id);
        if (!$usuarioAtual) {
            $this->redirect(URL_BASE);
        }

        $fotoAtual = (string) ($usuarioAtual['foto'] ?? '');

        $validador = new Validador();
        $email = trim((string) ($_POST['email'] ?? ''));
        $validador->obrigatorio('nomeUsuario', trim((string) ($_POST['nomeUsuario'] ?? '')), 'O nome é obrigatório.');
        $validador->obrigatorio('email', $email);
        $validador->email('email', $email);
        $validador->cpf('cpf', $_POST['cpf'] ?? '');
        $validador->telefone('telefone', $_POST['telefone'] ?? '');
        // Senha em branco mantém a atual; senha nova precisa seguir a regra
        $validador->senha('senha', $_POST['senha'] ?? '', false);
        if ($validador->temErros()) {
            $data['usuario'] = $this->montarDadosUsuarioFormulario($id, $fotoAtual);
            $data['erros'] = $validador->getErros();
            $this->view('usuarios/usuario_edit', $data);
            return;
        }

        $resultadoUpload = UploadImagem::salvar($_FILES['foto'] ?? null, 'usuarios', 'usuario_' . $id);

        if (!$resultadoUpload['sucesso']) {
            $data['usuario'] = $this->montarDadosUsuarioFormulario($id, $fotoAtual);
            $data['erros']['foto'] = $resultadoUpload['erro'];
            $this->view('usuarios/usuario_edit', $data);
            return;
        }

        $novaFoto = $resultadoUpload['foto'];
        $fotoParaSalvar = $novaFoto ?? $fotoAtual;

        // Ordem do construtor: id, nome, email, cpf, telefone, idade, tipo_usuario, foto, senha
        $usuario = new Usuario(
            $id,
            trim((string) ($_POST['nomeUsuario'] ?? '')),
            trim((string) ($_POST['email'] ?? '')),
            $_POST['cpf'] ?? '',
            $_POST['telefone'] ?? '',
            $_POST['idade'] ?? 0,
            // O tipo de usuário não pode ser alterado pelo próprio usuário
            (string) ($usuarioAtual['tipo_usuario'] ?? 'user'),
            $fotoParaSalvar,
            (string) ($_POST['senha'] ?? '')
        );

        if ($this->service->updateUsuario($usuario)) {
            if ($novaFoto !== null) {
                UploadImagem::remover($fotoAtual, 'usuarios');
            }
            $this->redirect($this->urlPerfil($id));
        } else {
            if ($novaFoto !== null) {
                UploadImagem::remover($novaFoto, 'usuarios');
            }
            $data['usuario'] = $this->montarDadosUsuarioFormulario($id, $fotoAtual);
            $data['erros']['email'] = "Este e-mail já está cadastrado.";

            $this->view('usuarios/usuario_edit', $data);
        }
    }

    public function verUsuario() {
        $usuarioLogadoId = $this->getUsuarioLogadoId();
        if ($usuarioLogadoId === null) {
            $this->redirect(URL_BASE);
        }

        // O usuário só pode ver o próprio perfil
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id !== $usuarioLogadoId) {
            $this->redirect($this->urlPerfil($usuarioLogadoId));
        }

        $data['usuario'] = $this->service->getUsuarioById($id);
        $this->view('usuarios/usuario_show', $data);
    }

    public function excluir()
    {
        $usuarioLogadoId = $this->getUsuarioLogadoId();
        if ($usuarioLogadoId === null) {
            $this->redirect(URL_BASE);
        }

        // Exclusão da própria conta somente por POST (botão com confirmação no perfil)
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if ($id !== $usuarioLogadoId) {
            $this->redirect($this->urlPerfil($usuarioLogadoId));
        }

        if ($this->service->deleteUsuario($id)) {
            unset($_SESSION['usuario_logado']);
            session_destroy();
            $this->redirect(URL_BASE);
        }

        $this->redirect($this->urlPerfil($id));
    }

    private function urlPerfil(int $usuarioId): string
    {
        return URL_BASE . '/usuarios/ver?id=' . $usuarioId;
    }


    private function montarDadosUsuarioFormulario(int $id, string $foto): array
    {
        return [
            'id' => $id,
            'nomeUsuario' => $_POST['nomeUsuario'] ?? '',
            'email' => trim((string) ($_POST['email'] ?? '')),
            'cpf' => $_POST['cpf'] ?? '',
            'telefone' => $_POST['telefone'] ?? '',
            'idade' => $_POST['idade'] ?? '',
            'tipo_usuario' => $_POST['tipo_usuario'] ?? 'user',
            'foto' => $foto
        ];
    }


}