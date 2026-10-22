<?php

namespace app\services;

use app\models\Usuario;
use app\repositories\EmpresaRepository;
use app\repositories\UsuarioRepository;

class UsuarioService
{
    private UsuarioRepository $repository;
    private EmpresaRepository $empresaRepository;

    public function __construct()
    {
        $this->repository = new UsuarioRepository();
        $this->empresaRepository = new EmpresaRepository();
    }

    public function getUsuarios(): array
    {
        return $this->repository->getUsuarios();
    }

    public function saveUsuario(Usuario $usuario): bool
    {
        $emailNormalizado = $this->normalizarEmail($usuario->getEmail());
        $usuario->setEmail($emailNormalizado);

        if ($this->repository->hasEmail($emailNormalizado) || $this->empresaRepository->hasEmail($emailNormalizado)) {
            return false;
        }

        return $this->repository->saveUsuario($usuario);
    }

    public function getUsuarioById(int $id)
    {
        return $this->repository->getUsuarioById($id);
    }

    public function updateUsuario(Usuario $usuario): bool
    {
        $emailNormalizado = $this->normalizarEmail($usuario->getEmail());
        $usuario->setEmail($emailNormalizado);

        if (
            $this->repository->hasEmail($emailNormalizado, $usuario->getIdUsuario()) ||
            $this->empresaRepository->hasEmail($emailNormalizado)
        ) {
            return false;
        }

        return $this->repository->updateUsuario($usuario);
    }

    public function deleteUsuario(int $id): bool
    {
        return $this->repository->deleteUsuario($id);
    }

    private function normalizarEmail(string $email): string
    {
        return strtolower(trim($email));
    }
}
