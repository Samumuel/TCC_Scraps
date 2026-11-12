<?php

namespace app\controllers;

use app\core\Controller;
use app\services\AvaliacaoService;

class AvaliacaoController extends Controller
{
    private AvaliacaoService $service;

    public function __construct()
    {
        $this->service = new AvaliacaoService();
    }

    public function salvar()
    {
        $usuarioId = $this->getClienteLogadoId();
        if ($usuarioId === null) {
            $this->redirect(URL_BASE);
        }

        $atividadeId = isset($_POST['atividade_id']) ? (int) $_POST['atividade_id'] : 0;
        $notaInput = trim((string) ($_POST['nota'] ?? ''));
        $comentario = (string) ($_POST['comentario'] ?? '');
        // A avaliação pode ser feita em Meus Agendamentos ou na página da atividade
        $pelaAtividade = ($_POST['origem'] ?? '') === 'atividade';

        if (!preg_match('/^[1-5]$/', $notaInput)) {
            $this->redirect($this->urlRetorno($pelaAtividade, $atividadeId, 'erro', 'nota'));
        }

        $resultado = $this->service->salvarAvaliacao($usuarioId, $atividadeId, (int) $notaInput, $comentario);

        if (!empty($resultado['sucesso'])) {
            $this->redirect($this->urlRetorno($pelaAtividade, $atividadeId, 'sucesso', 'avaliado'));
        }

        $this->redirect($this->urlRetorno($pelaAtividade, $atividadeId, 'erro', (string) ($resultado['erro'] ?? 'falha')));
    }

    // O dono da avaliação a exclui pela página da atividade
    public function excluir()
    {
        $usuarioId = $this->getClienteLogadoId();
        if ($usuarioId === null) {
            $this->redirect(URL_BASE);
        }

        $atividadeId = isset($_POST['atividade_id']) ? (int) $_POST['atividade_id'] : 0;

        if ($this->service->excluirAvaliacao($usuarioId, $atividadeId)) {
            $this->redirect($this->urlRetorno(true, $atividadeId, 'sucesso', 'excluida'));
        }

        $this->redirect($this->urlRetorno(true, $atividadeId, 'erro', 'falha'));
    }

    private function urlRetorno(bool $pelaAtividade, int $atividadeId, string $tipo, string $codigo): string
    {
        if ($pelaAtividade && $atividadeId > 0) {
            return URL_BASE . '/atividade/ver?id=' . $atividadeId . '&avaliacao_' . $tipo . '=' . rawurlencode($codigo) . '#avaliacoes';
        }

        return URL_BASE . '/agendamento/meus?' . $tipo . '=' . rawurlencode($codigo);
    }

}
