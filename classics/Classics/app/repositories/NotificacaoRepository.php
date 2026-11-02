<?php

namespace app\repositories;

use app\database\ConnectionFactory;
use app\models\Notificacao;
use PDO;

class NotificacaoRepository
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = ConnectionFactory::getConnection();
    }

    public function saveNotificacao(Notificacao $notificacao): bool
    {
        $sql = 'INSERT INTO Notificacao (Mensagem, Link, Lida, Data_hora, ID_Usuario, ID_Empresa)
                VALUES (:mensagem, :link, 0, :data_hora, :id_usuario, :id_empresa)';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':mensagem', $notificacao->getMensagem());
        $stmt->bindValue(':link', $notificacao->getLink());
        $stmt->bindValue(':data_hora', $notificacao->getDataHora());
        $stmt->bindValue(':id_usuario', $notificacao->getIdUsuario(), $notificacao->getIdUsuario() === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(':id_empresa', $notificacao->getIdEmpresa(), $notificacao->getIdEmpresa() === null ? PDO::PARAM_NULL : PDO::PARAM_INT);

        return $stmt->execute();
    }

    // $coluna é sempre ID_Usuario ou ID_Empresa, definido pelo service (nunca vem do usuário)
    public function getRecentes(string $coluna, int $destinatarioId, int $limite): array
    {
        $sql = 'SELECT * FROM Notificacao WHERE ' . $this->colunaDestinatario($coluna) . ' = :destinatario
                ORDER BY Data_hora DESC, ID_Notificacao DESC LIMIT ' . max(1, $limite);
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':destinatario', $destinatarioId, PDO::PARAM_INT);
        $stmt->execute();

        return array_map(fn($linha) => Notificacao::arrayParaObjeto($linha), $stmt->fetchAll(PDO::FETCH_ASSOC) ?: []);
    }

    public function countNaoLidas(string $coluna, int $destinatarioId): int
    {
        $sql = 'SELECT COUNT(*) AS total FROM Notificacao WHERE ' . $this->colunaDestinatario($coluna) . ' = :destinatario AND Lida = 0';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':destinatario', $destinatarioId, PDO::PARAM_INT);
        $stmt->execute();

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int) ($resultado['total'] ?? 0);
    }

    public function getNotificacaoDoDestinatario(int $notificacaoId, string $coluna, int $destinatarioId): ?Notificacao
    {
        $sql = 'SELECT * FROM Notificacao WHERE ID_Notificacao = :id_notificacao AND ' . $this->colunaDestinatario($coluna) . ' = :destinatario LIMIT 1';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id_notificacao', $notificacaoId, PDO::PARAM_INT);
        $stmt->bindValue(':destinatario', $destinatarioId, PDO::PARAM_INT);
        $stmt->execute();

        $notificacao = $stmt->fetch(PDO::FETCH_ASSOC);

        return $notificacao ? Notificacao::arrayParaObjeto($notificacao) : null;
    }

    public function marcarComoLida(int $notificacaoId, string $coluna, int $destinatarioId): bool
    {
        $sql = 'UPDATE Notificacao SET Lida = 1 WHERE ID_Notificacao = :id_notificacao AND ' . $this->colunaDestinatario($coluna) . ' = :destinatario';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id_notificacao', $notificacaoId, PDO::PARAM_INT);
        $stmt->bindValue(':destinatario', $destinatarioId, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function marcarTodasComoLidas(string $coluna, int $destinatarioId): bool
    {
        $sql = 'UPDATE Notificacao SET Lida = 1 WHERE ' . $this->colunaDestinatario($coluna) . ' = :destinatario AND Lida = 0';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':destinatario', $destinatarioId, PDO::PARAM_INT);

        return $stmt->execute();
    }

    private function colunaDestinatario(string $coluna): string
    {
        return $coluna === 'ID_Empresa' ? 'ID_Empresa' : 'ID_Usuario';
    }
}
