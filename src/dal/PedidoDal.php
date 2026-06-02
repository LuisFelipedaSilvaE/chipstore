<?php

namespace dal;

include_once(__DIR__ . '/Conexao.php');
include_once(__DIR__ . '/../model/Pedido.php');

use \model\Pedido;

class PedidoDal
{
    public function findAll()
    {
        try {
            $sql = "SELECT * FROM pedido";
            $con = Conexao::conectar();
            $stmt = $con->prepare($sql);
            $stmt->execute();
            $dadosBrutos = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $con = Conexao::desconectar();

            $listaPedidos = [];

            foreach ($dadosBrutos as $linha) {
                $pedido = new Pedido();
                $pedido->setId($linha['id_pedido']);
                $pedido->setIdCliente($linha['id_cliente']);
                $pedido->setDataPedido($linha['data_pedido']);
                $pedido->setStatus($linha['status']); 
                $pedido->setPagamento($linha['pagamento']);
                $pedido->setValorTotal($linha['valor_total']);
                $listaPedidos[] = $pedido;
            }

            return $listaPedidos;
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function findById(int $id)
    {
        try {
            $sql = "SELECT * FROM pedido WHERE id = ?";
            $con = Conexao::conectar();
            $stmt = $con->prepare($sql);
            $stmt->execute(array($id));
            $dadoBruto = $stmt->fetch(\PDO::FETCH_ASSOC);
            $con = Conexao::desconectar();

            if (!$dadoBruto) {
                return null;
            }

            $pedido = new Pedido();
            $pedido->setId($dadoBruto['id']);

            return $pedido;
        } catch (\PDOException $e) {
            return null;
        }
    }
}
