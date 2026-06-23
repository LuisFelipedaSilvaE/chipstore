<?php
session_start();
include_once($_SERVER['DOCUMENT_ROOT'] . '/dal/ClienteDal.php');

if (!isset($_SESSION['usuario-logado'])) {
  header("Location: /view/login");
  exit;
}

use \dal\ClienteDal;

$dal = new ClienteDal();
$listaClientes = $dal->findAll();
$quantidadeCadastrada = count($listaClientes);

function normalizeDate(string $data)
{
  return substr($data, 8, 2) . '/' . substr($data, 5, 2) . '/' . substr($data, 0, 4);
}

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/shared/head.html') ?>
  <title>Clientes</title>
</head>

<body class="flex flex-col lg:flex-row bg-[var(--main-bg-color)] text-white">
  <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/shared/components/sidebar/sidebar.php') ?>
  <main class="flex-1 overflow-hidden p-6">
    <header class="flex flex-col sm:flex-row gap-4 sm:items-center justify-between">
      <div class="flex gap-4 items-center">
        <i class="[display:inline-flex!important] justify-center items-center bg-[var(--main-color-transparent)] text-[var(--main-color)] w-[60px] rounded-2xl fa fa-user-group text-4xl p-3"></i>
        <div>
          <h1 class="text-2xl font-bold">Clientes</h1>
          <h2 class="text-gray-400"><?php echo $quantidadeCadastrada;
                                    echo $quantidadeCadastrada != 1 ? " Clientes cadastrados" : " Cliente Cadastrado" ?>
          </h2>
        </div>
      </div>
      <a href="./adicionar/" class="cursor-pointer px-4 py-2 bg-(--main-color) text-(--main-bg-color) w-full sm:w-fit rounded-lg hover:shadow-[0_0_7.5px_var(--main-color)] focus:shadow-[0_0_0_5px_var(--main-color-transparent)] transition-all flex gap-2 items-center justify-center"><i class="fa-solid fa-user-plus"></i> Novo Cliente</a>
    </header>
    <?php if (isset($_SESSION['msg-cliente-criado'])): ?>
      <div class="message-container flex gap-2 items-center justify-between bg-green-600/10 border border-green-600/50 rounded text-green-600 px-2 py-1 mt-4">
        Cliente cadastrado com sucesso.
        <i class="message fa fa-times cursor-pointer p-1"></i>
      </div>
    <?php
      unset($_SESSION['msg-cliente-criado']);
    endif;
    ?>
    <?php if (isset($_SESSION['msg-cliente-editado-sucesso'])): ?>
      <div class="message-container flex gap-2 items-center justify-between bg-green-600/10 border border-green-600/50 rounded text-green-600 px-2 py-1 mt-4">
        Cliente editado com sucesso.
        <i class="message fa fa-times cursor-pointer p-1"></i>
      </div>
    <?php
      unset($_SESSION['msg-cliente-editado-sucesso']);
    endif;
    ?>
    <?php if (isset($_SESSION['msg-cliente-deletado-sucesso'])): ?>
      <div class="message-container flex gap-2 items-center justify-between bg-green-600/10 border border-green-600/50 rounded text-green-600 px-2 py-1 mt-4">
        Cliente removido com sucesso.
        <i class="message fa fa-times cursor-pointer p-1"></i>
      </div>
    <?php
      unset($_SESSION['msg-cliente-deletado-sucesso']);
    endif;
    ?>
    <?php if (isset($_SESSION['msg-cliente-deletado-erro'])): ?>
      <div class="message-container flex gap-2 items-center justify-between bg-red-600/10 border border-red-600/50 rounded text-red-600 px-2 py-1 mt-4">
        Erro ao remover cliente.
        <i class="message fa fa-times cursor-pointer p-1"></i>
      </div>
    <?php
      unset($_SESSION['msg-cliente-deletado-erro']);
    endif;
    ?>
    <form id="filtros" class="flex flex-wrap gap-3 items-end mt-4" onsubmit="return false">
      <div class="w-full sm:w-auto">
        <label class="text-xs text-gray-400 block mb-1">Nome</label>
        <input name="nome" placeholder="Buscar por nome..." class="w-full sm:w-48 px-3 py-1.5 rounded-lg border border-gray-800 bg-(--input-bg-color) text-sm outline-none focus:border-(--main-color) focus:ring-(--main-color) transition-colors" data-filter="nome">
      </div>
      <div class="w-full sm:w-auto">
        <label class="text-xs text-gray-400 block mb-1">Email</label>
        <input name="email" placeholder="Buscar por email..." class="w-full sm:w-48 px-3 py-1.5 rounded-lg border border-gray-800 bg-(--input-bg-color) text-sm outline-none focus:border-(--main-color) focus:ring-(--main-color) transition-colors" data-filter="email">
      </div>
      <div class="w-full sm:w-auto">
        <label class="text-xs text-gray-400 block mb-1">Cidade</label>
        <input name="cidade" placeholder="Buscar por cidade..." class="w-full sm:w-48 px-3 py-1.5 rounded-lg border border-gray-800 bg-(--input-bg-color) text-sm outline-none focus:border-(--main-color) focus:ring-(--main-color) transition-colors" data-filter="cidade">
      </div>
      <button type="button" id="limpar-filtros" class="w-full sm:w-auto px-3 py-1.5 rounded-lg border border-gray-800 text-gray-400 hover:bg-(--main-bg-color) transition-colors text-sm">Limpar</button>
    </form>
    <div class="border border-gray-800 mt-4 rounded-2xl overflow-auto scrollbar-none max-h-194.5">
      <table class="border-collapse w-full min-w-220">
        <thead class="border-b border-b-gray-800 px-2">
          <tr class="text-gray-400">
            <th class="py-3 pl-4 text-left">Cliente</th>
            <th class="py-3">Telefone</th>
            <th class="py-3">Cidade</th>
            <th class="py-3">Perfil</th>
            <th class="py-3 pr-4">Desde</th>
            <th class="py-3 pr-4">Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr id="filtro-vazio" style="display: none">
            <td colspan="6" class="text-center text-gray-400 font-bold h-30">
              <div class="flex justify-center items-center gap-3 text-lg">
                <i class="fa fa-search text-2xl"></i>Nenhum cliente encontrado.
              </div>
            </td>
          </tr>
          <?php if (count($listaClientes) == 0): ?>
            <tr>
              <td class="py-4 pl-4 text-center text-gray-400 font-bold h-30" colspan="6">
                <div class="flex justify-center items-center gap-3 text-lg">
                  <i class="fa fa-info-circle text-2xl"></i>Nenhum cliente cadastrado.
                </div>
              </td>
            </tr>
          <?php endif; ?>
          <?php foreach ($listaClientes as $cliente): ?>
            <tr class="last:border-b-0 border-b border-b-gray-800 font-bold">
              <td class="py-4 pl-4">
                <div>
                  <h3 class="client-name font-semibold whitespace-nowrap overflow-hidden max-w-full"><?php echo $cliente->getNome() ?></h3>
                  <h4 class="text-sm text-gray-400 whitespace-nowrap overflow-hidden max-w-full"><?php echo $cliente->getEmail() ?></h4>
                </div>
              </td>
              <td class="py-4 text-center">
                <?php echo $cliente->getTelefone() ?>
              </td>
              <td class="py-4 text-center"><?php echo $cliente->getCidade() ?></td>
              <td class="py-4 text-center">
                <div class="flex justify-center items-center">
                  <p class="px-2 py-1 rounded-full bg-purple-600/20 w-fit text-purple-400 text-sm">
                    Cliente
                  </p>
                </div>
              </td>
              <td class="py-4 text-center"><?php echo normalizeDate($cliente->getDataCadastro()) ?></td>
              <td class="py-4 pr-4 text-center">
                <a href="./editar/?id=<?php echo $cliente->getId() ?>" class="inline-flex justify-center items-center w-10 h-10 p-1 rounded-md hover:bg-(--secondary-bg-color) cursor-pointer transition-all focus:shadow-[0_0_0_5px_var(--secondary-bg-color-transparent)]"><i class="fa fa-pencil text-gray-400"></i></a>
                <button data-cliente-id="<?php echo $cliente->getId() ?>" class="delete-button inline-flex justify-center items-center w-10 h-10 p-1 rounded-md hover:bg-(--secondary-bg-color) transition-all focus:shadow-[0_0_0_5px_var(--secondary-bg-color-transparent)]"><i class="fa fa-trash-alt text-red-600"></i></button>
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
      <p class="flex-1">Exclusão de Cliente</p>
      <i class="dialog-dismiss-button [display:inline-flex!important] items-center justify-center w-10 h-10 fa fa-times p-3 cursor-pointer transition-colors hover:bg-(--main-bg-color)"></i>
    </div>
    <div class="flex flex-col gap-4 bg-(--main-bg-color) p-4">
      <div class="flex items-center gap-2">
        <i class="fa fa-triangle-exclamation"></i>
        <p>Tem Certeza que deseja remover o(a) cliente <b id="client-to-delete-name"></b>?</p>

      </div>
      <div class="flex justify-end items-center gap-3">
        <button class="dialog-dismiss-button px-3 py-2 rounded-lg bg-(--main-bg-color) hover:ring-gray-400 hover:shadow-[0_0_7.5px] hover:shadow-gray-800 focus:shadow-[0_0_0_5px] focus:shadow-gray-800/10 transition-all border border-gray-800 text-center">Cancelar</button>
        <a id="dialog-confirm-button" href="" class="px-3 py-2 rounded-lg bg-(--main-color) hover:shadow-[0_0_7.5px_var(--main-color)] focus:shadow-[0_0_0_5px_var(--main-color-transparent)] transition-all text-(--secondary-bg-color) cursor-pointer text-center">Confirmar</a>
      </div>
    </div>
  </div>

  <script src="/shared/components/sidebar/sidebar.js"></script>
  <script src="./script.js?v=2"></script>
</body>

</html>