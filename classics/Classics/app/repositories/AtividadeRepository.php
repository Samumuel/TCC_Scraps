<?php 

namespace app\repositories;

use app\database\ConnectionFactory;
use app\models\Atividade;
use PDO;
use PDOException;

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
        $this->connection->beginTransaction();

        try {
            $sqlAtividade = 'SELECT ID_Atividade FROM Atividades WHERE ID_Atividade = :id AND Empresa_ID = :empresa_id FOR UPDATE';
            $stmtAtividade = $this->connection->prepare($sqlAtividade);
            $stmtAtividade->bindValue(':id', $id);
            $stmtAtividade->bindValue(':empresa_id', $empresaId);
            $stmtAtividade->execute();

            if (!$stmtAtividade->fetch()) {
                $this->connection->rollBack();
                return false;
            }

            $sqlLocalizacao = 'DELETE FROM Localizacao WHERE ID_Atividade = :id_atividade';
            $stmtLocalizacao = $this->connection->prepare($sqlLocalizacao);
            $stmtLocalizacao->bindValue(':id_atividade', $id);
            $stmtLocalizacao->execute();

            $sqlSessao = 'DELETE FROM Sessao WHERE ID_Atividade = :id_atividade';
            $stmtSessao = $this->connection->prepare($sqlSessao);
            $stmtSessao->bindValue(':id_atividade', $id);
            $stmtSessao->execute();

            $sql = 'DELETE FROM Atividades WHERE ID_Atividade = :id AND Empresa_ID = :empresa_id';
            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->bindValue(':empresa_id', $empresaId);
            $stmt->execute();

            $this->connection->commit();

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            if ($this->connection->inTransaction()) {
                $this->connection->rollBack();
            }
            throw $e;
        }
    }
}
