<?php

namespace app\repositories;

use app\database\ConnectionFactory;
use app\models\Usuario;
use PDO;
use Throwable;

class UsuarioRepository
{
    private PDO $connection;
    private Usuario $usuario;

    public function __construct()
    {
        $this->connection = ConnectionFactory::getConnection();
    }

    public function getUsuarios(): array
    {
        // A senha não é carregada na listagem
        $sql = "SELECT ID_Usuario, Nome, Email, CPF, Telefone, Idade, Tipo_usuario, Foto FROM Usuario ORDER BY Nome";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function saveUsuario(Usuario $usuario): bool
    {
        $sql = "INSERT INTO Usuario (nome, email, cpf, telefone, idade, tipo_usuario, senha, foto) VALUES (:nome, :email, :cpf, :telefone, :idade, :tipo_usuario, :senha, :foto)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':nome', $usuario->getNome());
        $stmt->bindValue(':email', $usuario->getEmail());
        $stmt->bindValue(':cpf', $usuario->getCpf());
        $stmt->bindValue(':telefone', $usuario->getTelefone());
        $stmt->bindValue(':idade', $usuario->getIdade());
        $stmt->bindValue(':tipo_usuario', $usuario->getTipoUsuario());
        $stmt->bindValue(':senha', password_hash($usuario->getSenha(), PASSWORD_BCRYPT));
        $stmt->bindValue(':foto', $usuario->getFoto());        
        return $stmt->execute();
    }

    public function getUsuarioById(int $id)
    {
        $sql = "SELECT ID_Usuario, Nome, Email, CPF, Telefone, Idade, Tipo_usuario, Foto FROM Usuario WHERE ID_Usuario = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        // Normalizar chaves para as views/controllers existentes
        return [
            'id' => $row['ID_Usuario'],
            'nomeUsuario' => $row['Nome'],
            'email' => $row['Email'],
            'cpf' => $row['CPF'],
            'telefone' => $row['Telefone'],
            'idade' => $row['Idade'],
            'tipo_usuario' => $row['Tipo_usuario'],
            'foto' => $row['Foto']
        ];
    }

    public function getUsuarioByEmail(string $email)
    {
        $sql = "SELECT * FROM Usuario WHERE LOWER(TRIM(email)) = LOWER(TRIM(:email))";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario == null) {
            return false;
        }

        return Usuario::arrayParaObjeto($usuario);
    }

    public function hasEmail(string $email, ?int $ignorarId = null): bool
    {
        $sql = "SELECT 1 FROM Usuario WHERE LOWER(TRIM(Email)) = LOWER(TRIM(:email))";

        if ($ignorarId !== null) {
            $sql .= " AND ID_Usuario <> :id";
        }

        $sql .= " LIMIT 1";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':email', $email);

        if ($ignorarId !== null) {
            $stmt->bindValue(':id', $ignorarId, PDO::PARAM_INT);
        }

        $stmt->execute();

        return (bool) $stmt->fetchColumn();
    }

    public function updateUsuario(Usuario $usuario): bool
    {
        $atualizarSenha = trim($usuario->getSenha()) !== '';
        $sql = "UPDATE Usuario SET nome = :nome, email = :email, cpf = :cpf, telefone = :telefone, idade = :idade, tipo_usuario = :tipo_usuario, foto = :foto";
        if ($atualizarSenha) {
            $sql .= ", senha = :senha";
        }
        $sql .= " WHERE ID_Usuario = :id";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':nome', $usuario->getNome());
        $stmt->bindValue(':email', $usuario->getEmail());
        $stmt->bindValue(':cpf', $usuario->getCpf());
        $stmt->bindValue(':telefone', $usuario->getTelefone());
        $stmt->bindValue(':idade', $usuario->getIdade());
        $stmt->bindValue(':tipo_usuario', $usuario->getTipoUsuario());
        $stmt->bindValue(':foto', $usuario->getFoto());
        if ($atualizarSenha) {
            $stmt->bindValue(':senha', password_hash($usuario->getSenha(), PASSWORD_BCRYPT));
        }
        $stmt->bindValue(':id', $usuario->getIdUsuario());
        return $stmt->execute();
    }

    // Exclui o usuário com agendamentos, avaliações, favoritos e notificações.
    // As vagas das reservas ativas (pendentes ou aprovadas) voltam para as sessões.
    public function deleteUsuario(int $id): bool
    {
        $this->connection->beginTransaction();

        try {
            $sqlReservas = "SELECT ID_Sessao, SUM(Quantidade_Pessoas) AS Total FROM Agendamento
                WHERE ID_Usuario = :id AND Status IN ('pendente', 'aprovado') AND ID_Sessao IS NOT NULL
                GROUP BY ID_Sessao";
            $stmtReservas = $this->connection->prepare($sqlReservas);
            $stmtReservas->bindValue(':id', $id, PDO::PARAM_INT);
            $stmtReservas->execute();

            $stmtVagas = $this->connection->prepare('UPDATE Sessao SET Qtd_disponivel = Qtd_disponivel + :quantidade WHERE ID_Sessao = :id_sessao');
            foreach ($stmtReservas->fetchAll(PDO::FETCH_ASSOC) ?: [] as $reserva) {
                $stmtVagas->bindValue(':quantidade', (int) $reserva['Total'], PDO::PARAM_INT);
                $stmtVagas->bindValue(':id_sessao', (int) $reserva['ID_Sessao'], PDO::PARAM_INT);
                $stmtVagas->execute();
            }

            foreach (['Agendamento', 'Avaliacao', 'Favorito', 'Notificacao'] as $tabela) {
                $stmt = $this->connection->prepare('DELETE FROM ' . $tabela . ' WHERE ID_Usuario = :id');
                $stmt->bindValue(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
            }

            $stmt = $this->connection->prepare('DELETE FROM Usuario WHERE ID_Usuario = :id');
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                $this->connection->rollBack();
                return false;
            }

            $this->connection->commit();

            return true;
        } catch (Throwable $e) {
            if ($this->connection->inTransaction()) {
                $this->connection->rollBack();
            }

            return false;
        }
    }

    public function getIdsAdministradores(): array
    {
        $stmt = $this->connection->prepare("SELECT ID_Usuario FROM Usuario WHERE LOWER(TRIM(Tipo_usuario)) = 'admin'");
        $stmt->execute();

        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN) ?: []);
    }
}
