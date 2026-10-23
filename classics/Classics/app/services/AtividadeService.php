<?php 

namespace app\services;

use app\models\Atividade;
use app\repositories\AtividadeRepository;

class AtividadeService {

    private AtividadeRepository $repository;

    public function __construct(){
        $this->repository = new AtividadeRepository();
    }

    public function getAtividades(){
        return $this->repository->getAtividades();
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
