<?php

namespace app\models;

class Atividade
{
	private int $id_atividade;
	private ?string $nome;
	private ?string $duracao;
	private ?string $descricao;
	private ?string $localizacao;
	private ?float $valor;
	private ?string $fotos;
	private ?int $categoria_id;
	private ?int $empresa_id;

	public function __construct(
		int $id = 0,
		?string $nome = null,
		?string $duracao = null,
		?string $descricao = null,
		?string $localizacao = null,
		?float $valor = null,
		?string $fotos = null,
		?int $categoria_id = null,
		?int $empresa_id = null
	) {
		$this->id_atividade = $id;
		$this->nome = $nome;
		$this->duracao = $duracao;
		$this->descricao = $descricao;
		$this->localizacao = $localizacao;
		$this->valor = $valor;
		$this->fotos = $fotos;
		$this->categoria_id = $categoria_id;
		$this->empresa_id = $empresa_id;
	}

	public function getIdAtividade(): int
	{
		return $this->id_atividade;
	}

	public function setIdAtividade(int $id_atividade): self
	{
		$this->id_atividade = $id_atividade;

		return $this;
	}

	public function getNome(): ?string
	{
		return $this->nome;
	}

	public function setNome(?string $nome): self
	{
		$this->nome = $nome;

		return $this;
	}

	public function getDuracao(): ?string
	{
		return $this->duracao;
	}

	public function setDuracao(?string $duracao): self
	{
		$this->duracao = $duracao;

		return $this;
	}

	public function getDescricao(): ?string
	{
		return $this->descricao;
	}

	public function setDescricao(?string $descricao): self
	{
		$this->descricao = $descricao;

		return $this;
	}

	public function getLocalizacao(): ?string
	{
		return $this->localizacao;
	}

	public function setLocalizacao(?string $localizacao): self
	{
		$this->localizacao = $localizacao;

		return $this;
	}

	public function getValor(): ?float
	{
		return $this->valor;
	}

	public function setValor(?float $valor): self
	{
		$this->valor = $valor;

		return $this;
	}

	public function getFotos(): ?string
	{
		return $this->fotos;
	}

	public function setFotos(?string $fotos): self
	{
		$this->fotos = $fotos;

		return $this;
	}

	public function getCategoriaId(): ?int
	{
		return $this->categoria_id;
	}

	public function setCategoriaId(?int $categoria_id): self
	{
		$this->categoria_id = $categoria_id;

		return $this;
	}

	public function getEmpresaId(): ?int
	{
		return $this->empresa_id;
	}

	public function setEmpresaId(?int $empresa_id): self
	{
		$this->empresa_id = $empresa_id;

		return $this;
	}

	public static function arrayParaObjeto(array $atividade): self
	{
		return new self(
			(int) ($atividade['ID_Atividade'] ?? 0),
			$atividade['Nome'] ?? null,
			$atividade['Duracao'] ?? null,
			$atividade['Descricao'] ?? null,
			$atividade['Localizacao'] ?? null,
			isset($atividade['Valor']) ? (float) $atividade['Valor'] : null,
			$atividade['Fotos'] ?? null,
			isset($atividade['Categoria_ID']) ? (int) $atividade['Categoria_ID'] : null,
			isset($atividade['Empresa_ID']) ? (int) $atividade['Empresa_ID'] : null
		);
	}
}