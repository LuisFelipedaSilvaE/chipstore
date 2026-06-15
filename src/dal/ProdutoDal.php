<?php

namespace dal;

include_once(__DIR__ . '/Conexao.php');
include_once(__DIR__ . '/../model/Produto.php');

use \model\Produto;

class ProdutoDal
{
    public function findAll()
    {
        try {
            $sql = "SELECT * FROM produto";
            $con = Conexao::conectar();
            $stmt = $con->prepare($sql);
            $stmt->execute();
            $dadosBrutos = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            Conexao::desconectar();

            $listaProdutos = [];

            foreach ($dadosBrutos as $linha) {
                $produto = new Produto();
                $produto->setId($linha['id']);
                $produto->setSku($linha['sku']);   //Sku é o código único do produto, tipo categoria com código(Stock Keeping Unit)
                $produto->setNome($linha['nome']);
                $produto->setCategoria($linha['categoria']);
                $produto->setPreco($linha['preco']);
                $produto->setEstoque($linha['estoque']);

                $listaProdutos[] = $produto;
            }

            return $listaProdutos;
        } catch (\PDOException $e) {
            // Pensando no registro de erro de log.
            return [];
        }
    }

    public function findById(int $id)
    {
        try {
            $sql = "SELECT * FROM produto WHERE id = ?";
            $con = Conexao::conectar();
            $stmt = $con->prepare($sql);
            $stmt->execute([$id]);
            $dadoBruto = $stmt->fetch(\PDO::FETCH_ASSOC);
            Conexao::desconectar();

            if (!$dadoBruto) {
                return null;
            }

            $produto = new Produto();
            $produto->setId($dadoBruto['id']);
            $produto->setSku($dadoBruto['sku']);
            $produto->setNome($dadoBruto['nome']);
            $produto->setCategoria($dadoBruto['categoria']);
            $produto->setPreco($dadoBruto['preco']);
            $produto->setEstoque($dadoBruto['estoque']);

            return $produto;
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function findByIdsForUpdate(array $ids, \PDO $con)
    {
        if (count($ids) === 0) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT * FROM produto WHERE id IN ($placeholders) FOR UPDATE";
        $stmt = $con->prepare($sql);
        $stmt->execute($ids);
        $dadosBrutos = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $produtos = [];

        foreach ($dadosBrutos as $linha) {
            $produto = new Produto();
            $produto->setId($linha['id']);
            $produto->setSku($linha['sku']);
            $produto->setNome($linha['nome']);
            $produto->setCategoria($linha['categoria']);
            $produto->setPreco($linha['preco']);
            $produto->setEstoque($linha['estoque']);
            $produtos[$produto->getId()] = $produto;
        }

        return $produtos;
    }

    public function decreaseStock(int $idProduto, int $quantidade, \PDO $con)
    {
        $sql = "UPDATE produto
                SET estoque = estoque - ?
                WHERE id = ? AND estoque >= ?";
        $stmt = $con->prepare($sql);
        $stmt->execute([$quantidade, $idProduto, $quantidade]);

        return $stmt->rowCount() === 1;
    }

    public function increaseStock(int $idProduto, int $quantidade, \PDO $con)
    {
        $sql = "UPDATE produto SET estoque = estoque + ? WHERE id = ?";
        $stmt = $con->prepare($sql);
        $stmt->execute([$quantidade, $idProduto]);

        return $stmt->rowCount() === 1;
    }

    public function isSkuRegisteredNotEquals(string $sku, int $id)
    {
        try {
            $sql = "SELECT id FROM produto WHERE sku = ? AND id != ?";
            $con = Conexao::conectar();
            $stmt = $con->prepare($sql);
            $stmt->execute([$sku, $id]);
            $dadoBruto = $stmt->fetch(\PDO::FETCH_ASSOC);
            Conexao::desconectar();

            if (!$dadoBruto) {
                return false;
            }

            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function isSkuRegistered(string $sku)
    {
        try {
            $sql = "SELECT sku FROM produto WHERE sku = ?";
            $con = Conexao::conectar();
            $stmt = $con->prepare($sql);
            $stmt->execute([$sku]);
            $dadoBruto = $stmt->fetch(\PDO::FETCH_ASSOC);
            Conexao::desconectar();

            if (!$dadoBruto) {
                return false;
            }

            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function Insert(Produto $produto)
    {
        try {
            $sql = "INSERT INTO produto (sku, nome, categoria, preco, estoque) VALUES (?, ?, ?, ?, ?)";

            $con = Conexao::conectar();
            $stmt = $con->prepare($sql);

            $result = $stmt->execute([
                $produto->getSku(),
                $produto->getNome(),
                $produto->getCategoria(),
                $produto->getPreco(),
                $produto->getEstoque()
            ]);

            Conexao::desconectar();

            return $result;
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function Update(Produto $produto)
    {
        try {
            $sql = "UPDATE produto SET sku = ?, nome = ?, categoria = ?, preco = ?, estoque = ? WHERE id = ?";

            $con = Conexao::conectar();
            $stmt = $con->prepare($sql);

            $result = $stmt->execute([
                $produto->getSku(),
                $produto->getNome(),
                $produto->getCategoria(),
                $produto->getPreco(),
                $produto->getEstoque(),
                $produto->getId()
            ]);

            Conexao::desconectar();

            return $result;
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function Delete(int $id)
    {
        try {
            $sql = "DELETE FROM produto WHERE id = ?";

            $con = Conexao::conectar();
            $stmt = $con->prepare($sql);
            $stmt->execute([$id]);


            $linhasAfetadas = $stmt->rowCount();

            Conexao::desconectar();

            return $linhasAfetadas > 0;
        } catch (\PDOException $e) {
            return false;
        }
    }
}
