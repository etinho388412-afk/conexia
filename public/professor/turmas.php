<?php
// Caminho: conexia/public/professor/turmas.php
require_once __DIR__ . '/../../src/auth/rbac.php';
require_once __DIR__ . '/../../src/config/database.php';

$usuario = conexia_exigir_perfil('professor');
$pdo = conexia_db();

$turmaId = isset($_GET['id']) ? (int) $_GET['id'] : null;
$alunos = [];
$turmaSelecionada = null;

if ($turmaId) {
    $stmt = $pdo->prepare('SELECT * FROM turmas WHERE id = ? AND professor_id = ?');
    $stmt->execute([$turmaId, $usuario['id']]);
    $turmaSelecionada = $stmt->fetch();

    if ($turmaSelecionada) {
        $stmt = $pdo->prepare(
            'SELECT u.id, u.nome, u.email, tp.nivel_atual, tp.patente_atual
             FROM turma_alunos ta
             JOIN usuarios u ON u.id = ta.aluno_id
             LEFT JOIN trilha_progresso tp ON tp.usuario_id = u.id
             WHERE ta.turma_id = ?'
        );
        $stmt->execute([$turmaId]);
        $alunos = $stmt->fetchAll();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conexia — Turmas</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/dark-mode.css">
</head>
<body>
    <a href="#conteudo" class="skip-link">Pular para o conteúdo</a>
    <header class="top-bar">
        <strong>Conexia — Turmas</strong>
        <a href="dashboard.php">← Voltar</a>
    </header>

    <main id="conteudo" class="container">
        <?php if ($turmaSelecionada): ?>
            <h1><?= htmlspecialchars($turmaSelecionada['nome']) ?></h1>
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="text-align:left; border-bottom:1px solid var(--cor-borda);">
                        <th>Aluno</th><th>E-mail</th><th>Nível</th><th>Patente</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($alunos as $aluno): ?>
                        <tr style="border-bottom:1px solid var(--cor-borda);">
                            <td><?= htmlspecialchars($aluno['nome']) ?></td>
                            <td><?= htmlspecialchars($aluno['email']) ?></td>
                            <td><?= (int) ($aluno['nivel_atual'] ?? 1) ?></td>
                            <td><?= htmlspecialchars($aluno['patente_atual'] ?? 'Aprendiz Conexia') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Selecione uma turma no painel para ver os detalhes.</p>
        <?php endif; ?>
    </main>
    <script src="../js/main.js"></script>
</body>
</html>
