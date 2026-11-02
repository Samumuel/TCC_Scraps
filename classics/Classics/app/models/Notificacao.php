<?php

namespace app\models;

class Notificacao
{
    private int $id_notificacao;
    private ?string $mensagem;
    private ?string $link;
    private bool $lida;
    private ?string $data_hora;
    private ?int $id_usuario;
    private ?int $id_empresa;

    public function __construct(
        int $id = 0,
        ?string $mensagem = null,
        ?string $link = null,
        bool $lida = false,
        ?string $data_hora = null,
        ?int $id_usuario = null,
        ?int $id_empresa = null
    ) {
        $this->id_notificacao = $id;
        $this->mensagem = $mensagem;
        $this->link = $link;
        $this->lida = $lida;
        $this->data_hora = $data_hora;
        $this->id_usuario = $id_usuario;
        $this->id_empresa = $id_empresa;
    }

    public function getIdNotificacao(): int
    {
        return $this->id_notificacao;
    }

    public function setIdNotificacao(int $id_notificacao): self
    {
        $this->id_notificacao = $id_notificacao;

        return $this;
    }

    public function getMensagem(): ?string
    {
        return $this->mensagem;
    }

    public function setMensagem(?string $mensagem): self
    {
        $this->mensagem = $mensagem;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function isLida(): bool
    {
        return $this->lida;
    }

    public function setLida(bool $lida): self
    {
        $this->lida = $lida;

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

    public function getIdUsuario(): ?int
    {
        return $this->id_usuario;
    }

    public function setIdUsuario(?int $id_usuario): self
    {
        $this->id_usuario = $id_usuario;

        return $this;
    }

    public function getIdEmpresa(): ?int
    {
        return $this->id_empresa;
    }

    public function setIdEmpresa(?int $id_empresa): self
    {
        $this->id_empresa = $id_empresa;

        return $this;
    }

    public static function arrayParaObjeto(array $notificacao): self
    {
        return new self(
            (int) ($notificacao['ID_Notificacao'] ?? 0),
            $notificacao['Mensagem'] ?? null,
            $notificacao['Link'] ?? null,
            (bool) ($notificacao['Lida'] ?? false),
            $notificacao['Data_hora'] ?? null,
            isset($notificacao['ID_Usuario']) ? (int) $notificacao['ID_Usuario'] : null,
            isset($notificacao['ID_Empresa']) ? (int) $notificacao['ID_Empresa'] : null
        );
    }
}
