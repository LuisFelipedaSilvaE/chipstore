<?php
session_start();
include_once($_SERVER['DOCUMENT_ROOT'] . '/dal/PedidoDal.php');

if (!isset($_SESSION['usuario-logado'])) {
  header('Location: /view/login');
  exit;
}

use \dal\PedidoDal;

$dal = new PedidoDal();
$listaPedidos = $dal->findAll();
$quantidadeCadastrada = count($listaPedidos);

function formatarDataPedido(string $data)
{
  return date('d/m/Y H:i', strtotime($data));
}

function formatarValorPedido(float $valor)
{
  return number_format($valor, 2, ',', '.');
}

function classesStatusPedido(string $status)
{
  $classes = [
    'Pendente' => 'bg-yellow-600/20 text-yellow-400',
    'Pago' => 'bg-blue-600/20 text-blue-400',
    'Enviado' => 'bg-purple-600/20 text-purple-400',
    'Entregue' => 'bg-green-600/20 text-green-400',
    'Cancelado' => 'bg-red-600/20 text-red-400',
  ];

  return $classes[$status] ?? 'bg-gray-600/20 text-gray-400';
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/shared/head.html') ?>
  <title>Pedidos</title>
</head>

<body class="flex flex-col lg:flex-row bg-[var(--main-bg-color)] text-white">
  <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/shared/components/sidebar/sidebar.php') ?>
  <main class="flex-1 overflow-hidden p-6">
    <header class="flex flex-col sm:flex-row gap-4 sm:items-center justify-between">
      <div class="flex gap-4 items-center">
        <i class="[display:inline-flex!important] justify-center items-center bg-[var(--main-color-transparent)] text-[var(--main-color)] w-[60px] rounded-2xl fa fa-cart-shopping text-3xl p-3"></i>
        <div>
          <h1 class="text-2xl font-bold">Pedidos</h1>
          <h2 class="text-gray-400">
            <?php echo $quantidadeCadastrada ?>
            <?php echo $quantidadeCadastrada !== 1 ? ' pedidos cadastrados' : ' pedido cadastrado' ?>
          </h2>
        </div>
      </div>
      <a href="./adicionar/" class="cursor-pointer px-4 py-2 bg-(--main-color) text-(--main-bg-color) w-full sm:w-fit rounded-lg hover:shadow-[0_0_7.5px_var(--main-color)] focus:shadow-[0_0_0_5px_var(--main-color-transparent)] transition-all flex items-center justify-center gap-2">
        <i class="fa fa-cart-plus"></i> Novo Pedido
      </a>
    </header>

    <?php
    $mensagens = [
      'msg-pedido-criado' => ['Pedido cadastrado com sucesso.', 'green'],
      'msg-pedido-editado-sucesso' => ['Pedido editado com sucesso.', 'green'],
      'msg-pedido-deletado-sucesso' => ['Pedido removido com sucesso.', 'green'],
      'msg-pedido-deletado-erro' => ['Erro ao remover pedido.', 'red'],
    ];
    foreach ($mensagens as $chave => [$texto, $cor]):
      if (isset($_SESSION[$chave])):
    ?>
        <div class="message-container flex gap-2 items-center justify-between bg-<?php echo $cor ?>-600/10 border border-<?php echo $cor ?>-600/50 rounded text-<?php echo $cor ?>-500 px-2 py-1 mt-4">
          <?php echo $texto ?>
          <i class="message fa fa-times cursor-pointer p-1"></i>
        </div>
    <?php
        unset($_SESSION[$chave]);
      endif;
    endforeach;
    ?>

    <div class="border border-gray-800 mt-6 rounded-2xl overflow-auto scrollbar-none max-h-194.5">
      <table class="border-collapse w-full min-w-240">
        <thead class="border-b border-b-gray-800 px-2">
          <tr class="text-gray-400">
            <th class="py-3 pl-4 text-left">Pedido</th>
            <th class="py-3 text-left">Cliente</th>
            <th class="py-3">Data</th>
            <th class="py-3">Status</th>
            <th class="py-3">Pagamento</th>
            <th class="py-3">Total</th>
            <th class="py-3 pr-4">Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($quantidadeCadastrada === 0): ?>
            <tr>
              <td class="py-4 pl-4 text-center text-gray-400 font-bold h-30" colspan="7">
                <div class="flex justify-center items-center gap-3 text-lg">
                  <i class="fa fa-info-circle text-2xl"></i>Nenhum pedido cadastrado.
                </div>
              </td>
            </tr>
          <?php endif; ?>
          <?php foreach ($listaPedidos as $pedido): ?>
            <tr class="last:border-b-0 border-b border-b-gray-800 font-bold">
              <td class="order-name py-4 pl-4">#<?php echo $pedido->getId() ?></td>
              <td class="py-4 text-left"><?php echo htmlspecialchars($pedido->getNomeCliente()) ?></td>
              <td class="py-4 text-center"><?php echo formatarDataPedido($pedido->getDataPedido()) ?></td>
              <td class="py-4 text-center">
                <div class="flex justify-center">
                  <span class="px-2 py-1 rounded-full text-sm <?php echo classesStatusPedido($pedido->getStatus()) ?>">
                    <?php echo htmlspecialchars($pedido->getStatus()) ?>
                  </span>
                </div>
              </td>
              <td class="py-4 text-center"><?php echo htmlspecialchars($pedido->getPagamento() ?: 'Não informado') ?></td>
              <td class="py-4 text-center text-(--main-color)">R$ <?php echo formatarValorPedido($pedido->getValorTotal()) ?></td>
              <td class="py-4 pr-4 text-center">
                <a href="./editar/?id=<?php echo $pedido->getId() ?>" class="inline-flex justify-center items-center w-10 h-10 p-1 rounded-md hover:bg-(--secondary-bg-color) transition-all focus:shadow-[0_0_0_5px_var(--secondary-bg-color-transparent)]">
                  <i class="fa fa-pencil text-gray-400"></i>
                </a>
                <button data-pedido-id="<?php echo $pedido->getId() ?>" class="delete-button inline-flex justify-center items-center w-10 h-10 p-1 rounded-md hover:bg-(--secondary-bg-color) transition-all focus:shadow-[0_0_0_5px_var(--secondary-bg-color-transparent)]">
                  <i class="fa fa-trash-alt text-red-600"></i>
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </main>

  <div id="delete-confirmation-dialog-mask" class="hidden opacity-0 fixed w-screen h-screen bg-black/50 transition-opacity"></div>
  <div id="delete-confirmation-dialog" class="[transition:opacity_150ms_cubic-bezier(0.4,0,0.2,1),_scale_150ms_cubic-bezier(0.4,0,0.2,1)] hidden opacity-0 scale-0 fixed left-1/2 top-1/2 translate-x-[-50%] translate-y-[-50%] z-10 max-w-160 w-full sm:w-3/4 border border-gray-800 overflow-hidden sm:rounded-md">
    <div class="flex pl-4 items-center bg-(--secondary-bg-color) border-b border-b-gray-800">
      <p class="flex-1">Exclusão de Pedido</p>
      <i class="dialog-dismiss-button [display:inline-flex!important] items-center justify-center w-10 h-10 fa fa-times p-3 cursor-pointer transition-colors hover:bg-(--main-bg-color)"></i>
    </div>
    <div class="flex flex-col gap-4 bg-(--main-bg-color) p-4">
      <div class="flex items-center gap-2">
        <i class="fa fa-triangle-exclamation"></i>
        <p>Tem certeza que deseja remover o pedido <b id="order-to-delete-name"></b>?</p>
      </div>
      <div class="flex justify-end items-center gap-3">
        <button class="dialog-dismiss-button px-3 py-2 rounded-lg bg-(--main-bg-color) hover:shadow-[0_0_7.5px] hover:shadow-gray-800 focus:shadow-[0_0_0_5px] focus:shadow-gray-800/10 transition-all border border-gray-800">Cancelar</button>
        <a id="dialog-confirm-button" href="" class="px-3 py-2 rounded-lg bg-(--main-color) hover:shadow-[0_0_7.5px_var(--main-color)] focus:shadow-[0_0_0_5px_var(--main-color-transparent)] transition-all text-(--secondary-bg-color)">Confirmar</a>
      </div>
    </div>
  </div>

  <script src="/shared/components/sidebar/sidebar.js"></script>
  <script src="./script.js"></script>
</body>

</html>
