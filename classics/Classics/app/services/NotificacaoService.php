<?php

namespace app\services;

use app\models\Agendamento;
use app\models\Notificacao;
use app\models\Sessao;
use app\repositories\AgendamentoRepository;
use app\repositories\AtividadeRepository;
use app\repositories\NotificacaoRepository;
use app\repositories\UsuarioRepository;
use DateTime;

class NotificacaoService
{
    public const DESTINATARIO_USUARIO = 'usuario';
    public const DESTINATARIO_EMPRESA = 'empresa';

    private const LIMITE_RECENTES = 10;
    private const LINK_MEUS_AGENDAMENTOS = '/agendamento/meus';
    private const LINK_RESERVAS_EMPRESA = '/agendamento/empresa';

    private NotificacaoRepository $repository;
    private AtividadeRepository $atividadeRepository;
    private AgendamentoRepository $agendamentoRepository;
    private UsuarioRepository $usuarioRepository;

    public function __construct()
    {
        $this->repository = new NotificacaoRepository();
        $this->atividadeRepository = new AtividadeRepository();
        $this->agendamentoRepository = new AgendamentoRepository();
        $this->usuarioRepository = new UsuarioRepository();
    }

    public function getResumo(string $tipoDestinatario, int $destinatarioId): array
    {
        if ($destinatarioId <= 0) {
            return ['nao_lidas' => 0, 'recentes' => []];
        }

        $coluna = $this->colunaDestinatario($tipoDestinatario);

        return [
            'nao_lidas' => $this->repository->countNaoLidas($coluna, $destinatarioId),
            'recentes' => $this->repository->getRecentes($coluna, $destinatarioId, self::LIMITE_RECENTES)
        ];
    }

    // Marca a notificação como lida e devolve o link interno para onde o usuário deve ir
    public function abrir(string $tipoDestinatario, int $destinatarioId, int $notificacaoId): ?string
    {
        $coluna = $this->colunaDestinatario($tipoDestinatario);
        $notificacao = $this->repository->getNotificacaoDoDestinatario($notificacaoId, $coluna, $destinatarioId);
        if ($notificacao === null) {
            return null;
        }

        $this->repository->marcarComoLida($notificacaoId, $coluna, $destinatarioId);

        return $this->linkInterno((string) $notificacao->getLink());
    }

    public function marcarTodasComoLidas(string $tipoDestinatario, int $destinatarioId): bool
    {
        return $this->repository->marcarTodasComoLidas($this->colunaDestinatario($tipoDestinatario), $destinatarioId);
    }

    // ---- Notificações para a empresa ----

    public function notificarNovaSolicitacao(int $atividadeId, Sessao $sessao, int $usuarioId, int $quantidadePessoas): void
    {
        $atividade = $this->atividadeRepository->getAtividade($atividadeId);
        if (!$atividade) {
            return;
        }

        $mensagem = $this->nomeCliente($usuarioId) . ' solicitou uma reserva para ' . $quantidadePessoas . ' '
            . ($quantidadePessoas > 1 ? 'pessoas' : 'pessoa') . ' em ' . $atividade['Nome'] . ' (' . $this->formatarSessao($sessao) . ').';

        $this->criar(null, (int) $atividade['Empresa_ID'], $mensagem, self::LINK_RESERVAS_EMPRESA . '?atividade=' . $atividadeId);
    }

    public function notificarReservaAlteradaPeloCliente(Agendamento $agendamento, Sessao $sessao): void
    {
        $atividade = $this->atividadeRepository->getAtividade((int) $agendamento->getIdAtividade());
        if (!$atividade) {
            return;
        }

        $mensagem = $this->nomeCliente((int) $agendamento->getIdUsuario()) . ' aumentou a reserva em ' . $atividade['Nome']
            . ' (' . $this->formatarSessao($sessao) . ') para ' . (int) $agendamento->getQuantidadePessoas() . ' pessoas. A reserva aguarda sua aprovação.';

        $this->criar(null, (int) $atividade['Empresa_ID'], $mensagem, self::LINK_RESERVAS_EMPRESA . '?atividade=' . (int) $agendamento->getIdAtividade());
    }

