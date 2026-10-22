<?php
// Criado por IA; este model deve ser analisado e validado antes de uso em produção.

namespace app\models;

class Favorito
{
    private int $id_favorito;
    private ?int $id_usuario;
    private ?int $id_atividade;

    public function __construct(int $id = 0, ?int $id_usuario = null, ?int $id_atividade = null)
    {
        $this->id_favorito = $id;
        $this->id_usuario = $id_usuario;
        $this->id_atividade = $id_atividade;
    }

    public function getIdFavorito(): int
    {
        return $this->id_favorito;
    }

    public function setIdFavorito(int $id_favorito): self
    {
        $this->id_favorito = $id_favorito;

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

    public static function arrayParaObjeto(array $favorito): self
    {
        return new self(
            (int) ($favorito['ID_Favorito'] ?? 0),
            isset($favorito['ID_Usuario']) ? (int) $favorito['ID_Usuario'] : null,
            isset($favorito['ID_Atividade']) ? (int) $favorito['ID_Atividade'] : null
        );
    }
}
