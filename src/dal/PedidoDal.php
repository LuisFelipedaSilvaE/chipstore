<?php

namespace dal;

include_once(__DIR__ . '/Conexao.php');
include_once(__DIR__ . '/ItemPedidoDal.php');
include_once(__DIR__ . '/ProdutoDal.php');
include_once(__DIR__ . '/../model/ItemPedido.php');
include_once(__DIR__ . '/../model/Pedido.php');

use \model\ItemPedido;
use \model\Pedido;

class PedidoDal
{
    public function findAll()
    {
        try {
            $sql = "SELECT pedido.*, cliente.nome AS nomeCliente FROM pedido INNER JOIN cliente ON cliente.id = pedido.idCliente ORDER BY pedido.dataPedido DESC, pedido.id DESC";
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
            Conexao::desconectar();
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
            Conexao::desconectar();
            return null;
        }
    }
    public function InsertAndReturnId(Pedido $pedido, \PDO $con)
    {
        $sql = "INSERT INTO pedido (idCliente, dataPedido, status, pagamento, valorTotal) VALUES (?, ?, ?, ?, ?)";

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

    public function Update(Pedido $pedido, \PDO $con)
    {
        $sql = "UPDATE pedido SET idCliente = ?, dataPedido = ?, status = ?, pagamento = ?, valorTotal = ? WHERE id = ?";
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

    public function CreateWithItems(Pedido $pedido, array $itensRecebidos)
    {
        $con = null;

        //Esse metodo vai englobar a logica complexa de criar um pedido e adicionar produtos a eles

        try {
            $con = Conexao::conectar();
            $con->beginTransaction();

            $ids = array_column($itensRecebidos, 'idProduto');
            $produtoDal = new ProdutoDal();
            $produtos = $produtoDal->findByIdsForUpdate($ids, $con);

            if (count($produtos) !== count($ids)) {
                throw new \RuntimeException('Um dos produtos selecionados não existe.');
            }

            $valorTotal = 0;

            foreach ($itensRecebidos as $item) {
                $produto = $produtos[$item['idProduto']];

                if ($item['quantidade'] > $produto->getEstoque()) {
                    throw new \RuntimeException(
                        'Estoque insuficiente para o produto ' . $produto->getNome() . '.'
                    );
                }

                $valorTotal += $produto->getPreco() * $item['quantidade'];
            }

            $pedido->setValorTotal(round($valorTotal, 2));
            $idPedido = $this->InsertAndReturnId($pedido, $con);

            if (!$idPedido) {
                throw new \RuntimeException('Não foi possível criar o pedido.');
            }

            $itemPedidoDal = new ItemPedidoDal();

            foreach ($itensRecebidos as $itemRecebido) {
                $produto = $produtos[$itemRecebido['idProduto']];
                $itemPedido = new ItemPedido();
                $itemPedido->setIdPedido($idPedido);
                $itemPedido->setIdProduto($produto->getId());
                $itemPedido->setQuantidade($itemRecebido['quantidade']);
                $itemPedido->setPrecoUnitario($produto->getPreco());

                if (!$itemPedidoDal->Insert($itemPedido, $con)) {
                    throw new \RuntimeException('Não foi possível adicionar os produtos ao pedido.');
                }

                if (!$produtoDal->decreaseStock($produto->getId(), $itemRecebido['quantidade'], $con)) {
                    throw new \RuntimeException('O estoque de um produto foi alterado. Tente novamente.');
                }
            }

            $con->commit();
            Conexao::desconectar();

            return true;
        } catch (\Throwable $e) {
            if ($con && $con->inTransaction()) {
                $con->rollBack();
            }

            Conexao::desconectar();
            throw $e;
        }
    }

    public function UpdateWithItems(Pedido $pedido, array $itensRecebidos)
    {
        $con = null;

        //Esse metodo vai englobar a logica complexa de atualizar um pedido
        try {
            $con = Conexao::conectar();
            $con->beginTransaction();

            $pedidoStmt = $con->prepare("SELECT id FROM pedido WHERE id = ? FOR UPDATE");
            $pedidoStmt->execute([$pedido->getId()]);

            if (!$pedidoStmt->fetchColumn()) {
                throw new \RuntimeException('Pedido não encontrado.');
            }

            $itemPedidoDal = new ItemPedidoDal();
            $itensAntigos = $itemPedidoDal->findByPedidoForUpdate($pedido->getId(), $con);
            $novosIds = array_column($itensRecebidos, 'idProduto');
            $todosIds = array_values(array_unique(array_merge(array_keys($itensAntigos), $novosIds)));
            $produtoDal = new ProdutoDal();
            $produtos = $produtoDal->findByIdsForUpdate($todosIds, $con);

            if (count($produtos) !== count($todosIds)) {
                throw new \RuntimeException('Um dos produtos selecionados não existe.');
            }

            foreach ($itensAntigos as $itemAntigo) {
                if (!$produtoDal->increaseStock(
                    $itemAntigo->getIdProduto(),
                    $itemAntigo->getQuantidade(),
                    $con
                )) {
                    throw new \RuntimeException('Não foi possível restaurar o estoque atual do pedido.');
                }
            }

            if (!$itemPedidoDal->DeleteByPedido($pedido->getId(), $con)) {
                throw new \RuntimeException('Não foi possível atualizar os itens do pedido.');
            }

            $valorTotal = 0;

            foreach ($itensRecebidos as $itemRecebido) {
                $produto = $produtos[$itemRecebido['idProduto']];
                $quantidadeAnterior = isset($itensAntigos[$produto->getId()])
                    ? $itensAntigos[$produto->getId()]->getQuantidade()
                    : 0;
                $quantidadeDisponivel = $produto->getEstoque() + $quantidadeAnterior;

                if ($itemRecebido['quantidade'] > $quantidadeDisponivel) {
                    throw new \RuntimeException(
                        'Estoque insuficiente para o produto ' . $produto->getNome() . '.'
                    );
                }

                $precoUnitario = isset($itensAntigos[$produto->getId()])
                    ? $itensAntigos[$produto->getId()]->getPrecoUnitario()
                    : $produto->getPreco();

                $itemPedido = new ItemPedido();
                $itemPedido->setIdPedido($pedido->getId());
                $itemPedido->setIdProduto($produto->getId());
                $itemPedido->setQuantidade($itemRecebido['quantidade']);
                $itemPedido->setPrecoUnitario($precoUnitario);

                if (!$itemPedidoDal->Insert($itemPedido, $con)) {
                    throw new \RuntimeException('Não foi possível adicionar os produtos ao pedido.');
                }

                if (!$produtoDal->decreaseStock($produto->getId(), $itemRecebido['quantidade'], $con)) {
                    throw new \RuntimeException('O estoque de um produto foi alterado. Tente novamente.');
                }

                $valorTotal += $precoUnitario * $itemRecebido['quantidade'];
            }

            $pedido->setValorTotal(round($valorTotal, 2));

            if (!$this->Update($pedido, $con)) {
                throw new \RuntimeException('Não foi possível atualizar o pedido.');
            }

            $con->commit();
            Conexao::desconectar();

            return true;
        } catch (\Throwable $e) {
            if ($con && $con->inTransaction()) {
                $con->rollBack();
            }

            Conexao::desconectar();
            throw $e;
        }
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
        } catch (\PDOException $e) {
            if ($con && $con->inTransaction()) {
                $con->rollBack();
            }

            Conexao::desconectar();
            return false;
        }
    }
}