    public function notificarSessaoTrocadaPeloCliente(Agendamento $agendamento, Sessao $sessaoAnterior, Sessao $sessaoNova): void
    {
        $atividade = $this->atividadeRepository->getAtividade((int) $agendamento->getIdAtividade());
        if (!$atividade) {
            return;
        }

        $mensagem = $this->nomeCliente((int) $agendamento->getIdUsuario()) . ' trocou a reserva em ' . $atividade['Nome']
            . ' de ' . $this->formatarSessao($sessaoAnterior) . ' para ' . $this->formatarSessao($sessaoNova)
            . '. A reserva aguarda sua aprovação.';

        $this->criar(null, (int) $atividade['Empresa_ID'], $mensagem, self::LINK_RESERVAS_EMPRESA . '?atividade=' . (int) $agendamento->getIdAtividade());
    }

    public function notificarCancelamentoPeloCliente(Agendamento $agendamento, Sessao $sessao): void
    {
        $atividade = $this->atividadeRepository->getAtividade((int) $agendamento->getIdAtividade());
        if (!$atividade) {
            return;
        }

        $mensagem = $this->nomeCliente((int) $agendamento->getIdUsuario()) . ' cancelou a reserva em ' . $atividade['Nome']
            . ' (' . $this->formatarSessao($sessao) . ').';

        $this->criar(null, (int) $atividade['Empresa_ID'], $mensagem, self::LINK_RESERVAS_EMPRESA . '?atividade=' . (int) $agendamento->getIdAtividade());
    }

    // ---- Notificações para o cliente (RF08, RF13 e RN07) ----

    public function notificarReservaAprovada(Agendamento $agendamento, Sessao $sessao): void
    {
        $this->notificarClienteSobreReserva($agendamento, $sessao, 'foi confirmada pela empresa.');
    }

    public function notificarReservaRecusada(Agendamento $agendamento, Sessao $sessao): void
    {
        $this->notificarClienteSobreReserva($agendamento, $sessao, 'foi recusada pela empresa.');
    }

    public function notificarReservaCanceladaPelaEmpresa(Agendamento $agendamento, Sessao $sessao): void
    {
        $this->notificarClienteSobreReserva($agendamento, $sessao, 'foi cancelada pela empresa.');
    }

    public function getClientesComReservaAtivaNaSessao(int $sessaoId): array
    {
        $agora = new DateTime();

        return $this->agendamentoRepository->getUsuariosComReservaAtivaNaSessao($sessaoId, $agora->format('Y-m-d'), $agora->format('H:i:s'));
    }

    public function notificarSessaoAlterada(array $usuariosIds, int $atividadeId, Sessao $sessaoAnterior, Sessao $sessaoNova): void
    {
        $nomeAtividade = $this->nomeAtividade($atividadeId);
        $mensagem = 'A sessão de ' . $nomeAtividade . ' de ' . $this->formatarSessao($sessaoAnterior)
            . ' foi alterada para ' . $this->formatarSessao($sessaoNova) . '.';

        foreach ($usuariosIds as $usuarioId) {
            $this->criar((int) $usuarioId, null, $mensagem, self::LINK_MEUS_AGENDAMENTOS);
        }
    }

    public function notificarSessaoExcluida(array $usuariosIds, int $atividadeId, Sessao $sessao): void
    {
        $mensagem = 'A sessão de ' . $this->nomeAtividade($atividadeId) . ' de ' . $this->formatarSessao($sessao)
            . ' foi cancelada pela empresa. Sua reserva para esta sessão foi removida.';

        foreach ($usuariosIds as $usuarioId) {
            $this->criar((int) $usuarioId, null, $mensagem, '/atividade/ver?id=' . $atividadeId);
        }
    }

    public function notificarLocalAlterado(int $atividadeId): void
    {
        $agora = new DateTime();
        $usuariosIds = $this->agendamentoRepository->getUsuariosComReservaAtivaNaAtividade($atividadeId, $agora->format('Y-m-d'), $agora->format('H:i:s'));
        $mensagem = 'O local de ' . $this->nomeAtividade($atividadeId) . ' foi alterado. Confira o novo endereço antes da sua sessão.';

        foreach ($usuariosIds as $usuarioId) {
            $this->criar((int) $usuarioId, null, $mensagem, '/atividade/ver?id=' . $atividadeId);
        }
    }

    // ---- Notificações do painel do administrador (RF02) ----

    public function notificarNovaEmpresa(string $nomeEmpresa): void
    {
        $nome = trim($nomeEmpresa) !== '' ? trim($nomeEmpresa) : 'Uma nova empresa';
        $mensagem = $nome . ' se cadastrou na plataforma e aguarda aprovação.';

        foreach ($this->usuarioRepository->getIdsAdministradores() as $adminId) {
            $this->criar($adminId, null, $mensagem, '/admin?aba=pendente');
        }
    }

