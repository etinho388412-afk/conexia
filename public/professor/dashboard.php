<?php
// Caminho: conexia/public/professor/dashboard.php
require_once __DIR__ . '/../../src/auth/rbac.php';
require_once __DIR__ . '/../../src/config/database.php';

$usuario = conexia_exigir_perfil('professor');

$pdo = conexia_db();
$stmt = $pdo->prepare('SELECT id, nome, ano_letivo FROM turmas WHERE professor_id = ?');
$stmt->execute([$usuario['id']]);
$turmas = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conexia — Painel do Professor</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/dark-mode.css">
</head>
<body>
    <a href="#conteudo" class="skip-link">Pular para o conteúdo</a>
    <header class="top-bar">
        <strong>Conexia — Professor(a) <?= htmlspecialchars($usuario['nome']) ?></strong>
        <nav style="display:flex; gap:1rem; align-items:center;">
            <a href="criar-tarefa.php">Criar tarefa</a>
            <a href="turmas.php">Turmas</a>
            <a href="bonus.php">Bônus de incentivo</a>
            <button id="alternar-tema" class="btn-secundario" aria-pressed="false">🌓 Tema</button>
        </nav>
    </header>

    <main id="conteudo" class="container">
        <h1>Suas turmas</h1>
        <div class="grid-cards">
            <?php foreach ($turmas as $turma): ?>
                <div class="card">
                    <h2><?= htmlspecialchars($turma['nome']) ?></h2>
                    <p style="color:var(--cor-texto-suave);">Ano letivo: <?= htmlspecialchars($turma['ano_letivo']) ?></p>
                    <a href="turmas.php?id=<?= (int) $turma['id'] ?>" class="btn-secundario">Ver turma</a>
                </div>
            <?php endforeach; ?>
            <?php if (empty($turmas)): ?>
                <p>Nenhuma turma cadastrada ainda.</p>
            <?php endif; ?>
        </div>
    </main>

    <script src="../js/main.js"></script>
</body>
</html>
