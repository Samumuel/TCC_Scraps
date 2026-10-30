<?php 

namespace app\services;

use app\models\Atividade;
use app\repositories\AtividadeRepository;
use DateTime;

class AtividadeService {

    private AtividadeRepository $repository;

    public function __construct(){
        $this->repository = new AtividadeRepository();
    }

    public function getAtividades(){
        return $this->repository->getAtividades();
    }

    public function filtrarAtividades(string $busca, string $categoria, string $data): array
    {
        $erros = [];
        $filtros = [
            'busca' => mb_substr(trim($busca), 0, 100),
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

        return [
            'atividades' => $this->repository->getAtividades($filtros),
            'filtros' => $filtros,
            'erros' => $erros
        ];
    }

    public function getAtividadesPorEmpresa(int $empresaId): array
    {
        $atividades = [];
        foreach ($this->repository->getAtividades() ?: [] as $atividade) {
            if ((int) ($atividade['Empresa_ID'] ?? 0) === $empresaId) {
                $atividades[] = Atividade::arrayParaObjeto($atividade);
            }
        }

        return $atividades;
    }

    public function getAtividade(int $id){
        return $this->repository->getAtividade($id);
    }

    public function saveAtividade(Atividade $atividade){
        return $this->repository->saveAtividade($atividade);
    }

    public function updateAtividade(Atividade $atividade){
        return $this->repository->updateAtividade($atividade);
    }

    public function deleteAtividade(int $id, int $empresaId){
        return $this->repository->deleteAtividade($id, $empresaId);
    }
}
