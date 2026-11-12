<?php

namespace app\controllers;

use app\core\Controller;
use app\services\FavoritoService;

class FavoritoController extends Controller
{
    private FavoritoService $service;

    public function __construct()
    {
        $this->service = new FavoritoService();
    }

    public function meusFavoritos()
    {
        $usuarioId = $this->getClienteLogadoId();
        if ($usuarioId === null) {
            $this->redirect(URL_BASE);
        }

        $data['favoritos'] = $this->service->getFavoritosPorUsuario($usuarioId);
        $this->view('favoritos/meus_favoritos', $data);
    }

    public function alternar()
    {
        $usuarioId = $this->getClienteLogadoId();
        if ($usuarioId === null) {
            $this->redirect(URL_BASE . '/login');
        }

        $atividadeId = isset($_POST['atividade_id']) ? (int) $_POST['atividade_id'] : 0;
        $origem = (string) ($_POST['origem'] ?? '');

        $resultado = $this->service->alternarFavorito($usuarioId, $atividadeId);

        if ($origem === 'favoritos') {
            if (!empty($resultado['sucesso'])) {
                $status = !empty($resultado['favoritado']) ? 'adicionado' : 'removido';
                $this->redirect(URL_BASE . '/favorito/meus?sucesso=' . $status);
            }

            $erro = (string) ($resultado['erro'] ?? 'falha');
            $this->redirect(URL_BASE . '/favorito/meus?erro=' . rawurlencode($erro));
        }

        if (($resultado['erro'] ?? '') === 'atividade') {
            $this->redirect(URL_BASE . '/');
        }

        $this->redirect(URL_BASE . '/atividade/ver?id=' . $atividadeId);
    }

}
