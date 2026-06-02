<?php

namespace model;

class Pedido
{
    private ?int $id_pedido;

    private ?int $id_cliente;

    private ?string $data_pedido;

    private ?string $status;

    private ?string $pagamento;

    private ?float $valor_total;


    public function __construct() {}

    public function getIdPedido()
    {
        return $this->id_pedido;
    }

    public function setPedido(int $id_pedido)
    {
        $this->id_pedido = $id_pedido;
    }

    public function getIdCliente()
    {
        return $this->id_cliente;
    }

    public function setIdCliente(int $id_cliente)
    {
        $this->id_cliente = $id_cliente;
    }


    public function getDataPedido()
    {
        return $this->data_pedido;
    }

    public function setDataPedido(string $data_pedido)
    {
        $this->data_pedido = $data_pedido;
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
        return $this->valor_total;
    }

    public function setValorTotal(float $valor_total)
    {
        $this->valor_total = $valor_total;
    }
}
