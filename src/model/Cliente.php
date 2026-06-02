<?php

namespace model;


class Cliente
{
    private ?int $id_cliente;
    private ?string $nome;
    private ?string $email;
    private ?string $senha;
    private ?string $telefone;
    private ?string $cidade;
    private ?string $data_cadastro; 
    
 

    public function __construct() {}

    public function getId()
    {
        return $this->id_cliente;
    }

    public function setId(int $id)
    {
        $this->id_cliente = $id; 
    }

    public function getNome()
    {
        return $this->nome;
    }

    public function setNome(string $nome)
    {
        $this->nome = $nome;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    public function getSenha()
    {
        return $this->senha;
    }

    public function setSenha(string $senha) 
    {
        $this->senha = $senha;
    }

    public function getTelefone()
    {
        return $this->telefone;
    }

    public function setTelefone(string $telefone)
    {
        $this->telefone = $telefone;
    }

    public function getCidade()
    {
        return $this->cidade;
    }

    public function setCidade(string $cidade)
    {
        $this->cidade = $cidade;
    }

    public function getDataCadastro()
    {
        return $this->data_cadastro;
    }

    public function setDataCadastro(string $data_cadastro)
    {
        $this->data_cadastro = $data_cadastro;
    }

    
}