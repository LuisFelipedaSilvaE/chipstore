<?php
session_start();
include_once($_SERVER['DOCUMENT_ROOT'] . '/dal/ProdutoDal.php');

if (!isset($_SESSION['usuario-logado'])) {
  header("Location: /view/login");
}

use \dal\ProdutoDal;

$dal = new ProdutoDal();
$listaProdutos = $dal->findAll();
$quantidadeCadastrada = count($listaProdutos);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/shared/head.html') ?>
  <title>Produtos</title>
</head>

<body class="flex flex-col lg:flex-row bg-[var(--main-bg-color)] text-white">
  <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/shared/components/sidebar/sidebar.php') ?>
  <main class="flex-1 overflow-hidden p-6">
    <header class="flex flex-col sm:flex-row gap-4 sm:items-center justify-between">
      <div class="flex gap-4 items-center">
        <i class="[display:inline-flex!important] justify-center items-center bg-[var(--main-color-transparent)] text-[var(--main-color)] w-[60px] rounded-2xl fa fa-box text-4xl p-3"></i>
        <div>
          <h1 class="text-2xl font-bold">Produtos</h1>
          <h2 class="text-gray-400"><?php echo $quantidadeCadastrada;
                                    echo $quantidadeCadastrada != 1 ? " Produtos cadastrados" : " Produto Cadastrado" ?>
          </h2>
        </div>
      </div>
      <a href="./adicionar/" class="cursor-pointer px-4 py-2 bg-(--main-color) text-(--main-bg-color) w-full sm:w-fit rounded-lg hover:shadow-[0_0_7.5px_var(--main-color)] focus:shadow-[0_0_0_5px_var(--main-color-transparent)] transition-all"><i class="fa fa-plus"></i> Novo Produto</a>
    </header>
    <div class="border border-gray-800 mt-6 rounded-2xl overflow-auto scrollbar-none max-h-197.5">
      <table class="border-collapse w-full min-w-220">
        <thead class="border-b border-b-gray-800 px-2">
          <tr class="text-gray-400">
            <th class="py-3 pl-4 text-left">Produto</th>
            <th class="py-3">Categoria</th>
            <th class="py-3">Preço</th>
            <th class="py-3">Estoque</th>
            <th class="py-3 pr-4">Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($listaProdutos as $produto): ?>
            <tr class="last:border-b-0 border-b border-b-gray-800 font-bold">
              <td class="py-4 pl-4"><?php echo $produto->getNome() ?></td>
              <td class="py-4">
                <div class="flex justify-center items-center">
                  <p class="px-2 py-1 rounded-full bg-purple-600/20 w-fit text-purple-400 text-sm">
                    <?php echo $produto->getCategoria() ?>
                  </p>
                </div>
              </td>
              <td class="py-4 text-center text-(--main-color)">R$ <?php echo $produto->getPreco() ?></td>
              <td class="py-4 text-center"><?php echo $produto->getEstoque() ?></td>
              <td class="py-4 pr-4 text-center">
                <button class="w-10 h-10 p-1 rounded-md"><i class="fa fa-pencil text-gray-400"></i></button>
                <button class="w-10 h-10 p-1 rounded-md"><i class="fa fa-trash-alt text-red-600"></i></button>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </main>
  <script src="/shared/components/sidebar/sidebar.js"></script>
</body>

</html>