-- =========================================================
-- SISTEMA DE AGENDAMENTO SIMPLIFICADO (6 Tabelas)
-- =========================================================

-- 1. Usuários (Engloba Clientes, Empresas e Administradores)
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo_perfil ENUM('CLIENTE', 'EMPRESA', 'ADMIN') NOT NULL,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    documento VARCHAR(20) UNIQUE, -- Armazena CPF ou CNPJ
    telefone VARCHAR(20),
    cidade VARCHAR(150), -- Usado principalmente por Empresas
    status ENUM('ATIVO', 'PENDENTE', 'BLOQUEADO') DEFAULT 'ATIVO',
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 2. Atividades
CREATE TABLE atividades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT NOT NULL,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT NOT NULL,
    categoria VARCHAR(100) NOT NULL, -- Ex: 'Passeio', 'Aventura', 'Cultural'
    cidade VARCHAR(150) NOT NULL,
    endereco VARCHAR(255) NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    capacidade_maxima INT NOT NULL,
    imagem_url VARCHAR(500), -- Simplificação: apenas a URL da foto principal
    FOREIGN KEY (empresa_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- 3. Agendas / Sessões (Controla as datas, horários e vagas das atividades)
CREATE TABLE agendas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    atividade_id INT NOT NULL,
    data_hora DATETIME NOT NULL,
    vagas_disponiveis INT NOT NULL,
    FOREIGN KEY (atividade_id) REFERENCES atividades(id) ON DELETE CASCADE,
    CHECK (vagas_disponiveis >= 0)
);

-- 4. Reservas / Agendamentos
CREATE TABLE reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    agenda_id INT NOT NULL,
    quantidade_pessoas INT NOT NULL,
    status ENUM('PENDENTE', 'CONFIRMADA', 'CANCELADA') DEFAULT 'PENDENTE',
    data_reserva DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (agenda_id) REFERENCES agendas(id) ON DELETE CASCADE
);

-- 5. Avaliações
CREATE TABLE avaliacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    atividade_id INT NOT NULL,
    nota INT NOT NULL CHECK (nota BETWEEN 1 AND 5),
    comentario TEXT,
    data_avaliacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (atividade_id) REFERENCES atividades(id) ON DELETE CASCADE,
    UNIQUE(cliente_id, atividade_id) -- Apenas uma avaliação por cliente por atividade
);

-- 6. Favoritos
CREATE TABLE favoritos (
    cliente_id INT NOT NULL,
    atividade_id INT NOT NULL,
    data_favoritado DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (cliente_id, atividade_id),
    FOREIGN KEY (cliente_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (atividade_id) REFERENCES atividades(id) ON DELETE CASCADE
);
