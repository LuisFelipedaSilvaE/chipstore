<?php
session_start();
include_once($_SERVER['DOCUMENT_ROOT'] . '/dal/ClienteDal.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/model/Cliente.php');

use \dal\ClienteDal;
use \model\Cliente;

$nome = $_POST['nome'] ?? null;
$email = $_POST['email'] ?? null;
$cidade = $_POST['cidade'] ?? null;
$telefone = $_POST['telefone'] ?? null;

$cliente = new Cliente();
$cliente->setNome($nome);
$cliente->setEmail($email);
$cliente->setCidade($cidade);
$cliente->setTelefone($telefone);

$dal = new ClienteDal();

if ($dal->isEmailRegistered($email)) {
  $_SESSION['msg-erro-criando-cliente-email-invalido'] = true;
  $_SESSION['conteudo-cliente-erro'] = $cliente;
  header('Location: /view/modules/cliente/adicionar/');
  exit;
}

$result = $dal->Insert($cliente);

if ($result) {
  $_SESSION['msg-produto-criado'] = true;
  header('Location: /view/modules/cliente/');
  exit;
}

$_SESSION['msg-erro-criando-cliente'] = true;
$_SESSION['conteudo-cliente-erro'] = $cliente;
header('Location: /view/modules/cliente/adicionar/');
exit;
