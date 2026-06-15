<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/model/Produto.php');
session_start();

if (!isset($_SESSION['usuario-logado'])) {
  header("Location: /view/login");
}

if (isset($_SESSION['conteudo-produto-erro'])) {
  $produto = $_SESSION['conteudo-produto-erro'];
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scDeuale=1.0">
  <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/shared/head.html') ?>
  <title>Novo Produto</title>
</head>

<body class="flex flex-col lg:flex-row bg-[var(--main-bg-color)] text-white">
  <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/shared/components/sidebar/sidebar.php') ?>
  <main class="flex-1 overflow-hidden p-6">
    <header class="flex gap-4 items-center">
      <a href="../" class="cursor-pointer [display:inline-flex!important] justify-center items-center bg-gray-800 text-gray-400 w-[60px] h-[60px] rounded-2xl text-2xl p-3 hover:shadow-[0_0_7.5px_var(--back-btn-color)] focus:shadow-[0_0_0_5px_var(--back-btn-color-transparent)] transition-all"><i class="fa fa-arrow-left"></i></a>
      <div>
        <h1 class="text-2xl font-bold">Novo Produto</h1>
        <h2 class="text-gray-400">Cadastre um novo produto no catálogo</h2>
      </div>
    </header>
    <div class="flex flex-col gap-4 mt-4 bg-(--secondary-bg-color) rounded-2xl p-6 border border-gray-800">
      <?php if (isset($_SESSION['msg-erro-criando-produto'])): ?>
        <div class="error-message-container flex gap-2 items-center justify-between bg-red-600/10 border border-red-600/50 rounded text-red-600 px-2 py-1">
          Erro ao cadastrar produto.
          <i class="error-message fa fa-times cursor-pointer p-1"></i>
        </div>
      <?php
        unset($_SESSION['msg-erro-criando-produto']);
      endif;
      ?>
      <?php if (isset($_SESSION['msg-erro-criando-produto-sku-invalido'])): ?>
        <div class="error-message-container flex gap-2 items-center justify-between bg-red-600/10 border border-red-600/50 rounded text-red-600 px-2 py-1">
          SKU já registrado. Informe outro valor.
          <i class="error-message fa fa-times cursor-pointer p-1"></i>
        </div>
      <?php
        unset($_SESSION['msg-erro-criando-produto-sku-invalido']);
      endif;
      ?>
      <form class="flex flex-col flex-wrap gap-8" method="POST" action="../../../actions/produto/adicionar-produto-action.php">
        <div class="flex flex-col flex-wrap gap-4">
          <div class="flex flex-col justify-center gap-1">
            <label class="font-bold text-sm" for="nome">Nome do produto</label>
            <input
              value="<?php echo isset($_SESSION['conteudo-produto-erro'])
                        ? $produto->getNome()
                        : ''
                      ?>" placeholder="Ex: Processador Ryzen 9 9950X" class="min-w-60 w-full px-2 py-1 rounded-lg border border-gray-800 bg-(--input-bg-color) focus:ring-(--main-color) focus:border-(--main-color) outline-none transition-colors focus:caret-(--main-color)" id="nome" name="nome" type="text" required>
          </div>
          <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex flex-col flex-1 gap-2">
              <div class="flex flex-col justify-center gap-1">
                <label class="font-bold text-sm" for="categoria">Categoria</label>
                <select id="categoria" name="categoria" class="min-w-60 w-full px-2 py-1 rounded-lg border border-gray-800 bg-(--input-bg-color) text-white focus:ring-(--main-color) focus:border-(--main-color) outline-none transition-colors" required>
                  <option value="" class="bg-(--input-bg-color)">Selecione uma Categoria</option>
                  <option value="Processadores" class="bg-(--input-bg-color)"
                    <?php echo isset($_SESSION['conteudo-produto-erro']) && 'Processadores' == $produto->getCategoria()
                      ? 'selected'
                      : ''
                    ?>>Processadores</option>
                  <option value="Placas-mãe" class="bg-(--input-bg-color)"
                    <?php echo isset($_SESSION['conteudo-produto-erro']) && 'Placas-mãe' == $produto->getCategoria()
                      ? 'selected'
                      : ''
                    ?>>Placas-mãe</option>
                  <option value="Placas de Vídeo" class="bg-(--input-bg-color)"
                    <?php echo isset($_SESSION['conteudo-produto-erro']) && 'Placas de Vídeo' == $produto->getCategoria()
                      ? 'selected'
                      : ''
                    ?>>Placas de Vídeo</option>
                  <option value="Memórias RAM" class="bg-(--input-bg-color)"
                    <?php echo isset($_SESSION['conteudo-produto-erro']) && 'Memórias RAM' == $produto->getCategoria()
                      ? 'selected'
                      : ''
                    ?>>Memórias RAM</option>
                  <option value="Armazenamento" class="bg-(--input-bg-color)"
                    <?php echo isset($_SESSION['conteudo-produto-erro']) && 'Armazenamento' == $produto->getCategoria()
                      ? 'selected'
                      : ''
                    ?>>Armazenamento (SSD/HDD)</option>
                  <option value="Fontes de Alimentação" class="bg-(--input-bg-color)"
                    <?php echo isset($_SESSION['conteudo-produto-erro']) && 'Fontes de Alimentação' == $produto->getCategoria()
                      ? 'selected'
                      : ''
                    ?>>Fontes de Alimentação</option>
                  <option value="Refrigeração" class="bg-(--input-bg-color)"
                    <?php echo isset($_SESSION['conteudo-produto-erro']) && 'Refrigeração' == $produto->getCategoria()
                      ? 'selected'
                      : ''
                    ?>>Refrigeração (Coolers/Water Coolers)</option>
                  <option value="Gabinetes" class="bg-(--input-bg-color)"
                    <?php echo isset($_SESSION['conteudo-produto-erro']) && 'Gabinetes' == $produto->getCategoria()
                      ? 'selected'
                      : ''
                    ?>>Gabinetes</option>
                  <option value="Monitores" class="bg-(--input-bg-color)"
                    <?php echo isset($_SESSION['conteudo-produto-erro']) && 'Monitores' == $produto->getCategoria()
                      ? 'selected'
                      : ''
                    ?>>Monitores</option>
                  <option value="Periféricos" class="bg-(--input-bg-color)"
                    <?php echo isset($_SESSION['conteudo-produto-erro']) && 'Periféricos' == $produto->getCategoria()
                      ? 'selected'
                      : ''
                    ?>>Periféricos (Mouse, Teclado, Headset)</option>
                  <option value="Acessórios" class="bg-(--input-bg-color)"
                    <?php echo isset($_SESSION['conteudo-produto-erro']) && 'Acessórios' == $produto->getCategoria()
                      ? 'selected'
                      : ''
                    ?>>Acessórios e Cabos</option>
                </select>
              </div>
              <div class="flex flex-col justify-center gap-1">
                <label class="font-bold text-sm" for="estoque">Estoque</label>
                <input value="<?php echo isset($_SESSION['conteudo-produto-erro'])
                                ? $produto->getEstoque()
                                : ''
                              ?>" placeholder="0" class="min-w-60 w-full px-2 py-1 rounded-lg border border-gray-800 bg-(--input-bg-color) focus:ring-(--main-color) focus:border-(--main-color) outline-none transition-colors focus:caret-(--main-color)" id="estoque" name="estoque" type="number" min="0" required>
              </div>
            </div>
            <div class="flex flex-col flex-1 gap-2">
              <div class="flex flex-col justify-center gap-1">
                <label class="font-bold text-sm" for="preco">Preço (R$)</label>
                <input value="<?php echo isset($_SESSION['conteudo-produto-erro'])
                                ? $produto->getPreco()
                                : ''
                              ?>" placeholder="0,00" class="min-w-60 w-full px-2 py-1 rounded-lg border border-gray-800 bg-(--input-bg-color) focus:ring-(--main-color) focus:border-(--main-color) outline-none transition-colors focus:caret-(--main-color)" id="preco" name="preco" type="number" min="0" step="0.01" required>
              </div>
              <div class="flex flex-col justify-center gap-1">
                <label class="font-bold text-sm" for="sku">SKU</label>
                <input value="<?php echo isset($_SESSION['conteudo-produto-erro'])
                                ? $produto->getSku()
                                : ''
                              ?>" placeholder="Ex: CHP-0001" class="min-w-60 w-full px-2 py-1 rounded-lg border border-gray-800 bg-(--input-bg-color) focus:ring-(--main-color) focus:border-(--main-color) outline-none transition-colors focus:caret-(--main-color)" id="sku" name="sku" type="text" required>
              </div>
            </div>
          </div>
        </div>
        <div class="flex justify-end items-center gap-3 flex-col-reverse sm:flex-row">
          <a href="../" class="w-full sm:w-fit px-3 py-2 rounded-lg bg-(--main-bg-color) hover:ring-gray-400 hover:shadow-[0_0_7.5px] hover:shadow-gray-800 focus:shadow-[0_0_0_5px] focus:shadow-gray-800/10 transition-all border border-gray-800 text-center">Cancelar</a>
          <button type="submit" class="w-full sm:w-fit px-3 py-2 rounded-lg bg-(--main-color) hover:shadow-[0_0_7.5px_var(--main-color)] focus:shadow-[0_0_0_5px_var(--main-color-transparent)] transition-all text-(--secondary-bg-color)">Salvar Produto</button>
        </div>
      </form>
      <?php
      unset($_SESSION['conteudo-produto-erro']);
      ?>
    </div>
  </main>
  <script src="/shared/components/sidebar/sidebar.js"></script>
  <script src="./script.js"></script>
</body>

</html>