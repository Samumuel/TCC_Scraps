<?php

namespace app\controllers;

use app\core\Controller;
use app\helpers\Validador;
use app\models\Usuario;
use app\services\UsuarioService;

class UsuarioController extends Controller
{
    private UsuarioService $service;
    private const TAMANHO_MAX_FOTO = 5_242_880; // 5 MB

    public function __construct()
    {
        $this->service = new UsuarioService();
    }

    public function index()
    {
        if ($this->getUsuarioLogadoId() === null) {
            $this->redirect(URL_BASE);
        }

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
        $email  = trim((string) ($_POST['email'] ?? ''));
        $cpf   = $_POST['cpf'] ?? '';
        $telefone = $_POST['telefone'] ?? '';
        $idade = $_POST['idade'] ?? '';
        $tipo_usuario = $_POST['tipo_usuario'] ?? 'user';
        $senha  = $_POST['senha'];
        $cadastroPublico = isset($_POST['cadastro_publico']);

        //Validar
        $validador->obrigatorio('nomeUsuario', $nomeUsuario);
        $validador->obrigatorio('email', $email);
        $validador->email('email', $email);
        $validador->cpf('cpf', $cpf);
        $validador->telefone('telefone', $telefone);

        if ($validador->temErros()) {

            $data['usuario'] = $_POST;
            $data['erros'] = $validador->getErros();

            $this->view($cadastroPublico ? 'autenticacao/cadastrar' : 'usuarios/usuario_create', $data);

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

            $this->view($cadastroPublico ? 'autenticacao/cadastrar' : 'usuarios/usuario_create', $data);
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
            $this->redirect(URL_BASE . '/usuarios');
        }

        $data['usuario'] = $this->service->getUsuarioById($id);
        if (!$data['usuario']) {
            $this->redirect(URL_BASE . '/usuarios');
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
            $this->redirect(URL_BASE . '/usuarios');
        }

        $usuarioAtual = $this->service->getUsuarioById($id);
        if (!$usuarioAtual) {
            $this->redirect(URL_BASE . '/usuarios');
        }

        $fotoAtual = (string) ($usuarioAtual['foto'] ?? '');

        $validador = new Validador();
        $email = trim((string) ($_POST['email'] ?? ''));
        $validador->obrigatorio('email', $email);
        $validador->email('email', $email);
        $validador->cpf('cpf', $_POST['cpf'] ?? '');
        $validador->telefone('telefone', $_POST['telefone'] ?? '');
        if ($validador->temErros()) {
            $data['usuario'] = $this->montarDadosUsuarioFormulario($id, $fotoAtual);
            $data['erros'] = $validador->getErros();
            $this->view('usuarios/usuario_edit', $data);
            return;
        }

        $resultadoUpload = $this->processarUploadFoto($id, $_FILES['foto'] ?? null);

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
            $_POST['nomeUsuario'],
            trim((string) ($_POST['email'] ?? '')),
            $_POST['cpf'] ?? '',
            $_POST['telefone'] ?? '',
            $_POST['idade'] ?? 0,
            $_POST['tipo_usuario'] ?? 'user',
            $fotoParaSalvar,
            $_POST['senha']
        );

        if ($this->service->updateUsuario($usuario)) {
            if ($novaFoto !== null) {
                $this->removerFotoLocal($fotoAtual);
            }
            $this->redirect(URL_BASE . '/usuarios');
        } else {
            if ($novaFoto !== null) {
                $this->removerFotoLocal($novaFoto);
            }
            $data['usuario'] = $this->montarDadosUsuarioFormulario($id, $fotoAtual);
            $data['erros']['email'] = "Este e-mail já está cadastrado.";

            $this->view('usuarios/usuario_edit', $data);
        }
    }

    public function verUsuario() {
        if ($this->getUsuarioLogadoId() === null) {
            $this->redirect(URL_BASE);
        }

        if (!isset($_GET['id'])) {
            $this->redirect(URL_BASE . '/usuarios');
        }

        $id = (int) $_GET['id'];
        $data['usuario'] = $this->service->getUsuarioById($id);
        $this->view('usuarios/usuario_show', $data);
    }

    public function excluir()
    {
        $usuarioLogadoId = $this->getUsuarioLogadoId();
        if ($usuarioLogadoId === null) {
            $this->redirect(URL_BASE);
        }

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id !== $usuarioLogadoId) {
            $this->redirect(URL_BASE . '/usuarios');
        }

        if ($this->service->deleteUsuario($id)) {
            unset($_SESSION['usuario_logado']);
            session_destroy();
            $this->redirect(URL_BASE);
        }

        $this->redirect(URL_BASE . '/usuarios');
    }

    private function getUsuarioLogadoId(): ?int
    {
        if (!isset($_SESSION['usuario_logado']) || !is_object($_SESSION['usuario_logado'])) {
            return null;
        }

        return (int) $_SESSION['usuario_logado']->getIdUsuario();
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

    private function processarUploadFoto(int $usuarioId, ?array $arquivo): array
    {
        if ($arquivo === null || !isset($arquivo['error'])) {
            return ['sucesso' => true, 'foto' => null, 'erro' => null];
        }

        if ((int) $arquivo['error'] === UPLOAD_ERR_NO_FILE) {
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

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
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

        if (!isset($extensoesPermitidas[$mime])) {
            return ['sucesso' => false, 'foto' => null, 'erro' => 'Formato de imagem inválido. Use JPG, PNG ou WEBP.'];
        }

        if (getimagesize($tmp) === false) {
            return ['sucesso' => false, 'foto' => null, 'erro' => 'O arquivo enviado não é uma imagem válida.'];
        }

        try {
            $identificador = bin2hex(random_bytes(8));
        } catch (\Exception $e) {
            return ['sucesso' => false, 'foto' => null, 'erro' => 'Não foi possível gerar o nome da imagem.'];
        }

        $nomeArquivo = 'usuario_' . $usuarioId . '_' . $identificador . '.' . $extensoesPermitidas[$mime];
        $caminhoRelativo = 'uploads/usuarios/' . $nomeArquivo;
        $diretorioUpload = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'usuarios';

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
        $prefixo = 'uploads/usuarios/';
        if ($caminhoRelativo === '' || strpos($caminhoRelativo, $prefixo) !== 0) {
            return;
        }

        $caminhoCompleto = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $caminhoRelativo);

        if (is_file($caminhoCompleto)) {
            unlink($caminhoCompleto);
        }
    }
}