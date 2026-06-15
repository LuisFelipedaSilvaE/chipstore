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

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$idsProdutos = $_POST['idProduto'] ?? [];
$quantidades = $_POST['quantidade'] ?? [];
$itensRecebidos = [];

foreach ($idsProdutos as $indice => $idProduto) {
  $idProdutoValidado = filter_var($idProduto, FILTER_VALIDATE_INT);
  $quantidade = filter_var($quantidades[$indice] ?? null, FILTER_VALIDATE_INT);

  if ($idProdutoValidado && $quantidade && $quantidade > 0) {
    $itensRecebidos[] = [
      'idProduto' => $idProdutoValidado,
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
$novosIds = array_column($itensRecebidos, 'idProduto');

if (
  !$id
  || !$dados['idCliente']
  || !$data
  || !in_array($dados['status'], $statusValidos, true)
  || count($itensRecebidos) === 0
  || count($novosIds) !== count(array_unique($novosIds))
) {
  $_SESSION['msg-erro-editando-pedido'] = 'Revise os dados e mantenha ao menos um produto válido.';
  $_SESSION['conteudo-edicao-pedido-erro'] = $dados;
  header('Location: /view/modules/pedido/editar/?id=' . (int) $id);
  exit;
}

$con = Conexao::conectar();

try {
  $con->beginTransaction();

  $pedidoStmt = $con->prepare("SELECT id FROM pedido WHERE id = ? FOR UPDATE");
  $pedidoStmt->execute([$id]);

  if (!$pedidoStmt->fetchColumn()) {
    throw new \RuntimeException('Pedido não encontrado.');
  }

  $itemPedidoDal = new ItemPedidoDal();
  $itensAntigos = $itemPedidoDal->findByPedidoForUpdate($id, $con);
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

  if (!$itemPedidoDal->DeleteByPedido($id, $con)) {
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
    $itemPedido->setIdPedido($id);
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

  $pedido = new Pedido();
  $pedido->setId($id);
  $pedido->setIdCliente($dados['idCliente']);
  $pedido->setDataPedido($data->format('Y-m-d H:i:s'));
  $pedido->setStatus($dados['status']);
  $pedido->setPagamento($dados['pagamento'] ?: null);
  $pedido->setValorTotal(round($valorTotal, 2));

  if (!(new PedidoDal())->UpdateWithConnection($pedido, $con)) {
    throw new \RuntimeException('Não foi possível atualizar o pedido.');
  }

  $con->commit();
  Conexao::desconectar();

  $_SESSION['msg-pedido-editado-sucesso'] = true;
  header('Location: /view/modules/pedido/');
  exit;
} catch (\Throwable $e) {
  if ($con->inTransaction()) {
    $con->rollBack();
  }

  Conexao::desconectar();
  $_SESSION['msg-erro-editando-pedido'] = $e->getMessage();
  $_SESSION['conteudo-edicao-pedido-erro'] = $dados;
  header('Location: /view/modules/pedido/editar/?id=' . (int) $id);
  exit;
}
