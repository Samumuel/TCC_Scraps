<?php 

namespace app\models;

use DateTimeImmutable;

class Empresa {

    private int $id_empresa;
    private string $nome;
    private ?string $email;
    private ?string $cnpj;
    private ?string $localizacao;
    private ?string $telefone;
    private ?string $senha;
    private ?string $status;
    private DateTimeImmutable $criadoEm;

    public function __construct(int $id = 0, string $nome = '', ?string $email = null, ?string $cnpj = null, ?string $localizacao = null, ?string $telefone = null, ?string $senha = null, ?string $status = null)
    {
        $this->id_empresa = $id;
        $this->nome = $nome;
        $this->email = $email;
        $this->cnpj = $cnpj;
        $this->localizacao = $localizacao;
        $this->telefone = $telefone;
        $this->senha = $senha;
        $this->status = $status;
        $this->criadoEm = new DateTimeImmutable();
    }  

    /**
     * Get the value of id_empresa
     */
    public function getIdEmpresa(): int
    {
        return $this->id_empresa;
    }

    /**
     * Set the value of id_empresa
     */
    public function setIdEmpresa(int $id_empresa): self
    {
        $this->id_empresa = $id_empresa;

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
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Set the value of email
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of cnpj
     */
    public function getCnpj(): ?string
    {
        return $this->cnpj;
    }

    /**
     * Set the value of cnpj
     */
    public function setCnpj(?string $cnpj): self
    {
        $this->cnpj = $cnpj;

        return $this;
    }

    /**
     * Get the value of localizacao
     */
    public function getLocalizacao(): ?string
    {
        return $this->localizacao;
    }

    /**
     * Set the value of localizacao
     */
    public function setLocalizacao(?string $localizacao): self
    {
        $this->localizacao = $localizacao;

        return $this;
    }

    /**
     * Get the value of telefone
     */
    public function getTelefone(): ?string
    {
        return $this->telefone;
    }

    /**
     * Set the value of telefone
     */
    public function setTelefone(?string $telefone): self
    {
        $this->telefone = $telefone;

        return $this;
    }

    /**
     * Get the value of senha
     */
    public function getSenha(): ?string
    {
        return $this->senha;
    }

    /**
     * Set the value of senha
     */
    public function setSenha(?string $senha): self
    {
        $this->senha = $senha;

        return $this;
    }

    /**
     * Get the value of status
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * Set the value of status
     */
    public function setStatus(?string $status): self
    {
        $this->status = $status;

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

        public static function arrayParaObjeto(array $empresa){

        return new self (
            $empresa['ID_Empresa'],
            $empresa['Nome'],
            $empresa['Email'],
            $empresa['CNPJ'],
            $empresa['Localizacao'],
            $empresa['Telefone'],
            $empresa['Senha'],
            $empresa['Status']
        );

    }
}