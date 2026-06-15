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
            $sql = "SELECT pedido.*, cliente.nome AS nomeCliente
                    FROM pedido
                    INNER JOIN cliente ON cliente.id = pedido.idCliente
                    ORDER BY pedido.dataPedido DESC, pedido.id DESC";
            $con = Conexao::conectar();
            $stmt = $con->prepare($sql);
            $stmt->execute();
            $dadosBrutos = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $con = Conexao::desconectar();

            $listaPedidos = [];

            foreach ($dadosBrutos as $linha) {
                $pedido = new Pedido();
                $pedido->setId($linha['id']);
                $pedido->setIdCliente($linha['idCliente']);
                $pedido->setNomeCliente($linha['nomeCliente']);
                $pedido->setDataPedido($linha['dataPedido']);
                $pedido->setStatus($linha['status']);
                $pedido->setPagamento($linha['pagamento']);
                $pedido->setValorTotal($linha['valorTotal']);
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
            $pedido->setIdCliente($dadoBruto['idCliente']);
            $pedido->setDataPedido($dadoBruto['dataPedido']);
            $pedido->setStatus($dadoBruto['status']);
            $pedido->setPagamento($dadoBruto['pagamento']);
            $pedido->setValorTotal($dadoBruto['valorTotal']);

            return $pedido;
        } catch (\PDOException $e) {
            return null;
        }
    }
    public function Insert(Pedido $pedido)
    {
        try {
            $sql = "INSERT INTO pedido (idCliente, dataPedido, status, pagamento, valorTotal) VALUES (?, ?, ?, ?, ?)";

            $con = Conexao::conectar();
            $stmt = $con->prepare($sql);

            $result = $stmt->execute([
                $pedido->getIdCliente(),
                $pedido->getDataPedido(),
                $pedido->getStatus(),
                $pedido->getPagamento(),
                $pedido->getValorTotal(),
            ]);

            Conexao::desconectar();

            return $result;
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function InsertAndReturnId(Pedido $pedido, \PDO $con)
    {
        $sql = "INSERT INTO pedido (idCliente, dataPedido, status, pagamento, valorTotal)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $con->prepare($sql);
        $result = $stmt->execute([
            $pedido->getIdCliente(),
            $pedido->getDataPedido(),
            $pedido->getStatus(),
            $pedido->getPagamento(),
            $pedido->getValorTotal(),
        ]);

        return $result ? (int) $con->lastInsertId() : null;
    }

    public function Update(Pedido $pedido)
    {
        try {
            $sql = "UPDATE pedido SET idCliente = ?, dataPedido = ?, status = ?, pagamento = ?, valorTotal = ? WHERE id = ?";

            $con = Conexao::conectar();
            $stmt = $con->prepare($sql);

            $result = $stmt->execute([
                $pedido->getIdCliente(),
                $pedido->getDataPedido(),
                $pedido->getStatus(),
                $pedido->getPagamento(),
                $pedido->getValorTotal(),
                $pedido->getId(),
            ]);

            Conexao::desconectar();

            return $result;
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function UpdateWithConnection(Pedido $pedido, \PDO $con)
    {
        $sql = "UPDATE pedido
                SET idCliente = ?, dataPedido = ?, status = ?, pagamento = ?, valorTotal = ?
                WHERE id = ?";
        $stmt = $con->prepare($sql);
        $stmt->execute([
            $pedido->getIdCliente(),
            $pedido->getDataPedido(),
            $pedido->getStatus(),
            $pedido->getPagamento(),
            $pedido->getValorTotal(),
            $pedido->getId(),
        ]);

        return $stmt->rowCount() >= 0;
    }

    public function Delete(int $id)
    {
        $con = null;

        try {
            $con = Conexao::conectar();
            $con->beginTransaction();

            $itensStmt = $con->prepare(
                "SELECT idProduto, quantidade FROM itemPedido WHERE idPedido = ? FOR UPDATE"
            );
            $itensStmt->execute([$id]);
            $itens = $itensStmt->fetchAll(\PDO::FETCH_ASSOC);

            $estoqueStmt = $con->prepare(
                "UPDATE produto SET estoque = estoque + ? WHERE id = ?"
            );

            foreach ($itens as $item) {
                $estoqueStmt->execute([$item['quantidade'], $item['idProduto']]);
            }

            $stmt = $con->prepare("DELETE FROM pedido WHERE id = ?");
            $stmt->execute([$id]);
            $linhasAfetadas = $stmt->rowCount();

            if ($linhasAfetadas === 0) {
                $con->rollBack();
                Conexao::desconectar();
                return false;
            }

            $con->commit();
            Conexao::desconectar();

            return true;
        } catch (\Throwable $e) {
            if ($con && $con->inTransaction()) {
                $con->rollBack();
            }

            Conexao::desconectar();
            return false;
        }
    }
}
