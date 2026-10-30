<?php

namespace app\repositories;

use app\database\ConnectionFactory;
use app\models\Atividade;
use app\models\Favorito;
use PDO;

class FavoritoRepository
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = ConnectionFactory::getConnection();
    }

    public function isFavorito(int $usuarioId, int $atividadeId): bool
    {
        $sql = 'SELECT COUNT(*) AS total FROM Favorito WHERE ID_Usuario = :id_usuario AND ID_Atividade = :id_atividade';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id_usuario', $usuarioId, PDO::PARAM_INT);
        $stmt->bindValue(':id_atividade', $atividadeId, PDO::PARAM_INT);
        $stmt->execute();

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int) ($resultado['total'] ?? 0) > 0;
    }

    public function saveFavorito(Favorito $favorito): bool
    {
        $sql = 'INSERT INTO Favorito (ID_Usuario, ID_Atividade) VALUES (:id_usuario, :id_atividade)';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id_usuario', $favorito->getIdUsuario(), PDO::PARAM_INT);
        $stmt->bindValue(':id_atividade', $favorito->getIdAtividade(), PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function deleteFavorito(int $usuarioId, int $atividadeId): bool
    {
        $sql = 'DELETE FROM Favorito WHERE ID_Usuario = :id_usuario AND ID_Atividade = :id_atividade';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id_usuario', $usuarioId, PDO::PARAM_INT);
        $stmt->bindValue(':id_atividade', $atividadeId, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function getFavoritosPorUsuario(int $usuarioId, string $hoje, string $agora): array
    {
        $sql = 'SELECT f.ID_Favorito, f.ID_Usuario, at.*, c.Nome_Categoria AS Categoria_Nome,
                    EXISTS (SELECT 1 FROM Sessao s WHERE s.ID_Atividade = at.ID_Atividade AND s.Qtd_disponivel > 0
                        AND (s.Data > :hoje OR (s.Data = :hoje_hora AND s.Hora > :agora))) AS Tem_Sessao_Disponivel
                FROM Favorito f
                INNER JOIN Atividades at ON at.ID_Atividade = f.ID_Atividade
                LEFT JOIN Categorias c ON c.ID_Categoria = at.Categoria_ID
                WHERE f.ID_Usuario = :id_usuario
                ORDER BY f.ID_Favorito DESC';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id_usuario', $usuarioId, PDO::PARAM_INT);
        $stmt->bindValue(':hoje', $hoje);
        $stmt->bindValue(':hoje_hora', $hoje);
        $stmt->bindValue(':agora', $agora);
        $stmt->execute();

        $favoritos = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) ?: [] as $linha) {
            $favoritos[] = [
                'favorito' => Favorito::arrayParaObjeto($linha),
                'atividade' => Atividade::arrayParaObjeto($linha),
                'categoria_nome' => (string) ($linha['Categoria_Nome'] ?? ''),
                'disponivel' => (bool) ($linha['Tem_Sessao_Disponivel'] ?? false)
            ];
        }

        return $favoritos;
    }
}
