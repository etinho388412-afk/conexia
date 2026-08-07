<?php
// Caminho: conexia/public/aluno/dashboard.php
require_once __DIR__ . '/../../src/auth/rbac.php';
require_once __DIR__ . '/../../src/models/Usuario.php';
require_once __DIR__ . '/../../src/controllers/TrilhaController.php';

$usuario = conexia_exigir_perfil('estudante');
$saldoMoedas = Usuario::saldoMoedas($usuario['id']);
$progresso = TrilhaController::buscarProgresso($usuario['id']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conexia — Meu Painel</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/dark-mode.css">
</head>
<body>
    <a href="#conteudo" class="skip-link">Pular para o conteúdo</a>
    <header class="top-bar">
        <strong>Conexia — Olá, <?= htmlspecialchars(explode(' ', $usuario['nome'])[0]) ?>!</strong>
        <nav style="display:flex; gap:1rem; align-items:center;">
            <span class="moedas-tempo">🪙 <?= (int) $saldoMoedas ?></span>
            <a href="tarefas.php">Tarefas</a>
            <a href="trilha.php">Trilha</a>
            <a href="banco-tempo.php">Banco de Tempo</a>
            <a href="avatar.php">Avatar & Mascote</a>
            <button id="alternar-tema" class="btn-secundario" aria-pressed="false">🌓 Tema</button>
        </nav>
    </header>

    <main id="conteudo" class="container">
        <div class="grid-cards">
            <div class="card">
                <h2>Sua evolução</h2>
                <p><strong><?= htmlspecialchars($progresso['patente_atual']) ?></strong> — Nível <?= (int) $progresso['nivel_atual'] ?></p>
                <a href="trilha.php" class="btn-secundario">Ver trilha completa</a>
            </div>
            <div class="card">
                <h2>Banco de Tempo</h2>
                <p>Use suas moedas para agendar aulas com colegas.</p>
                <a href="banco-tempo.php" class="btn-secundario">Agendar aula</a>
            </div>
            <div class="card">
                <h2>Tarefas</h2>
                <p>Veja o que está pendente e o que já foi corrigido.</p>
                <a href="tarefas.php" class="btn-secundario">Ver tarefas</a>
            </div>
        </div>
    </main>

    <script src="../js/main.js"></script>
    <script src="../js/mascote.js"></script>
</body>
</html>
