<?php

namespace app\repositories;

use app\database\ConnectionFactory;
use app\models\Avaliacao;
use PDO;

class AvaliacaoRepository
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = ConnectionFactory::getConnection();
    }

    public function getAvaliacaoPorUsuarioEAtividade(int $usuarioId, int $atividadeId): ?Avaliacao
    {
        $sql = 'SELECT * FROM Avaliacao WHERE ID_Usuario = :id_usuario AND ID_Atividade = :id_atividade
                ORDER BY ID_Avaliacao DESC LIMIT 1';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id_usuario', $usuarioId, PDO::PARAM_INT);
        $stmt->bindValue(':id_atividade', $atividadeId, PDO::PARAM_INT);
        $stmt->execute();

        $avaliacao = $stmt->fetch(PDO::FETCH_ASSOC);

        return $avaliacao ? Avaliacao::arrayParaObjeto($avaliacao) : null;
    }

    public function getAvaliacoesPorUsuario(int $usuarioId): array
    {
        $sql = 'SELECT * FROM Avaliacao WHERE ID_Usuario = :id_usuario ORDER BY ID_Avaliacao ASC';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id_usuario', $usuarioId, PDO::PARAM_INT);
        $stmt->execute();

        // Indexadas pela atividade: cada usuário tem uma avaliação por atividade
        $avaliacoes = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) ?: [] as $linha) {
            $avaliacao = Avaliacao::arrayParaObjeto($linha);
            $avaliacoes[(int) $avaliacao->getIdAtividade()] = $avaliacao;
        }

        return $avaliacoes;
    }

    public function getAvaliacoesPorAtividade(int $atividadeId): array
    {
        $sql = 'SELECT av.*, u.Nome AS Usuario_Nome
                FROM Avaliacao av
                LEFT JOIN Usuario u ON u.ID_Usuario = av.ID_Usuario
                WHERE av.ID_Atividade = :id_atividade
                ORDER BY av.ID_Avaliacao DESC';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id_atividade', $atividadeId, PDO::PARAM_INT);
        $stmt->execute();

        $avaliacoes = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) ?: [] as $linha) {
            $avaliacoes[] = [
                'avaliacao' => Avaliacao::arrayParaObjeto($linha),
                'usuario_nome' => (string) ($linha['Usuario_Nome'] ?? '')
            ];
        }

        return $avaliacoes;
    }

    public function getResumoPorAtividade(int $atividadeId): array
    {
        $sql = 'SELECT AVG(Nota) AS Media, COUNT(*) AS Total FROM Avaliacao WHERE ID_Atividade = :id_atividade';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id_atividade', $atividadeId, PDO::PARAM_INT);
        $stmt->execute();

        $resumo = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'media' => (float) ($resumo['Media'] ?? 0),
            'total' => (int) ($resumo['Total'] ?? 0)
        ];
    }

    public function saveAvaliacao(Avaliacao $avaliacao): bool
    {
        $sql = 'INSERT INTO Avaliacao (Nota, Comentario, ID_Usuario, ID_Atividade)
                VALUES (:nota, :comentario, :id_usuario, :id_atividade)';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':nota', $avaliacao->getNota(), PDO::PARAM_INT);
        $stmt->bindValue(':comentario', $avaliacao->getComentario());
        $stmt->bindValue(':id_usuario', $avaliacao->getIdUsuario(), PDO::PARAM_INT);
        $stmt->bindValue(':id_atividade', $avaliacao->getIdAtividade(), PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function updateAvaliacao(Avaliacao $avaliacao): bool
    {
        $sql = 'UPDATE Avaliacao SET Nota = :nota, Comentario = :comentario
                WHERE ID_Avaliacao = :id_avaliacao AND ID_Usuario = :id_usuario';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':nota', $avaliacao->getNota(), PDO::PARAM_INT);
        $stmt->bindValue(':comentario', $avaliacao->getComentario());
        $stmt->bindValue(':id_avaliacao', $avaliacao->getIdAvaliacao(), PDO::PARAM_INT);
        $stmt->bindValue(':id_usuario', $avaliacao->getIdUsuario(), PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Exclui a avaliação do próprio usuário naquela atividade
    public function deleteAvaliacao(int $usuarioId, int $atividadeId): bool
    {
        $stmt = $this->connection->prepare('DELETE FROM Avaliacao WHERE ID_Usuario = :id_usuario AND ID_Atividade = :id_atividade');
        $stmt->bindValue(':id_usuario', $usuarioId, PDO::PARAM_INT);
        $stmt->bindValue(':id_atividade', $atividadeId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