    public function notificarStatusEmpresa(int $empresaId, string $statusAnterior, string $novoStatus): void
    {
        $linkPerfil = '/empresa/ver?id=' . $empresaId;

        if ($novoStatus === 'aprovada') {
            $mensagem = $statusAnterior === 'suspensa'
                ? 'Sua empresa foi reativada pelo administrador. Suas atividades voltaram ao catálogo.'
                : 'Seu cadastro foi aprovado! Agora você já pode cadastrar atividades e receber agendamentos.';
            $link = $statusAnterior === 'suspensa' ? '/agendamento/empresa' : '/atividade/cadastrar';
        } elseif ($novoStatus === 'rejeitada') {
            $mensagem = 'Seu cadastro foi rejeitado pelo administrador da plataforma.';
            $link = $linkPerfil;
        } elseif ($novoStatus === 'suspensa') {
            $mensagem = 'Sua empresa foi suspensa pelo administrador. Suas atividades não aparecem no catálogo enquanto a suspensão durar.';
            $link = $linkPerfil;
        } else {
            $mensagem = 'Seu cadastro voltou para análise do administrador.';
            $link = $linkPerfil;
        }

        $this->criar(null, $empresaId, $mensagem, $link);
    }

    public function notificarAtividadeExcluida(array $usuariosIds, string $nomeAtividade): void
    {
        $nome = trim($nomeAtividade) !== '' ? trim($nomeAtividade) : 'uma atividade';
        $mensagem = 'A atividade ' . $nome . ' foi removida da plataforma. Sua reserva foi cancelada.';

        foreach ($usuariosIds as $usuarioId) {
            $this->criar((int) $usuarioId, null, $mensagem, '/');
        }
    }

    // Avisa os clientes com reservas futuras quando o administrador exclui a empresa
    public function notificarEmpresaExcluida(array $reservasAtivas): void
    {
        foreach ($reservasAtivas as $reserva) {
            $mensagem = 'Sua reserva em ' . (string) ($reserva['Nome'] ?? 'uma atividade')
                . ' foi cancelada porque a empresa responsável foi removida da plataforma.';
            $this->criar((int) ($reserva['ID_Usuario'] ?? 0), null, $mensagem, '/');
        }
    }

    private function notificarClienteSobreReserva(Agendamento $agendamento, Sessao $sessao, string $complemento): void
    {
        $mensagem = 'Sua reserva em ' . $this->nomeAtividade((int) $agendamento->getIdAtividade())
            . ' (' . $this->formatarSessao($sessao) . ') ' . $complemento;

        $this->criar((int) $agendamento->getIdUsuario(), null, $mensagem, self::LINK_MEUS_AGENDAMENTOS);
    }

    private function criar(?int $usuarioId, ?int $empresaId, string $mensagem, string $link): void
    {
        if (($usuarioId === null || $usuarioId <= 0) && ($empresaId === null || $empresaId <= 0)) {
            return;
        }

        $notificacao = new Notificacao();
        $notificacao->setMensagem(mb_substr($mensagem, 0, 255));
        $notificacao->setLink($link);
        $notificacao->setDataHora((new DateTime())->format('Y-m-d H:i:s'));
        $notificacao->setIdUsuario($usuarioId);
        $notificacao->setIdEmpresa($empresaId);

        $this->repository->saveNotificacao($notificacao);
    }

    private function nomeAtividade(int $atividadeId): string
    {
        $atividade = $this->atividadeRepository->getAtividade($atividadeId);

        return $atividade ? (string) $atividade['Nome'] : 'uma atividade';
    }

    private function nomeCliente(int $usuarioId): string
    {
        $usuario = $this->usuarioRepository->getUsuarioById($usuarioId);

        return $usuario ? (string) $usuario['nomeUsuario'] : 'Um cliente';
    }

    private function formatarSessao(Sessao $sessao): string
    {
        $partes = explode('-', (string) $sessao->getData());
        $data = count($partes) === 3 ? $partes[2] . '/' . $partes[1] . '/' . $partes[0] : (string) $sessao->getData();

        return $data . ' às ' . substr((string) $sessao->getHora(), 0, 5);
    }

    // Só permite redirecionar para caminhos internos do site
    private function linkInterno(string $link): string
    {
        return (str_starts_with($link, '/') && !str_starts_with($link, '//')) ? $link : '/';
    }

    private function colunaDestinatario(string $tipoDestinatario): string
    {
        return $tipoDestinatario === self::DESTINATARIO_EMPRESA ? 'ID_Empresa' : 'ID_Usuario';
    }
}
