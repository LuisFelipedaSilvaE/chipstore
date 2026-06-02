<?php

namespace model;

class ItemPedido
{
    private ?int $id_item;

    private ?int $id_pedido;

    private ?int $id_produto;

    private ?int $quantidade;

    private ?float $preco_unitario;

    public function __construct() {}

    public function getId()
    {
        return $this->id_item;
    }

    public function setId(int $id_item)
    {
        $this->id_item = $id_item;
    }


    public function getIdPedido()
    {
        return $this->id_pedido;
    }

    public function setIdPedido(int $id_pedido)
    {
        $this->id_pedido = $id_pedido;
    }


    public function getIdProduto()
    {
        return $this->id_produto;
    }

    public function setIdProduto(int $id_produto)
    {
        $this->id_produto = $id_produto;
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
        return $this->preco_unitario;
    }

    public function setPrecoUnitario(float $preco)
    {
        $this->preco_unitario = $preco;
    }
}
