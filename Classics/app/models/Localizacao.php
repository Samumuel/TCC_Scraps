<?php
// Criado por IA; este model deve ser analisado e validado antes de uso em produção.

namespace app\models;

class Localizacao
{
    private int $id_localizacao;
    private ?string $cep;
    private ?string $complemento;
    private ?int $id_atividade; 

    public function __construct(
        int $id = 0,
        ?string $cep = null,
        ?string $complemento = null,
        ?int $id_atividade = null
    ) {
        $this->id_localizacao = $id;
        $this->cep = $cep;
        $this->complemento = $complemento;
        $this->id_atividade = $id_atividade;
    }

    public function getIdLocalizacao(): int
    {
        return $this->id_localizacao;
    }

    public function setIdLocalizacao(int $id_localizacao): self
    {
        $this->id_localizacao = $id_localizacao;

        return $this;
    }

    public function getCep(): ?string
    {
        return $this->cep;
    }

    public function setCep(?string $cep): self
    {
        $this->cep = $cep;

        return $this;
    }

    public function getComplemento(): ?string
    {
        return $this->complemento;
    }

    public function setComplemento(?string $complemento): self
    {
        $this->complemento = $complemento;

        return $this;
    }

    public function getIdAtividade(): ?int
    {
        return $this->id_atividade;
    }

    public function setIdAtividade(?int $id_atividade): self
    {
        $this->id_atividade = $id_atividade;

        return $this;
    }

    public static function arrayParaObjeto(array $localizacao): self
    {
        return new self(
            (int) ($localizacao['ID_Localizacao'] ?? 0),
            $localizacao['CEP'] ?? null,
            $localizacao['Complemento'] ?? null,
            isset($localizacao['ID_Atividade']) ? (int) $localizacao['ID_Atividade'] : null
        );
    }
}
