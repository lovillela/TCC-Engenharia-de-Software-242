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