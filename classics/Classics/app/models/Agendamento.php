<?php
// Criado por IA; este model deve ser analisado e validado antes de uso em produção.

namespace app\models;

class Agendamento
{
    private int $id_agendamento;
    private ?int $quantidade_pessoas;
    private ?string $data_hora;
    private ?string $status;
    private ?int $id_atividade; //Pode ser removida, mas vai ter q mudar os diagramas 
    private ?int $id_usuario;
    private ?int $id_sessao;

    public function __construct(
        int $id = 0,
        ?int $quantidade_pessoas = null,
        ?string $data_hora = null,
        ?string $status = null,
        ?int $id_atividade = null,
        ?int $id_usuario = null,
        ?int $id_sessao = null
    ) {
        $this->id_agendamento = $id;
        $this->quantidade_pessoas = $quantidade_pessoas;
        $this->data_hora = $data_hora;
        $this->status = $status;
        $this->id_atividade = $id_atividade;
        $this->id_usuario = $id_usuario;
        $this->id_sessao = $id_sessao;
    }

    public function getIdAgendamento(): int
    {
        return $this->id_agendamento;
    }

    public function setIdAgendamento(int $id_agendamento): self
    {
        $this->id_agendamento = $id_agendamento;

        return $this;
    }

    public function getQuantidadePessoas(): ?int
    {
        return $this->quantidade_pessoas;
    }

    public function setQuantidadePessoas(?int $quantidade_pessoas): self
    {
        $this->quantidade_pessoas = $quantidade_pessoas;

        return $this;
    }

    public function getDataHora(): ?string
    {
        return $this->data_hora;
    }

    public function setDataHora(?string $data_hora): self
    {
        $this->data_hora = $data_hora;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

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

    public function getIdUsuario(): ?int
    {
        return $this->id_usuario;
    }

    public function setIdUsuario(?int $id_usuario): self
    {
        $this->id_usuario = $id_usuario;

        return $this;
    }

    public function getIdSessao(): ?int
    {
        return $this->id_sessao;
    }

    public function setIdSessao(?int $id_sessao): self
    {
        $this->id_sessao = $id_sessao;

        return $this;
    }

    public static function arrayParaObjeto(array $agendamento): self
    {
        return new self(
            (int) ($agendamento['ID_Agendamento'] ?? 0),
            isset($agendamento['Quantidade_Pessoas']) ? (int) $agendamento['Quantidade_Pessoas'] : null,
            $agendamento['Data_hora'] ?? null,
            $agendamento['Status'] ?? null,
            isset($agendamento['ID_Atividade']) ? (int) $agendamento['ID_Atividade'] : null,
            isset($agendamento['ID_Usuario']) ? (int) $agendamento['ID_Usuario'] : null,
            isset($agendamento['ID_Sessao']) ? (int) $agendamento['ID_Sessao'] : null
        );
    }
}
