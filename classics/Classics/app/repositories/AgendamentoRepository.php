<?php

namespace app\repositories;

use app\database\ConnectionFactory;
use app\models\Agendamento;
use app\models\Sessao;
use DateTime;
use PDO;
use Throwable;

class AgendamentoRepository
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = ConnectionFactory::getConnection();
    }

    public function registrarInscricao(Agendamento $agendamento, int $sessaoId, int $quantidadePessoas): array
    {
        $this->connection->beginTransaction();

        try {
            $sqlSessao = 'SELECT * FROM Sessao WHERE ID_Sessao = :id_sessao FOR UPDATE';
            $stmtSessao = $this->connection->prepare($sqlSessao);
            $stmtSessao->bindValue(':id_sessao', $sessaoId, PDO::PARAM_INT);
            $stmtSessao->execute();

            $sessao = $stmtSessao->fetch(PDO::FETCH_ASSOC);
            if (!$sessao) {
                $this->connection->rollBack();

                return [
                    'sucesso' => false,
                    'erro' => 'sessao'
                ];
            }

            $qtdDisponivel = isset($sessao['Qtd_disponivel']) ? (int) $sessao['Qtd_disponivel'] : 0;
            $qtdMaxima = isset($sessao['Qtd_max']) ? (int) $sessao['Qtd_max'] : 0;

            if ($quantidadePessoas <= 0) {
                $this->connection->rollBack();

                return [
                    'sucesso' => false,
                    'erro' => 'quantidade'
                ];
            }

            if ($qtdDisponivel <= 0 || $quantidadePessoas > $qtdDisponivel) {
                $this->connection->rollBack();

                return [
                    'sucesso' => false,
                    'erro' => 'lotacao'
                ];
            }

            if ($qtdMaxima > 0 && $quantidadePessoas > $qtdMaxima) {
                $this->connection->rollBack();

                return [
                    'sucesso' => false,
                    'erro' => 'lotacao'
                ];
            }

            // RN02: só recebe agendamentos a empresa aprovada pelo administrador
            $sqlEmpresa = "SELECT 1 FROM Atividades a INNER JOIN Empresa e ON e.ID_Empresa = a.Empresa_ID
                WHERE a.ID_Atividade = :id_atividade AND LOWER(TRIM(e.Status)) = 'aprovada'";
            $stmtEmpresa = $this->connection->prepare($sqlEmpresa);
            $stmtEmpresa->bindValue(':id_atividade', (int) ($sessao['ID_Atividade'] ?? 0), PDO::PARAM_INT);
            $stmtEmpresa->execute();
            if (!$stmtEmpresa->fetchColumn()) {
                $this->connection->rollBack();

                return [
                    'sucesso' => false,
                    'erro' => 'indisponivel'
                ];
            }

            $dataSessao = trim((string) ($sessao['Data'] ?? ''));
            $horaSessao = substr(trim((string) ($sessao['Hora'] ?? '')), 0, 8);
            if ($dataSessao === '' || $horaSessao === '') {
                $this->connection->rollBack();

                return [
                    'sucesso' => false,
                    'erro' => 'sessao'
                ];
            }

            // Não permite inscrição em sessão que já começou ou passou
            try {
                $sessaoEncerrada = new DateTime($dataSessao . ' ' . $horaSessao) <= new DateTime();
            } catch (Throwable $e) {
                $sessaoEncerrada = true;
            }

            if ($sessaoEncerrada) {
                $this->connection->rollBack();

                return [
                    'sucesso' => false,
                    'erro' => 'encerrada'
                ];
            }

            $agendamento->setIdSessao($sessaoId);
            $agendamento->setIdAtividade((int) ($sessao['ID_Atividade'] ?? 0));
            $agendamento->setQuantidadePessoas($quantidadePessoas);
            $agendamento->setDataHora($dataSessao . ' ' . $horaSessao);
            $agendamento->setStatus('pendente');

            $sqlAgendamento = 'INSERT INTO Agendamento (Quantidade_Pessoas, Data_hora, Status, ID_Atividade, ID_Usuario, ID_Sessao)
                VALUES (:quantidade_pessoas, :data_hora, :status, :id_atividade, :id_usuario, :id_sessao)';
            $stmtAgendamento = $this->connection->prepare($sqlAgendamento);
            $stmtAgendamento->bindValue(':quantidade_pessoas', $agendamento->getQuantidadePessoas(), PDO::PARAM_INT);
            $stmtAgendamento->bindValue(':data_hora', $agendamento->getDataHora());
            $stmtAgendamento->bindValue(':status', $agendamento->getStatus());
            $stmtAgendamento->bindValue(':id_atividade', $agendamento->getIdAtividade(), PDO::PARAM_INT);
            $stmtAgendamento->bindValue(':id_usuario', $agendamento->getIdUsuario(), PDO::PARAM_INT);
            $stmtAgendamento->bindValue(':id_sessao', $agendamento->getIdSessao(), PDO::PARAM_INT);
            $stmtAgendamento->execute();

            $sqlAtualizarSessao = 'UPDATE Sessao SET Qtd_disponivel = Qtd_disponivel - :quantidade_pessoas WHERE ID_Sessao = :id_sessao';
            $stmtAtualizarSessao = $this->connection->prepare($sqlAtualizarSessao);
            $stmtAtualizarSessao->bindValue(':quantidade_pessoas', $quantidadePessoas, PDO::PARAM_INT);
            $stmtAtualizarSessao->bindValue(':id_sessao', $sessaoId, PDO::PARAM_INT);
            $stmtAtualizarSessao->execute();

            $this->connection->commit();

            return [
                'sucesso' => true,
                'atividade_id' => (int) ($sessao['ID_Atividade'] ?? 0)
            ];
        } catch (Throwable $e) {
            if ($this->connection->inTransaction()) {
                $this->connection->rollBack();
            }

            return [
                'sucesso' => false,
                'erro' => 'falha'
            ];
        }
    }

    public function getAgendamentosPorUsuario(int $usuarioId): array
    {
        $sql = 'SELECT a.*, s.Data, s.Hora, s.Qtd_min, s.Qtd_max, s.Qtd_disponivel, s.ID_Atividade AS Sessao_ID_Atividade,
                    at.Nome AS Atividade_Nome, at.Fotos AS Atividade_Fotos, at.Duracao AS Atividade_Duracao
                FROM Agendamento a
                INNER JOIN Sessao s ON s.ID_Sessao = a.ID_Sessao
                LEFT JOIN Atividades at ON at.ID_Atividade = a.ID_Atividade
                WHERE a.ID_Usuario = :id_usuario
                ORDER BY s.Data DESC, s.Hora DESC';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id_usuario', $usuarioId, PDO::PARAM_INT);
        $stmt->execute();

        $agendamentos = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) ?: [] as $linha) {
            $sessao = $linha;
            $sessao['ID_Atividade'] = $linha['Sessao_ID_Atividade'] ?? null;

            $agendamentos[] = [
                'agendamento' => Agendamento::arrayParaObjeto($linha),
                'sessao' => Sessao::arrayParaObjeto($sessao),
                'atividade_nome' => (string) ($linha['Atividade_Nome'] ?? ''),
                'atividade_foto' => (string) ($linha['Atividade_Fotos'] ?? ''),
                'atividade_duracao' => (string) ($linha['Atividade_Duracao'] ?? '')
            ];
        }

        return $agendamentos;
    }

    public function getAgendamentosPorEmpresa(int $empresaId, int $atividadeId = 0): array
    {
        $sql = 'SELECT a.*, s.Data, s.Hora, s.Qtd_min, s.Qtd_max, s.Qtd_disponivel, s.ID_Atividade AS Sessao_ID_Atividade,
                    at.Nome AS Atividade_Nome, u.Nome AS Cliente_Nome, u.Email AS Cliente_Email
                FROM Agendamento a
                INNER JOIN Sessao s ON s.ID_Sessao = a.ID_Sessao
                INNER JOIN Atividades at ON at.ID_Atividade = a.ID_Atividade
                LEFT JOIN Usuario u ON u.ID_Usuario = a.ID_Usuario
                WHERE at.Empresa_ID = :empresa_id';

        if ($atividadeId > 0) {
            $sql .= ' AND a.ID_Atividade = :id_atividade';
        }

        $sql .= ' ORDER BY s.Data ASC, s.Hora ASC, at.Nome ASC';

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':empresa_id', $empresaId, PDO::PARAM_INT);
        if ($atividadeId > 0) {
            $stmt->bindValue(':id_atividade', $atividadeId, PDO::PARAM_INT);
        }
        $stmt->execute();

        $agendamentos = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) ?: [] as $linha) {
            $sessao = $linha;
            $sessao['ID_Atividade'] = $linha['Sessao_ID_Atividade'] ?? null;

            $agendamentos[] = [
                'agendamento' => Agendamento::arrayParaObjeto($linha),
                'sessao' => Sessao::arrayParaObjeto($sessao),
                'atividade_nome' => (string) ($linha['Atividade_Nome'] ?? ''),
                'cliente_nome' => (string) ($linha['Cliente_Nome'] ?? ''),
                'cliente_email' => (string) ($linha['Cliente_Email'] ?? '')
            ];
        }

        return $agendamentos;
    }

    public function getAgendamentoPorEmpresa(int $idAgendamento, int $empresaId): ?Agendamento
    {
        $sql = 'SELECT a.* FROM Agendamento a
                INNER JOIN Atividades at ON at.ID_Atividade = a.ID_Atividade
                WHERE a.ID_Agendamento = :id_agendamento AND at.Empresa_ID = :empresa_id LIMIT 1';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id_agendamento', $idAgendamento, PDO::PARAM_INT);
        $stmt->bindValue(':empresa_id', $empresaId, PDO::PARAM_INT);
        $stmt->execute();

        $agendamento = $stmt->fetch(PDO::FETCH_ASSOC);

        return $agendamento ? Agendamento::arrayParaObjeto($agendamento) : null;
    }

    // Total de pessoas com reserva ativa (pendente ou aprovada) na sessão
    public function getTotalReservadoNaSessao(int $sessaoId): int
    {
        $sql = "SELECT COALESCE(SUM(Quantidade_Pessoas), 0) AS total FROM Agendamento
                WHERE ID_Sessao = :id_sessao AND Status IN ('pendente', 'aprovado')";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id_sessao', $sessaoId, PDO::PARAM_INT);
        $stmt->execute();

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int) ($resultado['total'] ?? 0);
    }

    // Clientes com reserva ativa (pendente ou aprovada) em uma sessão que ainda não aconteceu
    public function getUsuariosComReservaAtivaNaSessao(int $sessaoId, string $hoje, string $agora): array
    {
        $sql = "SELECT DISTINCT a.ID_Usuario
                FROM Agendamento a
                INNER JOIN Sessao s ON s.ID_Sessao = a.ID_Sessao
                WHERE a.ID_Sessao = :id_sessao AND a.Status IN ('pendente', 'aprovado')
                AND (s.Data > :hoje OR (s.Data = :hoje_hora AND s.Hora > :agora))";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id_sessao', $sessaoId, PDO::PARAM_INT);
        $stmt->bindValue(':hoje', $hoje);
        $stmt->bindValue(':hoje_hora', $hoje);
        $stmt->bindValue(':agora', $agora);
        $stmt->execute();

        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN) ?: []);
    }

    // Clientes com reserva ativa (pendente ou aprovada) em qualquer sessão futura da atividade
    public function getUsuariosComReservaAtivaNaAtividade(int $atividadeId, string $hoje, string $agora): array
    {
        $sql = "SELECT DISTINCT a.ID_Usuario
                FROM Agendamento a
                INNER JOIN Sessao s ON s.ID_Sessao = a.ID_Sessao
                WHERE a.ID_Atividade = :id_atividade AND a.Status IN ('pendente', 'aprovado')
                AND (s.Data > :hoje OR (s.Data = :hoje_hora AND s.Hora > :agora))";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id_atividade', $atividadeId, PDO::PARAM_INT);
        $stmt->bindValue(':hoje', $hoje);
        $stmt->bindValue(':hoje_hora', $hoje);
        $stmt->bindValue(':agora', $agora);
        $stmt->execute();

        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN) ?: []);
    }

    // Reservas ativas em sessões futuras das atividades de uma empresa (cliente e nome da atividade)
    public function getReservasAtivasDaEmpresa(int $empresaId, string $hoje, string $agora): array
    {
        $sql = "SELECT DISTINCT a.ID_Usuario, at.Nome
                FROM Agendamento a
                INNER JOIN Sessao s ON s.ID_Sessao = a.ID_Sessao
                INNER JOIN Atividades at ON at.ID_Atividade = a.ID_Atividade
                WHERE at.Empresa_ID = :empresa_id AND a.Status IN ('pendente', 'aprovado')
                AND (s.Data > :hoje OR (s.Data = :hoje_hora AND s.Hora > :agora))";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':empresa_id', $empresaId, PDO::PARAM_INT);
        $stmt->bindValue(':hoje', $hoje);
        $stmt->bindValue(':hoje_hora', $hoje);
        $stmt->bindValue(':agora', $agora);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getAgendamentoPorUsuario(int $idAgendamento, int $usuarioId): ?Agendamento
    {
        $sql = 'SELECT * FROM Agendamento WHERE ID_Agendamento = :id_agendamento AND ID_Usuario = :id_usuario LIMIT 1';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id_agendamento', $idAgendamento, PDO::PARAM_INT);
        $stmt->bindValue(':id_usuario', $usuarioId, PDO::PARAM_INT);
        $stmt->execute();

        $agendamento = $stmt->fetch(PDO::FETCH_ASSOC);

        return $agendamento ? Agendamento::arrayParaObjeto($agendamento) : null;
    }

    public function atualizarQuantidadePessoas(Agendamento $agendamento, int $novaQuantidade, ?string $novoStatus = null): bool
    {
        $diferenca = $novaQuantidade - (int) $agendamento->getQuantidadePessoas();
        $novoStatus = $novoStatus ?? (string) $agendamento->getStatus();

        $this->connection->beginTransaction();

        try {
            $sqlAgendamento = 'UPDATE Agendamento SET Quantidade_Pessoas = :nova_quantidade, Status = :novo_status
                WHERE ID_Agendamento = :id_agendamento AND ID_Usuario = :id_usuario
                AND Quantidade_Pessoas = :quantidade_atual AND Status = :status';
            $stmtAgendamento = $this->connection->prepare($sqlAgendamento);
            $stmtAgendamento->bindValue(':nova_quantidade', $novaQuantidade, PDO::PARAM_INT);
            $stmtAgendamento->bindValue(':novo_status', $novoStatus);
            $stmtAgendamento->bindValue(':id_agendamento', $agendamento->getIdAgendamento(), PDO::PARAM_INT);
            $stmtAgendamento->bindValue(':id_usuario', $agendamento->getIdUsuario(), PDO::PARAM_INT);
            $stmtAgendamento->bindValue(':quantidade_atual', $agendamento->getQuantidadePessoas(), PDO::PARAM_INT);
            $stmtAgendamento->bindValue(':status', $agendamento->getStatus());
            $stmtAgendamento->execute();

            if ($stmtAgendamento->rowCount() === 0) {
                $this->connection->rollBack();

                return false;
            }

            // Só desconta as vagas se ainda houver disponibilidade suficiente (diferença negativa devolve vagas)
            $sqlAtualizarSessao = 'UPDATE Sessao SET Qtd_disponivel = Qtd_disponivel - :diferenca
                WHERE ID_Sessao = :id_sessao AND Qtd_disponivel >= :diferenca_minima';
            $stmtAtualizarSessao = $this->connection->prepare($sqlAtualizarSessao);
            $stmtAtualizarSessao->bindValue(':diferenca', $diferenca, PDO::PARAM_INT);
            $stmtAtualizarSessao->bindValue(':diferenca_minima', $diferenca, PDO::PARAM_INT);
            $stmtAtualizarSessao->bindValue(':id_sessao', $agendamento->getIdSessao(), PDO::PARAM_INT);
            $stmtAtualizarSessao->execute();

            if ($stmtAtualizarSessao->rowCount() === 0) {
                $this->connection->rollBack();

                return false;
            }

            $this->connection->commit();
            $agendamento->setQuantidadePessoas($novaQuantidade);
            $agendamento->setStatus($novoStatus);

            return true;
        } catch (Throwable $e) {
            if ($this->connection->inTransaction()) {
                $this->connection->rollBack();
            }

            return false;
        }
    }

    public function aprovarAgendamento(Agendamento $agendamento, string $statusAprovado): bool
    {
        $sql = 'UPDATE Agendamento SET Status = :status_aprovado
                WHERE ID_Agendamento = :id_agendamento AND Status = :status_atual';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':status_aprovado', $statusAprovado);
        $stmt->bindValue(':id_agendamento', $agendamento->getIdAgendamento(), PDO::PARAM_INT);
        $stmt->bindValue(':status_atual', $agendamento->getStatus());
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            return false;
        }

        $agendamento->setStatus($statusAprovado);

        return true;
    }

    public function cancelarAgendamento(Agendamento $agendamento, string $statusCancelado): bool
    {
        $this->connection->beginTransaction();

        try {
            $sqlAgendamento = 'UPDATE Agendamento SET Status = :status_cancelado
                WHERE ID_Agendamento = :id_agendamento AND ID_Usuario = :id_usuario AND Status = :status_atual';
            $stmtAgendamento = $this->connection->prepare($sqlAgendamento);
            $stmtAgendamento->bindValue(':status_cancelado', $statusCancelado);
            $stmtAgendamento->bindValue(':id_agendamento', $agendamento->getIdAgendamento(), PDO::PARAM_INT);
            $stmtAgendamento->bindValue(':id_usuario', $agendamento->getIdUsuario(), PDO::PARAM_INT);
            $stmtAgendamento->bindValue(':status_atual', $agendamento->getStatus());
            $stmtAgendamento->execute();

            if ($stmtAgendamento->rowCount() === 0) {
                $this->connection->rollBack();

                return false;
            }

            $sqlAtualizarSessao = 'UPDATE Sessao SET Qtd_disponivel = Qtd_disponivel + :quantidade_pessoas WHERE ID_Sessao = :id_sessao';
            $stmtAtualizarSessao = $this->connection->prepare($sqlAtualizarSessao);
            $stmtAtualizarSessao->bindValue(':quantidade_pessoas', (int) $agendamento->getQuantidadePessoas(), PDO::PARAM_INT);
            $stmtAtualizarSessao->bindValue(':id_sessao', $agendamento->getIdSessao(), PDO::PARAM_INT);
            $stmtAtualizarSessao->execute();

            $this->connection->commit();
            $agendamento->setStatus($statusCancelado);

            return true;
        } catch (Throwable $e) {
            if ($this->connection->inTransaction()) {
                $this->connection->rollBack();
            }

            return false;
        }
    }
}