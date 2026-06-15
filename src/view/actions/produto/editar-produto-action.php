<?php
session_start();
include_once($_SERVER['DOCUMENT_ROOT'] . '/dal/ProdutoDal.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/model/Produto.php');

use \dal\ProdutoDal;
use \model\Produto;

$id = $_POST['id'] ?? null;
$nome = $_POST['nome'] ?? null;
$categoria = $_POST['categoria'] ?? null;
$estoque = $_POST['estoque'] ?? null;
$preco = $_POST['preco'] ?? null;
$sku = $_POST['sku'] ?? null;

$produto = new Produto();
$produto->setId($id);
$produto->setNome($nome);
$produto->setCategoria($categoria);
$produto->setEstoque($estoque);
$produto->setPreco($preco);
$produto->setSku($sku);

$dal = new ProdutoDal();

if ($dal->isSkuRegisteredNotEquals($sku, $id)) {
  $_SESSION['msg-erro-editando-produto-sku-invalido'] = true;
  $_SESSION['conteudo-editando-produto-erro'] = $produto;
  header('Location: /view/modules/produto/editar/?id=' . $produto->getId());
  exit;
}

$result = $dal->Update($produto);

if ($result) {
  $_SESSION['msg-produto-editado-sucesso'] = true;
  header('Location: /view/modules/produto/');
} else {
  $_SESSION['msg-erro-editando-produto'] = true;
  $_SESSION['conteudo-editando-produto-erro'] = $produto;
  header('Location: /view/modules/produto/editar/?id=' . $produto->getId());
}

exit;
