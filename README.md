# 📝 Blog App — TCC Engenharia de Software (Turma 242)

> Sistema de Blog/CMS desenvolvido em **PHP 8.4 puro** (sem frameworks) como Trabalho de Conclusão de Curso (TCC) em Engenharia de Software — com foco nos **fundamentos da linguagem e da web**, arquitetura MVC com Camada de Serviço, e infraestrutura containerizada com Docker.

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

O **Blog App** é um CMS (Content Management System) de blog completo, construído em **PHP 8.4 puro** — sem o uso de frameworks — seguindo o padrão arquitetural **MVC com camada de Serviço**. A proposta do TCC é demonstrar domínio dos fundamentos de desenvolvimento web, padrões de projeto e boas práticas de segurança, ao invés de depender de abstrações prontas de frameworks.

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

O projeto implementa o padrão **MVC com camada de Serviço** e injeção de dependências manual via um array `$dependencyContainer`, construído no Kernel:

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

### Níveis de Permissão

| Valor | Enum | Descrição |
|-------|------|-----------|
| `1` | `UserPermissions::Admin` | Administrador — acesso total |
| `2` | `UserPermissions::Moderator` | Moderador — moderação de comentários |
| `3` | `UserPermissions::RegularUser` | Usuário Comum — CRUD de posts próprios |

---

## 🔄 Fluxo de Requisições

1. **Requisição HTTP** chega ao **Apache**, e pelas regras do `.htaccess`, redireciona para `index.php`.

2. O Apache encaminha a requisição ao **PHP-FPM** via `proxy:fcgi://php:9000`.

3. O `public/index.php` carrega o **Kernel** (`src/Kernel/kernel.php`), que executa o bootstrap:
   - Carrega o autoload do Composer (`vendor/autoload.php`)
   - Estabelece a **conexão com o banco** via `DatabaseConnectionService` (Doctrine DBAL + phpdotenv)
   - Instancia os **3 canais de log** do Monolog (Security, App, Infrastructure)
   - Instancia todos os **Repositories**, **Services** e monta o **Dependency Container**
   - Inicia a **sessão** e gera o **token CSRF**
   - Carrega as **definições de rotas** (front-end e admin)
   - Define as funções `RouteHandler()` e `AdminRouteHandler()`

4. O `index.php` examina a URL via `parse_url()`:
   - Se contém `/admin/` → chama `AdminRouteHandler()`
   - Caso contrário → chama `RouteHandler()`

5. O **RouteMatchService** utiliza o AltoRouter para encontrar a rota correspondente, sanitiza a URL, instancia o Controller apropriado e invoca o método.

6. O **Controller** utiliza os Serviços injetados para processar a lógica e chama `ViewRenderService::render()` para exibir a view com os headers de segurança.

---

## 🛣 Sistema de Rotas

As rotas são definidas em dois arquivos separados usando o **AltoRouter**:

### Rotas Públicas (`config/Routes/main.php`)

| Método | Rota | Controller#Método | Descrição |
|--------|------|-------------------|-----------|
| `GET` | `/` | `HomeController#index` | Página inicial |
| `GET` | `/post/` | `PostController#index` | Lista de posts |
| `GET` | `/post/[:slug]/` | `PostController#show` | Visualizar post por slug |
| `GET` | `/login/` | `RegularUserController#index` | Página de login |
| `POST` | `/login/` | `AuthController#login` | Ação de login |
| `GET` | `/logout/` | `AuthController#logout` | Ação de logout |
| `GET` | `/signup/` | `RegularUserController#signUpPage` | Página de cadastro |
| `POST` | `/signup/` | `RegularUserController#signUpAction` | Ação de cadastro |
| `GET` | `/dashboard/` | `RegularUserController#dashboard` | Dashboard do usuário |
| `GET` | `/dashboard/post/add/` | `PostController#addPostForm` | Formulário de novo post |
| `POST` | `/dashboard/post/add/` | `PostController#addPostAction` | Ação de criar post |
| `GET` | `/dashboard/post/edit/[:postId]` | `PostController#editPostForm` | Formulário de edição |
| `POST` | `/dashboard/post/edit/[:postId]` | `PostController#editPostAction` | Ação de editar post |
| `POST` | `/dashboard/post/delete/[:postId]` | `PostController#deletePostAction` | Ação de excluir post |
| `POST` | `/post/comment/create/` | `PostController#createCommentAction` | Criar comentário |
| `POST` | `/post/comment/delete/` | `PostController#deleteCommentAction` | Excluir comentário |

### Rotas Administrativas (`config/Routes/admin.php`)

