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