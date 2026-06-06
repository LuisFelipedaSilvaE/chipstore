<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/shared/head.html') ?>
  <title>Pedidos</title>
</head>

<body class="bg-[var(--main-bg-color)]">
  <main class="flex flex-col lg:flex-row w-screen h-screen overflow-hidden">
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/shared/components/sidebar/sidebar.php') ?>
  </main>
  <script src="/shared/components/sidebar/sidebar.js"></script>
</body>

</html>