<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/model/Cliente.php');
session_start();

if (!isset($_SESSION['usuario-logado'])) {
  header("Location: /view/login");
  exit;
}

if (isset($_SESSION['conteudo-cliente-erro'])) {
  $cliente = $_SESSION['conteudo-cliente-erro'];
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/shared/head.html') ?>
  <title>Novo Cliente</title>
</head>

<body class="flex flex-col lg:flex-row bg-[var(--main-bg-color)] text-white">
  <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/shared/components/sidebar/sidebar.php') ?>
  <main class="flex-1 overflow-hidden p-6">
    <header class="flex gap-4 items-center">
      <a href="../" class="cursor-pointer [display:inline-flex!important] justify-center items-center bg-gray-800 text-gray-400 w-[60px] h-[60px] rounded-2xl text-2xl p-3 hover:shadow-[0_0_7.5px_var(--back-btn-color)] focus:shadow-[0_0_0_5px_var(--back-btn-color-transparent)] transition-all"><i class="fa fa-arrow-left"></i></a>
      <div>
        <h1 class="text-2xl font-bold">Novo Cliente</h1>
        <h2 class="text-gray-400">Cadastre um novo cliente</h2>
      </div>
    </header>
    <div class="flex flex-col gap-4 mt-4 bg-(--secondary-bg-color) rounded-2xl p-6 border border-gray-800">
      <?php if (isset($_SESSION['msg-erro-criando-cliente'])): ?>
        <div class="error-message-container flex gap-2 items-center justify-between bg-red-600/10 border border-red-600/50 rounded text-red-600 px-2 py-1">
          Erro ao cadastrar cliente.
          <i class="error-message fa fa-times cursor-pointer p-1"></i>
        </div>
      <?php
        unset($_SESSION['msg-erro-criando-cliente']);
      endif;
      ?>
      <?php if (isset($_SESSION['msg-erro-criando-cliente-email-invalido'])): ?>
        <div class="error-message-container flex gap-2 items-center justify-between bg-red-600/10 border border-red-600/50 rounded text-red-600 px-2 py-1">
          Email já registrado. Informe outro valor.
          <i class="error-message fa fa-times cursor-pointer p-1"></i>
        </div>
      <?php
        unset($_SESSION['msg-erro-criando-cliente-email-invalido']);
      endif;
      ?>
      <form class="flex flex-col flex-wrap gap-8" method="POST" action="../../../actions/cliente/adicionar-cliente-action.php">
        <div class="flex flex-col flex-wrap gap-4">
          <div class="flex flex-col justify-center gap-1">
            <label class="font-bold text-sm" for="nome">Nome completo</label>
            <input
              value="<?php echo isset($_SESSION['conteudo-cliente-erro'])
                        ? $cliente->getNome()
                        : ''
                      ?>" placeholder="Ex: João Silva" class="min-w-60 w-full px-2 py-1 rounded-lg border border-gray-800 bg-(--input-bg-color) focus:ring-(--main-color) focus:border-(--main-color) outline-none transition-colors focus:caret-(--main-color)" id="nome" name="nome" type="text" required>
          </div>
          <div class="flex flex-col flex-1 gap-2">
            <div class="flex flex-col justify-center gap-1">
              <label class="font-bold text-sm" for="email">Email</label>
              <input value="<?php echo isset($_SESSION['conteudo-cliente-erro'])
                              ? $cliente->getEmail()
                              : ''
                            ?>" placeholder="nome@email.com" class="min-w-60 w-full px-2 py-1 rounded-lg border border-gray-800 bg-(--input-bg-color) focus:ring-(--main-color) focus:border-(--main-color) outline-none transition-colors focus:caret-(--main-color)" id="email" name="email" type="email" required>
            </div>
          </div>
          <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex flex-col justify-center gap-1 flex-1">
              <label class="font-bold text-sm" for="cidade">Cidade</label>
              <input value="<?php echo isset($_SESSION['conteudo-cliente-erro'])
                              ? $cliente->getCidade()
                              : ''
                            ?>" placeholder="Ex: São Paulo" class="min-w-60 w-full px-2 py-1 rounded-lg border border-gray-800 bg-(--input-bg-color) focus:ring-(--main-color) focus:border-(--main-color) outline-none transition-colors focus:caret-(--main-color)" id="cidade" name="cidade" type="text" required>
            </div>
            <div class="flex flex-col justify-center gap-1 flex-1">
              <label class="font-bold text-sm" for="telefone">Telefone</label>
              <input value="<?php echo isset($_SESSION['conteudo-cliente-erro'])
                              ? $cliente->getTelefone()
                              : ''
                            ?>" placeholder="(00) 00000-0000" class="min-w-60 w-full px-2 py-1 rounded-lg border border-gray-800 bg-(--input-bg-color) focus:ring-(--main-color) focus:border-(--main-color) outline-none transition-colors focus:caret-(--main-color)" id="telefone" name="telefone" type="tel" required>
            </div>
          </div>
        </div>
        <div class="flex justify-end items-center gap-3 flex-col-reverse sm:flex-row">
          <a href="../" class="w-full sm:w-fit px-3 py-2 rounded-lg bg-(--main-bg-color) hover:ring-gray-400 hover:shadow-[0_0_7.5px] hover:shadow-gray-800 focus:shadow-[0_0_0_5px] focus:shadow-gray-800/10 transition-all border border-gray-800 text-center">Cancelar</a>
          <button type="submit" class="w-full sm:w-fit px-3 py-2 rounded-lg bg-(--main-color) hover:shadow-[0_0_7.5px_var(--main-color)] focus:shadow-[0_0_0_5px_var(--main-color-transparent)] transition-all text-(--secondary-bg-color)">Salvar Cliente</button>
        </div>
      </form>
      <?php
      unset($_SESSION['conteudo-cliente-erro']);
      ?>
    </div>
  </main>
  <script src="/shared/components/sidebar/sidebar.js"></script>
  <script src="./script.js"></script>
</body>

</html>
