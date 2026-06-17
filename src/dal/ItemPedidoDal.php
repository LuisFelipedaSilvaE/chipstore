<?php

namespace dal;

include_once(__DIR__ . '/Conexao.php');
include_once(__DIR__ . '/../model/ItemPedido.php');

use \model\ItemPedido;

class ItemPedidoDal
{
  public function findByPedido(int $idPedido)
  {
    try {
      $sql = "SELECT * FROM itemPedido WHERE idPedido = ?";
      $con = Conexao::conectar();
      $stmt = $con->prepare($sql);
      $stmt->execute([$idPedido]);
      $dadosBrutos = $stmt->fetchAll(\PDO::FETCH_ASSOC);
      Conexao::desconectar();

      $itens = [];

      foreach ($dadosBrutos as $linha) {
        $item = new ItemPedido();
        $item->setIdPedido($linha['idPedido']);
        $item->setIdProduto($linha['idProduto']);
        $item->setQuantidade($linha['quantidade']);
        $item->setPrecoUnitario($linha['precoUnitario']);
        $itens[] = $item;
      }

      return $itens;
    } catch (\PDOException $e) {
      return [];
    }
  }

  public function findByPedidoForUpdate(int $idPedido, \PDO $con)
  {
    $sql = "SELECT * FROM itemPedido WHERE idPedido = ? FOR UPDATE";
    $stmt = $con->prepare($sql);
    $stmt->execute([$idPedido]);
    $dadosBrutos = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    $itens = [];

    foreach ($dadosBrutos as $linha) {
      $item = new ItemPedido();
      $item->setIdPedido($linha['idPedido']);
      $item->setIdProduto($linha['idProduto']);
      $item->setQuantidade($linha['quantidade']);
      $item->setPrecoUnitario($linha['precoUnitario']);
      $itens[$item->getIdProduto()] = $item;
    }

    return $itens;
  }

  public function Insert(ItemPedido $itemPedido, \PDO $con)
  {
    $sql = "INSERT INTO itemPedido (idPedido, idProduto, quantidade, precoUnitario) VALUES (?, ?, ?, ?)";
    $stmt = $con->prepare($sql);

    return $stmt->execute([
      $itemPedido->getIdPedido(),
      $itemPedido->getIdProduto(),
      $itemPedido->getQuantidade(),
      $itemPedido->getPrecoUnitario(),
    ]);
  }

  public function DeleteByPedido(int $idPedido, \PDO $con)
  {
    $stmt = $con->prepare("DELETE FROM itemPedido WHERE idPedido = ?");
    return $stmt->execute([$idPedido]);
  }
}
