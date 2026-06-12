<?php

namespace app\repositories;

use app\database\ConnectionFactory;
use app\models\Usuario;
use PDO;

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
        $sql = "SELECT * FROM Usuario";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
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
        $sql = "SELECT id, nome as nomeUsuario, email, cpf, telefone, idade, tipo_usuario, foto FROM Usuario WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getUsuarioByEmail(string $email)
    {
        $sql = "SELECT * FROM Usuario WHERE email = :email";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $usuario = $stmt->fetch();

        if ($usuario == null) {
            return false;
        }

        // return Usuario::arrayParaObjeto($usuario); <-- tem que verificar.
    }

    public function updateUsuario(Usuario $usuario): bool
    {
        $sql = "UPDATE Usuario SET nome = :nome, email = :email, cpf = :cpf, telefone = :telefone, idade = :idade, tipo_usuario = :tipo_usuario, foto = :foto, senha = :senha WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':nome', $usuario->getNome());
        $stmt->bindValue(':email', $usuario->getEmail());
        $stmt->bindValue(':cpf', $usuario->getCpf());
        $stmt->bindValue(':telefone', $usuario->getTelefone());
        $stmt->bindValue(':idade', $usuario->getIdade());
        $stmt->bindValue(':tipo_usuario', $usuario->getTipoUsuario());
        $stmt->bindValue(':foto', $usuario->getFoto());
        $stmt->bindValue(':senha', password_hash($usuario->getSenha(), PASSWORD_BCRYPT));
        $stmt->bindValue(':id', $usuario->getIdUsuario());
        return $stmt->execute();
    }

    public function deleteUsuario(int $id): bool
    {
        $sql = "DELETE FROM usuario WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }
}
