-- ============================================================
-- CONEXIA - Schema do Banco de Dados (MySQL/MariaDB)
-- Caminho: conexia/database/schema.sql
-- ============================================================

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    google_id VARCHAR(100) UNIQUE,
    foto_url VARCHAR(255),
    tipo_perfil ENUM('professor', 'estudante') NOT NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE turmas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    professor_id INT NOT NULL,
    ano_letivo YEAR NOT NULL,
    FOREIGN KEY (professor_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE turma_alunos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    turma_id INT NOT NULL,
    aluno_id INT NOT NULL,
    FOREIGN KEY (turma_id) REFERENCES turmas(id) ON DELETE CASCADE,
    FOREIGN KEY (aluno_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    UNIQUE KEY unico_aluno_turma (turma_id, aluno_id)
) ENGINE=InnoDB;

CREATE TABLE tarefas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    professor_id INT NOT NULL,
    turma_id INT NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    descricao TEXT,
    anexo_url VARCHAR(255),
    rubrica TEXT,
    data_entrega DATETIME NOT NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (professor_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (turma_id) REFERENCES turmas(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE tarefa_entregas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tarefa_id INT NOT NULL,
    aluno_id INT NOT NULL,
    arquivo_url VARCHAR(255),
    status ENUM('pendente', 'entregue', 'corrigida') DEFAULT 'pendente',
    nota DECIMAL(4,2),
    feedback TEXT,
    entregue_em DATETIME,
    corrigida_em DATETIME,
    FOREIGN KEY (tarefa_id) REFERENCES tarefas(id) ON DELETE CASCADE,
    FOREIGN KEY (aluno_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    UNIQUE KEY unico_entrega (tarefa_id, aluno_id)
) ENGINE=InnoDB;

CREATE TABLE moedas_tempo (
    usuario_id INT PRIMARY KEY,
    saldo INT NOT NULL DEFAULT 0,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE transacoes_moedas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    tipo ENUM('ganho', 'gasto', 'bonus') NOT NULL,
    quantidade INT NOT NULL,
    motivo VARCHAR(255),
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE habilidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    categoria VARCHAR(100)
) ENGINE=InnoDB;

CREATE TABLE usuario_habilidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    habilidade_id INT NOT NULL,
    tipo ENUM('ensina', 'aprende') NOT NULL,
    nivel TINYINT DEFAULT 1,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (habilidade_id) REFERENCES habilidades(id) ON DELETE CASCADE,
    UNIQUE KEY unico_usuario_habilidade (usuario_id, habilidade_id)
) ENGINE=InnoDB;

CREATE TABLE agendamentos_p2p (
    id INT AUTO_INCREMENT PRIMARY KEY,
    aluno_solicitante_id INT NOT NULL,
    aluno_tutor_id INT NOT NULL,
    habilidade_id INT NOT NULL,
    data_hora DATETIME NOT NULL,
    status ENUM('agendado', 'concluido', 'cancelado') DEFAULT 'agendado',
    custo_moedas INT NOT NULL DEFAULT 1,
    FOREIGN KEY (aluno_solicitante_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (aluno_tutor_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (habilidade_id) REFERENCES habilidades(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE avaliacoes_p2p (
    id INT AUTO_INCREMENT PRIMARY KEY,
    agendamento_id INT NOT NULL,
    avaliador_id INT NOT NULL,
    pontualidade BOOLEAN,
    clareza BOOLEAN,
    resolveu_duvida BOOLEAN,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (agendamento_id) REFERENCES agendamentos_p2p(id) ON DELETE CASCADE,
    FOREIGN KEY (avaliador_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE trilha_progresso (
    usuario_id INT PRIMARY KEY,
    xp_total INT NOT NULL DEFAULT 0,
    nivel_atual INT NOT NULL DEFAULT 1,
    patente_atual VARCHAR(100) DEFAULT 'Aprendiz Conexia',
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE conquistas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao VARCHAR(255),
    icone VARCHAR(255),
    criterio VARCHAR(255)
) ENGINE=InnoDB;

CREATE TABLE usuario_conquistas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    conquista_id INT NOT NULL,
    obtida_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (conquista_id) REFERENCES conquistas(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE itens_cosmeticos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    tipo ENUM('roupa', 'acessorio', 'cabelo', 'item_tematico') NOT NULL,
    raridade ENUM('comum', 'raro', 'epico', 'lendario') DEFAULT 'comum',
    xp_necessario INT DEFAULT 0,
    imagem_url VARCHAR(255)
) ENGINE=InnoDB;

CREATE TABLE avatares (
    usuario_id INT PRIMARY KEY,
    cor_pele VARCHAR(20) DEFAULT '#f2c9a0',
    cabelo_estilo VARCHAR(50) DEFAULT 'padrao',
    cabelo_cor VARCHAR(20) DEFAULT '#3b2b20',
    roupa_id INT,
    acessorio_id INT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (roupa_id) REFERENCES itens_cosmeticos(id),
    FOREIGN KEY (acessorio_id) REFERENCES itens_cosmeticos(id)
) ENGINE=InnoDB;

CREATE TABLE inventario_usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    item_id INT NOT NULL,
    equipado BOOLEAN DEFAULT FALSE,
    obtido_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES itens_cosmeticos(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE mascotes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL UNIQUE,
    tipo ENUM('gato', 'cachorro', 'capivara', 'coruja', 'robo') NOT NULL,
    nome_customizado VARCHAR(50),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE mascote_itens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mascote_id INT NOT NULL,
    item_id INT NOT NULL,
    equipado BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (mascote_id) REFERENCES mascotes(id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES itens_cosmeticos(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE mensagens_mascote (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    texto VARCHAR(255) NOT NULL,
    tipo ENUM('notificacao', 'dica', 'motivacional', 'alerta_prazo') NOT NULL,
    lida BOOLEAN DEFAULT FALSE,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;
