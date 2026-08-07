<?php
// Caminho: conexia/public/aluno/banco-tempo.php
require_once __DIR__ . '/../../src/auth/rbac.php';
require_once __DIR__ . '/../../src/config/database.php';
require_once __DIR__ . '/../../src/models/Usuario.php';

$usuario = conexia_exigir_perfil('estudante');
$pdo = conexia_db();
$saldoMoedas = Usuario::saldoMoedas($usuario['id']);

$habilidades = $pdo->query('SELECT id, nome FROM habilidades ORDER BY nome')->fetchAll();

// Agendamentos do próprio aluno
$stmt = $pdo->prepare(
    'SELECT ap.id, ap.data_hora, ap.status, h.nome AS habilidade, u.nome AS tutor
     FROM agendamentos_p2p ap
     JOIN habilidades h ON h.id = ap.habilidade_id
     JOIN usuarios u ON u.id = ap.aluno_tutor_id
     WHERE ap.aluno_solicitante_id = ?
     ORDER BY ap.data_hora DESC'
);
$stmt->execute([$usuario['id']]);
$meusAgendamentos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conexia — Banco de Tempo</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/dark-mode.css">
</head>
<body>
    <a href="#conteudo" class="skip-link">Pular para o conteúdo</a>
    <header class="top-bar">
        <strong>Conexia — Banco de Tempo</strong>
        <span class="moedas-tempo">🪙 <?= (int) $saldoMoedas ?></span>
        <a href="dashboard.php">← Voltar</a>
    </header>

    <main id="conteudo" class="container">
        <h1>Cadastre suas habilidades</h1>
        <p style="color:var(--cor-texto-suave);">Diga o que você sabe ensinar e o que quer aprender.</p>

        <form id="form-habilidade" class="card" style="max-width:480px;">
            <label for="habilidade_id">Habilidade</label><br>
            <select id="habilidade_id" name="habilidade_id" required>
                <?php foreach ($habilidades as $h): ?>
                    <option value="<?= (int) $h['id'] ?>"><?= htmlspecialchars($h['nome']) ?></option>
                <?php endforeach; ?>
            </select><br><br>

            <label><input type="radio" name="tipo" value="ensina" checked> Sei ensinar</label>
            <label style="margin-left:1rem;"><input type="radio" name="tipo" value="aprende"> Quero aprender</label><br><br>

            <button type="submit" class="btn">Salvar habilidade</button>
        </form>

        <h1 style="margin-top:2rem;">Meus agendamentos</h1>
        <div class="grid-cards">
            <?php foreach ($meusAgendamentos as $ag): ?>
                <div class="card">
                    <h2><?= htmlspecialchars($ag['habilidade']) ?></h2>
                    <p>Tutor: <?= htmlspecialchars($ag['tutor']) ?></p>
                    <p><?= htmlspecialchars(date('d/m/Y H:i', strtotime($ag['data_hora']))) ?></p>
                    <span class="badge badge-<?= $ag['status'] === 'concluido' ? 'corrigida' : 'pendente' ?>">
                        <?= htmlspecialchars(ucfirst($ag['status'])) ?>
                    </span>
                </div>
            <?php endforeach; ?>
            <?php if (empty($meusAgendamentos)): ?>
                <p>Nenhum agendamento ainda.</p>
            <?php endif; ?>
        </div>
    </main>

    <script src="../js/main.js"></script>
    <script src="../js/mascote.js"></script>
    <script>
        document.getElementById('form-habilidade').addEventListener('submit', async (evento) => {
            evento.preventDefault();
            const dados = Object.fromEntries(new FormData(evento.target));
            try {
                await Conexia.api('/src/api/habilidade.php', {
                    method: 'POST',
                    body: JSON.stringify(dados),
                });
                alert('Habilidade salva com sucesso!');
                evento.target.reset();
            } catch (erro) {
                alert('Erro ao salvar habilidade.');
            }
        });
    </script>
</body>
</html>
