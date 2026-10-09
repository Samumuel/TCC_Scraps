-- Criação das tabelas independentes (sem chaves estrangeiras iniciais)

CREATE TABLE Usuario ( 
    ID_Usuario INT PRIMARY KEY AUTO_INCREMENT, 
    Nome VARCHAR(255), 
    Email VARCHAR(255) UNIQUE, 
    CPF VARCHAR(14), 
    Telefone INT, 
    Idade INT, 
    Tipo_usuario VARCHAR(50), 
    Senha VARCHAR(255), 
    Foto VARCHAR(255)
);

CREATE TABLE Categorias ( 
    ID_Categoria INT PRIMARY KEY AUTO_INCREMENT, 
    Nome_Categoria VARCHAR(100) 
);

CREATE TABLE Empresa ( 
    ID_Empresa INT PRIMARY KEY AUTO_INCREMENT, 
    Name VARCHAR(255), 
    Email VARCHAR(255) UNIQUE, 
    CNPJ VARCHAR(20), 
    Localizacao VARCHAR(255), 
    Telefone VARCHAR(20), 
    Senha VARCHAR(255), 
    Status VARCHAR(50) 
);

-- Criação da tabela principal que depende de Categorias e Empresa

CREATE TABLE Atividades ( 
    ID_Atividade INT PRIMARY KEY AUTO_INCREMENT, 
    Nome VARCHAR(255), 
    Duracao TIME, 
    Descricao TEXT, 
    Localizacao VARCHAR(255), 
    Valor DECIMAL(18,2), 
    Fotos VARCHAR(255),
    Categoria_ID INT, 
    Empresa_ID INT,
    FOREIGN KEY (Categoria_ID) REFERENCES Categorias(ID_Categoria),
    FOREIGN KEY (Empresa_ID) REFERENCES Empresa(ID_Empresa)
);

-- Criação das tabelas dependentes de Atividades e Usuario

CREATE TABLE Localizacao ( 
    ID_Localizacao INT PRIMARY KEY AUTO_INCREMENT, 
    CEP VARCHAR(20), 
    Complemento VARCHAR(255), 
    ID_Atividade INT, 
    FOREIGN KEY (ID_Atividade) REFERENCES Atividades(ID_Atividade)
);

CREATE TABLE Sessao (
    ID_Sessao INT PRIMARY KEY AUTO_INCREMENT, 
    Data DATE, 
    Hora TIME, 
    Qtd_min INT, 
    Qtd_max INT, 
    Qtd_disponivel INT, 
    ID_Atividade INT, 
    FOREIGN KEY (ID_Atividade) REFERENCES Atividades(ID_Atividade)
);

CREATE TABLE Avaliacao ( 
    ID_Avaliacao INT PRIMARY KEY AUTO_INCREMENT, 
    Nota INT, 
    Comentario VARCHAR(500),
    ID_Usuario INT, 
    ID_Atividade INT,
    FOREIGN KEY (ID_Usuario) REFERENCES Usuario(ID_Usuario),
    FOREIGN KEY (ID_Atividade) REFERENCES Atividades(ID_Atividade)
);

CREATE TABLE Favorito ( 
    ID_Favorito INT PRIMARY KEY AUTO_INCREMENT, 
    ID_Usuario INT, 
    ID_Atividade INT, 
    FOREIGN KEY (ID_Usuario) REFERENCES Usuario(ID_Usuario),
    FOREIGN KEY (ID_Atividade) REFERENCES Atividades(ID_Atividade)
);

CREATE TABLE Agendamento ( 
    ID_Agendamento INT PRIMARY KEY AUTO_INCREMENT, 
    Quantidade_Pessoas INT, 
    Data_hora DATETIME, 
    Status VARCHAR(50), 
    ID_Atividade INT, 
    ID_Usuario INT, 
    FOREIGN KEY (ID_Atividade) REFERENCES Atividades(ID_Atividade),
    FOREIGN KEY (ID_Usuario) REFERENCES Usuario(ID_Usuario)
);

ALTER TABLE `categorias` ADD `Empresa_ID` INT NOT NULL AFTER `Nome_Categoria`;


INSERT INTO `usuario` (`ID_Usuario`, `Nome`, `Email`, `CPF`, `Telefone`, `Idade`, `Tipo_usuario`, `Senha`, `Foto`) VALUES 
    (1, 'Admin', 'samuelferreira.younis@gmail.com', '09104366905', '999045695', '17', 'admin', 'admin@123', 
    'https://imgs.search.brave.com/IK4rKRadzdZpQkIPw-ZEcxC52o1FlZ6htIU5GU5_RRw/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9tZWRp/YS5wcmludGFibGVz/LmNvbS9tZWRpYS9w/cmludHMvZjQ1NDUz/NmEtZWVjYS00NzI5/LWEyYzEtZjFjY2M5/NmMzMzRmL2ltYWdl/cy85NDU3OTM3XzA5/OTIyYzEzLTMxMGIt/NDEwZS')
