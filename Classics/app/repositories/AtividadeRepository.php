<?php 

namespace app\repositories;

use app\database\ConnectionFactory;
use app\models\Atividade;
use PDO;

class AtividadeRepository {

    private PDO $connection;

    public function __construct()
    {
        $this->connection = ConnectionFactory::getConnection();
    }

    public function getAtividades() {
        $sql = "
            SELECT
                a.*, 
                c.Nome_Categoria AS Categoria_Nome,
                l.ID_Localizacao AS Localizacao_ID,
                l.CEP AS Localizacao_CEP,
                l.Complemento AS Localizacao_Complemento
            FROM Atividades a
            LEFT JOIN Categorias c ON c.ID_Categoria = a.Categoria_ID
            LEFT JOIN Localizacao l ON l.ID_Atividade = a.ID_Atividade
        ";
        $stmt = $this->connection->query($sql);
        return $stmt->fetchAll();
    }

    public function getAtividade(int $id) {
        $sql = "
            SELECT
                a.*,
                c.Nome_Categoria AS Categoria_Nome,
                l.ID_Localizacao AS Localizacao_ID,
                l.CEP AS Localizacao_CEP,
                l.Complemento AS Localizacao_Complemento
            FROM Atividades a
            LEFT JOIN Categorias c ON c.ID_Categoria = a.Categoria_ID
            LEFT JOIN Localizacao l ON l.ID_Atividade = a.ID_Atividade
            WHERE a.ID_Atividade = :id
        ";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function saveAtividade(Atividade $atividade) {
        $sql = 'INSERT INTO Atividades (Nome, Duracao, Descricao, Localizacao, Valor, Fotos, Categoria_ID, Empresa_ID) VALUES (:nome, :duracao, :descricao, :localizacao, :valor, :fotos, :categoria_id, :empresa_id)';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':nome', $atividade->getNome());
        $stmt->bindValue(':duracao', $atividade->getDuracao());
        $stmt->bindValue(':descricao', $atividade->getDescricao());
        $stmt->bindValue(':localizacao', $atividade->getLocalizacao());
        $stmt->bindValue(':valor', $atividade->getValor());
        $stmt->bindValue(':fotos', $atividade->getFotos());
        $stmt->bindValue(':categoria_id', $atividade->getCategoriaId());
        $stmt->bindValue(':empresa_id', $atividade->getEmpresaId());

        if ($stmt->execute()) {
            return (int) $this->connection->lastInsertId();
        }

        return false;
    }

    public function updateAtividade(Atividade $atividade) {
        $sql = 'UPDATE Atividades SET Nome = :nome, Duracao = :duracao, Descricao = :descricao, Localizacao = :localizacao, Valor = :valor, Fotos = :fotos, Categoria_ID = :categoria_id, Empresa_ID = :empresa_id WHERE ID_Atividade = :id';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':nome', $atividade->getNome());
        $stmt->bindValue(':duracao', $atividade->getDuracao());
        $stmt->bindValue(':descricao', $atividade->getDescricao());
        $stmt->bindValue(':localizacao', $atividade->getLocalizacao());
        $stmt->bindValue(':valor', $atividade->getValor());
        $stmt->bindValue(':fotos', $atividade->getFotos());
        $stmt->bindValue(':categoria_id', $atividade->getCategoriaId());
        $stmt->bindValue(':empresa_id', $atividade->getEmpresaId());
        $stmt->bindValue(':id', $atividade->getIdAtividade());
        return $stmt->execute();
    }

    public function deleteAtividade(int $id, int $empresaId) {
        $sql = 'DELETE FROM Atividades WHERE ID_Atividade = :id AND Empresa_ID = :empresa_id';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':empresa_id', $empresaId);
        return $stmt->execute();
    }
}
