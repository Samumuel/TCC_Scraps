<?php

namespace app\repositories;

use app\database\ConnectionFactory;
use app\models\Sessao;
use PDO;

class SessaoRepository
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = ConnectionFactory::getConnection();
    }

    public function getSessoesPorAtividade(int $atividadeId): array
    {
        $sql = 'SELECT * FROM Sessao WHERE ID_Atividade = :id_atividade ORDER BY Data ASC, Hora ASC';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id_atividade', $atividadeId);
        $stmt->execute();

        return $stmt->fetchAll() ?: [];
    }

    public function countSessoesPorAtividade(int $atividadeId): int
    {
        $sql = 'SELECT COUNT(*) AS total FROM Sessao WHERE ID_Atividade = :id_atividade';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id_atividade', $atividadeId);
        $stmt->execute();

        $resultado = $stmt->fetch();

        return (int) ($resultado['total'] ?? 0);
    }

    public function saveSessao(Sessao $sessao): bool
    {
        if ($sessao->getIdAtividade() === null) {
            return false;
        }

        $sql = 'INSERT INTO Sessao (Data, Hora, Qtd_min, Qtd_max, Qtd_disponivel, ID_Atividade)
                VALUES (:data, :hora, :qtd_min, :qtd_max, :qtd_disponivel, :id_atividade)';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':data', $sessao->getData());
        $stmt->bindValue(':hora', $sessao->getHora());
        $stmt->bindValue(':qtd_min', $sessao->getQtdMin(), PDO::PARAM_INT);
        $stmt->bindValue(':qtd_max', $sessao->getQtdMax(), PDO::PARAM_INT);
        $stmt->bindValue(':qtd_disponivel', $sessao->getQtdDisponivel(), PDO::PARAM_INT);
        $stmt->bindValue(':id_atividade', $sessao->getIdAtividade(), PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function getSessaoPorAtividade(int $idSessao, int $atividadeId): ?array
    {
        $sql = 'SELECT * FROM Sessao WHERE ID_Sessao = :id_sessao AND ID_Atividade = :id_atividade LIMIT 1';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id_sessao', $idSessao, PDO::PARAM_INT);
        $stmt->bindValue(':id_atividade', $atividadeId, PDO::PARAM_INT);
        $stmt->execute();

        $sessao = $stmt->fetch();

        return $sessao ?: null;
    }

    public function updateSessao(Sessao $sessao): bool
    {
        if ($sessao->getIdSessao() <= 0 || $sessao->getIdAtividade() === null) {
            return false;
        }

        $sql = 'UPDATE Sessao
                SET Data = :data, Hora = :hora, Qtd_min = :qtd_min, Qtd_max = :qtd_max, Qtd_disponivel = :qtd_disponivel
                WHERE ID_Sessao = :id_sessao AND ID_Atividade = :id_atividade';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':data', $sessao->getData());
        $stmt->bindValue(':hora', $sessao->getHora());
        $stmt->bindValue(':qtd_min', $sessao->getQtdMin(), PDO::PARAM_INT);
        $stmt->bindValue(':qtd_max', $sessao->getQtdMax(), PDO::PARAM_INT);
        $stmt->bindValue(':qtd_disponivel', $sessao->getQtdDisponivel(), PDO::PARAM_INT);
        $stmt->bindValue(':id_sessao', $sessao->getIdSessao(), PDO::PARAM_INT);
        $stmt->bindValue(':id_atividade', $sessao->getIdAtividade(), PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function deleteSessao(int $idSessao, int $atividadeId): bool
    {
        $sql = 'DELETE FROM Sessao WHERE ID_Sessao = :id_sessao AND ID_Atividade = :id_atividade';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id_sessao', $idSessao, PDO::PARAM_INT);
        $stmt->bindValue(':id_atividade', $atividadeId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
