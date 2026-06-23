<?php
session_start();

include_once($_SERVER['DOCUMENT_ROOT'] . '/dal/ClienteDal.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/dal/ProdutoDal.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/dal/PedidoDal.php');

if (!isset($_SESSION['usuario-logado'])) {
  header("Location: /view/login");
  exit;
}

use dal\ClienteDal;
use dal\ProdutoDal;
use dal\PedidoDal;

$clienteDal = new ClienteDal();
$produtoDal = new ProdutoDal();
$pedidoDal = new PedidoDal();

$clientes = $clienteDal->findAll();
$produtos = $produtoDal->findAll();
$pedidos = $pedidoDal->findAll();

$totalClientes = count($clientes);
$totalProdutos = count($produtos);
$totalPedidos = count($pedidos);

$faturamentoTotal = array_reduce($pedidos, function ($carry, $pedido) {
  if ($pedido->getStatus() === 'Cancelado' || $pedido->getStatus() === 'Pendente') return $carry;
  return $carry + $pedido->getValorTotal();
}, 0);

$pedidosRecentes = array_slice($pedidos, 0, 3);

$faturamentoPorMes = [];
$mesesAbreviados = [
  '01' => 'jan.',
  '02' => 'fev.',
  '03' => 'mar.',
  '04' => 'abr.',
  '05' => 'mai.',
  '06' => 'jun.',
  '07' => 'jul.',
  '08' => 'ago.',
  '09' => 'set.',
  '10' => 'out.',
  '11' => 'nov.',
  '12' => 'dez.'
];

$anoAtual = date('Y');

foreach (array_reverse($pedidos) as $pedido) {
  $timestamp = strtotime($pedido->getDataPedido());

  if (date('Y', $timestamp) !== $anoAtual) continue;
  if ($pedido->getStatus() === 'Cancelado' || $pedido->getStatus() === 'Pendente') continue;


  $mesKey = date('m', $timestamp);
  $mesNome = $mesesAbreviados[$mesKey];

  if (!isset($faturamentoPorMes[$mesNome])) {
    $faturamentoPorMes[$mesNome] = 0;
  }
  $faturamentoPorMes[$mesNome] += $pedido->getValorTotal();
}

$labelsChart = array_keys($faturamentoPorMes);
$dataChart = array_values($faturamentoPorMes);

$temFaturamento = !empty($labelsChart);

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/shared/head.html') ?>
  <title>Dashboard</title>
</head>