| Método | Rota | Controller#Método | Descrição |
|--------|------|-------------------|-----------|
| `GET` | `/admin/` | `AdminController#index` | Página de login admin |
| `POST` | `/admin/` | `AuthController#login` | Ação de login admin |
| `GET` | `/admin/logout/` | `AuthController#logout` | Logout admin |
| `GET` | `/admin/dashboard/` | `AdminController#dashboard` | Dashboard admin |
| `GET` | `/admin/dashboard/create/user/` | `AdminController#userCreatorForm` | Formulário de criação de usuário |
| `POST` | `/admin/dashboard/create/user/` | `AdminController#createUserAction` | Ação de criar usuário |
| `GET` | `/admin/dashboard/list/posts/` | `AdminController#getAllUsersPosts` | Listar todos os posts |
| `POST` | `/admin/dashboard/list/posts/delete/[:id]` | `AdminController#deletePostByAdminAction` | Excluir post (admin) |
| `GET` | `/admin/dashboard/list/users/` | `AdminController#getAllUsers` | Listar todos os usuários |
| `POST` | `/admin/dashboard/user/delete/[:userId]` | `AdminController#deleteUserAction` | Excluir usuário |

---

## 🔐 Autenticação e Autorização

### Autenticação

O sistema de autenticação é gerenciado pelo **AuthManagerService**, que atua como fachada (Facade Pattern) para os serviços:

- **`AuthenticationControlService`** — verifica credenciais (email + senha) via `UserManagementService`
- **`SessionService`** — gerencia a sessão PHP com configurações endurecidas:
  - `cookie_httponly = 1` —  proteção contra XSS via cookies
  - `use_strict_mode = 1` — rejeita IDs de sessão não inicializados pelo servidor
  - `use_cookies = 1 / use_only_cookies = 1` -  o identificador de sessão (PHPSESSID) trafega exclusivamente via cookie, impedindo propagação por URL (?PHPSESSID=xyz)
  - `cookie_samesite = Strict` — proteção contra CSRF via cookies
  - `cookie_secure = 0` — desabilitado intencionalmente no ambiente de demonstração (certificado SSL autoassinado); deve ser habilitado (1) em produção
  - Regeneração do ID de sessão no login (`session_regenerate_id(true)`)
- **`CsrfService`** — geração e validação de tokens CSRF em todas as requisições POST

### Autorização (RBAC)

O **AuthorizationService** implementa controle de acesso baseado em papéis (Role-Based Access Control):

| Ação | Admin | Moderador | Usuário Comum |
|------|:-------:|:-----------:|:--------------:|
| Acessar dashboard admin | ✅ | ✅ | ❌ |
| Acessar dashboard regular | ❌ | ❌ | ✅ |
| Criar posts | ❌ | ❌ | ✅ |
| Editar posts próprios | ❌ | ❌ | ✅ |
| Excluir posts próprios | ✅ | ❌ | ✅ |
| Excluir qualquer post | ✅ | ❌ | ❌ |
| Criar usuários (qualquer perfil) | ✅ | ❌ | ❌ |
| Excluir usuários | ✅ | ❌ | ❌ |
| Moderar comentários | ✅ | ✅ | ❌ |

---

## ⚙ Camada de Serviços

Os Serviços encapsulam toda a lógica de negócio e são injetados nos Controllers via o `$dependencyContainer`:

| Serviço | Responsabilidade |
|---------:|:-----------------|
| `AuthManagerService` | Fachada para autenticação, autorização e CSRF |
| `AuthenticationControlService` | Verificação de credenciais de login |
| `AuthorizationService` | Controle de permissões RBAC |
| `PostManagementService` | CRUD de posts com transações, sanitização (via InputSanitizationService) e gerenciamento de slugs (via SlugService) |
| `UserManagementService` | CRUD de usuários com transações e exclusão em cascata |
| `CommentService` | CRUD de comentários com árvore de respostas aninhadas |
| `InputSanitizationService` | HTMLPurifier para conteúdo de texto rico; regex para URLs e slugs |
| `ViewRenderService` | Renderização de views com output buffering e headers de segurança |
| `RouteMatchService` | Correspondência de rotas via AltoRouter e instanciação de Controllers |
| `SessionService` | Gerenciamento de sessão segura (configuração, CSRF, dados do usuário) |
| `SlugService` | Geração e gestão de slugs únicos |
| `CsrfService` | Geração e validação de tokens CSRF |
| `RedirectService` | Redirecionamentos HTTP |
| `DatabaseConnectionService` | Conexão com o banco via Doctrine DBAL e variáveis de ambiente |

---

## 🖼 Sistema de Views

