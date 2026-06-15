<?php
session_start();
include_once($_SERVER['DOCUMENT_ROOT'] . '/dal/ProdutoDal.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/model/Produto.php');

use \dal\ProdutoDal;
use \model\Produto;

$nome = $_POST['nome'] ?? null;
$categoria = $_POST['categoria'] ?? null;
$estoque = $_POST['estoque'] ?? null;
$preco = $_POST['preco'] ?? null;
$sku = $_POST['sku'] ?? null;

$produto = new Produto();
$produto->setNome($nome);
$produto->setCategoria($categoria);
$produto->setEstoque($estoque);
$produto->setPreco($preco);
$produto->setSku($sku);

$dal = new ProdutoDal();

if ($dal->isSkuRegistered($sku)) {
  $_SESSION['msg-erro-criando-produto-sku-invalido'] = true;
  $_SESSION['conteudo-produto-erro'] = $produto;
  header('Location: /view/modules/produto/adicionar/');
  exit;
}

$result = $dal->Insert($produto);

if ($result) {
  $_SESSION['msg-produto-criado'] = true;
  header('Location: /view/modules/produto/');
  exit;
}

$_SESSION['msg-erro-criando-produto'] = true;
$_SESSION['conteudo-produto-erro'] = $produto;
header('Location: /view/modules/produto/adicionar/');
exit;
