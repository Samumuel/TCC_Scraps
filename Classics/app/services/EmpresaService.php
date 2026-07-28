<?php 

namespace app\services;

use app\models\Empresa;
use app\repositories\EmpresaRepository;

class EmpresaService {

    private EmpresaRepository $repository;

    public function __construct(){

        $this->repository = new EmpresaRepository();

    }

    public function getEmpresas(){
        return $this->repository->getEmpresas();
    
    }

    public function getEmpresa(int $id){
        return $this->repository->getEmpresa($id);
    }

    public function saveEmpresa(Empresa $empresa){
        return $this->repository->saveEmpresa($empresa);
    }

    public function deleteEmpresa(int $id) {
        return $this->repository->deleteEmpresa($id);
    }

    public function updateEmpresa(Empresa $empresa) {
        return $this->repository->updateEmpresa($empresa);
    }
}