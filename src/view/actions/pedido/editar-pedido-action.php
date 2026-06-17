<?php
session_start();
include_once($_SERVER['DOCUMENT_ROOT'] . '/dal/PedidoDal.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/model/Pedido.php');

use \dal\PedidoDal;
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

try {
  $pedido = new Pedido();
  $pedido->setId($id);
  $pedido->setIdCliente($dados['idCliente']);
  $pedido->setDataPedido($data->format('Y-m-d H:i:s'));
  $pedido->setStatus($dados['status']);
  $pedido->setPagamento($dados['pagamento'] ?: null);

  (new PedidoDal())->UpdateWithItems($pedido, $itensRecebidos);

  $_SESSION['msg-pedido-editado-sucesso'] = true;
  header('Location: /view/modules/pedido/');
  exit;
} catch (\Throwable $e) {
  $_SESSION['msg-erro-editando-pedido'] = $e->getMessage();
  $_SESSION['conteudo-edicao-pedido-erro'] = $dados;
  header('Location: /view/modules/pedido/editar/?id=' . (int) $id);
  exit;
}
