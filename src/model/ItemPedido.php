<?php

namespace model;

class ItemPedido
{
    private ?int $id;

    private ?int $idPedido;

    private ?int $idProduto;

    private ?int $quantidade;

    private ?float $precoUnitario;

    public function __construct() {}

    public function getId()
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }


    public function getIdPedido()
    {
        return $this->idPedido;
    }

    public function setIdPedido(int $idPedido)
    {
        $this->idPedido = $idPedido;
    }


    public function getIdProduto()
    {
        return $this->idProduto;
    }

    public function setIdProduto(int $idProduto)
    {
        $this->idProduto = $idProduto;
    }

    public function getQuantidade()
    {
        return $this->quantidade;
    }

    public function setQuantidade(int $quantidade)
    {
        $this->quantidade = $quantidade;
    }

    public function getPrecoUnitario()
    {
        return $this->precoUnitario;
    }

    public function setPrecoUnitario(float $preco)
    {
        $this->precoUnitario = $preco;
    }
}
