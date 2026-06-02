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
                $produto->setId($linha['id_produto']);
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

    public function Insert(Produto $produto)
    {
        // Usando prepared statements
        $sql = "INSERT INTO produto (sku, nome, categoria, preco, estoque) VALUES (?, ?, ?, ?, ?)";

        $con = Conexao::conectar();
        $query = $con->prepare($sql);
        
        $result = $query->execute([
            $produto->getSku(), 
            $produto->getNome(), 
            $produto->getCategoria(), 
            $produto->getPreco(), 
            $produto->getEstoque()
        ]);
        
        Conexao::desconectar();

        return $result;
    }

    public function Update(Produto $produto)
    {
        $sql = "UPDATE produto SET sku = ?, nome = ?, categoria = ?, preco = ?, estoque = ? WHERE id = ?";

        $con = Conexao::conectar();
        $query = $con->prepare($sql);
        
        $result = $query->execute([
            $produto->getSku(), 
            $produto->getNome(), 
            $produto->getCategoria(), 
            $produto->getPreco(), 
            $produto->getEstoque(), 
            $produto->getId()
        ]);
        
        Conexao::desconectar();

        return $result;
    }

    public function Delete(int $id)
    {
        $sql = "DELETE FROM produto WHERE id = ?";

        $con = Conexao::conectar();
        $query = $con->prepare($sql);
        $result = $query->execute([$id]);
        Conexao::desconectar();

        return $result;
    }
}