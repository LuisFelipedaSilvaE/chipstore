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

try {
  $pedido = new Pedido();
  $pedido->setIdCliente($dados['idCliente']);
  $pedido->setDataPedido($data->format('Y-m-d H:i:s'));
  $pedido->setStatus($dados['status']);
  $pedido->setPagamento($dados['pagamento'] ?: null);

  (new PedidoDal())->CreateWithItems($pedido, $itensRecebidos);

  $_SESSION['msg-pedido-criado'] = true;
  header('Location: /view/modules/pedido/');
  exit;
} catch (\Throwable $e) {
  $_SESSION['msg-erro-salvando-pedido'] = $e->getMessage();
  $_SESSION['conteudo-pedido-erro'] = $dados;
  header('Location: /view/modules/pedido/adicionar/');
  exit;
}
