<?php
// Caminho: conexia/public/aluno/trilha.php
require_once __DIR__ . '/../../src/auth/rbac.php';
$usuario = conexia_exigir_perfil('estudante');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conexia — Trilha de Evolução</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/dark-mode.css">
</head>
<body>
    <a href="#conteudo" class="skip-link">Pular para o conteúdo</a>
    <header class="top-bar">
        <strong>Conexia — Trilha de Evolução</strong>
        <a href="dashboard.php">← Voltar</a>
    </header>

    <main id="conteudo" class="container">
        <div id="trilha-xp" class="card"></div>

        <h2 style="margin-top:1.5rem;">Mapa de progresso</h2>
        <canvas id="trilha-mapa" width="900" height="140" style="width:100%; max-width:900px;"
                role="img" aria-label="Mapa de progresso da trilha de evolução"></canvas>

        <h2 style="margin-top:1.5rem;">Conquistas</h2>
        <div id="trilha-conquistas" class="grid-cards"></div>
    </main>

    <script src="../js/main.js"></script>
    <script src="../js/trilha.js"></script>
    <script src="../js/mascote.js"></script>
</body>
</html>
