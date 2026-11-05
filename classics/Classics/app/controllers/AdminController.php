<?php

namespace app\controllers;

use app\core\Controller;
use app\helpers\Validador;
use app\models\Empresa;
use app\models\Usuario;
use app\services\AtividadeService;
use app\services\EmpresaService;
use app\services\UsuarioService;

// Painel do administrador (RF02 e manutenção de usuários)
class AdminController extends Controller
{
    private EmpresaService $empresaService;
    private UsuarioService $usuarioService;
    private AtividadeService $atividadeService;

    public function __construct()
    {
        // Todas as páginas deste controller são exclusivas do administrador
        $this->adminRequired();

        $this->empresaService = new EmpresaService();
        $this->usuarioService = new UsuarioService();
        $this->atividadeService = new AtividadeService();
    }

    // ---- Empresas ----

    public function empresas()
    {
        $aba = $this->abaValida((string) ($_GET['aba'] ?? ''));

        $data['aba'] = $aba;
        $data['empresas'] = $this->empresaService->getEmpresasPorStatus($aba);
        $data['contagem'] = $this->empresaService->contarPorStatus();
        $data['total_atividades'] = $this->atividadeService->countAtividades();
        $this->view('admin/empresas', $data);
    }

    public function alterarStatusEmpresa()
    {
        $empresaId = isset($_POST['empresa_id']) ? (int) $_POST['empresa_id'] : 0;
        $acao = (string) ($_POST['acao'] ?? '');
        $aba = $this->abaValida((string) ($_POST['aba'] ?? ''));

        $resultado = $this->empresaService->alterarStatus($empresaId, $acao);

        if (!empty($resultado['sucesso'])) {
            $this->redirect(URL_BASE . '/admin?aba=' . $aba . '&sucesso=' . rawurlencode($acao));
        }

        $this->redirect(URL_BASE . '/admin?aba=' . $aba . '&erro=' . rawurlencode((string) ($resultado['erro'] ?? 'falha')));
    }

    public function editarEmpresa()
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $empresa = $this->empresaService->getEmpresa($id);
        if (!$empresa) {
            $this->redirect(URL_BASE . '/admin?erro=empresa');
        }

