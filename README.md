# 📝 Blog App — TCC Engenharia de Software (Turma 242)

> Sistema de Blog/CMS desenvolvido em **PHP 8.4 puro** (sem frameworks) como Trabalho de Conclusão de Curso (TCC) em Engenharia de Software — com foco nos **fundamentos da linguagem e da web**, arquitetura MVC com Service Layer, e infraestrutura containerizada com Docker.

**Autor:** Luís Otávio Villela Antunes  
**Contato:** luis.villela3@gmail.com

---

## 📑 Índice

- [Sobre o Projeto](#-sobre-o-projeto)
- [Tecnologias Utilizadas](#-tecnologias-utilizadas)
- [Arquitetura](#-arquitetura)
- [Estrutura de Diretórios](#-estrutura-de-diretórios)
- [Banco de Dados](#-banco-de-dados)
- [Fluxo de Requisições](#-fluxo-de-requisições)
- [Sistema de Rotas](#-sistema-de-rotas)
- [Autenticação e Autorização](#-autenticação-e-autorização)
- [Camada de Serviços](#-camada-de-serviços)
- [Sistema de Views](#-sistema-de-views)
- [Segurança](#-segurança)
- [Infraestrutura Docker](#-infraestrutura-docker)
- [Logging](#-logging)
- [Pré-requisitos](#-pré-requisitos)
- [Instalação e Configuração](#-instalação-e-configuração)
- [Variáveis de Ambiente](#-variáveis-de-ambiente)

---

## 🎯 Sobre o Projeto

O **Blog App** é um CMS (Content Management System) de blog completo, construído em **PHP 8.4 puro** — sem o uso de frameworks — seguindo o padrão arquitetural **MVC com camada de Service**. A proposta do TCC é demonstrar domínio dos fundamentos de desenvolvimento web, padrões de projeto e boas práticas de segurança, ao invés de depender de abstrações prontas de frameworks.

O sistema oferece três perfis de usuário com diferentes níveis de acesso:

| Perfil | Capacidades |
|--------|------------|
| **Usuário Comum** | Criar conta, fazer login, publicar/editar/excluir posts próprios, comentar em posts |
| **Moderador** | Moderar comentários (excluir comentários inadequados) |
| **Administrador** | Gerenciar todos os posts e usuários da plataforma (criar usuários de qualquer perfil, excluir posts de qualquer usuário, excluir usuários) |

### Filosofia do Projeto

- **Sem frameworks** — todo o código é escrito do zero, utilizando apenas bibliotecas pontuais para necessidades específicas (roteamento, DBAL, sanitização, etc.)
- **Foco em fundamentos** — o objetivo é aprofundar o entendimento de como a web funciona: ciclo de vida HTTP, sessões, segurança, acesso a banco de dados, gerenciamento de processos
- **Biblioteca, não framework** — cada dependência resolve uma responsabilidade isolada, sem ditar a estrutura do projeto

---

## 🛠 Tecnologias Utilizadas

### Aplicação

| Camada | Tecnologia | Versão | Finalidade |
|--------|-----------|--------|-----------|
| **Linguagem** | PHP | 8.4 | Linguagem principal do back-end |
| **Roteamento** | [altorouter/altorouter](https://github.com/dannyvankooten/AltoRouter) | ^2.0 | Mapeamento de URLs para Controllers |
| **Banco de Dados (DBAL)** | [doctrine/dbal](https://www.doctrine-project.org/projects/dbal.html) | ^4.4 | Abstração de acesso ao banco de dados |
| **Migrations** | [doctrine/migrations](https://www.doctrine-project.org/projects/migrations.html) | ^3.9 | Controle de versão do schema do banco |
| **Sanitização** | [ezyang/htmlpurifier](http://htmlpurifier.org/) | ^4.0 | Filtragem de conteúdo HTML (entrada e saída) |
| **Variáveis de Ambiente** | [vlucas/phpdotenv](https://github.com/vlucas/phpdotenv) | ^5.6 | Carregamento de `.env` |
| **Logs** | [monolog/monolog](https://github.com/Seldaek/monolog) | ^3.10 | Log de erros estruturado com múltiplos canais |

### Infraestrutura

| Componente | Tecnologia | Versão | Finalidade |
|-----------|-----------|--------|-----------|
| **Servidor Web** | Apache HTTP Server | 2.4 | Proxy reverso + servidor de arquivos estáticos |
| **Processamento PHP** | PHP-FPM | 8.4 | Gerenciamento de processos PHP |
| **Banco de Dados** | MySQL | 8.4 | Armazenamento relacional |
| **Admin de Banco** | phpMyAdmin | 5.2 | Interface web para administração do MySQL |
| **Containerização** | Docker Compose | — | Orquestração de todos os serviços |

### Desenvolvimento

| Ferramenta | Finalidade |
|-----------|-----------|
| **PHPMetrics** (^2.9, dev) | Geração de relatórios de métricas de código |
---

## 🏗 Arquitetura

O projeto implementa o padrão **MVC com camada de Service** e injeção de dependências manual via um array `$dependencyContainer`, construído no Kernel:

```
                     Requisição HTTP
                          │
                          ▼
               ┌─────────────────────┐
               │   Apache (httpd)    │  Proxy reverso
               │   mod_rewrite       │  Reescrita de URLs
               │   mod_deflate       │  Compressão gzip
               └─────────┬───────────┘
                         │ proxy:fcgi://php:9000
                         ▼
               ┌─────────────────────┐
               │  public/index.php   │  Ponto de entrada único
               └─────────┬───────────┘
                         │ require
                         ▼
               ┌─────────────────────┐
               │  src/Kernel/        │  Bootstrap:
               │  kernel.php         │  • Sessão e CSRF
               │                     │  • Conexão com o Banco de Dados
               │                     │  • DI Container (Instanciação e injeção)
               │                     │  • Definição de Rotas
               └──────────┬──────────┘
                          │
                   ┌──────┴──────┐
                   │             │
                /admin/*     outras rotas
                   │             │
                   ▼             ▼
          AdminRouteHandler  RouteHandler
                   │             │
                   └──────┬──────┘
                          ▼
               ┌─────────────────────┐
               │  RouteMatchService  │  AltoRouter → Controller#método
               └─────────┬───────────┘
                         ▼
               ┌─────────────────────┐
               │     Controller      │  Camada fina: delega ao Serviço
               └─────────┬───────────┘
                         ▼
               ┌─────────────────────┐
               │      Service        │  Regras de negócio
               │                     │  Sanitização e Validação
               │                     │  Transações
               └─────────┬───────────┘
                         ▼
               ┌─────────────────────┐
               │    Repository       │  Queries e Statments via Doctrine DBAL
               └─────────┬───────────┘
                         ▼
               ┌─────────────────────┐
               │  ViewRenderService  │  Renderiza a View
               │                     │  Output Buffering
               │                     │  Headers de Segurança
               └─────────────────────┘
```

### Princípios Fundamentais

| Princípio | Descrição |
|----------|-----------|
| **Ponto de entrada único** | Todas as requisições passam por `public/index.php` via `.htaccess` |
| **MVC + Camada de Serviços** | Controllers são finos; a lógica de negócio está nos Serviços |
| **Injeção de dependências** | Array `$dependencyContainer` montado no Kernel e passado aos Controllers |
| **Separação de rotas** | Rotas `/admin/*` usam handler separado para controle de permissões |
| **DTOs imutáveis** | Classes `readonly` para transferência segura de dados (`ViewData`, `UserIdentity`) |

---

## 📂 Estrutura de Diretórios

```
TCC-Engenharia-de-Software-242/
│
├── config/                          # Configurações da aplicação
│   ├── .env                         # Variáveis de ambiente (não versionado)
│   ├── .env.sample                  # Modelo das variáveis de ambiente
│   ├── Permissions/
│   │   └── UserPermissions.php      # Enum: Admin=1, Moderator=2, RegularUser=3
│   ├── Routes/
│   │   ├── admin.php                # Rotas administrativas (AltoRouter)
│   │   └── main.php                 # Rotas do front-end (AltoRouter)
│   ├── Session/
│   │   └── SessionTime.php          # Enum: tempos de sessão
│   └── Views/
│       └── ViewPath.php             # Enum: caminhos(paths) de todas as views
│
├── public/                          # DocumentRoot do Apache
│   ├── .htaccess                    # Reescrita de URLs → index.php
│   ├── index.php                    # Pontro de Entrada Único
│   └── assets/                      # Scripts e estilos do Editor Quill e Bootstrap
│       ├── css/                     # Folhas de estilo
│       └── js/                      # Scripts
│
├── src/                              # Código-fonte da aplicação
│   ├── Controllers/                  # Camada de apresentação (MVC)
│   │   ├── BaseController.php        # Classe abstrata (prepareView, authManager)
│   │   ├── AuthController.php        # Login e Logout
│   │   ├── HomeController.php        # Página inicial
│   │   ├── PostController.php        # CRUD de posts + comentários
│   │   ├── AdminController.php       # Dashboard e operações administrativas
│   │   └── RegularUserController.php # Cadastro, login e dashboard do usuário
│   │
│   ├── Kernel/
│   │   └── kernel.php               # Bootstrap: DI container, sessão, rotas
│   │
│   ├── Models/                      # DTOs e modelos de dados
│   │   ├── Comments/
│   │   │   └── CommentData.php      # DTO de comentário (respostas aninhadas)
│   │   ├── Users/
│   │   │   ├── User.php             # Modelo de usuário
│   │   │   └── UserIdentity.php     # DTO imutável (readonly)
│   │   └── Views/
│   │       └── ViewData.php         # DTO imutável da view (readonly)
│   │
│   ├── Repositories/                # Acesso a dados (Doctrine DBAL)
│   │   ├── CommentRepository.php    # Operações de comentários
│   │   ├── PostRepository.php       # Operações de posts
│   │   ├── SlugRepository.php       # Operações de slugs
│   │   └── UserRepository.php       # Operações de usuários
│   │
│   ├── Services/                            # Regras de negócio
│   │   ├── AuthManagerService.php           # Fachada: autenticação + autorização + CSRF
│   │   ├── AuthenticationControlService.php # Verificação de credenciais
│   │   ├── AuthorizationService.php         # RBAC (controle por papéis)
│   │   ├── CommentService.php               # Lógica de comentários (árvore de respostas)
│   │   ├── CsrfService.php                  # Tokens CSRF
│   │   ├── DatabaseConnectionService.php    # Conexão via Doctrine DBAL + phpdotenv
│   │   ├── InputSanitizationService.php     # HTMLPurifier + regex
│   │   ├── PostManagementService.php        # CRUD de posts com transações
│   │   ├── RedirectService.php              # Redirecionamentos HTTP
│   │   ├── RouteMatchService.php            # AltoRouter → Controller
│   │   ├── SessionService.php               # Sessão segura
│   │   ├── SlugService.php                  # Geração e unicidade de slugs
│   │   ├── UserManagementService.php        # CRUD de usuários com transações
│   │   └── ViewRenderService.php            # Renderização com output buffering
│   │
│   ├── Utils/
│   │   └── PasswordHash.php         # Helper: password_hash (bcrypt)
│   │
│   ├── Views/                            # Templates PHP
│   │   ├── BaseView.php                  # Layout base (HTML shell)
│   │   ├── Admin/                        # Views do painel administrativo
│   │   │   ├── AddUserView.php           # Formulário administrativo de adição de usuários
│   │   │   ├── DashBoardView.php         # Home do painel administrativo
│   │   │   ├── ListAllUsersPostsView.php # Lista administrativa de todos os artigos
│   │   │   ├── ListAllUsersView.php      # Lista administrativa de todos os usuários
│   │   │   └── LoginView.php             # Página de login administrativo
│   │   ├── Frontend/                        # Views públicas e do usuário comum
│   │   │   ├── DashBoardViewRegularUser.php # Home do painel do usuário
│   │   │   ├── HomePageView.php             # Home do CMS
│   │   │   ├── LoginViewRegularUser.php     # Página de login
│   │   │   ├── PostFormEditView.php         # Form de edição de post
│   │   │   ├── PostFormView.php             # Fomr de criação de post
│   │   │   ├── PostHomeView.php             # Página com todos os posts
│   │   │   ├── PostView.php                 # Página de visualização de post
│   │   │   └── SignupView.php               # Página de cadastro
│   │   └── Partial/                    # Views parciais reutilizáveis
│   │       ├── CommentPartialView.php  # Comentários e respostas
│   │       ├── PostListPartialView.php # Listagem de posts (para o dashboard)
│   │       ├── QuillPartialView.php    # Editor de texto (Quill)
│   │       └── UserListPartialView.php # Listagem de usuários (para o dashboard)
│   │
│   └── Interfaces/                  # Interfaces (reservado para uso futuro, para reduzir acoplamento)
│
├── setup/                           # Infraestrutura e dados iniciais
│   ├── blog_app_SCHEMA.sql          # DDL completo do banco de dados
│   ├── blog_app_DATA.sql            # Dados iniciais
│   ├── docker-blogapp/              # Configuração Docker
│   │   ├── compose.yml              # Orquestração: Apache + PHP-FPM + MySQL + phpMyAdmin
│   │   ├── .env / .env.sample       # Variáveis do Docker (senhas MySQL)
│   │   ├── apache/                  # Dockerfile + VirtualHosts + SSL
│   │   ├── php/                     # Dockerfile + configs PHP-FPM + OPcache
│   │   └── mysql/                   # Configuração customizada (tcc-mysql.cnf)
│   └── migrations/                  # Doctrine Migrations
│
├── cache/                           # Cache do HTMLPurifier (gerado automaticamente)
├── logs/                            # Logs da aplicação (security, app, infrastructure)
├── composer.json                    # Dependências e autoload PSR-4
└── composer.lock                    # Versões exatas (não versionado)
```

## 🗄 Banco de Dados

O banco utiliza **MySQL 8.4** com charset `utf8mb4` e collation `utf8mb4_0900_ai_ci`. A estrutura relacional é composta por **11 tabelas**:

### Descrição das Tabelas

| Tabela | Descrição | Destaques |
|--------|-----------|-----------|
| `users` | Usuários do sistema | Constraints UNIQUE em `username` e `email` |
| `post` | Posts/artigos do blog | FULLTEXT index em `title` + `content`; slug UNIQUE |
| `post_users` | Relacionamento M:N posts ↔ autores | FK com `ON DELETE CASCADE` |
| `category` | Categorias de posts | FULLTEXT index em `title` + `description` |
| `tag` | Tags para classificação | Slug UNIQUE |
| `post_category` | M:N posts ↔ categorias | FK com `ON DELETE CASCADE` |
| `post_tag` | M:N posts ↔ tags | FK com `ON DELETE CASCADE` |
| `category_tag` | M:N categorias ↔ tags | FK com `ON DELETE CASCADE` |
| `user_comment_post` | Comentários em posts | Self-referencing FK (`parent`) para respostas aninhadas; FULLTEXT em `content` |
| `user_reaction_post` | Reações (like/dislike) em posts | PK composta `(id_user, id_post)` |
| `slug_map` | Mapeamento polimórfico de slugs | Entidade + tipo (post, category, tag) com UNIQUE constraint |