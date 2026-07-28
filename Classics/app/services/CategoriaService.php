<?php 

namespace app\services;

use app\models\Categoria;
use app\repositories\CategoriaRepository;

class CategoriaService
{
    private CategoriaRepository $repository;

    public function __construct()
    {
        $this->repository = new CategoriaRepository();
    }

    public function getCategorias(int $empresaId): array
    {
        return $this->repository->getCategorias($empresaId);
    }

    public function getCategoria(int $id, int $empresaId)
    {
        return $this->repository->getCategoria($id, $empresaId);
    }

    public function saveCategoria(Categoria $categoria, int $empresaId): bool
    {
        return $this->repository->saveCategoria($categoria, $empresaId);
    }

    public function updateCategoria(Categoria $categoria, int $empresaId): bool
    {
        return $this->repository->updateCategoria($categoria, $empresaId);
    }

    public function deleteCategoria(int $id, int $empresaId): bool
    {
        return $this->repository->deleteCategoria($id, $empresaId);
    }
}
