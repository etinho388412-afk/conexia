<?php
// Caminho: conexia/public/aluno/tarefas.php
require_once __DIR__ . '/../../src/auth/rbac.php';
require_once __DIR__ . '/../../src/controllers/TarefaController.php';

$usuario = conexia_exigir_perfil('estudante');
$tarefas = TarefaController::listarParaAluno($usuario['id']);

$rotulosStatus = [
    'pendente'  => ['texto' => 'Pendente', 'classe' => 'badge-pendente'],
    'entregue'  => ['texto' => 'Entregue', 'classe' => 'badge-entregue'],
    'corrigida' => ['texto' => 'Corrigida', 'classe' => 'badge-corrigida'],
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conexia — Minhas Tarefas</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/dark-mode.css">
</head>
<body>
    <a href="#conteudo" class="skip-link">Pular para o conteúdo</a>
    <header class="top-bar">
        <strong>Conexia — Minhas Tarefas</strong>
        <a href="dashboard.php">← Voltar</a>
    </header>

    <main id="conteudo" class="container">
        <div class="grid-cards">
            <?php foreach ($tarefas as $tarefa): ?>
                <?php $status = $rotulosStatus[$tarefa['status']]; ?>
                <div class="card">
                    <h2><?= htmlspecialchars($tarefa['titulo']) ?></h2>
                    <p>Entrega: <?= htmlspecialchars(date('d/m/Y H:i', strtotime($tarefa['data_entrega']))) ?></p>
                    <span class="badge <?= $status['classe'] ?>"><?= $status['texto'] ?></span>
                    <?php if ($tarefa['nota'] !== null): ?>
                        <p>Nota: <strong><?= htmlspecialchars($tarefa['nota']) ?></strong></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            <?php if (empty($tarefas)): ?>
                <p>Nenhuma tarefa por aqui ainda.</p>
            <?php endif; ?>
        </div>
    </main>

    <script src="../js/main.js"></script>
    <script src="../js/mascote.js"></script>
</body>
</html>
