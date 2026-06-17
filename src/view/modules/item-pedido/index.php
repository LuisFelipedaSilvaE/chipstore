<?php
session_start();

if (!isset($_SESSION['usuario-logado'])) {
  header("Location: /view/login");
  exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/shared/head.html') ?>
  <title>Itens de Pedido</title>
</head>

<body class="flex flex-col lg:flex-row bg-[var(--main-bg-color)] text-white">
  <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/shared/components/sidebar/sidebar.php') ?>
  <main class="flex-1 overflow-hidden p-3">
  </main>
  <script src="/shared/components/sidebar/sidebar.js"></script>
</body>

</html>
