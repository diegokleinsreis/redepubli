-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 06/11/2025 às 08:55
-- Versão do servidor: 10.6.23-MariaDB
-- Versão do PHP: 8.4.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `klscom_social`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `Amizades`
--

CREATE TABLE `Amizades` (
  `id` int(11) NOT NULL,
  `usuario_um_id` int(11) NOT NULL COMMENT 'ID do usuário que enviou o pedido',
  `usuario_dois_id` int(11) NOT NULL COMMENT 'ID do usuário que recebeu o pedido',
  `status` enum('pendente','aceite','recusado','bloqueado') NOT NULL DEFAULT 'pendente',
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `Amizades`
--

INSERT INTO `Amizades` (`id`, `usuario_um_id`, `usuario_dois_id`, `status`, `data_criacao`, `data_atualizacao`) VALUES
(8, 5, 9, 'pendente', '2025-10-15 19:04:00', '2025-10-15 19:04:00'),
(9, 5, 4, 'pendente', '2025-10-15 19:04:12', '2025-10-15 19:04:12'),
(13, 12, 5, 'aceite', '2025-10-16 16:05:52', '2025-10-16 16:05:59');

-- --------------------------------------------------------

--
-- Estrutura para tabela `Bairros`
--

CREATE TABLE `Bairros` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `id_cidade` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `Bairros`
--

INSERT INTO `Bairros` (`id`, `nome`, `id_cidade`) VALUES
(1, 'Espinheiros', 129),
(2, 'Santa Regina', 129),
(3, 'Itaipava', 129),
(4, 'Independência', 129),
(5, 'Loteamento São Francisco de Assis', 129),
(6, 'Quilômetro 12', 129),
(7, 'Arraial dos Cunhas', 129),
(8, 'Salseiros', 129),
(9, 'Espinheirinhos', 129),
(10, 'Campeche', 129),
(11, 'Limoeiro', 129),
(12, 'São Roque', 129),
(13, 'Colônia Japonesa', 129),
(14, 'Brilhante', 129),
(15, 'Cordeiros', 129),
(16, 'Murta', 129),
(17, 'São Judas', 129),
(18, 'Barra do Rio', 129),
(19, 'Vila Operária', 129),
(20, 'Dom Bosco', 129),
(21, 'Praia Brava', 129),
(22, 'Centro', 129),
(23, 'São João', 129),
(24, 'São Vicente', 129),
(25, 'Ressacada', 129),
(26, 'Fazenda', 129),
(27, 'Cabeçudas', 129);

-- --------------------------------------------------------

--
-- Estrutura para tabela `Cidades`
--

CREATE TABLE `Cidades` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `id_estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `Cidades`
--

INSERT INTO `Cidades` (`id`, `nome`, `id_estado`) VALUES
(1, 'Teste', 24),
(2, 'Abdon Batista', 24),
(3, 'Abelardo Luz', 24),
(4, 'Agrolândia', 24),
(5, 'Agronômica', 24),
(6, 'Água Doce', 24),
(7, 'Águas de Chapecó', 24),
(8, 'Águas Frias', 24),
(9, 'Águas Mornas', 24),
(10, 'Alfredo Wagner', 24),
(11, 'Alto Bela Vista', 24),
(12, 'Anchieta', 24),
(13, 'Angelina', 24),
(14, 'Anita Garibaldi', 24),
(15, 'Anitápolis', 24),
(16, 'Antônio Carlos', 24),
(17, 'Apiúna', 24),
(18, 'Arabutã', 24),
(19, 'Araquari', 24),
(20, 'Araranguá', 24),
(21, 'Armazém', 24),
(22, 'Arroio Trinta', 24),
(23, 'Arvoredo', 24),
(24, 'Ascurra', 24),
(25, 'Atalanta', 24),
(26, 'Aurora', 24),
(27, 'Balneário Arroio do Silva', 24),
(28, 'Balneário Barra do Sul', 24),
(29, 'Balneário Camboriú', 24),
(30, 'Balneário Gaivota', 24),
(31, 'Balneário Piçarras', 24),
(32, 'Balneário Rincão', 24),
(33, 'Bandeirante', 24),
(34, 'Barra Bonita', 24),
(35, 'Barra Velha', 24),
(36, 'Bela Vista do Toldo', 24),
(37, 'Belmonte', 24),
(38, 'Benedito Novo', 24),
(39, 'Biguaçu', 24),
(40, 'Blumenau', 24),
(41, 'Bocaina do Sul', 24),
(42, 'Bom Jardim da Serra', 24),
(43, 'Bom Jesus', 24),
(44, 'Bom Jesus do Oeste', 24),
(45, 'Bom Retiro', 24),
(46, 'Bombinhas', 24),
(47, 'Botuverá', 24),
(48, 'Braço do Norte', 24),
(49, 'Braço do Trombudo', 24),
(50, 'Brunópolis', 24),
(51, 'Brusque', 24),
(52, 'Caçador', 24),
(53, 'Caibi', 24),
(54, 'Calmon', 24),
(55, 'Camboriú', 24),
(56, 'Campo Alegre', 24),
(57, 'Campo Belo do Sul', 24),
(58, 'Campo Erê', 24),
(59, 'Campos Novos', 24),
(60, 'Canelinha', 24),
(61, 'Canoinhas', 24),
(62, 'Capão Alto', 24),
(63, 'Capinzal', 24),
(64, 'Capivari de Baixo', 24),
(65, 'Catanduvas', 24),
(66, 'Caxambu do Sul', 24),
(67, 'Celso Ramos', 24),
(68, 'Cerro Negro', 24),
(69, 'Chapadão do Lageado', 24),
(70, 'Chapecó', 24),
(71, 'Cocal do Sul', 24),
(72, 'Concórdia', 24),
(73, 'Cordilheira Alta', 24),
(74, 'Coronel Freitas', 24),
(75, 'Coronel Martins', 24),
(76, 'Correia Pinto', 24),
(77, 'Corupá', 24),
(78, 'Criciúma', 24),
(79, 'Cunha Porã', 24),
(80, 'Cunhataí', 24),
(81, 'Curitibanos', 24),
(82, 'Descanso', 24),
(83, 'Dionísio Cerqueira', 24),
(84, 'Dona Emma', 24),
(85, 'Doutor Pedrinho', 24),
(86, 'Entre Rios', 24),
(87, 'Ermo', 24),
(88, 'Erval Velho', 24),
(89, 'Faxinal dos Guedes', 24),
(90, 'Flor do Sertão', 24),
(91, 'Florianópolis', 24),
(92, 'Formosa do Sul', 24),
(93, 'Forquilhinha', 24),
(94, 'Fraiburgo', 24),
(95, 'Frei Rogério', 24),
(96, 'Galvão', 24),
(97, 'Garopaba', 24),
(98, 'Garuva', 24),
(99, 'Gaspar', 24),
(100, 'Governador Celso Ramos', 24),
(101, 'Grão-Pará', 24),
(102, 'Gravatal', 24),
(103, 'Guabiruba', 24),
(104, 'Guaraciaba', 24),
(105, 'Guaramirim', 24),
(106, 'Guarujá do Sul', 24),
(107, 'Guatambú', 24),
(108, 'Herval d\'Oeste', 24),
(109, 'Ibiam', 24),
(110, 'Ibicaré', 24),
(111, 'Ibirama', 24),
(112, 'Içara', 24),
(113, 'Ilhota', 24),
(114, 'Imaruí', 24),
(115, 'Imbituba', 24),
(116, 'Imbuia', 24),
(117, 'Indaial', 24),
(118, 'Iomerê', 24),
(119, 'Ipira', 24),
(120, 'Iporã do Oeste', 24),
(121, 'Ipuaçu', 24),
(122, 'Ipumirim', 24),
(123, 'Iraceminha', 24),
(124, 'Irani', 24),
(125, 'Irati', 24),
(126, 'Irineópolis', 24),
(127, 'Itá', 24),
(128, 'Itaiópolis', 24),
(129, 'Itajaí', 24),
(130, 'Itapema', 24),
(131, 'Itapiranga', 24),
(132, 'Itapoá', 24),
(133, 'Ituporanga', 24),
(134, 'Jaborá', 24),
(135, 'Jacinto Machado', 24),
(136, 'Jaguaruna', 24),
(137, 'Jaraguá do Sul', 24),
(138, 'Jardinópolis', 24),
(139, 'Joaçaba', 24),
(140, 'Joinville', 24),
(141, 'José Boiteux', 24),
(142, 'Jupiá', 24),
(143, 'Lacerdópolis', 24),
(144, 'Lages', 24),
(145, 'Laguna', 24),
(146, 'Lajeado Grande', 24),
(147, 'Laurentino', 24),
(148, 'Lauro Müller', 24),
(149, 'Lebon Régis', 24),
(150, 'Leoberto Leal', 24),
(151, 'Lindóia do Sul', 24),
(152, 'Lontras', 24),
(153, 'Luiz Alves', 24),
(154, 'Luzerna', 24),
(155, 'Macieira', 24),
(156, 'Mafra', 24),
(157, 'Major Gercino', 24),
(158, 'Major Vieira', 24),
(159, 'Maracajá', 24),
(160, 'Maravilha', 24),
(161, 'Marema', 24),
(162, 'Massaranduba', 24),
(163, 'Matos Costa', 24),
(164, 'Meleiro', 24),
(165, 'Mirim Doce', 24),
(166, 'Modelo', 24),
(167, 'Mondaí', 24),
(168, 'Monte Carlo', 24),
(169, 'Monte Castelo', 24),
(170, 'Morro da Fumaça', 24),
(171, 'Morro Grande', 24),
(172, 'Navegantes', 24),
(173, 'Nova Erechim', 24),
(174, 'Nova Itaberaba', 24),
(175, 'Nova Trento', 24),
(176, 'Nova Veneza', 24),
(177, 'Novo Horizonte', 24),
(178, 'Orleans', 24),
(179, 'Otacílio Costa', 24),
(180, 'Ouro', 24),
(181, 'Ouro Verde', 24),
(182, 'Paial', 24),
(183, 'Painel', 24),
(184, 'Palhoça', 24),
(185, 'Palma Sola', 24),
(186, 'Palmeira', 24),
(187, 'Palmitos', 24),
(188, 'Papanduva', 24),
(189, 'Paraíso', 24),
(190, 'Passo de Torres', 24),
(191, 'Passos Maia', 24),
(192, 'Paulo Lopes', 24),
(193, 'Pedras Grandes', 24),
(194, 'Penha', 24),
(195, 'Peritiba', 24),
(196, 'Pescaria Brava', 24),
(197, 'Petrolândia', 24),
(198, 'Pinhalzinho', 24),
(199, 'Pinheiro Preto', 24),
(200, 'Piratuba', 24),
(201, 'Planalto Alegre', 24),
(202, 'Pomerode', 24),
(203, 'Ponte Alta', 24),
(204, 'Ponte Alta do Norte', 24),
(205, 'Ponte Serrada', 24),
(206, 'Porto Belo', 24),
(207, 'Porto União', 24),
(208, 'Pouso Redondo', 24),
(209, 'Praia Grande', 24),
(210, 'Presidente Castello Branco', 24),
(211, 'Presidente Getúlio', 24),
(212, 'Presidente Nereu', 24),
(213, 'Princesa', 24),
(214, 'Quilombo', 24),
(215, 'Rancho Queimado', 24),
(216, 'Rio das Antas', 24),
(217, 'Rio do Campo', 24),
(218, 'Rio do Oeste', 24),
(219, 'Rio do Sul', 24),
(220, 'Rio dos Cedros', 24),
(221, 'Rio Fortuna', 24),
(222, 'Rio Negrinho', 24),
(223, 'Rio Rufino', 24),
(224, 'Riqueza', 24),
(225, 'Rodeio', 24),
(226, 'Romelândia', 24),
(227, 'Salete', 24),
(228, 'Saltinho', 24),
(229, 'Salto Veloso', 24),
(230, 'Sangão', 24),
(231, 'Santa Cecília', 24),
(232, 'Santa Helena', 24),
(233, 'Santa Rosa de Lima', 24),
(234, 'Santa Rosa do Sul', 24),
(235, 'Santa Terezinha', 24),
(236, 'Santa Terezinha do Progresso', 24),
(237, 'Santiago do Sul', 24),
(238, 'Santo Amaro da Imperatriz', 24),
(239, 'São Bento do Sul', 24),
(240, 'São Bernardino', 24),
(241, 'São Bonifácio', 24),
(242, 'São Carlos', 24),
(243, 'São Cristóvão do Sul', 24),
(244, 'São Domingos', 24),
(245, 'São Francisco do Sul', 24),
(246, 'São João Batista', 24),
(247, 'São João do Itaperiú', 24),
(248, 'São João do Oeste', 24),
(249, 'São João do Sul', 24),
(250, 'São Joaquim', 24),
(251, 'São José', 24),
(252, 'São José do Cedro', 24),
(253, 'São José do Cerrito', 24),
(254, 'São Lourenço do Oeste', 24),
(255, 'São Ludgero', 24),
(256, 'São Martinho', 24),
(257, 'São Miguel da Boa Vista', 24),
(258, 'São Miguel do Oeste', 24),
(259, 'São Pedro de Alcântara', 24),
(260, 'Saudades', 24),
(261, 'Schroeder', 24),
(262, 'Seara', 24),
(263, 'Serra Alta', 24),
(264, 'Siderópolis', 24),
(265, 'Sombrio', 24),
(266, 'Sul Brasil', 24),
(267, 'Taió', 24),
(268, 'Tangará', 24),
(269, 'Tigrinhos', 24),
(270, 'Tijucas', 24),
(271, 'Timbé do Sul', 24),
(272, 'Timbó', 24),
(273, 'Timbó Grande', 24),
(274, 'Três Barras', 24),
(275, 'Treviso', 24),
(276, 'Treze de Maio', 24),
(277, 'Treze Tílias', 24),
(278, 'Trombudo Central', 24),
(279, 'Tubarão', 24),
(280, 'Tunápolis', 24),
(281, 'Turvo', 24),
(282, 'União do Oeste', 24),
(283, 'Urubici', 24),
(284, 'Urupema', 24),
(285, 'Urussanga', 24),
(286, 'Vargeão', 24),
(287, 'Vargem', 24),
(288, 'Vargem Bonita', 24),
(289, 'Vidal Ramos', 24),
(290, 'Videira', 24),
(291, 'Vitor Meireles', 24),
(292, 'Witmarsum', 24),
(293, 'Xanxerê', 24),
(294, 'Xavantina', 24),
(295, 'Xaxim', 24),
(296, 'Zortéa', 24);

-- --------------------------------------------------------

--
-- Estrutura para tabela `Comentarios`
--

CREATE TABLE `Comentarios` (
  `id` int(11) NOT NULL,
  `id_postagem` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_comentario_pai` int(11) DEFAULT NULL,
  `conteudo_texto` text NOT NULL,
  `status` enum('ativo','inativo','excluido_pelo_usuario') NOT NULL DEFAULT 'ativo',
  `data_comentario` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `Comentarios`
--

INSERT INTO `Comentarios` (`id`, `id_postagem`, `id_usuario`, `id_comentario_pai`, `conteudo_texto`, `status`, `data_comentario`) VALUES
(1, 3, 9, NULL, 'Olá', 'ativo', '2025-10-03 20:57:18'),
(2, 3, 9, 1, 'ola', 'ativo', '2025-10-03 21:39:21'),
(3, 3, 9, NULL, 'eae', 'ativo', '2025-10-03 21:39:32'),
(4, 3, 5, 1, 'opa', 'excluido_pelo_usuario', '2025-10-03 21:39:46'),
(5, 3, 5, NULL, 'boaa meu povo', 'ativo', '2025-10-03 21:40:28'),
(6, 2, 5, NULL, 'opa', 'ativo', '2025-10-06 15:16:45'),
(7, 3, 5, 1, 'boa tarde tudo bem?', 'ativo', '2025-10-06 15:55:03'),
(8, 3, 5, NULL, 'Opa', 'ativo', '2025-10-08 19:09:49'),
(9, 3, 9, NULL, 'legal', 'ativo', '2025-10-10 16:53:13'),
(10, 5, 12, NULL, 'Q legal', 'ativo', '2025-10-13 01:44:39'),
(11, 3, 12, NULL, 'Tudooo', 'ativo', '2025-10-13 01:45:39'),
(12, 2, 12, NULL, 'Opa', 'ativo', '2025-10-13 01:45:51'),
(13, 7, 5, NULL, 'ola', 'ativo', '2025-10-14 14:58:59'),
(14, 7, 5, 13, 'TESTE', 'ativo', '2025-10-15 14:13:57'),
(15, 8, 5, NULL, 'teste', 'ativo', '2025-10-15 14:19:10'),
(16, 10, 5, NULL, 'Teste', 'ativo', '2025-10-16 23:03:04'),
(17, 12, 5, NULL, 'Teste', 'ativo', '2025-10-16 23:03:26'),
(18, 10, 5, NULL, 'Aa', 'ativo', '2025-10-17 03:18:06'),
(19, 10, 5, NULL, 'Dddd', 'ativo', '2025-10-17 03:18:11'),
(20, 10, 5, 19, 'Serd', 'ativo', '2025-10-17 03:18:14'),
(21, 10, 5, NULL, 'Hehejdjd', 'ativo', '2025-10-17 03:18:19'),
(22, 10, 5, NULL, 'Ysydududu', 'ativo', '2025-10-17 03:18:23'),
(23, 10, 5, NULL, 'Uxhdhdjrjd', 'ativo', '2025-10-17 03:18:26'),
(24, 10, 5, NULL, 'teste', 'ativo', '2025-10-17 14:27:45'),
(25, 10, 5, NULL, 'teste', 'ativo', '2025-10-17 14:37:49'),
(26, 10, 5, NULL, 'teste 1133', 'ativo', '2025-10-17 14:37:58'),
(27, 10, 5, NULL, 'teste 1150', 'ativo', '2025-10-17 14:55:19'),
(28, 10, 5, NULL, 'teste 1154', 'ativo', '2025-10-17 14:59:23'),
(29, 10, 5, NULL, 'teste 12', 'ativo', '2025-10-17 15:05:02'),
(30, 13, 5, NULL, 'teste', 'ativo', '2025-10-17 15:35:41'),
(31, 13, 5, NULL, 'teste', 'ativo', '2025-10-17 15:45:37'),
(32, 13, 5, NULL, 'tessadasa', 'ativo', '2025-10-17 15:45:41'),
(33, 13, 5, NULL, 'teetasf', 'ativo', '2025-10-17 15:45:43'),
(34, 13, 5, NULL, 'teste 1252', 'ativo', '2025-10-17 15:57:27'),
(35, 13, 5, NULL, 'teste', 'ativo', '2025-10-17 16:40:40'),
(36, 13, 5, NULL, 'AGAGDAD', 'ativo', '2025-10-17 16:52:07'),
(37, 14, 5, NULL, 'TESTE', 'ativo', '2025-10-17 16:53:29'),
(38, 14, 5, NULL, 'tesssss', 'ativo', '2025-10-17 17:09:09'),
(39, 13, 5, NULL, 'tesssteetet', 'ativo', '2025-10-17 17:09:19'),
(40, 13, 5, NULL, 'Teste', 'ativo', '2025-10-17 18:51:10'),
(41, 13, 5, 30, 'ollaaaa', 'ativo', '2025-10-17 20:29:51'),
(42, 13, 5, 30, 'teeetet', 'ativo', '2025-10-18 21:51:32'),
(43, 15, 5, NULL, 'teste', 'ativo', '2025-10-31 20:58:15'),
(44, 13, 5, 30, 'Teste', 'ativo', '2025-11-01 18:54:36'),
(45, 13, 5, 30, 'Teste', 'ativo', '2025-11-01 18:54:43');

-- --------------------------------------------------------

--
-- Estrutura para tabela `Comentarios_Edicoes`
--

CREATE TABLE `Comentarios_Edicoes` (
  `id` int(11) NOT NULL,
  `id_comentario` int(11) NOT NULL,
  `conteudo_antigo` text NOT NULL,
  `data_edicao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `Comentarios_Edicoes`
--

INSERT INTO `Comentarios_Edicoes` (`id`, `id_comentario`, `conteudo_antigo`, `data_edicao`) VALUES
(1, 7, 'boa tarde', '2025-10-06 17:59:30'),
(2, 5, 'boaa', '2025-10-06 18:00:03');

-- --------------------------------------------------------

--
-- Estrutura para tabela `Configuracoes`
--

CREATE TABLE `Configuracoes` (
  `id` int(11) NOT NULL,
  `chave` varchar(100) NOT NULL,
  `valor` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `Configuracoes`
--

INSERT INTO `Configuracoes` (`id`, `chave`, `valor`) VALUES
(1, 'site_nome', 'Social SC'),
(2, 'site_descricao', 'Sua rede social hiperlocal focada em conectar vizinhos.'),
(3, 'site_url', 'https://seusite.com.br'),
(4, 'email_contato', 'contato@seusite.com.br'),
(5, 'url_logo_header', 'assets/images/logo.png'),
(6, 'url_favicon', 'assets/images/favicon.png'),
(7, 'cor_tema_primaria', '#0c2d54'),
(8, 'modo_manutencao', '0'),
(9, 'permite_cadastro', '1'),
(10, 'modo_dev', '1'),
(11, 'versao_assets', '1.0.0');

-- --------------------------------------------------------

--
-- Estrutura para tabela `Curtidas`
--

CREATE TABLE `Curtidas` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_postagem` int(11) NOT NULL,
  `data_curtida` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `Curtidas`
--

INSERT INTO `Curtidas` (`id`, `id_usuario`, `id_postagem`, `data_curtida`) VALUES
(1, 4, 2, '2025-10-01 17:29:01'),
(2, 5, 3, '2025-10-02 16:08:42'),
(3, 5, 2, '2025-10-03 20:56:42'),
(7, 5, 6, '2025-10-12 23:47:42'),
(8, 9, 3, '2025-10-12 23:57:38'),
(9, 9, 5, '2025-10-12 23:58:06'),
(11, 12, 6, '2025-10-13 01:45:30'),
(13, 12, 2, '2025-10-13 01:45:34'),
(24, 5, 7, '2025-10-14 22:56:16'),
(25, 12, 5, '2025-10-14 22:57:23'),
(26, 12, 3, '2025-10-14 22:57:25'),
(27, 5, 8, '2025-10-14 23:25:44'),
(28, 5, 10, '2025-10-17 14:33:30'),
(41, 5, 14, '2025-10-17 17:09:06'),
(42, 5, 13, '2025-10-17 17:09:24'),
(43, 5, 15, '2025-10-31 16:40:40');

-- --------------------------------------------------------

--
-- Estrutura para tabela `Curtidas_Comentarios`
--

CREATE TABLE `Curtidas_Comentarios` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_comentario` int(11) NOT NULL,
  `data_curtida` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `Curtidas_Comentarios`
--

INSERT INTO `Curtidas_Comentarios` (`id`, `id_usuario`, `id_comentario`, `data_curtida`) VALUES
(2, 5, 5, '2025-10-09 00:06:06'),
(3, 5, 1, '2025-10-09 01:21:09'),
(10, 12, 13, '2025-10-14 18:17:03'),
(11, 12, 6, '2025-10-14 22:57:32'),
(12, 5, 10, '2025-10-14 22:58:12'),
(13, 5, 16, '2025-10-17 14:37:43'),
(22, 5, 30, '2025-10-17 15:57:17'),
(25, 5, 41, '2025-11-02 21:22:10');

-- --------------------------------------------------------

--
-- Estrutura para tabela `Denuncias`
--

CREATE TABLE `Denuncias` (
  `id` int(11) NOT NULL,
  `id_usuario_denunciou` int(11) NOT NULL,
  `tipo_conteudo` enum('post','comentario','usuario') NOT NULL,
  `id_conteudo` int(11) NOT NULL,
  `motivo` text NOT NULL,
  `data_denuncia` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pendente','revisado','ignorado','excluida_pelo_adm') NOT NULL DEFAULT 'pendente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `Denuncias`
--

INSERT INTO `Denuncias` (`id`, `id_usuario_denunciou`, `tipo_conteudo`, `id_conteudo`, `motivo`, `data_denuncia`, `status`) VALUES
(1, 5, 'post', 7, 'Bullying, assédio ou abuso', '2025-10-14 21:38:31', 'revisado'),
(2, 12, 'post', 10, 'Conteúdo violento, que promove o ódio ou é perturbador', '2025-10-16 20:56:16', 'revisado'),
(3, 12, 'post', 14, 'Bullying, assédio ou abuso', '2025-10-17 17:34:31', 'pendente'),
(4, 12, 'post', 13, 'Golpe, fraude ou informação falsa', '2025-10-17 17:34:58', 'pendente'),
(5, 12, 'post', 14, 'Bullying, assédio ou abuso', '2025-10-17 17:40:48', 'pendente'),
(6, 12, 'post', 13, 'Golpe, fraude ou informação falsa', '2025-10-17 17:40:54', 'pendente'),
(7, 12, 'post', 13, 'Bullying, assédio ou abuso', '2025-10-17 17:40:58', 'revisado'),
(8, 12, 'post', 14, 'Spam', '2025-10-17 17:41:06', 'revisado'),
(9, 5, 'usuario', 12, 'Perfil Falso', '2025-10-17 18:17:01', 'revisado'),
(10, 12, 'post', 15, 'Spam', '2025-10-18 18:12:38', 'revisado'),
(11, 5, 'usuario', 12, 'Perfil Falso', '2025-10-31 23:25:55', 'pendente');

-- --------------------------------------------------------

--
-- Estrutura para tabela `Estados`
--

CREATE TABLE `Estados` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `sigla` varchar(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `Estados`
--

INSERT INTO `Estados` (`id`, `nome`, `sigla`) VALUES
(1, 'Acre', 'AC'),
(2, 'Alagoas', 'AL'),
(3, 'Amapá', 'AP'),
(4, 'Amazonas', 'AM'),
(5, 'Bahia', 'BA'),
(6, 'Ceará', 'CE'),
(7, 'Distrito Federal', 'DF'),
(8, 'Espírito Santo', 'ES'),
(9, 'Goiás', 'GO'),
(10, 'Maranhão', 'MA'),
(11, 'Mato Grosso', 'MT'),
(12, 'Mato Grosso do Sul', 'MS'),
(13, 'Minas Gerais', 'MG'),
(14, 'Pará', 'PA'),
(15, 'Paraíba', 'PB'),
(16, 'Paraná', 'PR'),
(17, 'Pernambuco', 'PE'),
(18, 'Piauí', 'PI'),
(19, 'Rio de Janeiro', 'RJ'),
(20, 'Rio Grande do Norte', 'RN'),
(21, 'Rio Grande do Sul', 'RS'),
(22, 'Rondônia', 'RO'),
(23, 'Roraima', 'RR'),
(24, 'Santa Catarina', 'SC'),
(25, 'São Paulo', 'SP'),
(26, 'Sergipe', 'SE'),
(27, 'Tocantins', 'TO');

-- --------------------------------------------------------

--
-- Estrutura para tabela `Logs_Login`
--

CREATE TABLE `Logs_Login` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `data_login` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip_usuario` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `Logs_Login`
--

INSERT INTO `Logs_Login` (`id`, `id_usuario`, `data_login`, `ip_usuario`) VALUES
(1, 13, '2025-11-02 17:10:46', '191.187.237.97'),
(2, 5, '2025-11-02 19:46:06', '189.35.9.14'),
(3, 5, '2025-11-02 20:12:25', '189.35.9.14'),
(4, 5, '2025-11-02 20:12:32', '189.35.9.14'),
(5, 5, '2025-11-02 20:13:12', '189.35.9.14'),
(6, 5, '2025-11-02 20:14:13', '189.35.9.14'),
(7, 5, '2025-11-02 20:28:23', '189.35.9.14'),
(8, 5, '2025-11-02 20:55:19', '191.187.237.97'),
(9, 5, '2025-11-03 00:05:01', '189.35.9.14'),
(10, 5, '2025-11-03 00:26:30', '189.35.9.14'),
(11, 5, '2025-11-03 00:46:15', '189.35.9.14'),
(12, 5, '2025-11-03 01:26:15', '189.35.9.14'),
(13, 5, '2025-11-04 01:21:11', '189.35.9.14'),
(14, 5, '2025-11-04 01:31:45', '189.35.9.14'),
(15, 5, '2025-11-04 01:34:37', '189.35.9.14'),
(16, 5, '2025-11-04 01:51:37', '189.35.9.14');

-- --------------------------------------------------------

--
-- Estrutura para tabela `Logs_Visualizacao_Post`
--

CREATE TABLE `Logs_Visualizacao_Post` (
  `id` int(11) NOT NULL,
  `id_postagem` int(11) NOT NULL,
  `id_usuario_visualizou` int(11) DEFAULT NULL,
  `data_visualizacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `Logs_Visualizacao_Post`
--

INSERT INTO `Logs_Visualizacao_Post` (`id`, `id_postagem`, `id_usuario_visualizou`, `data_visualizacao`) VALUES
(1, 15, 5, '2025-11-02 18:03:23'),
(2, 15, 5, '2025-11-02 18:03:26'),
(3, 13, 5, '2025-11-02 21:22:04'),
(4, 13, 5, '2025-11-02 21:48:19'),
(5, 13, 5, '2025-11-02 22:00:04'),
(6, 13, 5, '2025-11-02 22:04:37'),
(7, 8, 5, '2025-11-03 00:05:40'),
(8, 13, 5, '2025-11-03 00:47:56');

-- --------------------------------------------------------

--
-- Estrutura para tabela `notificacoes`
--

CREATE TABLE `notificacoes` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `remetente_id` int(11) NOT NULL,
  `tipo` enum('curtida_post','comentario_post','curtida_comentario','pedido_amizade') NOT NULL,
  `id_referencia` int(11) NOT NULL,
  `lida` tinyint(1) NOT NULL DEFAULT 0,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `notificacoes`
--

INSERT INTO `notificacoes` (`id`, `usuario_id`, `remetente_id`, `tipo`, `id_referencia`, `lida`, `data_criacao`) VALUES
(1, 5, 9, 'curtida_post', 3, 1, '2025-10-12 23:01:54'),
(2, 5, 9, 'curtida_post', 5, 1, '2025-10-12 23:02:23'),
(3, 9, 5, 'curtida_post', 6, 1, '2025-10-12 23:47:42'),
(4, 5, 9, 'curtida_post', 3, 1, '2025-10-12 23:57:38'),
(5, 5, 9, 'curtida_post', 5, 1, '2025-10-12 23:58:06'),
(6, 5, 12, 'curtida_post', 5, 1, '2025-10-13 01:44:35'),
(7, 5, 12, 'comentario_post', 5, 1, '2025-10-13 01:44:39'),
(8, 9, 12, 'curtida_post', 6, 0, '2025-10-13 01:45:30'),
(9, 5, 12, 'curtida_post', 3, 1, '2025-10-13 01:45:33'),
(10, 4, 12, 'curtida_post', 2, 0, '2025-10-13 01:45:34'),
(11, 5, 12, 'comentario_post', 3, 1, '2025-10-13 01:45:39'),
(12, 4, 12, 'comentario_post', 2, 0, '2025-10-13 01:45:51'),
(13, 12, 5, 'curtida_post', 7, 1, '2025-10-13 01:46:51'),
(14, 12, 5, 'curtida_post', 7, 1, '2025-10-14 14:24:38'),
(15, 12, 5, 'curtida_post', 7, 1, '2025-10-14 14:24:50'),
(16, 12, 5, 'curtida_post', 7, 1, '2025-10-14 14:29:23'),
(17, 12, 5, 'curtida_post', 7, 1, '2025-10-14 14:32:43'),
(18, 12, 5, 'curtida_post', 7, 1, '2025-10-14 14:58:38'),
(19, 12, 5, 'curtida_post', 7, 1, '2025-10-14 14:58:55'),
(20, 12, 5, 'comentario_post', 7, 1, '2025-10-14 14:58:59'),
(21, 5, 12, 'curtida_post', 5, 1, '2025-10-14 15:17:02'),
(22, 12, 5, 'curtida_post', 7, 1, '2025-10-14 15:42:50'),
(23, 5, 12, 'curtida_comentario', 7, 1, '2025-10-14 17:42:24'),
(24, 5, 12, 'curtida_comentario', 7, 1, '2025-10-14 17:46:19'),
(25, 5, 12, 'curtida_comentario', 7, 1, '2025-10-14 17:46:44'),
(26, 5, 12, 'curtida_comentario', 7, 1, '2025-10-14 17:52:50'),
(27, 5, 12, 'curtida_comentario', 7, 1, '2025-10-14 18:17:03'),
(28, 12, 5, 'curtida_post', 7, 1, '2025-10-14 18:22:37'),
(29, 12, 5, 'curtida_post', 7, 1, '2025-10-14 22:56:16'),
(30, 5, 12, 'curtida_post', 5, 1, '2025-10-14 22:57:23'),
(31, 5, 12, 'curtida_post', 3, 1, '2025-10-14 22:57:25'),
(32, 5, 12, 'curtida_comentario', 2, 1, '2025-10-14 22:57:32'),
(33, 12, 5, 'curtida_comentario', 5, 0, '2025-10-14 22:58:12'),
(34, 12, 5, 'curtida_post', 8, 0, '2025-10-14 23:25:44'),
(35, 12, 5, 'comentario_post', 7, 0, '2025-10-15 14:13:57'),
(36, 12, 5, 'comentario_post', 8, 0, '2025-10-15 14:19:10'),
(37, 12, 5, 'pedido_amizade', 5, 1, '2025-10-15 17:50:32'),
(38, 5, 12, 'pedido_amizade', 12, 1, '2025-10-15 17:51:41'),
(39, 12, 5, 'pedido_amizade', 5, 1, '2025-10-15 17:52:13'),
(40, 12, 5, 'pedido_amizade', 5, 1, '2025-10-15 17:53:16'),
(41, 5, 12, 'pedido_amizade', 12, 1, '2025-10-15 17:53:28'),
(42, 12, 5, 'pedido_amizade', 5, 1, '2025-10-15 18:07:50'),
(43, 12, 5, 'pedido_amizade', 5, 1, '2025-10-15 18:08:03'),
(44, 9, 5, 'pedido_amizade', 5, 0, '2025-10-15 19:04:00'),
(45, 4, 5, 'pedido_amizade', 5, 0, '2025-10-15 19:04:12'),
(46, 5, 12, 'pedido_amizade', 12, 1, '2025-10-16 14:22:48'),
(47, 5, 12, 'pedido_amizade', 12, 1, '2025-10-16 14:30:15'),
(48, 5, 12, 'pedido_amizade', 12, 1, '2025-10-16 16:05:35'),
(49, 5, 12, 'pedido_amizade', 12, 1, '2025-10-16 16:05:52');

-- --------------------------------------------------------

--
-- Estrutura para tabela `Postagens`
--

CREATE TABLE `Postagens` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `conteudo_texto` text NOT NULL,
  `tipo_media` enum('imagem','video') DEFAULT NULL,
  `status` enum('ativo','inativo','excluido_pelo_usuario') NOT NULL DEFAULT 'ativo',
  `url_media` varchar(255) DEFAULT NULL,
  `data_postagem` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `Postagens`
--

INSERT INTO `Postagens` (`id`, `id_usuario`, `conteudo_texto`, `tipo_media`, `status`, `url_media`, `data_postagem`) VALUES
(2, 4, 'teste', NULL, 'ativo', NULL, '2025-10-01 17:11:08'),
(3, 5, 'Olá pessoas tudo bom????', NULL, 'ativo', NULL, '2025-10-01 20:21:21'),
(4, 5, 'teste', NULL, 'excluido_pelo_usuario', NULL, '2025-10-06 21:27:13'),
(5, 5, 'teste 1', NULL, 'ativo', NULL, '2025-10-12 23:02:07'),
(6, 9, 'TESSSSTE', NULL, 'ativo', NULL, '2025-10-12 23:47:36'),
(7, 12, 'Ola pessoas', NULL, 'ativo', NULL, '2025-10-13 01:45:25'),
(8, 12, 'Diego reis', NULL, 'ativo', NULL, '2025-10-14 22:57:12'),
(9, 5, 'teste', NULL, 'excluido_pelo_usuario', NULL, '2025-10-16 16:33:43'),
(10, 5, 'teste', NULL, 'ativo', 'uploads/posts/post_5_1760633778.jpeg', '2025-10-16 16:56:18'),
(11, 5, 'Olaaaw', NULL, 'excluido_pelo_usuario', 'uploads/posts/post_5_1760646734.jpg', '2025-10-16 20:32:14'),
(12, 5, 'Kkkkkkk', NULL, 'excluido_pelo_usuario', 'uploads/posts/post_5_1760649510.jpg', '2025-10-16 21:18:30'),
(13, 5, 'IMAGEM TESTE 17/10', 'imagem', 'ativo', 'uploads/posts/post_5_1760715213.jpeg', '2025-10-17 15:33:33'),
(14, 5, 'video testee', 'video', 'excluido_pelo_usuario', 'uploads/posts/post_5_1760716955.mp4', '2025-10-17 16:02:37'),
(15, 5, 'Pokemon', 'video', 'ativo', 'uploads/posts/post_5_1760811037.mp4', '2025-10-18 18:10:37');

-- --------------------------------------------------------

--
-- Estrutura para tabela `Postagens_Edicoes`
--

CREATE TABLE `Postagens_Edicoes` (
  `id` int(11) NOT NULL,
  `id_postagem` int(11) NOT NULL,
  `conteudo_antigo` text NOT NULL,
  `data_edicao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `Postagens_Edicoes`
--

INSERT INTO `Postagens_Edicoes` (`id`, `id_postagem`, `conteudo_antigo`, `data_edicao`) VALUES
(1, 3, 'Olá pessoas', '2025-10-06 21:20:02'),
(2, 3, 'Olá pessoas tudo bom?', '2025-10-06 21:25:53'),
(3, 5, 'teste', '2025-10-15 14:44:57');

-- --------------------------------------------------------

--
-- Estrutura para tabela `Postagens_Salvas`
--

CREATE TABLE `Postagens_Salvas` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_postagem` int(11) NOT NULL,
  `data_salvo` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `Postagens_Salvas`
--

INSERT INTO `Postagens_Salvas` (`id`, `id_usuario`, `id_postagem`, `data_salvo`) VALUES
(10, 5, 2, '2025-10-09 15:32:55'),
(12, 12, 6, '2025-10-16 14:24:13'),
(13, 5, 12, '2025-10-16 23:04:42');

-- --------------------------------------------------------

--
-- Estrutura para tabela `Usuarios`
--

CREATE TABLE `Usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `sobrenome` varchar(100) NOT NULL,
  `data_nascimento` date DEFAULT NULL,
  `nome_de_usuario` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha_hash` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'membro',
  `status` enum('ativo','suspenso') NOT NULL DEFAULT 'ativo',
  `foto_perfil_url` varchar(255) DEFAULT NULL,
  `id_bairro` int(11) DEFAULT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp(),
  `relacionamento` enum('Não especificado','Solteiro(a)','Em um relacionamento sério','Casado(a)','Divorciado(a)') NOT NULL DEFAULT 'Não especificado' COMMENT 'Status de relacionamento do usuário',
  `biografia` text DEFAULT NULL COMMENT 'Pequena biografia ou descrição do usuário',
  `perfil_privado` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = Público, 1 = Privado (apenas amigos)',
  `privacidade_amigos` enum('todos','amigos','ninguem') NOT NULL DEFAULT 'amigos' COMMENT 'Define quem pode ver a lista de amigos do utilizador',
  `ultimo_acesso` timestamp NULL DEFAULT NULL COMMENT 'Timestamp da última atividade do usuário'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `Usuarios`
--

INSERT INTO `Usuarios` (`id`, `nome`, `sobrenome`, `data_nascimento`, `nome_de_usuario`, `email`, `senha_hash`, `role`, `status`, `foto_perfil_url`, `id_bairro`, `data_cadastro`, `relacionamento`, `biografia`, `perfil_privado`, `privacidade_amigos`, `ultimo_acesso`) VALUES
(4, 'teste1', 'adm', '2025-09-18', 'teste', 'teste1@teste.com', '$2y$10$jhpLUvgFXuNjDsld1M8uVeIFXZUJ84eo7AQ3WUTYd06eAA5Wdtjva', 'membro', 'ativo', 'uploads/avatars/user_4_1759353654.png', 12, '2025-09-29 20:46:34', 'Não especificado', NULL, 0, 'amigos', NULL),
(5, 'Diego', 'Kleins', '1997-10-30', 'diegokleins', 'didiego2010.dr@gmail.com', '$2y$10$T4P5xv4CTL3L3nySpUKpiuJEilrK.V.v3g2looKSU7f9hWWqm73vS', 'admin', 'ativo', 'uploads/avatars/user_5_1760544118.jpeg', 24, '2025-10-01 20:20:10', 'Casado(a)', 'Fundador e CEO desse site', 1, 'amigos', '2025-11-04 01:53:46'),
(9, 'Centro', 'Itajai', '2025-10-04', 'centroitajai2', 'centroitajai@gmail.com', '$2y$10$U3OktZm8BQskcbyr64GJP.9I8WxJehrTAcklOLw7r8vOFjwsbUomq', 'membro', 'ativo', NULL, 22, '2025-10-03 17:52:02', 'Não especificado', NULL, 0, 'amigos', NULL),
(11, 'conta', 'teste', '2005-06-15', 'contateste', 'email@email.com', '$2y$10$uThJHp0XYx1VilFB4osTeuxor2HqbbcKD18nYV1WZqMWkcnrjGC.q', 'membro', 'ativo', NULL, 20, '2025-10-03 18:04:32', 'Não especificado', NULL, 0, 'amigos', NULL),
(12, 'Diego', 'Reis', '2023-06-05', 'diegoreis', 'testeg@teste.com', '$2y$10$v5nD5q90ptjx7xIAg78zx.9neqmDuWI656exSBmXq7YaZ2svtRWWS', 'membro', 'ativo', 'uploads/avatars/user_12_1760319912.jpg', 22, '2025-10-13 01:43:48', 'Não especificado', NULL, 0, 'amigos', '2025-11-02 16:28:58'),
(13, 'diego', 'teste', '1999-10-30', 'diegoteste', 'teste@teste.com', '$2y$10$5VVJnAXvO0knZ1n7r7IUA.g7snkNXv6Njeu2.YLLmyBmEos0MpCT6', 'membro', 'ativo', NULL, 13, '2025-11-02 16:31:29', 'Não especificado', NULL, 0, 'amigos', '2025-11-02 18:16:57');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `Amizades`
--
ALTER TABLE `Amizades`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `amizade_unica` (`usuario_um_id`,`usuario_dois_id`),
  ADD KEY `idx_usuario_dois` (`usuario_dois_id`);

--
-- Índices de tabela `Bairros`
--
ALTER TABLE `Bairros`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_cidade` (`id_cidade`);

--
-- Índices de tabela `Cidades`
--
ALTER TABLE `Cidades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_estado` (`id_estado`);

--
-- Índices de tabela `Comentarios`
--
ALTER TABLE `Comentarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_postagem` (`id_postagem`),
  ADD KEY `idx_usuario` (`id_usuario`),
  ADD KEY `fk_comentario_pai` (`id_comentario_pai`);

--
-- Índices de tabela `Comentarios_Edicoes`
--
ALTER TABLE `Comentarios_Edicoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_id_comentario` (`id_comentario`);

--
-- Índices de tabela `Configuracoes`
--
ALTER TABLE `Configuracoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `chave_unica` (`chave`);

--
-- Índices de tabela `Curtidas`
--
ALTER TABLE `Curtidas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `curtida_unica` (`id_usuario`,`id_postagem`),
  ADD KEY `id_postagem` (`id_postagem`);

--
-- Índices de tabela `Curtidas_Comentarios`
--
ALTER TABLE `Curtidas_Comentarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `curtida_comentario_unica` (`id_usuario`,`id_comentario`),
  ADD KEY `fk_curtida_comentario_comentario` (`id_comentario`);

--
-- Índices de tabela `Denuncias`
--
ALTER TABLE `Denuncias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario_denunciou` (`id_usuario_denunciou`);

--
-- Índices de tabela `Estados`
--
ALTER TABLE `Estados`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `Logs_Login`
--
ALTER TABLE `Logs_Login`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_id_usuario` (`id_usuario`);

--
-- Índices de tabela `Logs_Visualizacao_Post`
--
ALTER TABLE `Logs_Visualizacao_Post`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_id_postagem` (`id_postagem`),
  ADD KEY `idx_id_usuario` (`id_usuario_visualizou`);

--
-- Índices de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `remetente_id` (`remetente_id`);

--
-- Índices de tabela `Postagens`
--
ALTER TABLE `Postagens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Índices de tabela `Postagens_Edicoes`
--
ALTER TABLE `Postagens_Edicoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_id_postagem` (`id_postagem`);

--
-- Índices de tabela `Postagens_Salvas`
--
ALTER TABLE `Postagens_Salvas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `salvo_unico` (`id_usuario`,`id_postagem`),
  ADD KEY `fk_salvo_postagem` (`id_postagem`);

--
-- Índices de tabela `Usuarios`
--
ALTER TABLE `Usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome_de_usuario` (`nome_de_usuario`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_usuario_bairro` (`id_bairro`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `Amizades`
--
ALTER TABLE `Amizades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `Bairros`
--
ALTER TABLE `Bairros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de tabela `Cidades`
--
ALTER TABLE `Cidades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=297;

--
-- AUTO_INCREMENT de tabela `Comentarios`
--
ALTER TABLE `Comentarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT de tabela `Comentarios_Edicoes`
--
ALTER TABLE `Comentarios_Edicoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `Configuracoes`
--
ALTER TABLE `Configuracoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `Curtidas`
--
ALTER TABLE `Curtidas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT de tabela `Curtidas_Comentarios`
--
ALTER TABLE `Curtidas_Comentarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de tabela `Denuncias`
--
ALTER TABLE `Denuncias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `Estados`
--
ALTER TABLE `Estados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de tabela `Logs_Login`
--
ALTER TABLE `Logs_Login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de tabela `Logs_Visualizacao_Post`
--
ALTER TABLE `Logs_Visualizacao_Post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT de tabela `Postagens`
--
ALTER TABLE `Postagens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `Postagens_Edicoes`
--
ALTER TABLE `Postagens_Edicoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `Postagens_Salvas`
--
ALTER TABLE `Postagens_Salvas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `Usuarios`
--
ALTER TABLE `Usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `Amizades`
--
ALTER TABLE `Amizades`
  ADD CONSTRAINT `fk_amizade_usuario_dois` FOREIGN KEY (`usuario_dois_id`) REFERENCES `Usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_amizade_usuario_um` FOREIGN KEY (`usuario_um_id`) REFERENCES `Usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `Bairros`
--
ALTER TABLE `Bairros`
  ADD CONSTRAINT `Bairros_ibfk_1` FOREIGN KEY (`id_cidade`) REFERENCES `Cidades` (`id`);

--
-- Restrições para tabelas `Cidades`
--
ALTER TABLE `Cidades`
  ADD CONSTRAINT `Cidades_ibfk_1` FOREIGN KEY (`id_estado`) REFERENCES `Estados` (`id`);

--
-- Restrições para tabelas `Comentarios`
--
ALTER TABLE `Comentarios`
  ADD CONSTRAINT `fk_comentario_pai` FOREIGN KEY (`id_comentario_pai`) REFERENCES `Comentarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_comentario_postagem` FOREIGN KEY (`id_postagem`) REFERENCES `Postagens` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_comentario_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `Usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `Comentarios_Edicoes`
--
ALTER TABLE `Comentarios_Edicoes`
  ADD CONSTRAINT `fk_edicao_comentario` FOREIGN KEY (`id_comentario`) REFERENCES `Comentarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `Curtidas`
--
ALTER TABLE `Curtidas`
  ADD CONSTRAINT `Curtidas_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `Usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `Curtidas_ibfk_2` FOREIGN KEY (`id_postagem`) REFERENCES `Postagens` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `Curtidas_Comentarios`
--
ALTER TABLE `Curtidas_Comentarios`
  ADD CONSTRAINT `fk_curtida_comentario_comentario` FOREIGN KEY (`id_comentario`) REFERENCES `Comentarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_curtida_comentario_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `Usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `Denuncias`
--
ALTER TABLE `Denuncias`
  ADD CONSTRAINT `denuncias_ibfk_1` FOREIGN KEY (`id_usuario_denunciou`) REFERENCES `Usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `Logs_Visualizacao_Post`
--
ALTER TABLE `Logs_Visualizacao_Post`
  ADD CONSTRAINT `fk_log_post_postagem` FOREIGN KEY (`id_postagem`) REFERENCES `Postagens` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_log_post_usuario` FOREIGN KEY (`id_usuario_visualizou`) REFERENCES `Usuarios` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD CONSTRAINT `notificacoes_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `Usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notificacoes_ibfk_2` FOREIGN KEY (`remetente_id`) REFERENCES `Usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `Postagens`
--
ALTER TABLE `Postagens`
  ADD CONSTRAINT `Postagens_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `Usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `Postagens_Edicoes`
--
ALTER TABLE `Postagens_Edicoes`
  ADD CONSTRAINT `fk_edicao_postagem` FOREIGN KEY (`id_postagem`) REFERENCES `Postagens` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `Postagens_Salvas`
--
ALTER TABLE `Postagens_Salvas`
  ADD CONSTRAINT `fk_salvo_postagem` FOREIGN KEY (`id_postagem`) REFERENCES `Postagens` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_salvo_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `Usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `Usuarios`
--
ALTER TABLE `Usuarios`
  ADD CONSTRAINT `fk_usuario_bairro` FOREIGN KEY (`id_bairro`) REFERENCES `Bairros` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
