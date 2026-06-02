<?php

namespace model;

class Produto
{
  private ?int $id_produto;

  private ?string $sku;

  private ?string $nome;

  private ?string $categoria;

  private ?float $preco;

  private ?int $estoque;

  public function __construct() {}

  public function getId()
  {
    return $this->id_produto;
  }

  public function setId(int $id_produto)
  {
    $this->id_produto = $id_produto;
  }

  public function getSku()
  {
    return $this->sku;
  }

  public function setSku(string $sku)
  {
    $this->sku = $sku;
  }

  public function getNome()
  {
    return $this->nome;
  }

  public function setNome(string $nome)
  {
    $this->nome = $nome;
  }

  public function getCategoria()
  {
    return $this->categoria;
  }

  public function setCategoria(string $categoria)
  {
    $this->categoria = $categoria;
  }

  public function getPreco()
  {
    return $this->preco;
  }

  public function setPreco(float $preco)
  {
    $this->preco = $preco;
  }

  public function getEstoque()
  {
    return $this->estoque;
  }

  public function setEstoque(int $estoque)
  {
    $this->estoque = $estoque;
  }
}
