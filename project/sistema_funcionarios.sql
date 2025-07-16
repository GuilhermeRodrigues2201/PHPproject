-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 16/06/2025 às 05:37
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `sistema_funcionarios`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `funcionarios`
--

CREATE TABLE `funcionarios` (
  `idFunc` int(11) NOT NULL,
  `nickname` varchar(50) NOT NULL,
  `senha` varchar(16) NOT NULL,
  `nomeCompleto` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `funcao` varchar(50) NOT NULL,
  `tipo` enum('gerente','funcionario','repositor') NOT NULL DEFAULT 'funcionario'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `funcionarios`
--

INSERT INTO `funcionarios` (`idFunc`, `nickname`, `senha`, `nomeCompleto`, `email`, `funcao`, `tipo`) VALUES
(1, 'admin', 'Admin123', 'Administrador Principal', 'admin@sistema.com', 'Gerência', 'gerente'),
(2, 'Guilherme', '$2y$10$nO9nfNuC5', 'Guilherme Rodrigues', 'gui@empresa.com', '', 'repositor');

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id_produto` int(11) NOT NULL,
  `nome_produto` varchar(100) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `estado` enum('disponivel','vendido') NOT NULL DEFAULT 'disponivel',
  `quantidade_estoque` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id_produto`, `nome_produto`, `preco`, `estado`, `quantidade_estoque`) VALUES
(1, 'Intel Core i9-13900K', 589.99, 'disponivel', 15),
(2, 'AMD Ryzen 9 7950X', 549.99, 'vendido', 0),
(3, 'NVIDIA GeForce RTX 4090', 1599.99, 'disponivel', 8),
(4, 'AMD Radeon RX 7900 XTX', 999.99, 'disponivel', 12),
(5, 'Corsair Vengeance RGB 32GB DDR5', 129.99, 'disponivel', 25),
(6, 'Kingston Fury Beast 16GB DDR4', 59.99, 'vendido', 0),
(7, 'Samsung 980 Pro SSD 1TB NVMe', 89.99, 'disponivel', 30),
(8, 'WD Black SN850X 2TB SSD', 149.99, 'disponivel', 18),
(9, 'ASUS ROG Strix Z790-E', 449.99, 'disponivel', 10),
(10, 'Gigabyte B650 AORUS Elite', 219.99, 'vendido', 0),
(11, 'Corsair RM850x 80 Plus Gold', 134.99, 'disponivel', 20);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `funcionarios`
--
ALTER TABLE `funcionarios`
  ADD PRIMARY KEY (`idFunc`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id_produto`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `funcionarios`
--
ALTER TABLE `funcionarios`
  MODIFY `idFunc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id_produto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