O sistema de renderização utiliza **output buffering** do PHP (`ob_start()` / `ob_get_clean()`) e templates PHP puros:

1. O Controller chama `prepareView()` do `BaseController`, que cria um **ViewData** (DTO `readonly`)
2. O `ViewRenderService::render()` recebe o `ViewData`, carrega a view específica e injeta os dados via `extract()`
3. O conteúdo renderizado é inserido no layout base (`BaseView.php`)
4. Headers de segurança (CSP, X-Frame-Options, etc.) são adicionados antes do envio da resposta

### Organização das Views

| Diretório | Conteúdo |
|-----------|----------|
| `Views/Admin/` | Dashboard admin, gerenciamento de usuários, login admin |
| `Views/Frontend/` | Home, posts, login, cadastro, dashboard do usuário |
| `Views/Partial/` | Componentes reutilizáveis (editor Quill, lista de posts/usuários, comentários) |

---

## 🔒 Segurança

O projeto implementa múltiplas camadas de segurança:

### Proteções na Aplicação

| Medida | Implementação |
|--------|--------------|
| **Sanitização de entrada e saída** | HTMLPurifier para conteúdo rico; `strip_tags`, `preg_replace` e `htmlspecialchars` para campos simples |
| **Proteção CSRF** | Token por sessão, validado em todas as ações POST |
| **Hashing de senhas** | `password_hash()` com bcrypt |
| **Sessão segura** | `httponly`, `strict_mode`, `samesite=Strict`, regeneração de ID no login |
| **Transações no banco** | `beginTransaction()` / `commit()` / `rollBack()` em operações de escrita |

### Headers HTTP de Segurança

| Header | Valor | Proteção |
|--------|-------|----------|
| `Content-Security-Policy` | `default-src 'self'` | Prevenção de injeção de scripts/recursos externos |
| `X-Frame-Options` | `SAMEORIGIN` | Proteção contra clickjacking |
| `X-Content-Type-Options` | `nosniff` | Prevenção de MIME sniffing |

### Proteções no Servidor

| Medida | Implementação |
|--------|--------------|
| **ServerTokens** | `Prod` — oculta versão do Apache |
| **ServerSignature** | `Off` — sem assinatura nos erros |
| **expose_php** | `Off` — oculta o header `X-Powered-By` |
| **display_errors** | `Off` — sem exposição de erros em produção |
| **bind-address (MySQL)** | Apenas do hostname do container — sem acesso externo ao banco |
| **local-infile (MySQL)** | `0` — desabilita carregamento de arquivos via SQL |
| **MYSQL_ROOT_HOST** | `localhost` — root apenas via container |

---

## 🐳 Infraestrutura Docker

A aplicação roda em 4 containers orquestrados via **Docker Compose**, em duas redes isoladas:

### Arquitetura dos Containers

```
                    ┌─────────────────────────────────┐
                    │        Rede: blogapp_web        │
    Conexões        │                                 │
    Externas        │                                 │
    :80/:443 ──►    │  ┌───────────┐  ┌─────────────┐ │
                    │  │  Apache   │  │ phpMyAdmin  │ │
                    │  │  (httpd)  │  │ :80 interno │ │
                    │  └─────┬─────┘  └──────┬──────┘ │
                    │        │               │        │
                    └────────┼───────────────┼────────┘
                             │               │
                    ┌────────┼───────────────┼────────┐
                    │        │               │        │
                    │  ┌─────▼─────┐  ┌──────▼──────┐ │
                    │  │  PHP-FPM  │  │   MySQL     │ │
                    │  │  :9000    │  │   8.4       │ │
                    │  └───────────┘  └─────────────┘ │
                    │                                 │
                    │     Rede: blogapp_database      │
                    └─────────────────────────────────┘
```

### Serviços

| Container | Imagem | Detalhes |
|----------|--------|---------|
| `apache-httpd` | `httpd:2.4` (custom Dockerfile) | mod_rewrite, mod_proxy_fcgi, mod_ssl, mod_http2, mod_deflate; VirtualHosts para HTTP e HTTPS; proxy para PHP-FPM e phpMyAdmin; compressão gzip nível 5 |
| `php-fpm` | `php:8.4-fpm` (custom Dockerfile) | Extensões: pdo_mysql, zip, mbstring, opcache; OPcache habilitado (128MB, 4000 arquivos); PM dinâmico (máx 10 processos, restart após 500 requisições); slow log (>5s); UID/GID mapeados para o host |
| `mysql-db` | `mysql:8.4` | InnoDB buffer pool 256MB; slow query log (>1s); log de queries sem índice; máx 100 conexões; wait_timeout 60s; charset utf8mb4; scripts SQL carregados automaticamente via `docker-entrypoint-initdb.d/` |
| `phpmyadmin` | `phpmyadmin:5.2` | Acessível via `/phpmyadmin/` no Apache (proxy reverso) |

