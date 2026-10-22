<?php
// Criado por IA; este model deve ser analisado e validado antes de uso em produção.

namespace app\models;

class Categoria
{
    private int $id_categoria;
    private ?string $nome_categoria;
    private int $empresa_id;

    public function __construct(int $id = 0, ?string $nome_categoria = null, int $empresa_id = 0)
    {
        $this->id_categoria = $id;
        $this->nome_categoria = $nome_categoria;
        $this->empresa_id = $empresa_id;
    }

    public function getIdCategoria(): int
    {
        return $this->id_categoria;
    }

    public function setIdCategoria(int $id_categoria): self
    {
        $this->id_categoria = $id_categoria;

        return $this;
    }

    public function getNomeCategoria(): ?string
    {
        return $this->nome_categoria;
    }

    public function setNomeCategoria(?string $nome_categoria): self
    {
        $this->nome_categoria = $nome_categoria;

        return $this;
    }

    public function getEmpresaId(): int
    {
        return $this->empresa_id;
    }

    public function setEmpresaId(int $empresa_id): self
    {
        $this->empresa_id = $empresa_id;

        return $this;
    }

    public static function arrayParaObjeto(array $categoria): self
    {
        return new self(
            (int) ($categoria['ID_Categoria'] ?? 0),
            $categoria['Nome_Categoria'] ?? null,
            (int) ($categoria['Empresa_ID'] ?? 0)
        );
    }
}
