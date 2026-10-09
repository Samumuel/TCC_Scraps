<?php 

namespace app\services;

use app\repositories\UsuarioRepository;
use app\repositories\EmpresaRepository;

class AutenticacaoService {

    private UsuarioRepository $usuarioRepository;
    private EmpresaRepository $empresaRepository;
        
    public function __construct(){
        $this->usuarioRepository = new UsuarioRepository();
        $this->empresaRepository = new EmpresaRepository();
    }


    public function logar(string $email, string $senha) : bool {

        unset($_SESSION['usuario_logado'], $_SESSION['empresa_logada']);

        $usuario = $this->usuarioRepository->getUsuarioByEmail($email);

        if ($usuario && password_verify($senha, $usuario->getSenha())) {

            $_SESSION['usuario_logado'] = $usuario;
            return true;
            
        } else if (!$usuario) {
            $empresa = $this->empresaRepository->getEmpresaByEmail($email);

            if ($empresa && password_verify($senha, $empresa->getSenha())) {
                $_SESSION['empresa_logada'] = $empresa;
                return true;
            }
        }

        return false;
    }

    public function logout(){
        session_destroy();
    }




}