<body class="flex flex-col lg:flex-row bg-[var(--main-bg-color)] text-white">
  <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/shared/components/sidebar/sidebar.php') ?>

  <main class="flex-1 overflow-auto p-6">
    <header class="flex gap-4 items-center mb-8">
      <div class="flex gap-4 items-center">
        <i class="[display:inline-flex!important] justify-center items-center bg-[var(--main-color-transparent)] text-[var(--main-color)] w-[60px] rounded-2xl fa fa-chart-line text-4xl p-3"></i>
        <div>
          <h1 class="text-2xl font-bold">Dashboard</h1>
          <h2 class="text-gray-400 text-sm">Visão geral da loja ChipStore.</h2>
        </div>
      </div>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-6">
      <div class="bg-(--secondary-bg-color) border border-gray-800 rounded-2xl p-6 flex justify-between items-center">
        <div>
          <p class="text-gray-400 text-sm mb-1">Faturamento</p>
          <p class="text-2xl font-bold">R$ <?php echo number_format($faturamentoTotal, 2, ',', '.') ?></p>
        </div>
        <div class="bg-teal-900/30 text-teal-400 w-10 h-10 rounded-lg flex items-center justify-center">
          <i class="fa fa-dollar-sign"></i>
        </div>
      </div>

      <div class="bg-(--secondary-bg-color) border border-gray-800 rounded-2xl p-6 flex justify-between items-center">
        <div>
          <p class="text-gray-400 text-sm mb-1">Pedidos</p>
          <p class="text-2xl font-bold"><?php echo $totalPedidos ?></p>
        </div>
        <div class="bg-purple-900/30 text-purple-400 w-10 h-10 rounded-lg flex items-center justify-center">
          <i class="fa fa-shopping-cart"></i>
        </div>
      </div>

      <div class="bg-(--secondary-bg-color) border border-gray-800 rounded-2xl p-6 flex justify-between items-center">
        <div>
          <p class="text-gray-400 text-sm mb-1">Produtos</p>
          <p class="text-2xl font-bold"><?php echo $totalProdutos ?></p>
        </div>
        <div class="bg-emerald-900/30 text-emerald-400 w-10 h-10 rounded-lg flex items-center justify-center">
          <i class="fa fa-box"></i>
        </div>
      </div>

      <div class="bg-(--secondary-bg-color) border border-gray-800 rounded-2xl p-6 flex justify-between items-center">
        <div>
          <p class="text-gray-400 text-sm mb-1">Clientes</p>
          <p class="text-2xl font-bold"><?php echo $totalClientes ?></p>
        </div>
        <div class="bg-orange-900/30 text-orange-400 w-10 h-10 rounded-lg flex items-center justify-center">
          <i class="fa fa-users"></i>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
      <div class="xl:col-span-2 bg-(--secondary-bg-color) border border-gray-800 rounded-2xl p-6">
        <h3 class="font-bold mb-6">Faturamento por mês</h3>
        <?php if ($temFaturamento): ?>
        <div class="w-full h-64">
          <canvas id="faturamentoChart"></canvas>
        </div>
        <?php else: ?>
        <div class="flex flex-col items-center justify-center h-64 text-gray-400 gap-4">
          <i class="fa fa-chart-bar text-5xl"></i>
          <p class="text-lg">Nenhum dado de faturamento para exibir.</p>
        </div>
        <?php endif; ?>
      </div>

      <div class="bg-(--secondary-bg-color) border border-gray-800 rounded-2xl p-6">
        <h3 class="font-bold mb-6">Pedidos recentes</h3>
        <div class="flex flex-col gap-4">
          <?php foreach ($pedidosRecentes as $pedido): ?>
            <div class="border border-gray-800 rounded-xl p-4 flex flex-col sm:flex-row xl:flex-col 2xl:flex-row justify-between items-start sm:items-center xl:items-start 2xl:items-center gap-4 sm:gap-0 xl:gap-4 2xl:gap-0 bg-[var(--main-bg-color)]">
              <div>
                <p class="font-bold text-sm"><?php echo htmlspecialchars($pedido->getNomeCliente()) ?></p>
                <p class="text-xs text-gray-400 mt-1"><?php echo date('d/m/Y', strtotime($pedido->getDataPedido())) ?></p>
              </div>
              <div class="flex items-center justify-between sm:justify-end xl:justify-between 2xl:justify-end gap-4 w-full sm:w-auto xl:w-full 2xl:w-auto">
                <?php
                $statusClass = 'bg-gray-800 text-gray-400';
                if ($pedido->getStatus() === 'Pendente') $statusClass = 'border border-orange-800/50 text-orange-400';
                else if ($pedido->getStatus() === 'Enviado') $statusClass = 'border border-purple-800/50 text-purple-400';
                else if ($pedido->getStatus() === 'Entregue') $statusClass = 'border border-green-800/50 text-green-400';
                else if ($pedido->getStatus() === 'Pago') $statusClass = 'border border-blue-800/50 text-blue-400';
                else if ($pedido->getStatus() === 'Cancelado') $statusClass = 'border border-red-800/50 text-red-400';
                ?>
                <span class="px-3 py-1 rounded-full text-[10px] font-semibold <?php echo $statusClass ?>"><?php echo $pedido->getStatus() ?></span>
                <span class="font-bold text-sm whitespace-nowrap">R$ <?php echo number_format($pedido->getValorTotal(), 2, ',', '.') ?></span>
              </div>
            </div>
          <?php endforeach; ?>
          <?php if (count($pedidosRecentes) === 0): ?>
            <p class="text-gray-400 text-sm">Nenhum pedido recente.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </main>

  <script src="/shared/components/sidebar/sidebar.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <?php if ($temFaturamento): ?>
  <script>
    const ctx = document.getElementById('faturamentoChart');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: <?php echo json_encode($labelsChart) ?>,
        datasets: [{
          label: 'Faturamento',
          data: <?php echo json_encode($dataChart) ?>,
          backgroundColor: '#06b6d4',
          borderRadius: 4,
          borderSkipped: false,
          barPercentage: 0.6
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            display: false,
            beginAtZero: true
          },
          x: {
            grid: {
              display: false,
              drawBorder: false
            },
            ticks: {
              color: '#9ca3af',
              font: {
                size: 12
              }
            }
          }
        },
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            backgroundColor: '#1f2937',
            titleColor: '#f3f4f6',
            bodyColor: '#f3f4f6',
            displayColors: false,
            callbacks: {
              label: function(context) {
                let value = context.raw;
                return 'R$ ' + value.toLocaleString('pt-BR', {
                  minimumFractionDigits: 2
                });
              }
            }
          }
        }
      }
    });
  </script>
  <?php endif; ?>
</body>

</html>