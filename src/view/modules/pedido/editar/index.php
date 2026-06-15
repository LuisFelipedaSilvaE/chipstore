<?php
session_start();
include_once($_SERVER['DOCUMENT_ROOT'] . '/dal/ClienteDal.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/dal/ItemPedidoDal.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/dal/PedidoDal.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/dal/ProdutoDal.php');

if (!isset($_SESSION['usuario-logado'])) {
  header('Location: /view/login');
  exit;
}

use \dal\ClienteDal;
use \dal\ItemPedidoDal;
use \dal\PedidoDal;
use \dal\ProdutoDal;

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$pedido = $id ? (new PedidoDal())->findById($id) : null;

if (!$pedido) {
  header('Location: /view/modules/pedido/');
  exit;
}

$clientes = (new ClienteDal())->findAll();
$todosProdutos = (new ProdutoDal())->findAll();
$itensPersistidos = (new ItemPedidoDal())->findByPedido($pedido->getId());
$itensPorProduto = [];

foreach ($itensPersistidos as $item) {
  $itensPorProduto[$item->getIdProduto()] = $item;
}

$produtos = array_values(array_filter(
  $todosProdutos,
  fn($produto) => $produto->getEstoque() > 0 || isset($itensPorProduto[$produto->getId()])
));

$conteudo = $_SESSION['conteudo-edicao-pedido-erro'] ?? null;
$itensFormulario = $conteudo['itens'] ?? array_map(
  fn($item) => [
    'idProduto' => $item->getIdProduto(),
    'quantidade' => $item->getQuantidade(),
  ],
  $itensPersistidos
);

if (count($itensFormulario) === 0) {
  $itensFormulario = [['idProduto' => '', 'quantidade' => 1]];
}

function dadosProdutoEdicao($produto, array $itensPorProduto)
{
  $itemExistente = $itensPorProduto[$produto->getId()] ?? null;

  return [
    'preco' => $itemExistente ? $itemExistente->getPrecoUnitario() : $produto->getPreco(),
    'estoque' => $produto->getEstoque() + ($itemExistente ? $itemExistente->getQuantidade() : 0),
  ];
}

