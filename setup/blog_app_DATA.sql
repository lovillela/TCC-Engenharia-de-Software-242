-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: mysql-db:3306
-- Generation Time: Apr 11, 2026 at 04:43 PM
-- Server version: 8.4.8
-- PHP Version: 8.3.30

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `blog_app`
--
USE `blog_app`;
--
-- Truncate table before insert `category`
--

TRUNCATE TABLE `category`;
--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `title`, `description`, `slug`) VALUES
(1, 'Tecnologia', 'Posts sobre tecnologia e programação.', 'tecnologia'),
(2, 'Tutorial', 'Guias passo a passo para desenvolvedores.', 'tutorial'),
(3, 'Arquitetura', 'Padrões e design de software.', 'arquitetura'),
(4, 'Banco de Dados', 'SQL, NoSQL e otimização de queries.', 'banco-de-dados');

--
-- Truncate table before insert `category_tag`
--

TRUNCATE TABLE `category_tag`;
--
-- Dumping data for table `category_tag`
--

INSERT INTO `category_tag` (`id_category`, `id_tag`) VALUES
(1, 1),
(2, 1),
(4, 2),
(1, 3),
(2, 4),
(4, 4),
(3, 6);

--
-- Truncate table before insert `migrations`
--

TRUNCATE TABLE `migrations`;
--
-- Truncate table before insert `post`
--

TRUNCATE TABLE `post`;
--
-- Dumping data for table `post`
--

INSERT INTO `post` (`id`, `title`, `content`, `slug`) VALUES
(1, 'A Evolução do PHP: Do 8.0 ao 8.4', '<p>O PHP não é mais o mesmo de 10 anos atrás. Com a chegada da versão 8.x, a linguagem ganhou recursos de linguagens de alto nível que mudaram o jogo para desenvolvedores backend.</p><ul><li><strong>JIT (Just-In-Time):</strong> Melhoria significativa em tarefas computacionais pesadas.</li><li><strong>Readonly Properties:</strong> Facilitando a criação de DTOs imutáveis.</li><li><strong>Enums:</strong> Finalmente uma forma nativa de lidar com tipos enumerados.</li></ul><p>Neste artigo, exploramos como essas mudanças impactam a arquitetura de sistemas modernos.</p>', 'a-evolucao-do-php-8'),
(2, 'Docker para PHP: O Guia Definitivo', '<p>Containerizar uma aplicação PHP pode parecer simples, mas otimizar a imagem para produção é onde a mágica acontece. Não basta apenas um <code>docker-compose up</code>.</p><p>Discutimos o uso de imagens <em>Alpine Linux</em> para reduzir o tamanho, a separação de responsabilidades entre Nginx e PHP-FPM, e como gerenciar permissões de usuário de forma segura dentro dos containers.</p>', 'docker-php-guia-definitivo'),
(3, 'Dominando Queries Recursivas (CTE) no MySQL', '<p>Você já precisou carregar uma árvore de categorias ou comentários com infinitos níveis? No passado, isso exigia várias queries ou lógica complexa no código.</p><p>As <strong>Common Table Expressions (CTEs)</strong> recursivas do MySQL 8.0 permitem que você resolva hierarquias complexas em uma única viagem ao banco de dados. Vamos analisar a sintaxe <code>WITH RECURSIVE</code> e casos de uso reais.</p>', 'queries-recursivas-mysql-8'),
(4, 'Arquitetura sem Frameworks: Vale a pena?', '<p>Em um mundo dominado por Laravel e Symfony, por que alguém construiria algo em PHP puro? O segredo não é reinventar a roda, mas entender como ela gira.</p><p>Ao implementar o <strong>Repository Pattern</strong> e uma <strong>Service Layer</strong> manualmente, você ganha controle total sobre o acoplamento e a performance da sua aplicação. É um exercício fundamental para todo Engenheiro de Software.</p>', 'arquitetura-php-sem-framework'),
(5, 'Performance: O Poder dos Índices no InnoDB', '<p>Um índice mal planejado pode ser pior do que a ausência de um. No motor InnoDB do MySQL, entender como a <em>Clustered Index</em> funciona é vital.</p><p>Neste post, comparamos índices B-Tree com índices de busca textual (Full-Text) e mostramos como usar o <code>EXPLAIN</code> para identificar gargalos em consultas que pareciam inofensivas.</p>', 'performance-indices-innodb');

--
-- Truncate table before insert `post_category`
--

TRUNCATE TABLE `post_category`;
--
-- Dumping data for table `post_category`
--

INSERT INTO `post_category` (`id_post`, `id_category`) VALUES
(1, 1),
(2, 1),
(1, 2),
(2, 2),
(4, 3),
(3, 4),
(5, 4);

--
-- Truncate table before insert `post_tag`
--

TRUNCATE TABLE `post_tag`;
--
-- Dumping data for table `post_tag`
--

INSERT INTO `post_tag` (`id_post`, `id_tag`) VALUES
(1, 1),
(2, 1),
(4, 1),
(3, 2),
(5, 2),
(2, 3),
(3, 4),
(5, 4),
(4, 5),
(4, 6);

--
-- Truncate table before insert `post_users`
--

TRUNCATE TABLE `post_users`;
--
-- Dumping data for table `post_users`
--

INSERT INTO `post_users` (`id_user`, `id_post`) VALUES
(2, 1),
(4, 2),
(1, 3),
(3, 4),
(3, 5),
(6, 6),
(6, 7);

--
-- Truncate table before insert `slug_map`
--

