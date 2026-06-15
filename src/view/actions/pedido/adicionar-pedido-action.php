<?php
session_start();
include_once($_SERVER['DOCUMENT_ROOT'] . '/dal/Conexao.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/dal/ItemPedidoDal.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/dal/PedidoDal.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/dal/ProdutoDal.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/model/ItemPedido.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/model/Pedido.php');

use \dal\Conexao;
use \dal\ItemPedidoDal;
use \dal\PedidoDal;
use \dal\ProdutoDal;
use \model\ItemPedido;
use \model\Pedido;

if (!isset($_SESSION['usuario-logado'])) {
  header('Location: /view/login');
  exit;
}

$idsProdutos = $_POST['idProduto'] ?? [];
$quantidades = $_POST['quantidade'] ?? [];
$itensRecebidos = [];

foreach ($idsProdutos as $indice => $idProduto) {
  $id = filter_var($idProduto, FILTER_VALIDATE_INT);
  $quantidade = filter_var($quantidades[$indice] ?? null, FILTER_VALIDATE_INT);

  if ($id && $quantidade && $quantidade > 0) {
    $itensRecebidos[] = [
      'idProduto' => $id,
      'quantidade' => $quantidade,
    ];
  }
}

$dados = [
  'idCliente' => filter_input(INPUT_POST, 'idCliente', FILTER_VALIDATE_INT),
  'dataPedido' => $_POST['dataPedido'] ?? '',
  'status' => $_POST['status'] ?? '',
  'pagamento' => trim($_POST['pagamento'] ?? ''),
  'itens' => $itensRecebidos,
];

$statusValidos = ['Pendente', 'Pago', 'Enviado', 'Entregue', 'Cancelado'];
$data = \DateTime::createFromFormat('Y-m-d\TH:i', $dados['dataPedido']);
$ids = array_column($itensRecebidos, 'idProduto');

if (
  !$dados['idCliente']
  || !$data
  || !in_array($dados['status'], $statusValidos, true)
  || count($itensRecebidos) === 0
  || count($ids) !== count(array_unique($ids))
) {
  $_SESSION['msg-erro-salvando-pedido'] = 'Revise os dados e adicione ao menos um produto válido.';
  $_SESSION['conteudo-pedido-erro'] = $dados;
  header('Location: /view/modules/pedido/adicionar/');
  exit;
}

$con = Conexao::conectar();

try {
  $con->beginTransaction();

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

  $pedido = new Pedido();
  $pedido->setIdCliente($dados['idCliente']);
  $pedido->setDataPedido($data->format('Y-m-d H:i:s'));
  $pedido->setStatus($dados['status']);
  $pedido->setPagamento($dados['pagamento'] ?: null);
  $pedido->setValorTotal(round($valorTotal, 2));

  $idPedido = (new PedidoDal())->InsertAndReturnId($pedido, $con);

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

  $_SESSION['msg-pedido-criado'] = true;
  header('Location: /view/modules/pedido/');
  exit;
} catch (\Throwable $e) {
  if ($con->inTransaction()) {
    $con->rollBack();
  }

  Conexao::desconectar();
  $_SESSION['msg-erro-salvando-pedido'] = $e->getMessage();
  $_SESSION['conteudo-pedido-erro'] = $dados;
  header('Location: /view/modules/pedido/adicionar/');
  exit;
}
