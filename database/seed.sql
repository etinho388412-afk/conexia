-- ============================================================
-- CONEXIA - Dados iniciais
-- Caminho: conexia/database/seed.sql
-- ============================================================

INSERT INTO itens_cosmeticos (nome, tipo, raridade, xp_necessario, imagem_url) VALUES
('Camisa Padrão Conexia', 'roupa', 'comum', 0, 'img/avatars/roupa_padrao.svg'),
('Jaqueta Explorador', 'roupa', 'raro', 200, 'img/avatars/jaqueta_explorador.svg'),
('Manto Lendário', 'roupa', 'lendario', 1500, 'img/avatars/manto_lendario.svg'),
('Óculos Nerd', 'acessorio', 'comum', 0, 'img/avatars/oculos.svg'),
('Coroa de Sabedoria', 'acessorio', 'epico', 800, 'img/avatars/coroa.svg'),
('Cabelo Moicano', 'cabelo', 'raro', 150, 'img/avatars/cabelo_moicano.svg');

INSERT INTO conquistas (nome, descricao, icone, criterio) VALUES
('Primeira Tarefa', 'Entregou sua primeira tarefa', 'img/icons/badge_primeira_tarefa.svg', 'tarefas_entregues >= 1'),
('Mentor Iniciante', 'Ensinou sua primeira aula no Banco de Tempo', 'img/icons/badge_mentor.svg', 'aulas_ensinadas >= 1'),
('Cem Moedas', 'Acumulou 100 Moedas de Tempo', 'img/icons/badge_100_moedas.svg', 'moedas_acumuladas >= 100'),
('Trilha Nível 10', 'Alcançou o nível 10 na Trilha de Evolução', 'img/icons/badge_nivel10.svg', 'nivel_atual >= 10');

INSERT INTO habilidades (nome, categoria) VALUES
('Matemática Básica', 'Exatas'),
('Álgebra', 'Exatas'),
('Redação', 'Linguagens'),
('Inglês Conversação', 'Linguagens'),
('Física Mecânica', 'Exatas'),
('História do Brasil', 'Humanas');
