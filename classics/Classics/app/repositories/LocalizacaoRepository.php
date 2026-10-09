<?php

namespace app\repositories;

use app\database\ConnectionFactory;
use app\models\Localizacao;
use PDO;

class LocalizacaoRepository
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = ConnectionFactory::getConnection();
    }

    public function getLocalizacaoPorAtividade(int $atividadeId): ?array
    {
        $sql = 'SELECT * FROM Localizacao WHERE ID_Atividade = :id_atividade LIMIT 1';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id_atividade', $atividadeId);
        $stmt->execute();

        $localizacao = $stmt->fetch();

        return $localizacao ?: null;
    }

    public function saveOrUpdateLocalizacao(Localizacao $localizacao): bool
    {
        if ($localizacao->getIdAtividade() === null) {
            return false;
        }

        $existente = $this->getLocalizacaoPorAtividade($localizacao->getIdAtividade());

        if ($existente) {
            $sql = 'UPDATE Localizacao SET CEP = :cep, Complemento = :complemento WHERE ID_Atividade = :id_atividade';
        } else {
            $sql = 'INSERT INTO Localizacao (CEP, Complemento, ID_Atividade) VALUES (:cep, :complemento, :id_atividade)';
        }

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':cep', $localizacao->getCep());
        $stmt->bindValue(':complemento', $localizacao->getComplemento());
        $stmt->bindValue(':id_atividade', $localizacao->getIdAtividade());

        return $stmt->execute();
    }
}
