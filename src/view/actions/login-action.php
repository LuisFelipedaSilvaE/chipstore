<?php
session_start();

include_once($_SERVER['DOCUMENT_ROOT'] . '/dal/UsuarioDal.php');

use \dal\UsuarioDal;


$email = $_POST['email'] ?? null;
$senha = $_POST['password'] ?? null;

$dal = new UsuarioDal();
$usuario = $dal->findByEmail($email);

if (isset($usuario)) {
  if (password_verify($senha, $usuario->getSenha())) {
    $_SESSION['usuario-logado'] = $usuario->getId();
    header('Location: /view/');
    exit;
  }
}

$_SESSION['credenciais-invalidas'] = true;
header('Location: /view/login/');
exit;
