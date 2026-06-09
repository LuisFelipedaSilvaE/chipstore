<?php

namespace model;

class Pedido
{
    private ?int $id;

    private ?int $idCliente;

    private ?string $dataPedido;

    private ?string $status;

    private ?string $pagamento;

    private ?float $valorTotal;


    public function __construct() {}

    public function getId()
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function getIdCliente()
    {
        return $this->idCliente;
    }

    public function setIdCliente(int $idCliente)
    {
        $this->idCliente = $idCliente;
    }


    public function getDataPedido()
    {
        return $this->dataPedido;
    }

    public function setDataPedido(string $dataPedido)
    {
        $this->dataPedido = $dataPedido;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus(string $status)
    {
        $this->status = $status;
    }

    public function getPagamento()
    {
        return $this->pagamento;
    }

    public function setPagamento(string $pagamento)
    {
        $this->pagamento = $pagamento;
    }

    public function getValorTotal()
    {
        return $this->valorTotal;
    }

    public function setValorTotal(float $valorTotal)
    {
        $this->valorTotal = $valorTotal;
    }
}
