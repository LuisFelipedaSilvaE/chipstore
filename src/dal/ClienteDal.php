<?php

namespace dal;

include_once(__DIR__ . '/Conexao.php');
include_once(__DIR__ . '/../model/Cliente.php');

use \model\Cliente;

class ClienteDal
{
  public function findAll()
  {
    try {
      $sql = "SELECT * FROM cliente";
      $con = Conexao::conectar();
      $stmt = $con->prepare($sql);
      $stmt->execute();
      $dadosBrutos = $stmt->fetchAll(\PDO::FETCH_ASSOC);

      Conexao::desconectar();

      $listaClientes = [];

      foreach ($dadosBrutos as $linha) {
        $cliente = new Cliente();


        $cliente->setId($linha['id_cliente']);
        $cliente->setNome($linha['nome']);
        $cliente->setEmail($linha['email']);
        $cliente->setTelefone($linha['telefone']);
        $cliente->setCidade($linha['cidade']);

        $listaClientes[] = $cliente;
      }

      return $listaClientes;
    } catch (\PDOException $e) {
      return [];
    }
  }

  public function findById(int $id)
  {
    try {
      $sql = "SELECT * FROM cliente WHERE id = ?";
      $con = Conexao::conectar();
      $stmt = $con->prepare($sql);


      $stmt->execute([$id]);
      $dadoBruto = $stmt->fetch(\PDO::FETCH_ASSOC);
      Conexao::desconectar();

      if (!$dadoBruto) {
        return null;
      }

      $cliente = new Cliente();

      $cliente->setId($dadoBruto['id']);
      $cliente->setNome($dadoBruto['nome']);
      $cliente->setEmail($dadoBruto['email']);
      $cliente->setTelefone($dadoBruto['telefone']);
      $cliente->setCidade($dadoBruto['cidade']);

      return $cliente;
    } catch (\PDOException $e) {
      return null;
    }
  }

  public function Insert(Cliente $cliente)
  {
    try {
      $sql = "INSERT INTO cliente (nome, email, telefone, cidade) VALUES (?, ?, ?, ?)";

      $con = Conexao::conectar();
      $stmt = $con->prepare($sql);

      $result = $stmt->execute([
        $cliente->getNome(),
        $cliente->getEmail(),
        $cliente->getTelefone(),
        $cliente->getCidade()
      ]);

      Conexao::desconectar();

      return $result;
    } catch (\PDOException $e) {
      return false;
    }
  }

  public function Update(Cliente $cliente)
  {
    try {
      $sql = "UPDATE cliente SET nome = ?, email = ?, senha = ?, telefone = ?, cidade = ? WHERE id = ?";

      $con = Conexao::conectar();
      $stmt = $con->prepare($sql);

      $result = $stmt->execute([
        $cliente->getNome(),
        $cliente->getEmail(),
        $cliente->getTelefone(),
        $cliente->getCidade(),
        $cliente->getId()
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

      $sql = "DELETE FROM cliente WHERE id = ?";

      $con = Conexao::conectar();
      $stmt = $con->prepare($sql);
      $result = $stmt->execute([$id]);


      $linhasAfetadas = $stmt->rowCount();

      Conexao::desconectar();

      return $linhasAfetadas > 0;
    } catch (\PDOException $e) {
      return false;
    }
  }
}
