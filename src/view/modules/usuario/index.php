<?php
session_start();
include_once($_SERVER['DOCUMENT_ROOT'] . '/dal/UsuarioDal.php');

if (!isset($_SESSION['usuario-logado'])) {
  header("Location: /view/login");
  exit;
}

use \dal\UsuarioDal;

$dal = new UsuarioDal();
$listaUsuarios = $dal->findAll();
$quantidadeCadastrada = count($listaUsuarios);

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/shared/head.html') ?>
  <title>Usuários</title>
</head>

<body class="flex flex-col lg:flex-row bg-[var(--main-bg-color)] text-white">
  <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/shared/components/sidebar/sidebar.php') ?>
  <main class="flex-1 overflow-hidden p-6">
    <header class="flex flex-col sm:flex-row gap-4 sm:items-center justify-between">
      <div class="flex gap-4 items-center">
        <i class="[display:inline-flex!important] justify-center items-center bg-[var(--main-color-transparent)] text-[var(--main-color)] w-[60px] rounded-2xl fa fa-user-shield text-4xl p-3"></i>
        <div>
          <h1 class="text-2xl font-bold">Usuários</h1>
          <h2 class="text-gray-400"><?php echo $quantidadeCadastrada;
                                    echo $quantidadeCadastrada != 1 ? " Usuários cadastrados" : " Usuário Cadastrado" ?>
          </h2>
        </div>
      </div>
      <a href="./adicionar/" class="cursor-pointer px-4 py-2 bg-(--main-color) text-(--main-bg-color) w-full sm:w-fit rounded-lg hover:shadow-[0_0_7.5px_var(--main-color)] focus:shadow-[0_0_0_5px_var(--main-color-transparent)] transition-all flex gap-2 items-center justify-center"><i class="fa-solid fa-user-plus"></i> Novo Usuário</a>
    </header>
    <?php if (isset($_SESSION['msg-usuario-criado'])): ?>
      <div class="message-container flex gap-2 items-center justify-between bg-green-600/10 border border-green-600/50 rounded text-green-600 px-2 py-1 mt-4">
        Usuário cadastrado com sucesso.
        <i class="message fa fa-times cursor-pointer p-1"></i>
      </div>
    <?php
      unset($_SESSION['msg-usuario-criado']);
    endif;
    ?>
    <?php if (isset($_SESSION['msg-usuario-editado-sucesso'])): ?>
      <div class="message-container flex gap-2 items-center justify-between bg-green-600/10 border border-green-600/50 rounded text-green-600 px-2 py-1 mt-4">
        Usuário editado com sucesso.
        <i class="message fa fa-times cursor-pointer p-1"></i>
      </div>
    <?php
      unset($_SESSION['msg-usuario-editado-sucesso']);
    endif;
    ?>
    <?php if (isset($_SESSION['msg-usuario-deletado-sucesso'])): ?>
      <div class="message-container flex gap-2 items-center justify-between bg-green-600/10 border border-green-600/50 rounded text-green-600 px-2 py-1 mt-4">
        Usuário removido com sucesso.
        <i class="message fa fa-times cursor-pointer p-1"></i>
      </div>
    <?php
      unset($_SESSION['msg-usuario-deletado-sucesso']);
    endif;
    ?>
    <?php if (isset($_SESSION['msg-usuario-deletado-erro'])): ?>
      <div class="message-container flex gap-2 items-center justify-between bg-red-600/10 border border-red-600/50 rounded text-red-600 px-2 py-1 mt-4">
        Erro ao remover usuário.
        <i class="message fa fa-times cursor-pointer p-1"></i>
      </div>
    <?php
      unset($_SESSION['msg-usuario-deletado-erro']);
    endif;
    ?>
    <?php if (isset($_SESSION['msg-usuario-deletado-erro-unico'])): ?>
      <div class="message-container flex gap-2 items-center justify-between bg-red-600/10 border border-red-600/50 rounded text-red-600 px-2 py-1 mt-4">
        Não é possível remover o único usuário do sistema.
        <i class="message fa fa-times cursor-pointer p-1"></i>
      </div>
    <?php
      unset($_SESSION['msg-usuario-deletado-erro-unico']);
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
      <button type="button" id="limpar-filtros" class="w-full sm:w-auto px-3 py-1.5 rounded-lg border border-gray-800 text-gray-400 hover:bg-(--main-bg-color) transition-colors text-sm">Limpar</button>
    </form>
    <div class="border border-gray-800 mt-4 rounded-2xl overflow-auto scrollbar-none max-h-194.5">
      <table class="border-collapse w-full min-w-220">
        <thead class="border-b border-b-gray-800 px-2">
          <tr class="text-gray-400">
            <th class="py-3 pl-4 text-center">ID</th>
            <th class="py-3 px-4 text-center">Nome</th>
            <th class="py-3 px-4 text-center">Email</th>
            <th class="py-3 px-4 text-center">Perfil</th>
            <th class="py-3 pr-4 text-center">Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr id="filtro-vazio" style="display: none">
            <td colspan="5" class="text-center text-gray-400 font-bold h-30">
              <div class="flex justify-center items-center gap-3 text-lg">
                <i class="fa fa-search text-2xl"></i>Nenhum usuário encontrado.
              </div>
            </td>
          </tr>
          <?php if (count($listaUsuarios) == 0): ?>
            <tr>
              <td class="py-4 pl-4 text-center text-gray-400 font-bold h-30" colspan="5">
                <div class="flex justify-center items-center gap-3 text-lg">
                  <i class="fa fa-info-circle text-2xl"></i>Nenhum usuário cadastrado.
                </div>
              </td>
            </tr>
          <?php endif; ?>
          <?php foreach ($listaUsuarios as $usuario): ?>
            <tr class="last:border-b-0 border-b border-b-gray-800 font-bold">
              <td class="py-4 pl-4 text-center"><?php echo $usuario->getId() ?></td>
              <td class="py-4 px-4 text-center">
                <h3 class="user-name font-semibold whitespace-nowrap overflow-hidden max-w-full"><?php echo $usuario->getNome() ?></h3>
              </td>
              <td class="py-4 px-4 text-center text-sm text-gray-400 whitespace-nowrap overflow-hidden max-w-full"><?php echo $usuario->getEmail() ?></td>
              <td class="py-4 px-4">
                <div class="flex justify-center items-center">
                  <p class="px-2 py-1 rounded-full bg-cyan-600/20 w-fit text-cyan-400 text-sm">
                    Admin
                  </p>
                </div>
              </td>
              <td class="py-4 pr-4 text-center">
                <a href="./editar/?id=<?php echo $usuario->getId() ?>" class="inline-flex justify-center items-center w-10 h-10 p-1 rounded-md hover:bg-(--secondary-bg-color) cursor-pointer transition-all focus:shadow-[0_0_0_5px_var(--secondary-bg-color-transparent)]"><i class="fa fa-pencil text-gray-400"></i></a>
                <button <?php echo $quantidadeCadastrada <= 1 ? 'disabled' : '' ?> data-usuario-id="<?php echo $usuario->getId() ?>" class="delete-button inline-flex justify-center items-center w-10 h-10 p-1 rounded-md hover:bg-(--secondary-bg-color) transition-all focus:shadow-[0_0_0_5px_var(--secondary-bg-color-transparent)] <?php echo $quantidadeCadastrada <= 1 ? 'opacity-50 cursor-not-allowed' : '' ?>"><i class="fa fa-trash-alt text-red-600"></i></button>
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
      <p class="flex-1">Exclusão de Usuário</p>
      <i class="dialog-dismiss-button [display:inline-flex!important] items-center justify-center w-10 h-10 fa fa-times p-3 cursor-pointer transition-colors hover:bg-(--main-bg-color)"></i>
    </div>
    <div class="flex flex-col gap-4 bg-(--main-bg-color) p-4">
      <div class="flex items-center gap-2">
        <i class="fa fa-triangle-exclamation"></i>
        <p>Tem Certeza que deseja remover o(a) usuário(a) <b id="user-to-delete-name"></b>?</p>

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