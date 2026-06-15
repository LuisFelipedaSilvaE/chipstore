<?php
session_start();

include_once($_SERVER['DOCUMENT_ROOT'] . "/dal/ClienteDal.php");

use \dal\ClienteDal;

$id = $_GET['id'] ?? null;

$dal = new ClienteDal();
$resultado = $dal->Delete($id);

if ($resultado) {
  $_SESSION['msg-cliente-deletado-sucesso'] = true;
} else {
  $_SESSION['msg-cliente-deletado-erro'] = true;
}

header('Location: /view/modules/cliente/');
exit;
