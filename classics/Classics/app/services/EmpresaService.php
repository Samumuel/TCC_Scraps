<?php 

namespace app\services;

use app\models\Empresa;
use app\repositories\EmpresaRepository;
use app\repositories\UsuarioRepository;

class EmpresaService {

    private EmpresaRepository $repository;
    private UsuarioRepository $usuarioRepository;

    public function __construct(){

        $this->repository = new EmpresaRepository();
        $this->usuarioRepository = new UsuarioRepository();

    }

    public function getEmpresas(){
        return $this->repository->getEmpresas();
    
    }

    public function getEmpresa(int $id){
        return $this->repository->getEmpresa($id);
    }

    public function saveEmpresa(Empresa $empresa){
        $emailNormalizado = $this->normalizarEmail((string) $empresa->getEmail());
        $empresa->setEmail($emailNormalizado);

        if ($this->repository->hasEmail($emailNormalizado) || $this->usuarioRepository->hasEmail($emailNormalizado)) {
            return false;
        }

        return $this->repository->saveEmpresa($empresa);
    }

    public function deleteEmpresa(int $id) {
        return $this->repository->deleteEmpresa($id);
    }

    public function updateEmpresa(Empresa $empresa) {
        $emailNormalizado = $this->normalizarEmail((string) $empresa->getEmail());
        $empresa->setEmail($emailNormalizado);

        if (
            $this->repository->hasEmail($emailNormalizado, $empresa->getIdEmpresa()) ||
            $this->usuarioRepository->hasEmail($emailNormalizado)
        ) {
            return false;
        }

        $empresaAtual = $this->repository->getEmpresa($empresa->getIdEmpresa());
        if (!$empresaAtual) {
            return false;
        }

        // O status só será alterado pelo administrador (RF02)
        $empresa->setStatus($empresaAtual['Status'] ?? 'pendente');

        // Senha em branco mantém a atual; senha nova é gravada criptografada (RNF04)
        $novaSenha = (string) $empresa->getSenha();
        $empresa->setSenha($novaSenha === '' ? (string) ($empresaAtual['Senha'] ?? '') : password_hash($novaSenha, PASSWORD_BCRYPT));

        return $this->repository->updateEmpresa($empresa);
    }

    private function normalizarEmail(string $email): string
    {
        return strtolower(trim($email));
    }
}