<?php
session_start();
include_once($_SERVER['DOCUMENT_ROOT'] . '/dal/ClienteDal.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/model/Cliente.php');

use \dal\ClienteDal;
use \model\Cliente;

$id = $_POST['id'] ?? null;
$nome = $_POST['nome'] ?? null;
$email = $_POST['email'] ?? null;
$cidade = $_POST['cidade'] ?? null;
$telefone = $_POST['telefone'] ?? null;

$cliente = new Cliente();
$cliente->setId($id);
$cliente->setNome($nome);
$cliente->setEmail($email);
$cliente->setCidade($cidade);
$cliente->setTelefone($telefone);


$dal = new ClienteDal();

if ($dal->isEmailRegisteredNotEquals($email, $id)) {
  $_SESSION['msg-erro-editando-cliente-email-invalido'] = true;
  $_SESSION['conteudo-editando-cliente-erro'] = $cliente;
  header('Location: /view/modules/cliente/editar/?id=' . $cliente->getId());
  exit;
}

$result = $dal->Update($cliente);

if ($result) {
  $_SESSION['msg-cliente-editado-sucesso'] = true;
  header('Location: /view/modules/cliente/');
} else {
  $_SESSION['msg-erro-editando-cliente'] = true;
  $_SESSION['conteudo-editando-cliente-erro'] = $cliente;
  header('Location: /view/modules/cliente/editar/?id=' . $cliente->getId());
}

exit;