function renderizarOpcoesEdicao(array $produtos, array $itensPorProduto, $selecionado = null)
{
  foreach ($produtos as $produto) {
    $dados = dadosProdutoEdicao($produto, $itensPorProduto);
    $selected = (string) $selecionado === (string) $produto->getId() ? 'selected' : '';
    printf(
      '<option value="%d" data-price="%.2f" data-stock="%d" %s>%s - R$ %s (%d disponíveis)</option>',
      $produto->getId(),
      $dados['preco'],
      $dados['estoque'],
      $selected,
      htmlspecialchars($produto->getNome()),
      number_format($dados['preco'], 2, ',', '.'),
      $dados['estoque']
    );
  }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/shared/head.html') ?>
  <title>Editar Pedido</title>
</head>

<body class="flex flex-col lg:flex-row bg-[var(--main-bg-color)] text-white">
  <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/shared/components/sidebar/sidebar.php') ?>
  <main class="flex-1 overflow-auto p-6">
    <header class="flex gap-4 items-center">
      <a href="../" class="cursor-pointer [display:inline-flex!important] justify-center items-center bg-gray-800 text-gray-400 w-[60px] h-[60px] rounded-2xl text-2xl p-3">
        <i class="fa fa-arrow-left"></i>
      </a>
      <div>
        <h1 class="text-2xl font-bold">Editar Pedido #<?php echo $pedido->getId() ?></h1>
        <h2 class="text-gray-400">Atualize os dados e produtos do pedido</h2>
      </div>
    </header>

    <div class="flex flex-col gap-4 mt-4 bg-(--secondary-bg-color) rounded-2xl p-6 border border-gray-800">
      <?php if (isset($_SESSION['msg-erro-editando-pedido'])): ?>
        <div class="flex gap-2 items-center bg-red-600/10 border border-red-600/50 rounded text-red-500 px-3 py-2">
          <i class="fa fa-circle-exclamation"></i>
          <?php echo htmlspecialchars($_SESSION['msg-erro-editando-pedido']) ?>
        </div>
      <?php unset($_SESSION['msg-erro-editando-pedido']);
      endif; ?>

      <form id="order-form" class="flex flex-col gap-8" method="POST" action="../../../actions/pedido/editar-pedido-action.php">
        <input type="hidden" name="id" value="<?php echo $pedido->getId() ?>">

        <section>
          <h3 class="font-bold text-lg mb-4">Dados do pedido</h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="flex flex-col gap-1">
              <label class="font-bold text-sm" for="cliente">Cliente</label>
              <select class="w-full px-2 py-2 rounded-lg border border-gray-800 bg-(--input-bg-color) outline-none focus:border-(--main-color)" id="cliente" name="idCliente" required>
                <?php foreach ($clientes as $cliente): ?>
                  <option value="<?php echo $cliente->getId() ?>" <?php echo ($conteudo['idCliente'] ?? $pedido->getIdCliente()) == $cliente->getId() ? 'selected' : '' ?>>
                    <?php echo htmlspecialchars($cliente->getNome()) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="flex flex-col gap-1">
              <label class="font-bold text-sm" for="dataPedido">Data do pedido</label>
              <input class="w-full px-2 py-2 rounded-lg border border-gray-800 bg-(--input-bg-color) outline-none focus:border-(--main-color)" id="dataPedido" name="dataPedido" type="datetime-local" value="<?php echo htmlspecialchars($conteudo['dataPedido'] ?? date('Y-m-d\TH:i', strtotime($pedido->getDataPedido()))) ?>" required>
            </div>
            <div class="flex flex-col gap-1">
              <label class="font-bold text-sm" for="status">Status</label>
              <select class="w-full px-2 py-2 rounded-lg border border-gray-800 bg-(--input-bg-color) outline-none focus:border-(--main-color)" id="status" name="status" required>
                <?php foreach (['Pendente', 'Pago', 'Enviado', 'Entregue', 'Cancelado'] as $status): ?>
                  <option value="<?php echo $status ?>" <?php echo ($conteudo['status'] ?? $pedido->getStatus()) === $status ? 'selected' : '' ?>><?php echo $status ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="flex flex-col gap-1">
              <label class="font-bold text-sm" for="pagamento">Forma de pagamento</label>
              <input class="w-full px-2 py-2 rounded-lg border border-gray-800 bg-(--input-bg-color) outline-none focus:border-(--main-color)" id="pagamento" name="pagamento" type="text" value="<?php echo htmlspecialchars($conteudo['pagamento'] ?? $pedido->getPagamento() ?? '') ?>">
            </div>
          </div>
        </section>

        <section class="border-t border-gray-800 pt-6">
          <div class="flex flex-col sm:flex-row gap-3 sm:items-center justify-between mb-4">
            <div>
              <h3 class="font-bold text-lg">Produtos</h3>
              <p class="text-sm text-gray-400">Altere quantidades, remova ou adicione produtos.</p>
            </div>
            <button id="add-item-button" type="button" class="px-3 py-2 rounded-lg border border-(--main-color) text-(--main-color) hover:bg-(--main-color-transparent) transition-colors">
              <i class="fa fa-plus mr-2"></i>Adicionar produto
            </button>
          </div>

          <div id="items-container" class="flex flex-col gap-3">
            <?php foreach ($itensFormulario as $item): ?>
              <div class="order-item grid grid-cols-1 sm:grid-cols-[1fr_120px_150px_44px] gap-3 items-end rounded-xl border border-gray-800 bg-(--main-bg-color) p-4">
                <div class="flex flex-col gap-1">
                  <label class="font-bold text-sm">Produto</label>
                  <select name="idProduto[]" class="product-select w-full px-2 py-2 rounded-lg border border-gray-800 bg-(--input-bg-color) outline-none focus:border-(--main-color)" required>
                    <option value="">Selecione um produto</option>
                    <?php renderizarOpcoesEdicao($produtos, $itensPorProduto, $item['idProduto']) ?>
                  </select>
                </div>
                <div class="flex flex-col gap-1">
                  <label class="font-bold text-sm">Quantidade</label>
                  <input name="quantidade[]" class="quantity-input w-full px-2 py-2 rounded-lg border border-gray-800 bg-(--input-bg-color) outline-none focus:border-(--main-color)" type="number" min="1" value="<?php echo max(1, (int) $item['quantidade']) ?>" required>
                </div>
                <div class="flex flex-col gap-1">
                  <span class="font-bold text-sm">Subtotal</span>
                  <span class="item-subtotal h-[42px] flex items-center text-(--main-color) font-bold">R$ 0,00</span>
                </div>
                <button type="button" class="remove-item-button h-[42px] rounded-lg text-red-500 hover:bg-red-600/10" title="Remover produto">
                  <i class="fa fa-trash"></i>
                </button>
              </div>
            <?php endforeach; ?>
          </div>
          <p id="items-error" class="hidden mt-3 text-sm text-red-500"></p>

          <div class="flex justify-end mt-5">
            <div class="w-full sm:w-72 rounded-xl border border-gray-800 bg-(--main-bg-color) p-4">
              <div class="flex items-center justify-between">
                <span class="text-gray-400">Total calculado</span>
                <strong id="order-total" class="text-xl text-(--main-color)">R$ 0,00</strong>
              </div>
              <p class="text-xs text-gray-500 mt-2">O total é calculado pelos produtos e não pode ser editado manualmente.</p>
            </div>
          </div>
        </section>

        <div class="flex justify-end items-center gap-3 flex-col-reverse sm:flex-row">
          <a href="../" class="w-full sm:w-fit px-3 py-2 rounded-lg bg-(--main-bg-color) border border-gray-800 text-center">Cancelar</a>
          <button type="submit" class="w-full sm:w-fit px-3 py-2 rounded-lg bg-(--main-color) text-(--secondary-bg-color)">Salvar Alterações</button>
        </div>
      </form>
      <?php unset($_SESSION['conteudo-edicao-pedido-erro']); ?>
    </div>
  </main>

  <template id="order-item-template">
    <div class="order-item grid grid-cols-1 sm:grid-cols-[1fr_120px_150px_44px] gap-3 items-end rounded-xl border border-gray-800 bg-(--main-bg-color) p-4">
      <div class="flex flex-col gap-1">
        <label class="font-bold text-sm">Produto</label>
        <select name="idProduto[]" class="product-select w-full px-2 py-2 rounded-lg border border-gray-800 bg-(--input-bg-color) outline-none focus:border-(--main-color)" required>
          <option value="">Selecione um produto</option>
          <?php renderizarOpcoesEdicao($produtos, $itensPorProduto) ?>
        </select>
      </div>
      <div class="flex flex-col gap-1">
        <label class="font-bold text-sm">Quantidade</label>
        <input name="quantidade[]" class="quantity-input w-full px-2 py-2 rounded-lg border border-gray-800 bg-(--input-bg-color) outline-none focus:border-(--main-color)" type="number" min="1" value="1" required>
      </div>
      <div class="flex flex-col gap-1">
        <span class="font-bold text-sm">Subtotal</span>
        <span class="item-subtotal h-[42px] flex items-center text-(--main-color) font-bold">R$ 0,00</span>
      </div>
      <button type="button" class="remove-item-button h-[42px] rounded-lg text-red-500 hover:bg-red-600/10" title="Remover produto">
        <i class="fa fa-trash"></i>
      </button>
    </div>
  </template>

  <script src="/shared/components/sidebar/sidebar.js"></script>
  <script src="./script.js"></script>
</body>

</html>
