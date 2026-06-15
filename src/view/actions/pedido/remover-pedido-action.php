<?php
session_start();
include_once($_SERVER['DOCUMENT_ROOT'] . '/dal/PedidoDal.php');

use \dal\PedidoDal;

if (!isset($_SESSION['usuario-logado'])) {
  header('Location: /view/login');
  exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$resultado = $id ? (new PedidoDal())->Delete($id) : false;

$_SESSION[$resultado ? 'msg-pedido-deletado-sucesso' : 'msg-pedido-deletado-erro'] = true;
header('Location: /view/modules/pedido/');
exit;
