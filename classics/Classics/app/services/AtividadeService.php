<?php 

namespace app\services;

use app\models\Atividade;
use app\repositories\AgendamentoRepository;
use app\repositories\AtividadeRepository;
use DateTime;

class AtividadeService {

    private AtividadeRepository $repository;

    public function __construct(){
        $this->repository = new AtividadeRepository();
    }

    public function filtrarAtividades(string $busca, string $cidade, string $categoria, string $data): array
    {
        $erros = [];
        $filtros = [
            'busca' => mb_substr(trim($busca), 0, 100),
            'cidade' => mb_substr(trim($cidade), 0, 100),
            'categoria' => mb_substr(trim($categoria), 0, 100),
            'data' => trim($data)
        ];

        if ($filtros['data'] !== '') {
            $dataInformada = DateTime::createFromFormat('!Y-m-d', $filtros['data']);

            if ($dataInformada === false || $dataInformada->format('Y-m-d') !== $filtros['data']) {
                $erros['data'] = 'Informe uma data valida.';
                $filtros['data'] = '';
            } elseif ($dataInformada < new DateTime('today')) {
                // RN04: nao permitir buscar atividades em datas passadas
                $erros['data'] = 'Nao e possivel buscar atividades em datas passadas.';
                $filtros['data'] = '';
            }
        }

        // RN03: o Explorar lista só atividades que podem ser agendadas (sessão futura com vaga), para todos.
        // As atividades indisponíveis da empresa aparecem em "Minhas Atividades".
        $agora = new DateTime();
        $consulta = $filtros + [
            'somente_disponiveis' => true,
            // RN02: somente atividades de empresas aprovadas
            'somente_empresas_aprovadas' => true,
            // RF06: atividades mais bem avaliadas primeiro
            'ordenar_por_avaliacao' => true,
            'hoje' => $agora->format('Y-m-d'),
            'agora' => $agora->format('H:i:s')
        ];

        return [
            'atividades' => $this->repository->getAtividades($consulta),
            'filtros' => $filtros,
            'erros' => $erros
        ];
    }

    // Atividades de uma empresa (consulta filtrada no banco, sem carregar todas as atividades)
    public function getAtividadesPorEmpresa(int $empresaId): array
    {
        return array_map(
            fn(array $atividade) => Atividade::arrayParaObjeto($atividade),
            $this->repository->getAtividadesPorEmpresa($empresaId)
        );
    }

    public function countAtividades(): int
    {
        return $this->repository->countAtividades();
    }

    public function getAtividade(int $id){
        return $this->repository->getAtividade($id);
    }

    public function saveAtividade(Atividade $atividade){
        return $this->repository->saveAtividade($atividade);
    }

    public function updateAtividade(Atividade $atividade){
        $atividadeAnterior = $this->repository->getAtividade($atividade->getIdAtividade());

        if (!$this->repository->updateAtividade($atividade)) {
            return false;
        }

        // RN07: mudança de cidade avisa os clientes com reserva ativa
        if ($atividadeAnterior && trim((string) ($atividadeAnterior['Localizacao'] ?? '')) !== trim((string) $atividade->getLocalizacao())) {
            (new NotificacaoService())->notificarLocalAlterado($atividade->getIdAtividade());
        }

        return true;
    }

    public function getAtividadesDaEmpresaComResumo(int $empresaId, string $hoje, string $agora): array
    {
        return $this->repository->getAtividadesDaEmpresaComResumo($empresaId, $hoje, $agora);
    }

    public function deleteAtividade(int $id, int $empresaId){
        $atividade = $this->repository->getAtividade($id);
        $agora = new DateTime();
        $clientes = (new AgendamentoRepository())->getUsuariosComReservaAtivaNaAtividade($id, $agora->format('Y-m-d'), $agora->format('H:i:s'));

        if (!$this->repository->deleteAtividade($id, $empresaId)) {
            return false;
        }

        // Clientes com reserva futura são avisados de que a atividade deixou de existir
        (new NotificacaoService())->notificarAtividadeExcluida($clientes, (string) ($atividade['Nome'] ?? ''));

        return true;
    }
}
