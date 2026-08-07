<?php
// Caminho: conexia/public/professor/criar-tarefa.php
require_once __DIR__ . '/../../src/auth/rbac.php';
require_once __DIR__ . '/../../src/config/database.php';

$usuario = conexia_exigir_perfil('professor');

$pdo = conexia_db();
$stmt = $pdo->prepare('SELECT id, nome FROM turmas WHERE professor_id = ?');
$stmt->execute([$usuario['id']]);
$turmas = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conexia — Criar Tarefa</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/dark-mode.css">
</head>
<body>
    <a href="#conteudo" class="skip-link">Pular para o conteúdo</a>
    <header class="top-bar">
        <strong>Conexia — Nova Tarefa</strong>
        <a href="dashboard.php">← Voltar</a>
    </header>

    <main id="conteudo" class="container">
        <form id="form-tarefa" class="card" style="max-width:560px;">
            <label for="turma_id">Turma</label><br>
            <select id="turma_id" name="turma_id" required>
                <?php foreach ($turmas as $turma): ?>
                    <option value="<?= (int) $turma['id'] ?>"><?= htmlspecialchars($turma['nome']) ?></option>
                <?php endforeach; ?>
            </select><br><br>

            <label for="titulo">Título</label><br>
            <input type="text" id="titulo" name="titulo" required style="width:100%;"><br><br>

            <label for="descricao">Descrição</label><br>
            <textarea id="descricao" name="descricao" rows="4" style="width:100%;"></textarea><br><br>

            <label for="rubrica">Rubrica de avaliação</label><br>
            <textarea id="rubrica" name="rubrica" rows="3" style="width:100%;" placeholder="Critérios de correção..."></textarea><br><br>

            <label for="data_entrega">Data de entrega</label><br>
            <input type="datetime-local" id="data_entrega" name="data_entrega" required><br><br>

            <button type="submit" class="btn">Criar tarefa</button>
            <p id="mensagem-status" role="status" aria-live="polite"></p>
        </form>
    </main>

    <script src="../js/main.js"></script>
    <script>
        document.getElementById('form-tarefa').addEventListener('submit', async (evento) => {
            evento.preventDefault();
            const dados = Object.fromEntries(new FormData(evento.target));

            try {
                const resultado = await Conexia.api('/src/api/tarefas.php', {
                    method: 'POST',
                    body: JSON.stringify(dados),
                });
                document.getElementById('mensagem-status').textContent = resultado.sucesso
                    ? 'Tarefa criada com sucesso!' : (resultado.erro || 'Erro ao criar tarefa.');
                if (resultado.sucesso) evento.target.reset();
            } catch (erro) {
                document.getElementById('mensagem-status').textContent = 'Erro ao criar tarefa.';
            }
        });
    </script>
</body>
</html>
