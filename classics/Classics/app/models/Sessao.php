<?php
// Criado por IA; este model deve ser analisado e validado antes de uso em produção.

namespace app\models;

class Sessao
{
    private int $id_sessao;
    private ?string $data;
    private ?string $hora;
    private ?int $qtd_min;
    private ?int $qtd_max;
    private ?int $qtd_disponivel;
    private ?int $id_atividade;

    public function __construct(
        int $id = 0,
        ?string $data = null,
        ?string $hora = null,
        ?int $qtd_min = null,
        ?int $qtd_max = null,
        ?int $qtd_disponivel = null,
        ?int $id_atividade = null
    ) {
        $this->id_sessao = $id;
        $this->data = $data;
        $this->hora = $hora;
        $this->qtd_min = $qtd_min;
        $this->qtd_max = $qtd_max;
        $this->qtd_disponivel = $qtd_disponivel;
        $this->id_atividade = $id_atividade;
    }

    public function getIdSessao(): int
    {
        return $this->id_sessao;
    }

    public function setIdSessao(int $id_sessao): self
    {
        $this->id_sessao = $id_sessao;

        return $this;
    }

    public function getData(): ?string
    {
        return $this->data;
    }

    public function setData(?string $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getHora(): ?string
    {
        return $this->hora;
    }

    public function setHora(?string $hora): self
    {
        $this->hora = $hora;

        return $this;
    }

    public function getQtdMin(): ?int
    {
        return $this->qtd_min;
    }

    public function setQtdMin(?int $qtd_min): self
    {
        $this->qtd_min = $qtd_min;

        return $this;
    }

    public function getQtdMax(): ?int
    {
        return $this->qtd_max;
    }

    public function setQtdMax(?int $qtd_max): self
    {
        $this->qtd_max = $qtd_max;

        return $this;
    }

    public function getQtdDisponivel(): ?int
    {
        return $this->qtd_disponivel;
    }

    public function setQtdDisponivel(?int $qtd_disponivel): self
    {
        $this->qtd_disponivel = $qtd_disponivel;

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

    public static function arrayParaObjeto(array $sessao): self
    {
        return new self(
            (int) ($sessao['ID_Sessao'] ?? 0),
            $sessao['Data'] ?? null,
            $sessao['Hora'] ?? null,
            isset($sessao['Qtd_min']) ? (int) $sessao['Qtd_min'] : null,
            isset($sessao['Qtd_max']) ? (int) $sessao['Qtd_max'] : null,
            isset($sessao['Qtd_disponivel']) ? (int) $sessao['Qtd_disponivel'] : null,
            isset($sessao['ID_Atividade']) ? (int) $sessao['ID_Atividade'] : null
        );
    }
}
