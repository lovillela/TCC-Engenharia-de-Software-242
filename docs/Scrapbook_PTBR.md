# Blog CMS Scrapbook

Bem-vindo ao meu Scrapbook.

Este documento detalha como este sistema funciona internamente.

## Índice

- [Visão Geral da Arquitetura](#visão-geral-da-arquitetura)
- [Fluxo de Requisições](#fluxo-de-requisições)
- [Sistema de Autenticação](#sistema-de-autenticação)
- [Regras de Roteamento](#regras-de-roteamento)
- [Convenções do Banco de Dados](#convenções-do-banco-de-dados)
- [Diretrizes de Segurança](#diretrizes-de-segurança)
- [Padrões de Controller](#padrões-de-controller)
- [Organização de Serviços](#organização-de-serviços)
- [Renderização de Views](#renderização-de-views)
- [Convenções de Desenvolvimento](#convenções-de-desenvolvimento)

## Visão Geral da Arquitetura

### Princípios Fundamentais

- Ponto de entrada único para TODAS as requisições
- Padrão "MVC" com camada de Service
- Detecção de permissões baseada em rotas
- Injeção de dependência sempre que possível

### Por Que Essas Decisões Foram Tomadas

Eu navego para onde devo ir.

------------------------------------------------------------

# Como os Scraps Funcionam

Este sistema contém certas funções de backend e server-side (Scraps, neste livro).

Embora este sistema meio que siga o padrão MVC, funções importantes como `RenderView`, `routeMatch`, etc. são **services**.

## Fluxo de Requisições

O fluxo de requisições do usuário acontece de forma simples e direta.

O Apache (`.htaccess`) redirecionará **TODAS** as requisições recebidas para o arquivo `public/index.php`.

Com este ponto de entrada centralizado, o `kernel.php` será carregado.

O `kernel.php` irá:

- Iniciar a sessão
- Carregar o autoload do composer
- Estabelecer a conexão com o banco de dados
- Carregar as definições de rotas para o Front-end e Admins (`config/Routes`)
- Fornecer duas funções RouteHandler() para Front-end e Admins

O `public/index.php` mencionado anteriormente chamará ambas as funções para corresponder às rotas apropriadas.

## Scrap - Serviço de Roteamento

Por baixo dos panos, todas as rotas são correspondidas usando o `routeMatchService.php` **centralizado**

Obviamente, se nem a rota nem o Controller e/ou o método existir, o sistema lançará um `404 Not Found`.

Se o Controller e a função existem, sua classe relacionada será instanciada e sua função chamada.

**xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx**

**Nota: Dependendo da rota, a classe pode ser instanciada usando parâmetros especiais**

**xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx**

## Scrap - Sistema de Autenticação

O processo de autenticação começa com seu roteamento relacionado.

