-- 1. Tabela de Clientes (Turistas)
CREATE TABLE cliente (
    id_cliente INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL, -- RN01: Email único
    senha VARCHAR(255) NOT NULL, -- RN02: Validação feita no backend
    cpf VARCHAR(14) UNIQUE NOT NULL,
    telefone VARCHAR(20) -- RF01: Telefone opcional
);

-- 2. Tabela de Empresas Prestadoras
CREATE TABLE empresa (
    id_empresa INT PRIMARY KEY AUTO_INCREMENT,
    nome_empresa VARCHAR(255) NOT NULL,
    cnpj VARCHAR(20) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL, -- RN01: Email único
    senha VARCHAR(255) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    cidade_atuacao VARCHAR(100) NOT NULL,
    status VARCHAR(20) DEFAULT 'Pendente' 
        CHECK (status IN ('Pendente', 'Aprovada', 'Rejeitada')) -- RN22: Aprovação obrigatória
);

-- 3. Tabela de Atividades
CREATE TABLE atividade (
    id_atividade INT PRIMARY KEY AUTO_INCREMENT,
    id_empresa INT NOT NULL,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT NOT NULL,
    cidade VARCHAR(100) NOT NULL,
    endereco VARCHAR(255) NOT NULL,
    valor DECIMAL(10, 2) NOT NULL,
    categoria VARCHAR(50) NOT NULL, -- ex: passeio, parque, aventura, cultural
    min_participantes INT NOT NULL DEFAULT 1,
    max_participantes INT NOT NULL, -- RN19: Limite escolhido pela empresa
    FOREIGN KEY (id_empresa) REFERENCES empresa(id_empresa) ON DELETE CASCADE
);

-- 4. Tabela de Sessões / Datas Disponíveis da Atividade
CREATE TABLE sessao_atividade (
    id_sessao INT PRIMARY KEY AUTO_INCREMENT,
    id_atividade INT NOT NULL,
    data_horario DATETIME NOT NULL, -- RF08: Selecionar data e horário
    vagas_disponiveis INT NOT NULL CHECK (vagas_disponiveis >= 0), -- RN10 e RN14: Redução e limite de vagas
    FOREIGN KEY (id_atividade) REFERENCES atividade(id_atividade) ON DELETE CASCADE
);

-- 5. Tabela de Agendamentos (Reservas)
CREATE TABLE agendamento (
    id_agendamento INT PRIMARY KEY AUTO_INCREMENT,
    id_cliente INT NOT NULL,
    id_sessao INT NOT NULL,
    quantidade_pessoas INT NOT NULL CHECK (quantidade_pessoas > 0),
    data_reserva DATETIME DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(20) DEFAULT 'Pendente' 
        CHECK (status IN ('Confirmada', 'Cancelada', 'Pendente')), -- RN32: Status das reservas
    FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente),
    FOREIGN KEY (id_sessao) REFERENCES sessao_atividade(id_sessao)
);

-- 6. Tabela de Avaliações
CREATE TABLE avaliacao (
    id_avaliacao INT PRIMARY KEY AUTO_INCREMENT,
    id_cliente INT NOT NULL,
    id_atividade INT NOT NULL,
    nota INT NOT NULL CHECK (nota >= 1 AND nota <= 5), -- RN26: Nota entre 1 e 5 estrelas
    comentario TEXT,
    FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente) ON DELETE CASCADE,
    FOREIGN KEY (id_atividade) REFERENCES atividade(id_atividade) ON DELETE CASCADE,
    UNIQUE (id_cliente, id_atividade) -- RN27: Uma avaliação por usuário por atividade
);

-- 7. Tabela de Favoritos
CREATE TABLE favorito (
    id_cliente INT NOT NULL,
    id_atividade INT NOT NULL,
    PRIMARY KEY (id_cliente, id_atividade),
    FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente) ON DELETE CASCADE,
    FOREIGN KEY (id_atividade) REFERENCES atividade(id_atividade) ON DELETE CASCADE
);