        $data['empresa'] = $this->dadosEmpresaDoBanco($empresa);
        $this->view('admin/empresa_edit', $data);
    }

    public function atualizarEmpresa()
    {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $empresaAtual = $this->empresaService->getEmpresa($id);
        if (!$empresaAtual) {
            $this->redirect(URL_BASE . '/admin?erro=empresa');
        }

        $dados = $this->dadosEmpresaDoFormulario($id);

        $validador = new Validador();
        $validador->obrigatorio('nome', trim($dados['nome']), 'O nome da empresa é obrigatório.');
        $validador->obrigatorio('email', $dados['email'], 'O e-mail da empresa é obrigatório.');
        $validador->email('email', $dados['email']);
        $validador->cnpj('cnpj', $dados['cnpj']);
        $validador->telefone('telefone', $dados['telefone']);
        if (!in_array($dados['status'], EmpresaService::STATUS_VALIDOS, true)) {
            $validador->obrigatorio('status', '', 'Selecione um status válido.');
        }

        if ($validador->temErros()) {
            $this->view('admin/empresa_edit', ['empresa' => $dados, 'erros' => $validador->getErros()]);
            return;
        }

        $empresa = new Empresa(
            $id,
            trim($dados['nome']),
            $dados['email'],
            $dados['cnpj'],
            $dados['localizacao'],
            $dados['telefone'],
            (string) ($_POST['senha'] ?? ''),
            $dados['status']
        );

        if ($this->empresaService->updateEmpresaPeloAdmin($empresa)) {
            $this->redirect(URL_BASE . '/admin?aba=' . $dados['status'] . '&sucesso=editada');
        }

        $this->view('admin/empresa_edit', [
            'empresa' => $dados,
            'erros' => ['email' => 'Este e-mail já está cadastrado.']
        ]);
    }

    public function excluirEmpresa()
    {
        $id = isset($_POST['empresa_id']) ? (int) $_POST['empresa_id'] : 0;
        $aba = $this->abaValida((string) ($_POST['aba'] ?? ''));

        if ($id > 0 && $this->empresaService->deleteEmpresa($id)) {
            $this->redirect(URL_BASE . '/admin?aba=' . $aba . '&sucesso=excluida');
        }

        $this->redirect(URL_BASE . '/admin?aba=' . $aba . '&erro=excluir');
    }

    // ---- Usuários ----

    public function usuarios()
    {
        $data['usuarios'] = $this->usuarioService->getUsuarios();
        $data['admin_id'] = $this->getAdminId();
        $this->view('admin/usuarios', $data);
    }

    public function editarUsuario()
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $usuario = $this->usuarioService->getUsuarioById($id);
        if (!$usuario) {
            $this->redirect(URL_BASE . '/admin/usuarios?erro=usuario');
        }

        $data['usuario'] = $usuario;
        $this->view('admin/usuario_edit', $data);
    }

    public function atualizarUsuario()
    {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $usuarioAtual = $this->usuarioService->getUsuarioById($id);
        if (!$usuarioAtual) {
            $this->redirect(URL_BASE . '/admin/usuarios?erro=usuario');
        }

        $dados = [
            'id' => $id,
            'nomeUsuario' => trim((string) ($_POST['nomeUsuario'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
            'cpf' => trim((string) ($_POST['cpf'] ?? '')),
            'telefone' => trim((string) ($_POST['telefone'] ?? '')),
            'idade' => trim((string) ($_POST['idade'] ?? '')),
            // O tipo do usuário não é alterado pelo painel
            'tipo_usuario' => (string) ($usuarioAtual['tipo_usuario'] ?? 'user'),
            'foto' => (string) ($usuarioAtual['foto'] ?? '')
        ];

        $validador = new Validador();
        $validador->obrigatorio('nomeUsuario', $dados['nomeUsuario'], 'O nome é obrigatório.');
        $validador->obrigatorio('email', $dados['email'], 'O e-mail é obrigatório.');
        $validador->email('email', $dados['email']);
        $validador->cpf('cpf', $dados['cpf']);
        $validador->telefone('telefone', $dados['telefone']);
        if ($dados['idade'] !== '' && (!ctype_digit($dados['idade']) || (int) $dados['idade'] > 120)) {
            $validador->obrigatorio('idade', '', 'Informe uma idade válida.');
        }

        if ($validador->temErros()) {
            $this->view('admin/usuario_edit', ['usuario' => $dados, 'erros' => $validador->getErros()]);
            return;
        }

        // Ordem do construtor: id, nome, email, cpf, telefone, idade, tipo_usuario, foto, senha
        $usuario = new Usuario(
            $id,
            $dados['nomeUsuario'],
            $dados['email'],
            $dados['cpf'],
            $dados['telefone'],
            // Idade em branco mantém a atual (a coluna é numérica)
            $dados['idade'] !== '' ? $dados['idade'] : (string) (int) ($usuarioAtual['idade'] ?? 0),
            $dados['tipo_usuario'],
            $dados['foto'],
            // Senha em branco mantém a atual
            (string) ($_POST['senha'] ?? '')
        );

        if ($this->usuarioService->updateUsuario($usuario)) {
            // Mantém o nome e o e-mail do administrador logado atualizados na sessão
            if ($id === $this->getAdminId()) {
                $_SESSION['admin_logado']->setNome($usuario->getNome());
                $_SESSION['admin_logado']->setEmail($usuario->getEmail());
            }
            $this->redirect(URL_BASE . '/admin/usuarios?sucesso=editado');
        }

        $this->view('admin/usuario_edit', [
            'usuario' => $dados,
            'erros' => ['email' => 'Este e-mail já está cadastrado.']
        ]);
    }

    public function excluirUsuario()
    {
        $id = isset($_POST['usuario_id']) ? (int) $_POST['usuario_id'] : 0;

        // O administrador não pode excluir a própria conta
        if ($id === $this->getAdminId()) {
            $this->redirect(URL_BASE . '/admin/usuarios?erro=proprio');
        }

        if ($id > 0 && $this->usuarioService->deleteUsuario($id)) {
            $this->redirect(URL_BASE . '/admin/usuarios?sucesso=excluido');
        }

        $this->redirect(URL_BASE . '/admin/usuarios?erro=excluir');
    }

    // ---- Auxiliares ----

    private function abaValida(string $aba): string
    {
        $aba = strtolower(trim($aba));

        return in_array($aba, EmpresaService::STATUS_VALIDOS, true) ? $aba : EmpresaService::STATUS_PENDENTE;
    }

    private function getAdminId(): int
    {
        return (int) $_SESSION['admin_logado']->getIdUsuario();
    }

    private function dadosEmpresaDoBanco(array $empresa): array
    {
        return [
            'id' => (int) $empresa['ID_Empresa'],
            'nome' => (string) ($empresa['Name'] ?? ''),
            'email' => (string) ($empresa['Email'] ?? ''),
            'cnpj' => (string) ($empresa['CNPJ'] ?? ''),
            'localizacao' => (string) ($empresa['Localizacao'] ?? ''),
            'telefone' => (string) ($empresa['Telefone'] ?? ''),
            'status' => strtolower(trim((string) ($empresa['Status'] ?? '')))
        ];
    }

    private function dadosEmpresaDoFormulario(int $id): array
    {
        return [
            'id' => $id,
            'nome' => (string) ($_POST['nome'] ?? ''),
            'email' => trim((string) ($_POST['email'] ?? '')),
            'cnpj' => trim((string) ($_POST['cnpj'] ?? '')),
            'localizacao' => trim((string) ($_POST['localizacao'] ?? '')),
            'telefone' => trim((string) ($_POST['telefone'] ?? '')),
            'status' => strtolower(trim((string) ($_POST['status'] ?? '')))
        ];
    }
}