TRUNCATE TABLE `slug_map`;
--
-- Dumping data for table `slug_map`
--

INSERT INTO `slug_map` (`entity_id`, `entity_type`, `slug`) VALUES
(1, 'post', 'introducao-ao-php-moderno'),
(2, 'post', 'docker-para-desenvolvedores-php'),
(3, 'post', 'queries-recursivas-no-mysql'),
(4, 'post', 'padroes-arquitetura-apis-rest'),
(5, 'post', 'otimizacao-indices-innodb'),
(6, 'post', 'padres-de-arquitetura-para-apis-rest'),
(7, 'post', 'teste'),
(1, 'category', 'tecnologia'),
(2, 'category', 'tutorial'),
(3, 'category', 'arquitetura'),
(4, 'category', 'banco-de-dados'),
(1, 'tag', 'php'),
(2, 'tag', 'mysql'),
(3, 'tag', 'docker'),
(4, 'tag', 'sql'),
(5, 'tag', 'rest-api'),
(6, 'tag', 'clean-code');

--
-- Truncate table before insert `tag`
--

TRUNCATE TABLE `tag`;
--
-- Dumping data for table `tag`
--

INSERT INTO `tag` (`id`, `title`, `slug`) VALUES
(1, 'PHP', 'php'),
(2, 'MySQL', 'mysql'),
(3, 'Docker', 'docker'),
(4, 'SQL', 'sql'),
(5, 'REST API', 'rest-api'),
(6, 'Clean Code', 'clean-code');

--
-- Truncate table before insert `users`
--

TRUNCATE TABLE `users`;
--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `permissions`, `password`, `isActive`) VALUES
(1, 'joao_silva', 'joao@email.com', 3, '$2y$12$xEcLa7drozS.W15N5qC9terhMKyNjPmQo.8vFPLOChNTBB145iiUi', 1),
(2, 'maria_souza', 'maria@email.com', 3, '$2y$12$xEcLa7drozS.W15N5qC9terhMKyNjPmQo.8vFPLOChNTBB145iiUi', 1),
(3, 'carlos_dev', 'carlos@email.com', 3, '$2y$12$xEcLa7drozS.W15N5qC9terhMKyNjPmQo.8vFPLOChNTBB145iiUi', 1),
(4, 'ana_lima', 'ana@email.com', 3, '$2y$12$xEcLa7drozS.W15N5qC9terhMKyNjPmQo.8vFPLOChNTBB145iiUi', 1),
(5, 'admin_user', 'admin@email.com', 1, '$2y$12$xEcLa7drozS.W15N5qC9terhMKyNjPmQo.8vFPLOChNTBB145iiUi', 1),
(6, 'teste2', 'teste2@email.com', 3, '$2y$12$gmrlghc3ycyQ.v1RGbfWfeq6TJPV77gXmBanINf5Z8SpfajbbhFce', 1),
(7, 'teste1', 'teste1@email.com', 3, '$2y$12$iXkF8Hi8X0UGfwlZr710.eHtZTcAJlOSGnykC8oQWKefeYbn/0mji', 1);

--
-- Truncate table before insert `user_comment_post`
--

TRUNCATE TABLE `user_comment_post`;
--
-- Dumping data for table `user_comment_post`
--

-- Atualização da tabela user_comment_post com discussões simuladas
INSERT INTO `user_comment_post` (`id`, `id_user`, `id_post`, `content`, `parent`, `created_at`, `is_visible`) VALUES
(4, 1, 3, 'Excelente explicação! As CTEs salvaram a performance do meu sistema de organograma.', NULL, '2026-01-12 14:00:00', 1),
(9, 2, 3, 'João, você notou alguma diferença de performance entre CTE e o Nested Set Model para árvores muito grandes?', 4, '2026-01-12 15:00:00', 1),
(13, 3, 3, 'Maria, para leitura simples a CTE costuma ser mais rápida no MySQL 8, mas o Nested Set ainda vence se você tiver milhares de níveis e poucas escritas.', 9, '2026-01-12 16:00:00', 1),
(5, 2, 4, 'Acho essa abordagem de PHP puro sensacional para aprendizado. Como você recomenda lidar com a Injeção de Dependência sem um container automatizado?', NULL, '2026-01-13 08:30:00', 1),
(10, 4, 4, 'Maria, eu costumo usar uma classe Kernel simples que instancia os serviços no início do ciclo da requisição. Resolve 90% dos casos!', 5, '2026-01-13 09:00:00', 1),
(14, 5, 4, 'Exato, Ana. Menos "magia" de frameworks facilita muito o debug e os testes unitários.', 10, '2026-01-13 10:00:00', 1),
(33, 6, 1, 'Os Enums foram a melhor adição do PHP 8.1. Adeus constantes espalhadas pelo código!', NULL, '2026-03-28 16:05:31', 1),
(34, 4, 1, 'Concordo plenamente. Já começou a usar os Fibers também para concorrência?', 33, '2026-03-28 16:05:36', 1);

--
-- Truncate table before insert `user_reaction_post`
--

TRUNCATE TABLE `user_reaction_post`;
--
-- Dumping data for table `user_reaction_post`
--

INSERT INTO `user_reaction_post` (`id_user`, `id_post`, `reaction`) VALUES
(1, 1, 1),
(1, 3, 1),
(1, 4, 0),
(2, 1, 1),
(2, 2, 1),
(3, 3, 1),
(3, 4, 1),
(4, 2, 1),
(4, 5, 1),
(5, 1, 1);
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
