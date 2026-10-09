<?php
// Criado por IA; este model deve ser analisado e validado antes de uso em produção.

namespace app\models;

class Avaliacao
{
    private int $id_avaliacao;
    private ?int $nota;
    private ?string $comentario;
    private ?int $id_usuario;
    private ?int $id_atividade;

    public function __construct(
        int $id = 0,
        ?int $nota = null,
        ?string $comentario = null,
        ?int $id_usuario = null,
        ?int $id_atividade = null
    ) {
        $this->id_avaliacao = $id;
        $this->nota = $nota;
        $this->comentario = $comentario;
        $this->id_usuario = $id_usuario;
        $this->id_atividade = $id_atividade;
    }

    public function getIdAvaliacao(): int
    {
        return $this->id_avaliacao;
    }

    public function setIdAvaliacao(int $id_avaliacao): self
    {
        $this->id_avaliacao = $id_avaliacao;

        return $this;
    }

    public function getNota(): ?int
    {
        return $this->nota;
    }

    public function setNota(?int $nota): self
    {
        $this->nota = $nota;

        return $this;
    }

    public function getComentario(): ?string
    {
        return $this->comentario;
    }

    public function setComentario(?string $comentario): self
    {
        $this->comentario = $comentario;

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

    public function getIdAtividade(): ?int
    {
        return $this->id_atividade;
    }

    public function setIdAtividade(?int $id_atividade): self
    {
        $this->id_atividade = $id_atividade;

        return $this;
    }

    public static function arrayParaObjeto(array $avaliacao): self
    {
        return new self(
            (int) ($avaliacao['ID_Avaliacao'] ?? 0),
            isset($avaliacao['Nota']) ? (int) $avaliacao['Nota'] : null,
            $avaliacao['Comentario'] ?? null,
            isset($avaliacao['ID_Usuario']) ? (int) $avaliacao['ID_Usuario'] : null,
            isset($avaliacao['ID_Atividade']) ? (int) $avaliacao['ID_Atividade'] : null
        );
    }
}
