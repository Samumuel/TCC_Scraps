<?php 

namespace app\repositories;
use app\models\Empresa;
use app\database\ConnectionFactory;
use PDO;
use Throwable;

class EmpresaRepository {

    private PDO $connection;

    public function __construct()
    {
        $this->connection = ConnectionFactory::getConnection();
    }

    public function getEmpresaByEmail(string $email) {
        $stmt = $this->connection->prepare("SELECT * FROM Empresa WHERE LOWER(TRIM(Email)) = LOWER(TRIM(:email))");
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $empresa = $stmt->fetch();
        
        if ($empresa == null) {
            return false;
        }

        return Empresa::arrayParaObjeto($empresa);
    }

    public function hasEmail(string $email, ?int $ignorarId = null): bool
    {
        $sql = "SELECT 1 FROM Empresa WHERE LOWER(TRIM(Email)) = LOWER(TRIM(:email))";

        if ($ignorarId !== null) {
            $sql .= " AND ID_Empresa <> :id";
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

    public function getEmpresa(int $id) {
        $stmt = $this->connection->prepare("SELECT * FROM Empresa WHERE ID_Empresa = :id");
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $empresa = $stmt->fetch();
        return $empresa; 
    }

    public function saveEmpresa(Empresa $empresa) {
        $sql = "INSERT INTO Empresa (Name, Email, CNPJ, Localizacao, Telefone, Senha, Status) VALUES (:nome, :email, :cnpj, :localizacao, :telefone, :senha, :status)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':nome', $empresa->getNome());
        $stmt->bindValue(':email', $empresa->getEmail());
        $stmt->bindValue(':cnpj', $empresa->getCnpj());
        $stmt->bindValue(':localizacao', $empresa->getLocalizacao());
        $stmt->bindValue(':telefone', $empresa->getTelefone());
        $stmt->bindValue(':senha', password_hash($empresa->getSenha(), PASSWORD_BCRYPT));
        $stmt->bindValue(':status', $empresa->getStatus());

        return $stmt->execute();
    }

    public function deleteEmpresa(int $id) {
        $sql = "DELETE FROM Empresa WHERE ID_Empresa = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }

    public function updateEmpresa(Empresa $empresa) {
        $sql = "UPDATE Empresa SET Name = :nome, Email = :email, CNPJ = :cnpj, Localizacao = :localizacao, Telefone = :telefone, Senha = :senha, Status = :status WHERE ID_Empresa = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':nome', $empresa->getNome());
        $stmt->bindValue(':email', $empresa->getEmail());
        $stmt->bindValue(':cnpj', $empresa->getCnpj());
        $stmt->bindValue(':localizacao', $empresa->getLocalizacao());
        $stmt->bindValue(':telefone', $empresa->getTelefone());
        $stmt->bindValue(':senha', $empresa->getSenha());
        $stmt->bindValue(':status', $empresa->getStatus());
        $stmt->bindValue(':id', $empresa->getIdEmpresa());

        return $stmt->execute();
    }

    // Painel do administrador: empresas de um status, das mais recentes para as mais antigas
    public function getEmpresasPorStatus(string $status): array
    {
        $sql = 'SELECT * FROM Empresa WHERE LOWER(TRIM(Status)) = :status ORDER BY ID_Empresa DESC';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':status', $status);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    // Quantidade de empresas em cada status, ex: ['pendente' => 3, 'aprovada' => 2]
    public function contarPorStatus(): array
    {
        $stmt = $this->connection->prepare('SELECT LOWER(TRIM(Status)) AS Status, COUNT(*) AS Total FROM Empresa GROUP BY LOWER(TRIM(Status))');
        $stmt->execute();

        $contagem = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) ?: [] as $linha) {
            $contagem[(string) $linha['Status']] = (int) $linha['Total'];
        }

        return $contagem;
    }

    public function getStatus(int $id): ?string
    {
        $stmt = $this->connection->prepare('SELECT Status FROM Empresa WHERE ID_Empresa = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $status = $stmt->fetchColumn();

        return $status === false ? null : strtolower(trim((string) $status));
    }

    public function atualizarStatus(int $id, string $status): bool
    {
        $stmt = $this->connection->prepare('UPDATE Empresa SET Status = :status WHERE ID_Empresa = :id');
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    // Exclui a empresa com tudo que depende dela: atividades (sessões, localização,
    // agendamentos, avaliações e favoritos), categorias e notificações
    public function deleteEmpresaComDados(int $id): bool
    {
        $atividadesDaEmpresa = 'SELECT ID_Atividade FROM Atividades WHERE Empresa_ID = :id';
        $comandos = [
            'DELETE FROM Localizacao WHERE ID_Atividade IN (' . $atividadesDaEmpresa . ')',
            'DELETE FROM Agendamento WHERE ID_Atividade IN (' . $atividadesDaEmpresa . ')',
            'DELETE FROM Avaliacao WHERE ID_Atividade IN (' . $atividadesDaEmpresa . ')',
            'DELETE FROM Favorito WHERE ID_Atividade IN (' . $atividadesDaEmpresa . ')',
            'DELETE FROM Sessao WHERE ID_Atividade IN (' . $atividadesDaEmpresa . ')',
            'DELETE FROM Atividades WHERE Empresa_ID = :id',
            // Garantia: nenhuma outra atividade fica apontando para uma categoria apagada
            'UPDATE Atividades SET Categoria_ID = NULL WHERE Categoria_ID IN (SELECT ID_Categoria FROM Categorias WHERE Empresa_ID = :id)',
            'DELETE FROM Categorias WHERE Empresa_ID = :id',
            'DELETE FROM Notificacao WHERE ID_Empresa = :id'
        ];

        $this->connection->beginTransaction();

        try {
            foreach ($comandos as $sql) {
                $stmt = $this->connection->prepare($sql);
                $stmt->bindValue(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
            }

            $stmt = $this->connection->prepare('DELETE FROM Empresa WHERE ID_Empresa = :id');
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
}

