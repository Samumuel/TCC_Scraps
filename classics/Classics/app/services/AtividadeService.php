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
