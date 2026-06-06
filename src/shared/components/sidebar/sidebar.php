<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/dal/UsuarioDal.php');

use \dal\UsuarioDal;

$idUsuarioLogado = $_SESSION['usuario-logado'];

$dal = new UsuarioDal();
$usuario = $dal->findById($idUsuarioLogado);
?>
<aside class="text-white">
  <div class="w-full flex flex-row items-center lg:hidden bg-[var(--sidebar-bg-color)] border-b border-b-gray-800 p-3 pr-0 gap-4">


    <div class="flex items-center w-full justify-between">
      <div class="flex gap-3 items-center">
        <div
          class="relative before:shadow-[0px_0px_30px_10px_var(--main-color)] before:bottom-1/2 before:absolute before:right-1/2">
          <img class="w-10 h-10" src="/images/icon.svg" alt="" />
        </div>

        <div class="flex">
          <h4 class="font-bold">Chip</h4>
          <h4 class="text-[var(--main-color)] font-bold">Store</h4>
        </div>
      </div>
      <i id="toggle-mobile-sidebar-open" class="toggle-mobile-sidebar cursor-pointer rounded hover:bg-[var(--main-bg-color)] inline-flex bi bi-layout-sidebar-inset-reverse text-lg p-3 transition-colors"></i>
    </div>
  </div>
  <nav
    id="sidebar"
    class="absolute top-0 right-full lg:static w-screen sm:w-4/5 lg:w-100 h-screen flex flex-col bg-[var(--sidebar-bg-color)] border-r border-r-gray-800 transition-all duration-300 ease-in-out lg:translate-x-0 z-10">
    <div id="sidebar-header" class="sidebar-header flex items-center justify-center p-3 lg:pr-3 gap-2">
      <div
        id="sidebar-logo" class="relative before:shadow-[0px_0px_30px_10px_var(--main-color)] before:bottom-1/2 before:absolute before:right-1/2">
        <img class="w-10 h-10" src="/images/icon.svg" alt="" />
      </div>

      <div class="flex collapsable-text">
        <h4 class="font-bold">Chip</h4>
        <h4 class="text-[var(--main-color)] font-bold">Store</h4>
      </div>

      <div id="collapse-side-bar" class="flex items-center justify-end flex-1">
        <i class="inline-flex bi bi-layout-sidebar cursor-pointer rounded hover:bg-[var(--main-bg-color)] p-3 transition-colors"></i>
      </div>
      <div id="uncollapse-side-bar" class="hidden items-center justify-end">
        <i class="inline-flex bi bi bi-layout-sidebar-reverse cursor-pointer rounded hover:bg-[var(--main-bg-color)] p-3 transition-colors"></i>
      </div>
      <div class="toggle-mobile-sidebar lg:hidden flex items-center justify-end flex-1">
        <i class="cursor-pointer rounded hover:bg-[var(--main-bg-color)] inline-flex bi bi-layout-sidebar-inset text-lg p-3 transition-colors"></i>
      </div>
    </div>

    <div class="p-3 border-y border-y-gray-800 flex-1">
      <h5 class="collapsable-text text-sm ml-2 mb-2 text-gray-400">Gerenciamento</h5>
      <ul class="list-none flex flex-col gap-2">
        <li class="sidebar-item rounded-lg transition-all cursor-pointer">
          <a class="flex-1 p-3 flex gap-2" href="/view/">
            <i
              style="display: inline-flex"
              class="fa fa-cubes basis-5 justify-center items-center"></i>
            <p class="collapsable-text">Dashboard</p>
          </a>
        </li>
        <li class="sidebar-item rounded-lg transition-all cursor-pointer">
          <a class="flex-1 p-3 flex gap-2" href="/view/modules/produto/">
            <i
              style="display: inline-flex"
              class="fa fa-box basis-5 justify-center items-center"></i>
            <p class="collapsable-text">Produtos</p>
          </a>
        </li>
        <li class="sidebar-item rounded-lg transition-all cursor-pointer">
          <a class="flex-1 p-3 flex gap-2" href="/view/modules/cliente/">
            <i
              style="display: inline-flex"
              class="fa fa-user-group basis-5 justify-center items-center"></i>
            <p class="collapsable-text">Clientes</p>
          </a>
        </li>
        <li class="sidebar-item rounded-lg transition-all cursor-pointer">
          <a class="flex-1 p-3 flex gap-2" href="/view/modules/pedido/">
            <i
              style="display: inline-flex"
              class="fa fa-cart-shopping basis-5 justify-center items-center"></i>
            <p class="collapsable-text">Pedidos</p>
          </a>
        </li>
        <li class="sidebar-item rounded-lg transition-all cursor-pointer">
          <a class="flex-1 p-3 flex gap-2" href="/view/modules/item-pedido/">
            <i
              style="display: inline-flex"
              class="fa fa-list-ol basis-5 justify-center items-center"></i>
            <p class="collapsable-text">Itens de Pedido</p>
          </a>
        </li>
      </ul>
    </div>

    <div class="flex flex-col justify-center gap-4 p-3">
      <div class="collapsable-text">
        <?php if (isset($usuario)): ?>
          <h3 class="font-semibold"><?php echo $usuario->getNome() ?></h3>
          <h4 class="text-sm text-gray-400"><?php echo $usuario->getEmail() ?></h4>
        <?php
        endif; ?>
      </div>
      <a
        id="logout-button"
        href="../../../view/actions/logout.php"
        class="flex items-center gap-3 p-3 rounded-lg bg-[var(--main-bg-color)] cursor-pointer border border-gray-800 hover:bg-[var(--main-bg-color-hover)] transition-all ease-in-out delay-100">
        <i class="fa fa-arrow-right-from-bracket"></i>
        <p class="collapsable-text">Sair</p>
      </a>
    </div>
  </nav>
  <div class="sidebar-mobile-mask hidden absolute left-0 top-0 w-screen opacity-0 h-screen lg:hidden bg-black/50 transition-opacity"></div>
</aside>