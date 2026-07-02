<?php

namespace app\models;

use DateTimeImmutable;

class Usuario
{
    private int $id_usuario;
    private string $nome;
    private string $email;
    private string $cpf;
    private string $telefone;
    private string $idade;
    private string $tipo_usuario;
    private string $foto;
    private string $senha;
    private DateTimeImmutable $criadoEm;

    public function __construct(int $id = 0, string $nome = '', ?string $email = null, ?string $cpf = null, ?
    string $telefone = null, ?string $idade = null, ?string $tipo_usuario = null, ?string $foto = null, ?string $senha = null){
        $this->id_usuario = $id;
        $this->nome = $nome;
        $this->email = $email ?? '';
        $this->cpf = $cpf ?? '';
        $this->telefone = $telefone ?? '';
        $this->idade = $idade ?? '';
        $this->tipo_usuario = $tipo_usuario ?? '';
        $this->foto = $foto ?? '';
        $this->senha = $senha ?? '';
        $this->criadoEm = new DateTimeImmutable();
    } 

    /**
     * Get the value of id_usuario
     */
    public function getIdUsuario(): int
    {
        return $this->id_usuario;
    }

    /**
     * Set the value of id_usuario
     */
    public function setIdUsuario(int $id_usuario): self
    {
        $this->id_usuario = $id_usuario;

        return $this;
    }

    /**
     * Get the value of nome
     */
    public function getNome(): string
    {
        return $this->nome;
    }

    /**
     * Set the value of nome
     */
    public function setNome(string $nome): self
    {
        $this->nome = $nome;

        return $this;
    }

    /**
     * Get the value of email
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Set the value of email
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of cpf
     */
    public function getCpf(): string
    {
        return $this->cpf;
    }

    /**
     * Set the value of cpf
     */
    public function setCpf(string $cpf): self
    {
        $this->cpf = $cpf;

        return $this;
    }

    /**
     * Get the value of telefone
     */
    public function getTelefone(): string
    {
        return $this->telefone;
    }

    /**
     * Set the value of telefone
     */
    public function setTelefone(string $telefone): self
    {
        $this->telefone = $telefone;

        return $this;
    }

    /**
     * Get the value of idade
     */
    public function getIdade(): int
    {
        return $this->idade;
    }

    /**
     * Set the value of idade
     */
    public function setIdade(int $idade): self
    {
        $this->idade = $idade;

        return $this;
    }

    /**
     * Get the value of tipo_usuario
     */
    public function getTipoUsuario(): string
    {
        return $this->tipo_usuario;
    }

    /**
     * Set the value of tipo_usuario
     */
    public function setTipoUsuario(string $tipo_usuario): self
    {
        $this->tipo_usuario = $tipo_usuario;

        return $this;
    }

    /**
     * Get the value of foto
     */
    public function getFoto(): string
    {
        return $this->foto;
    }

    /**
     * Set the value of foto
     */
    public function setFoto(string $foto): self
    {
        $this->foto = $foto;

        return $this;
    }

    /**
     * Get the value of senha
     */
    public function getSenha(): string
    {
        return $this->senha;
    }

    /**
     * Set the value of senha
     */
    public function setSenha(string $senha): self
    {
        $this->senha = $senha;

        return $this;
    }

    /**
     * Get the value of criadoEm
     */
    public function getCriadoEm(): DateTimeImmutable
    {
        return $this->criadoEm;
    }

    /**
     * Set the value of criadoEm
     */
    public function setCriadoEm(DateTimeImmutable $criadoEm): self
    {
        $this->criadoEm = $criadoEm;

        return $this;
    }

    public static function arrayParaObjeto(array $usuario){

        return new self (
            $usuario['ID_Usuario'],
            $usuario['Nome'],
            $usuario['Email'],
            $usuario['CPF'],
            $usuario['Telefone'],
            $usuario['Idade'],
            $usuario['Tipo_usuario'],
            $usuario['Foto'],
            $usuario['Senha']
        );

    }
}