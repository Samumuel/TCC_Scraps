<?php

namespace app\repositories;

use app\database\ConnectionFactory;
use app\models\Agendamento;
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

            $dataSessao = trim((string) ($sessao['Data'] ?? ''));
            $horaSessao = substr(trim((string) ($sessao['Hora'] ?? '')), 0, 8);
            if ($dataSessao === '' || $horaSessao === '') {
                $this->connection->rollBack();

                return [
                    'sucesso' => false,
                    'erro' => 'sessao'
                ];
            }

            $agendamento->setIdSessao($sessaoId);
            $agendamento->setIdAtividade((int) ($sessao['ID_Atividade'] ?? 0));
            $agendamento->setQuantidadePessoas($quantidadePessoas);
            $agendamento->setDataHora($dataSessao . ' ' . $horaSessao);
            $agendamento->setStatus('aprovado');

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
}