<?php
// Caminho: conexia/public/professor/bonus.php
require_once __DIR__ . '/../../src/auth/rbac.php';
require_once __DIR__ . '/../../src/config/database.php';
require_once __DIR__ . '/../../src/models/Usuario.php';

$usuario = conexia_exigir_perfil('professor');
$pdo = conexia_db();
$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $alunoId = (int) $_POST['aluno_id'];
    $quantidade = (int) $_POST['quantidade'];
    $motivo = trim($_POST['motivo'] ?? 'Bônus de incentivo');

    Usuario::creditarMoedas($alunoId, $quantidade, $motivo, 'bonus');
    $mensagem = 'Bônus enviado com sucesso!';
}

$stmt = $pdo->prepare(
    'SELECT DISTINCT u.id, u.nome FROM turma_alunos ta
     JOIN turmas t ON t.id = ta.turma_id
     JOIN usuarios u ON u.id = ta.aluno_id
     WHERE t.professor_id = ?'
);
$stmt->execute([$usuario['id']]);
$alunos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conexia — Bônus de Incentivo</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/dark-mode.css">
</head>
<body>
    <a href="#conteudo" class="skip-link">Pular para o conteúdo</a>
    <header class="top-bar">
        <strong>Conexia — Bônus de Incentivo</strong>
        <a href="dashboard.php">← Voltar</a>
    </header>

    <main id="conteudo" class="container">
        <?php if ($mensagem): ?><p role="status" class="card"><?= htmlspecialchars($mensagem) ?></p><?php endif; ?>

        <form method="POST" class="card" style="max-width:480px;">
            <label for="aluno_id">Aluno</label><br>
            <select id="aluno_id" name="aluno_id" required>
                <?php foreach ($alunos as $aluno): ?>
                    <option value="<?= (int) $aluno['id'] ?>"><?= htmlspecialchars($aluno['nome']) ?></option>
                <?php endforeach; ?>
            </select><br><br>

            <label for="quantidade">Quantidade de moedas</label><br>
            <input type="number" id="quantidade" name="quantidade" min="1" required><br><br>

            <label for="motivo">Motivo</label><br>
            <input type="text" id="motivo" name="motivo" style="width:100%;" placeholder="Ex: destaque na entrega da tarefa"><br><br>

            <button type="submit" class="btn">Enviar bônus</button>
        </form>
    </main>
    <script src="../js/main.js"></script>
</body>
</html>
