<?php
// Caminho: conexia/public/aluno/avatar.php
require_once __DIR__ . '/../../src/auth/rbac.php';
require_once __DIR__ . '/../../src/controllers/AvatarController.php';
require_once __DIR__ . '/../../src/models/Mascote.php';

$usuario = conexia_exigir_perfil('estudante');
$avatar = AvatarController::buscar($usuario['id']);
$inventario = AvatarController::inventarioDisponivel($usuario['id']);
$mascote = Mascote::buscarPorUsuario($usuario['id']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conexia — Avatar & Mascote</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/dark-mode.css">
</head>
<body>
    <a href="#conteudo" class="skip-link">Pular para o conteúdo</a>
    <header class="top-bar">
        <strong>Conexia — Avatar & Mascote</strong>
        <a href="dashboard.php">← Voltar</a>
    </header>

    <main id="conteudo" class="container">
        <div class="grid-cards">
            <div class="card">
                <h2>Seu avatar</h2>
                <div id="avatar-preview"></div>
                <div id="seletor-itens" style="margin-top:1rem; display:flex; flex-wrap:wrap; gap:0.5rem;"></div>
            </div>

            <div class="card">
                <h2>Seu mascote</h2>
                <?php if ($mascote): ?>
                    <p><strong><?= htmlspecialchars($mascote['nome_customizado'] ?: ucfirst($mascote['tipo'])) ?></strong> (<?= htmlspecialchars($mascote['tipo']) ?>)</p>
                <?php else: ?>
                    <form id="form-mascote">
                        <label for="tipo_mascote">Escolha seu mascote</label><br>
                        <select id="tipo_mascote" name="tipo">
                            <option value="gato">Gato</option>
                            <option value="cachorro">Cachorro</option>
                            <option value="capivara">Capivara</option>
                            <option value="coruja">Coruja</option>
                            <option value="robo">Robô</option>
                        </select><br><br>
                        <label for="nome_mascote">Nome</label><br>
                        <input type="text" id="nome_mascote" name="nome_customizado" maxlength="50"><br><br>
                        <button type="submit" class="btn">Adotar mascote</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script src="../js/main.js"></script>
    <script src="../js/avatar.js"></script>
    <script>
        const dadosAvatar = <?= json_encode($avatar) ?>;
        AvatarConexia.renderizar('avatar-preview', dadosAvatar);
        AvatarConexia.montarSeletorDeItens('seletor-itens', <?= json_encode($inventario) ?>, async (item) => {
            // Aplica localmente e persiste (endpoint /src/api/avatar.php pode ser adicionado seguindo o mesmo padrão dos demais)
            console.log('Item equipado:', item);
        });
    </script>
    <script src="../js/mascote.js"></script>
</body>
</html>
