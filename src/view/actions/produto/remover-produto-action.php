<?php
session_start();

include_once($_SERVER['DOCUMENT_ROOT'] . "/dal/ProdutoDal.php");

use \dal\ProdutoDal;

$id = $_GET['id'] ?? null;

$dal = new ProdutoDal();
$resultado = $dal->Delete($id);

if ($resultado) {
  $_SESSION['msg-produto-deletado-sucesso'] = true;
} else {
  $_SESSION['msg-produto-deletado-erro'] = true;
}

header('Location: /view/modules/produto/');
exit;
