<?php 

namespace app\repositories;
use app\core\Controller;
use app\models\Empresa;
use app\services\EmpresaService;
use app\database\ConnectionFactory;
use PDO;

class EmpresaRepository {

    private PDO $connection;

    public function __construct()
    {
        $this->connection = ConnectionFactory::getConnection();
    }

    public function getEmpresas() {
        $stmt = $this->connection->query("SELECT * FROM Empresa");
        
        $empresas = $stmt->fetchALL();
        return $empresas;
    }

    public function getEmpresaByEmail(string $email) {
        $stmt = $this->connection->prepare("SELECT * FROM Empresa WHERE email = :email");
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $empresa = $stmt->fetch();
        
        if ($empresa == null) {
            return false;
        }

        return Empresa::arrayParaObjeto($empresa);
    }

    public function getEmpresa(int $id) {
        $stmt = $this->connection->prepare("SELECT * FROM Empresa WHERE ID_Empresa = :id");
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $empresa = $stmt->fetch();
        return $empresa; 
    }

    public function saveEmpresa(Empresa $empresa) {
        $sql = "INSERT INTO Empresa (nome, email, cnpj, localizacao, telefone, senha, status) VALUES (:nome, :email, :cnpj, :localizacao, :telefone, :senha, :status)";
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
        $sql = "UPDATE Empresa SET nome = :nome, email = :email, cnpj = :cnpj, localizacao = :localizacao, telefone = :telefone, senha = :senha, status = :status WHERE ID_Empresa = :id";
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
}


