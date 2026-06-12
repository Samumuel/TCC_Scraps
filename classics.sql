-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 12/06/2026 às 17:27
-- Versão do servidor: 8.0.45-0ubuntu0.24.04.1
-- Versão do PHP: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `classics`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `Agendamento`
--

CREATE TABLE `Agendamento` (
  `ID_Agendamento` int NOT NULL,
  `Quantidade_Pessoas` int DEFAULT NULL,
  `Data_hora` datetime DEFAULT NULL,
  `Status` varchar(50) DEFAULT NULL,
  `ID_Atividade` int DEFAULT NULL,
  `ID_Usuario` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `Atividades`
--

CREATE TABLE `Atividades` (
  `ID_Atividade` int NOT NULL,
  `Nome` varchar(255) DEFAULT NULL,
  `Duracao` time DEFAULT NULL,
  `Descricao` text,
  `Localizacao` varchar(255) DEFAULT NULL,
  `Valor` decimal(18,2) DEFAULT NULL,
  `Fotos` varchar(255) DEFAULT NULL,
  `Categoria_ID` int DEFAULT NULL,
  `Empresa_ID` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `Avaliacao`
--

CREATE TABLE `Avaliacao` (
  `ID_Avaliacao` int NOT NULL,
  `Nota` int DEFAULT NULL,
  `Comentario` varchar(500) DEFAULT NULL,
  `ID_Usuario` int DEFAULT NULL,
  `ID_Atividade` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `Categorias`
--

CREATE TABLE `Categorias` (
  `ID_Categoria` int NOT NULL,
  `Nome_Categoria` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `Empresa`
--

CREATE TABLE `Empresa` (
  `ID_Empresa` int NOT NULL,
  `Nome` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `CNPJ` varchar(20) DEFAULT NULL,
  `Localizacao` varchar(255) DEFAULT NULL,
  `Telefone` varchar(20) DEFAULT NULL,
  `Senha` varchar(255) DEFAULT NULL,
  `Status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `Empresa`
--

INSERT INTO `Empresa` (`ID_Empresa`, `Nome`, `Email`, `CNPJ`, `Localizacao`, `Telefone`, `Senha`, `Status`) VALUES
(1, 'Pousada Nanaí', 'NanaiPousada@gmail.com', '28.865.171/0001-00', 'Foz do iguaçu', '6711111', '12345', NULL),
(4, 'Vassoura de Bruxa Doces', 'VassouraBruxa@gmail.com', '65.131.648/0001-42', 'Foz do Iguaçu', '45999999', '123456', 'pendente');

-- --------------------------------------------------------

--
-- Estrutura para tabela `Favorito`
--

CREATE TABLE `Favorito` (
  `ID_Favorito` int NOT NULL,
  `ID_Usuario` int DEFAULT NULL,
  `ID_Atividade` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `Localizacao`
--

CREATE TABLE `Localizacao` (
  `ID_Localizacao` int NOT NULL,
  `CEP` varchar(20) DEFAULT NULL,
  `Complemento` varchar(255) DEFAULT NULL,
  `ID_Atividade` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `Sessao`
--

CREATE TABLE `Sessao` (
  `ID_Sessao` int NOT NULL,
  `Data` date DEFAULT NULL,
  `Hora` time DEFAULT NULL,
  `Qtd_min` int DEFAULT NULL,
  `Qtd_max` int DEFAULT NULL,
  `Qtd_disponivel` int DEFAULT NULL,
  `ID_Atividade` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `Usuario`
--

CREATE TABLE `Usuario` (
  `ID_Usuario` int NOT NULL,
  `Nome` varchar(255) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `CPF` varchar(14) DEFAULT NULL,
  `Telefone` varchar(35) DEFAULT NULL,
  `Idade` int DEFAULT NULL,
  `Tipo_usuario` varchar(50) DEFAULT NULL,
  `Senha` varchar(255) DEFAULT NULL,
  `Foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `Usuario`
--

INSERT INTO `Usuario` (`ID_Usuario`, `Nome`, `Email`, `CPF`, `Telefone`, `Idade`, `Tipo_usuario`, `Senha`, `Foto`) VALUES
(1, 'Admin', 'samuelferreira.younis@gmail.com', '09104366905', '999045695', 17, 'admin', 'admin@123', 'https://imgs.search.brave.com/IK4rKRadzdZpQkIPw-ZEcxC52o1FlZ6htIU5GU5_RRw/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9tZWRp/YS5wcmludGFibGVz/LmNvbS9tZWRpYS9w/cmludHMvZjQ1NDUz/NmEtZWVjYS00NzI5/LWEyYzEtZjFjY2M5/NmMzMzRmL2ltYWdl/cy85NDU3OTM3XzA5/OTIyYzEzLTMxMGIt/NDEwZS'),
(11, 'testeNome5', 'testeNome5@gmail.com', '12345678900', '4599999', 17, 'user', '$2y$10$p2KfbF5x2R2r9/KYObyKV.JkZbmbpus4nTNWALlImojZZEK89Y6FS', '');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `Agendamento`
--
ALTER TABLE `Agendamento`
  ADD PRIMARY KEY (`ID_Agendamento`),
  ADD KEY `ID_Atividade` (`ID_Atividade`),
  ADD KEY `ID_Usuario` (`ID_Usuario`);

--
-- Índices de tabela `Atividades`
--
ALTER TABLE `Atividades`
  ADD PRIMARY KEY (`ID_Atividade`),
  ADD KEY `Categoria_ID` (`Categoria_ID`),
  ADD KEY `Empresa_ID` (`Empresa_ID`);

--
-- Índices de tabela `Avaliacao`
--
ALTER TABLE `Avaliacao`
  ADD PRIMARY KEY (`ID_Avaliacao`),
  ADD KEY `ID_Usuario` (`ID_Usuario`),
  ADD KEY `ID_Atividade` (`ID_Atividade`);

--
-- Índices de tabela `Categorias`
--
ALTER TABLE `Categorias`
  ADD PRIMARY KEY (`ID_Categoria`);

--
-- Índices de tabela `Empresa`
--
ALTER TABLE `Empresa`
  ADD PRIMARY KEY (`ID_Empresa`);

--
-- Índices de tabela `Favorito`
--
ALTER TABLE `Favorito`
  ADD PRIMARY KEY (`ID_Favorito`),
  ADD KEY `ID_Usuario` (`ID_Usuario`),
  ADD KEY `ID_Atividade` (`ID_Atividade`);

--
-- Índices de tabela `Localizacao`
--
ALTER TABLE `Localizacao`
  ADD PRIMARY KEY (`ID_Localizacao`),
  ADD KEY `ID_Atividade` (`ID_Atividade`);

--
-- Índices de tabela `Sessao`
--
ALTER TABLE `Sessao`
  ADD PRIMARY KEY (`ID_Sessao`),
  ADD KEY `ID_Atividade` (`ID_Atividade`);

--
-- Índices de tabela `Usuario`
--
ALTER TABLE `Usuario`
  ADD PRIMARY KEY (`ID_Usuario`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `Agendamento`
--
ALTER TABLE `Agendamento`
  MODIFY `ID_Agendamento` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `Atividades`
--
ALTER TABLE `Atividades`
  MODIFY `ID_Atividade` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `Avaliacao`
--
ALTER TABLE `Avaliacao`
  MODIFY `ID_Avaliacao` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `Categorias`
--
ALTER TABLE `Categorias`
  MODIFY `ID_Categoria` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `Empresa`
--
ALTER TABLE `Empresa`
  MODIFY `ID_Empresa` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `Favorito`
--
ALTER TABLE `Favorito`
  MODIFY `ID_Favorito` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `Localizacao`
--
ALTER TABLE `Localizacao`
  MODIFY `ID_Localizacao` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `Sessao`
--
ALTER TABLE `Sessao`
  MODIFY `ID_Sessao` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `Usuario`
--
ALTER TABLE `Usuario`
  MODIFY `ID_Usuario` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `Agendamento`
--
ALTER TABLE `Agendamento`
  ADD CONSTRAINT `Agendamento_ibfk_1` FOREIGN KEY (`ID_Atividade`) REFERENCES `Atividades` (`ID_Atividade`),
  ADD CONSTRAINT `Agendamento_ibfk_2` FOREIGN KEY (`ID_Usuario`) REFERENCES `Usuario` (`ID_Usuario`);

--
-- Restrições para tabelas `Atividades`
--
ALTER TABLE `Atividades`
  ADD CONSTRAINT `Atividades_ibfk_1` FOREIGN KEY (`Categoria_ID`) REFERENCES `Categorias` (`ID_Categoria`),
  ADD CONSTRAINT `Atividades_ibfk_2` FOREIGN KEY (`Empresa_ID`) REFERENCES `Empresa` (`ID_Empresa`);

--
-- Restrições para tabelas `Avaliacao`
--
ALTER TABLE `Avaliacao`
  ADD CONSTRAINT `Avaliacao_ibfk_1` FOREIGN KEY (`ID_Usuario`) REFERENCES `Usuario` (`ID_Usuario`),
  ADD CONSTRAINT `Avaliacao_ibfk_2` FOREIGN KEY (`ID_Atividade`) REFERENCES `Atividades` (`ID_Atividade`);

--
-- Restrições para tabelas `Favorito`
--
ALTER TABLE `Favorito`
  ADD CONSTRAINT `Favorito_ibfk_1` FOREIGN KEY (`ID_Usuario`) REFERENCES `Usuario` (`ID_Usuario`),
  ADD CONSTRAINT `Favorito_ibfk_2` FOREIGN KEY (`ID_Atividade`) REFERENCES `Atividades` (`ID_Atividade`);

--
-- Restrições para tabelas `Localizacao`
--
ALTER TABLE `Localizacao`
  ADD CONSTRAINT `Localizacao_ibfk_1` FOREIGN KEY (`ID_Atividade`) REFERENCES `Atividades` (`ID_Atividade`);

--
-- Restrições para tabelas `Sessao`
--
ALTER TABLE `Sessao`
  ADD CONSTRAINT `Sessao_ibfk_1` FOREIGN KEY (`ID_Atividade`) REFERENCES `Atividades` (`ID_Atividade`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
