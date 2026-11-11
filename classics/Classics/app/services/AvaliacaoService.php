<?php

namespace app\services;

use app\models\Avaliacao;
use app\repositories\AvaliacaoRepository;

class AvaliacaoService
{
    private const TAMANHO_MAX_COMENTARIO = 500;

    private AvaliacaoRepository $repository;
    private AgendamentoService $agendamentoService;

    public function __construct()
    {
        $this->repository = new AvaliacaoRepository();
        $this->agendamentoService = new AgendamentoService();
    }

    public function getAvaliacoesPorAtividade(int $atividadeId): array
    {
        if ($atividadeId <= 0) {
            return [];
        }

        return $this->repository->getAvaliacoesPorAtividade($atividadeId);
    }

    public function getResumoPorAtividade(int $atividadeId): array
    {
        if ($atividadeId <= 0) {
            return ['media' => 0.0, 'total' => 0];
        }

        return $this->repository->getResumoPorAtividade($atividadeId);
    }

    public function salvarAvaliacao(int $usuarioId, int $atividadeId, int $nota, string $comentario): array
    {
        if ($usuarioId <= 0) {
            return [
                'sucesso' => false,
                'erro' => 'acesso'
            ];
        }

        if ($nota < 1 || $nota > 5) {
            return [
                'sucesso' => false,
                'erro' => 'nota'
            ];
        }

        $comentario = trim($comentario);
        if (mb_strlen($comentario) > self::TAMANHO_MAX_COMENTARIO) {
            return [
                'sucesso' => false,
                'erro' => 'comentario'
            ];
        }

        // RF12: só pode avaliar quem participou (agendamento aprovado com sessão encerrada)
        if (!$this->participouDaAtividade($usuarioId, $atividadeId)) {
            return [
                'sucesso' => false,
                'erro' => 'nao_permitido'
            ];
        }

        // Uma avaliação por usuário e atividade: se já existe, é atualizada
        $avaliacao = $this->repository->getAvaliacaoPorUsuarioEAtividade($usuarioId, $atividadeId) ?? new Avaliacao();
        $avaliacao->setNota($nota);
        $avaliacao->setComentario($comentario !== '' ? $comentario : null);
        $avaliacao->setIdUsuario($usuarioId);
        $avaliacao->setIdAtividade($atividadeId);

        $salvou = $avaliacao->getIdAvaliacao() > 0
            ? $this->repository->updateAvaliacao($avaliacao)
            : $this->repository->saveAvaliacao($avaliacao);

        if (!$salvou) {
            return [
                'sucesso' => false,
                'erro' => 'falha'
            ];
        }

        return [
            'sucesso' => true
        ];
    }

    public function getAvaliacaoDoUsuario(int $usuarioId, int $atividadeId): ?Avaliacao
    {
        if ($usuarioId <= 0 || $atividadeId <= 0) {
            return null;
        }

        return $this->repository->getAvaliacaoPorUsuarioEAtividade($usuarioId, $atividadeId);
    }

    // RF12: o cliente pode avaliar pela página da atividade se participou dela
    public function podeAvaliar(int $usuarioId, int $atividadeId): bool
    {
        return $usuarioId > 0 && $atividadeId > 0 && $this->participouDaAtividade($usuarioId, $atividadeId);
    }

    // O dono da avaliação pode excluí-la
    public function excluirAvaliacao(int $usuarioId, int $atividadeId): bool
    {
        if ($usuarioId <= 0 || $atividadeId <= 0) {
            return false;
        }

        return $this->repository->deleteAvaliacao($usuarioId, $atividadeId);
    }

    private function participouDaAtividade(int $usuarioId, int $atividadeId): bool
    {
        foreach ($this->agendamentoService->getAgendamentosPorUsuario($usuarioId) as $item) {
            if ((int) $item['agendamento']->getIdAtividade() === $atividadeId && !empty($item['pode_avaliar'])) {
                return true;
            }
        }

        return false;
    }
}
