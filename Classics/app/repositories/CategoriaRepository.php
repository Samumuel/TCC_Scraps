<?php 

namespace app\repositories;

use app\database\ConnectionFactory;
use app\models\Categoria;
use PDO;

class CategoriaRepository
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = ConnectionFactory::getConnection();
    }

    public function getCategorias(int $empresaId): array
    {
        $sql = 'SELECT * FROM Categorias WHERE Empresa_ID = :empresa_id ORDER BY Nome_Categoria ASC';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':empresa_id', $empresaId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategoria(int $id, int $empresaId)
    {
        $sql = 'SELECT * FROM Categorias WHERE ID_Categoria = :id AND Empresa_ID = :empresa_id';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':empresa_id', $empresaId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function saveCategoria(Categoria $categoria, int $empresaId): bool
    {
        $sql = 'INSERT INTO Categorias (Nome_Categoria, Empresa_ID) VALUES (:nome_categoria, :empresa_id)';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':nome_categoria', $categoria->getNomeCategoria());
        $stmt->bindValue(':empresa_id', $empresaId, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function updateCategoria(Categoria $categoria, int $empresaId): bool
    {
        $sql = 'UPDATE Categorias SET Nome_Categoria = :nome_categoria WHERE ID_Categoria = :id AND Empresa_ID = :empresa_id';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':nome_categoria', $categoria->getNomeCategoria());
        $stmt->bindValue(':id', $categoria->getIdCategoria(), PDO::PARAM_INT);
        $stmt->bindValue(':empresa_id', $empresaId, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function deleteCategoria(int $id, int $empresaId): bool
    {
        $sql = 'DELETE FROM Categorias WHERE ID_Categoria = :id AND Empresa_ID = :empresa_id';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':empresa_id', $empresaId, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