### Healthcheck

O MySQL possui healthcheck configurado que verifica a disponibilidade do banco a cada 5 segundos, garantindo que o PHP-FPM só inicie após o banco estar pronto (`depends_on: condition: service_healthy`).

---

## 📊 Logging

O sistema utiliza **Monolog** com três canais separados, cada um com `IntrospectionProcessor` (call stack) e `BufferHandler` (otimização de I/O):

| Canal | Arquivo | Finalidade |
|-------|---------|-----------|
| `Security` | `logs/security.log` | Falhas de login, tokens CSRF inválidos, acesso negado |
| `App` | `logs/app.log` | Erros de negócio (CRUD de posts, usuários, comentários) |
| `Infrastructure` | `logs/infrastructure.log` | Erros de repositório e banco de dados |

Além dos logs da aplicação, a infraestrutura também registra:
- **PHP-FPM slow log** — requisições que levam mais de 5 segundos
- **MySQL slow query log** — queries que levam mais de 1 segundo
- **MySQL no-index log** — queries que não utilizam índices

---
## 📋 Pré-requisitos

### Com Docker (recomendado)

- **Docker** (>= 20.x)
- **Docker Compose** (>= 2.x)
- **Git**
- **Ubuntu 24.04 - WSL**

---

## 🚀 Instalação e Configuração

### Com Docker (recomendado)

1. **Clone o repositório:**

   ```bash
   git clone https://github.com/lovillela/TCC-Engenharia-de-Software-242.git
   cd TCC-Engenharia-de-Software-242
   ```

2. **Configure as variáveis de ambiente do Docker:**

   ```bash
   cp setup/docker-blogapp/.env.sample setup/docker-blogapp/.env
   ```

   Edite `setup/docker-blogapp/.env` com as senhas desejadas para o MySQL:

   ```env
   DB_ROOT_PASSWORD='sua_senha_root'
   DB_PASSWORD='sua_senha_app'
   ```

3. **Configure as variáveis de ambiente da aplicação:**

   ```bash
   cp config/.env.sample config/.env
   ```

   Edite `config/.env` com os dados de conexão:

   ```env
   DB_NAME="blog_app"
   DB_USER="blogApp"
   DB_PASSWORD="sua_senha_app"
   DB_HOST="mysql-db"
   DB_PORT="3306"
   DB_DRIVER="pdo_mysql"
   DB_CHARSET="utf8mb4"
   DB_COLLATION="utf8mb4_0900_ai_ci"
   ```

   > **Importante:** O valor de `DB_PASSWORD` no `config/.env` deve ser igual ao `DB_PASSWORD` do `setup/docker-blogapp/.env`.

4. **Suba os containers:**

   ```bash
   cd setup/docker-blogapp
   docker compose up -d
   ```

5. **Acesse a aplicação:**

   | Serviço | URL |
   |---------|-----|
   | Blog App | `http://localhost` (apenas de referência, utilize o HTTPS) |
   | Blog App (HTTPS) | `https://localhost` (certificado autoassinado) |
   | phpMyAdmin | `http://localhost/phpmyadmin/` |

   > O banco de dados é populado automaticamente com o schema (`blog_app_SCHEMA.sql`) e os dados iniciais (`blog_app_DATA.sql`) na primeira inicialização.

---

## 🔧 Variáveis de Ambiente

### Aplicação (`config/.env`)

| Variável | Descrição | Exemplo |
|----------|-----------|---------|
| `DB_NAME` | Nome do banco de dados | `blog_app` |
| `DB_USER` | Usuário do banco | `blogApp` |
| `DB_PASSWORD` | Senha do usuário do banco | `sua_senha` |
| `DB_HOST` | Host do banco de dados | `mysql-db` (Docker) / `localhost` (manual) |
| `DB_PORT` | Porta do banco | `3306` |
| `DB_DRIVER` | Driver do Doctrine DBAL | `pdo_mysql` |
| `DB_CHARSET` | Charset do banco | `utf8mb4` |
| `DB_COLLATION` | Collation do banco | `utf8mb4_0900_ai_ci` |

### Docker (`setup/docker-blogapp/.env`)

| Variável | Descrição |
|----------|-----------|
| `DB_ROOT_PASSWORD` | Senha do usuário root do MySQL |
| `DB_PASSWORD` | Senha do usuário da aplicação (`blogApp`) |

---
