-- 1. Criação do Banco de Dados
CREATE DATABASE IF NOT EXISTS chipstore CHARACTER
SET
    utf8mb4 COLLATE utf8mb4_unicode_ci;

USE chipstore;

-- 2. Tabela: Cliente
CREATE TABLE
    IF NOT EXISTS cliente (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        senha VARCHAR(255) NULL,
        telefone VARCHAR(20) NULL,
        cidade VARCHAR(100) NULL,
        dataCadastro DATETIME DEFAULT CURRENT_TIMESTAMP
    );

-- 3. Tabela: Produto
CREATE TABLE
    IF NOT EXISTS produto (
        id INT AUTO_INCREMENT PRIMARY KEY,
        sku VARCHAR(50) NOT NULL UNIQUE,
        nome VARCHAR(150) NOT NULL,
        categoria VARCHAR(50) NULL,
        preco DECIMAL(10, 2) NOT NULL,
        estoque INT DEFAULT 0
    );

-- 4. Tabela: Pedido
CREATE TABLE
    IF NOT EXISTS pedido (
        id INT AUTO_INCREMENT PRIMARY KEY,
        idCliente INT NOT NULL,
        dataPedido DATETIME DEFAULT CURRENT_TIMESTAMP,
        status ENUM (
            'Pendente',
            'Pago',
            'Enviado',
            'Entregue',
            'Cancelado'
        ) DEFAULT 'Pendente',
        pagamento VARCHAR(50) NULL,
        valorTotal DECIMAL(10, 2) NOT NULL,
        CONSTRAINT fkPedidoCliente FOREIGN KEY (idCliente) REFERENCES cliente (id) ON DELETE RESTRICT
    );

-- 5. Tabela: ItemPedido
CREATE TABLE
    IF NOT EXISTS itemPedido (
        idPedido INT NOT NULL,
        idProduto INT NOT NULL,
        quantidade INT NOT NULL DEFAULT 1,
        precoUnitario DECIMAL(10, 2) NOT NULL,
        PRIMARY KEY (idPedido, idProduto),
        CONSTRAINT fkItemPedido FOREIGN KEY (idPedido) REFERENCES pedido (id) ON DELETE CASCADE,
        CONSTRAINT fkItemProduto FOREIGN KEY (idProduto) REFERENCES produto (id) ON DELETE RESTRICT
    );

-- 6. Tabela: Usuário
CREATE TABLE
    IF NOT EXISTS usuario (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        senha VARCHAR(255) NOT NULL
    );