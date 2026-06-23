# ChipStore — Sistema de Gerenciamento

Sistema web de gerenciamento para uma loja de componentes eletrônicos, desenvolvido em PHP com arquitetura MVC adaptada (Model / DAL / View + Actions). Permite o controle completo de clientes, produtos, pedidos e usuários do sistema.

---

## Sumário

- [Tecnologias](#tecnologias)
- [Arquitetura](#arquitetura)
- [Estrutura de Pastas](#estrutura-de-pastas)
- [Banco de Dados](#banco-de-dados)
- [Rotas](#rotas)
- [Como Executar](#como-executar)
- [Variáveis de Ambiente / Configuração](#variáveis-de-ambiente--configuração)

---

## Tecnologias

| Camada         | Tecnologia                      |
| -------------- | ------------------------------- |
| Backend        | PHP 8.5 (Apache)                |
| Banco de dados | MySQL 8.4                       |
| ORM / Acesso   | PDO (PHP Data Objects)          |
| Frontend       | HTML5, Tailwind CSS, JavaScript |
| Ícones         | Font Awesome (CDN)              |
| Containers     | Docker & Docker Compose         |
| Admin DB       | phpMyAdmin                      |

---

## Arquitetura

O projeto segue uma variação do padrão **MVC** dividida em quatro camadas:

```
┌─────────────┐     POST/GET     ┌──────────────┐
│   View      │ ──────────────▶  │   Actions    │  ← Controllers
│  (modules/) │ ◀──  redirect    │  (actions/)  │
└──────┬──────┘                  └──────┬───────┘
       │ lê dados                       │ persiste / busca
       ▼                                ▼
┌─────────────┐                  ┌──────────────┐
│    Model    │ ◀─── instancia ─ │     DAL      │
│  (model/)   │                  │   (dal/)     │
└─────────────┘                  └──────┬───────┘
                                        │
                                  ┌─────▼──────┐
                                  │   MySQL    │
                                  └────────────┘
```

- **Model** (`src/model/`) — Classes POPO com propriedades privadas e getters/setters que representam as entidades do banco.
- **DAL — Data Access Layer** (`src/dal/`) — Responsável por toda a lógica SQL via PDO. Cada entidade tem sua classe DAL com métodos `findAll()`, `findById()`, `Insert()`, `Update()`, `Delete()`.
- **View** (`src/view/modules/`) — Arquivos PHP que renderizam o HTML. Consultam a DAL diretamente para exibir dados e submetem formulários para as Actions.
- **Actions** (`src/view/actions/`) — Scripts que processam requisições POST/GET, instanciam Models, chamam a DAL e redirecionam de volta para as Views com mensagens de status via sessão.

---

## Estrutura de Pastas

```
chipstore/
│
├── Dockerfile                        # Imagem PHP 8.5 + Apache + PDO MySQL
├── docker-compose.yaml               # Orquestração dos containers
├── tailwind.config.js                # Configuração do Tailwind CSS
│
└── src/                              # Raiz do documento Apache
    │
    ├── index.php                     # Entry point — redireciona para o dashboard
    │
    ├── model/                        # Entidades (POPOs)
    │   ├── Cliente.php
    │   ├── Produto.php
    │   ├── Pedido.php
    │   ├── ItemPedido.php
    │   └── Usuario.php
    │
    ├── dal/                          # Data Access Layer
    │   ├── Conexao.php               # Singleton de conexão PDO
    │   ├── ClienteDal.php
    │   ├── ProdutoDal.php
    │   ├── PedidoDal.php
    │   ├── ItemPedidoDal.php
    │   ├── UsuarioDal.php
    │   └── db/
    │       └── chipstore.sql         # Schema completo do banco de dados
    │
    ├── shared/                       # Componentes e assets globais
    │   ├── head.html                 # Tags <head> globais (CDN, meta)
    │   ├── shared.css                # Estilos globais
    │   └── components/
    │       └── sidebar/
    │           └── sidebar.php       # Navegação lateral (menu principal)
    │
    └── view/
        ├── index.php                 # Verificação de sessão / guard de autenticação
        │
        ├── login/
        │   └── index.php             # Página de login
        │
        ├── actions/                  # Controllers (processam formulários)
        │   ├── login-action.php
        │   ├── logout.php
        │   ├── cliente/
        │   │   ├── adicionar-cliente-action.php
        │   │   ├── editar-cliente-action.php
        │   │   └── remover-cliente-action.php
        │   ├── produto/
        │   │   ├── adicionar-produto-action.php
        │   │   ├── editar-produto-action.php
        │   │   └── remover-produto-action.php
        │   ├── pedido/
        │   │   ├── adicionar-pedido-action.php
        │   │   ├── editar-pedido-action.php
        │   │   └── remover-pedido-action.php
        │   └── usuario/
        │       ├── adicionar-usuario-action.php
        │       ├── editar-usuario-action.php
        │       └── remover-usuario-action.php
        │
        └── modules/                  # Views (interfaces do sistema)
            ├── dashboard/
            │   └── index.php
            ├── cliente/
            │   ├── index.php         # Listagem de clientes
            │   ├── adicionar/
            │   │   └── index.php
            │   └── editar/
            │       └── index.php     # Recebe ?id=X via GET
            ├── produto/
            │   ├── index.php
            │   ├── adicionar/
            │   │   └── index.php
            │   └── editar/
            │       └── index.php
            ├── pedido/
            │   ├── index.php
            │   ├── adicionar/
            │   │   └── index.php
            │   └── editar/
            │       └── index.php
            └── usuario/
                ├── index.php
                ├── adicionar/
                │   └── index.php
                └── editar/
                    └── index.php
```

---

## Banco de Dados

O schema é inicializado automaticamente pelo Docker a partir do arquivo `src/dal/db/chipstore.sql`.

### Diagrama de Entidades

```
┌──────────┐       ┌──────────┐       ┌────────────┐       ┌──────────┐
│ cliente  │──────▶│  pedido  │◀──────│ itemPedido │──────▶│ produto  │
└──────────┘  1:N  └──────────┘  1:N  └────────────┘  N:1  └──────────┘

┌──────────┐
│ usuario  │  (usuários do sistema — acesso ao painel)
└──────────┘
```

### Tabelas

#### `cliente`

| Coluna       | Tipo         | Descrição              |
| ------------ | ------------ | ---------------------- |
| id           | INT PK AI    | Identificador único    |
| nome         | VARCHAR(100) | Nome completo          |
| email        | VARCHAR(100) | E-mail único           |
| senha        | VARCHAR(255) | Senha (opcional)       |
| telefone     | VARCHAR(20)  | Telefone de contato    |
| cidade       | VARCHAR(100) | Cidade do cliente      |
| dataCadastro | DATETIME     | Data de criação (auto) |

#### `produto`

| Coluna    | Tipo          | Descrição             |
| --------- | ------------- | --------------------- |
| id        | INT PK AI     | Identificador único   |
| sku       | VARCHAR(50)   | Código SKU único      |
| nome      | VARCHAR(150)  | Nome do produto       |
| categoria | VARCHAR(50)   | Categoria             |
| preco     | DECIMAL(10,2) | Preço de venda        |
| estoque   | INT           | Quantidade em estoque |

#### `pedido`

| Coluna     | Tipo                                               | Descrição             |
| ---------- | -------------------------------------------------- | --------------------- |
| id         | INT PK AI                                          | Identificador único   |
| idCliente  | INT FK → cliente.id                                | Cliente do pedido     |
| dataPedido | DATETIME                                           | Data do pedido (auto) |
| status     | ENUM(Pendente, Pago, Enviado, Entregue, Cancelado) | Status atual          |
| pagamento  | VARCHAR(50)                                        | Forma de pagamento    |
| valorTotal | DECIMAL(10,2)                                      | Valor total           |

#### `itemPedido`

| Coluna        | Tipo          | Descrição                  |
| ------------- | ------------- | -------------------------- |
| idPedido      | INT FK PK     | Referência ao pedido       |
| idProduto     | INT FK PK     | Referência ao produto      |
| quantidade    | INT           | Quantidade do item         |
| precoUnitario | DECIMAL(10,2) | Preço no momento da compra |

#### `usuario`

| Coluna | Tipo         | Descrição            |
| ------ | ------------ | -------------------- |
| id     | INT PK AI    | Identificador único  |
| nome   | VARCHAR(100) | Nome do usuário      |
| email  | VARCHAR(100) | E-mail único (login) |
| senha  | VARCHAR(255) | Senha                |

---

## Rotas

O roteamento é baseado em diretórios (padrão Apache/PHP). Todas as rotas são relativas à raiz do servidor (porta `8000`).

### Autenticação

| Método | Rota                             | Descrição                      |
| ------ | -------------------------------- | ------------------------------ |
| GET    | `/view/login/`                   | Exibe a página de login        |
| POST   | `/view/actions/login-action.php` | Processa o login               |
| GET    | `/view/actions/logout.php`       | Encerra a sessão e redireciona |

### Dashboard

| Método | Rota                       | Descrição                    |
| ------ | -------------------------- | ---------------------------- |
| GET    | `/` ou `/index.php`        | Redireciona para o dashboard |
| GET    | `/view/modules/dashboard/` | Painel principal             |

### Clientes

| Método | Rota                                                 | Descrição              |
| ------ | ---------------------------------------------------- | ---------------------- |
| GET    | `/view/modules/cliente/`                             | Listagem de clientes   |
| GET    | `/view/modules/cliente/adicionar/`                   | Formulário de cadastro |
| POST   | `/view/actions/cliente/adicionar-cliente-action.php` | Salva novo cliente     |
| GET    | `/view/modules/cliente/editar/?id=X`                 | Formulário de edição   |
| POST   | `/view/actions/cliente/editar-cliente-action.php`    | Atualiza cliente       |
| POST   | `/view/actions/cliente/remover-cliente-action.php`   | Remove cliente         |

### Produtos

| Método | Rota                                                 | Descrição              |
| ------ | ---------------------------------------------------- | ---------------------- |
| GET    | `/view/modules/produto/`                             | Listagem de produtos   |
| GET    | `/view/modules/produto/adicionar/`                   | Formulário de cadastro |
| POST   | `/view/actions/produto/adicionar-produto-action.php` | Salva novo produto     |
| GET    | `/view/modules/produto/editar/?id=X`                 | Formulário de edição   |
| POST   | `/view/actions/produto/editar-produto-action.php`    | Atualiza produto       |
| POST   | `/view/actions/produto/remover-produto-action.php`   | Remove produto         |

### Pedidos

| Método | Rota                                               | Descrição             |
| ------ | -------------------------------------------------- | --------------------- |
| GET    | `/view/modules/pedido/`                            | Listagem de pedidos   |
| GET    | `/view/modules/pedido/adicionar/`                  | Formulário de criação |
| POST   | `/view/actions/pedido/adicionar-pedido-action.php` | Salva novo pedido     |
| GET    | `/view/modules/pedido/editar/?id=X`                | Formulário de edição  |
| POST   | `/view/actions/pedido/editar-pedido-action.php`    | Atualiza pedido       |
| POST   | `/view/actions/pedido/remover-pedido-action.php`   | Remove pedido         |

### Usuários

| Método | Rota                                                 | Descrição              |
| ------ | ---------------------------------------------------- | ---------------------- |
| GET    | `/view/modules/usuario/`                             | Listagem de usuários   |
| GET    | `/view/modules/usuario/adicionar/`                   | Formulário de cadastro |
| POST   | `/view/actions/usuario/adicionar-usuario-action.php` | Salva novo usuário     |
| GET    | `/view/modules/usuario/editar/?id=X`                 | Formulário de edição   |
| POST   | `/view/actions/usuario/editar-usuario-action.php`    | Atualiza usuário       |
| POST   | `/view/actions/usuario/remover-usuario-action.php`   | Remove usuário         |

---

## Como Executar

### Pré-requisitos

- [Docker](https://www.docker.com/) instalado
- [Docker Compose](https://docs.docker.com/compose/) instalado

### 1. Clonar o repositório

```bash
git clone <url-do-repositorio>
cd chipstore
```

### 2. Subir os containers

```bash
docker compose up -d
```

Isso irá iniciar três serviços:

| Serviço      | Container        | Porta local | Descrição                   |
| ------------ | ---------------- | ----------- | --------------------------- |
| PHP + Apache | `php_app`        | `8000`      | Aplicação web               |
| MySQL        | `mysql_db`       | `3306`      | Banco de dados              |
| phpMyAdmin   | `phpmyadmin_app` | `8080`      | Interface de admin do banco |

O banco de dados é populado automaticamente com o schema de `src/dal/db/chipstore.sql` na primeira execução.

### 3. Acessar o sistema

- **Aplicação:** [http://localhost:8000](http://localhost:8000)
- **phpMyAdmin:** [http://localhost:8080](http://localhost:8080)
  - Usuário: `user` | Senha: `user`

### 4. Parar os containers

```bash
docker compose down
```

Para remover também os volumes (dados do banco):

```bash
docker compose down -v
```

---

## Variáveis de Ambiente / Configuração

As credenciais do banco estão definidas em dois lugares (ambiente Docker):

**`docker-compose.yaml`** — define as variáveis do container MySQL:

```yaml
MYSQL_DATABASE: chipstore
MYSQL_USER: user
MYSQL_PASSWORD: user
MYSQL_ROOT_PASSWORD: root
```

**`src/dal/Conexao.php`** — utiliza as credenciais via PDO:

```php
private static $dbname   = 'chipstore';
private static $dbHost   = 'db';       // nome do serviço no docker-compose
private static $dbUsuario = 'user';
private static $dbSenha  = 'user';
```

> Para ambientes de produção, substitua as credenciais por variáveis de ambiente seguras e nunca exponha `MYSQL_ROOT_PASSWORD` publicamente.
