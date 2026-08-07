<?php
// Caminho: conexia/public/index.php
require_once __DIR__ . '/../src/auth/session.php';
$oauthConfig = require __DIR__ . '/../src/config/oauth.php';

$usuario = conexia_usuario_logado();
if ($usuario) {
    header('Location: ' . ($usuario['tipo_perfil'] === 'professor' ? '/professor/dashboard.php' : '/aluno/dashboard.php'));
    exit;
}

$urlLoginGoogle = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
    'client_id'     => $oauthConfig['client_id'],
    'redirect_uri'  => $oauthConfig['redirect_uri'],
    'response_type' => 'code',
    'scope'         => 'openid email profile',
    'hd'            => ltrim($oauthConfig['dominio_permitido'], '@'),
]);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conexia — Entrar</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dark-mode.css">
</head>
<body>
    <a href="#conteudo" class="skip-link">Pular para o conteúdo</a>
    <main id="conteudo" class="container" style="display:flex; align-items:center; justify-content:center; min-height:90vh;">
        <div class="card" style="text-align:center; max-width:400px;">
            <h1>Conexia</h1>
            <p style="color:var(--cor-texto-suave);">Aprendizagem cooperativa e gestão escolar</p>
            <a href="<?= htmlspecialchars($urlLoginGoogle) ?>" class="btn" style="justify-content:center; width:100%; margin-top:1rem;">
                Entrar com Google
            </a>
            <p style="font-size:0.8rem; color:var(--cor-texto-suave); margin-top:1rem;">
                Restrito a e-mails institucionais (<?= htmlspecialchars($oauthConfig['dominio_permitido']) ?>)
            </p>
        </div>
    </main>
    <script src="js/main.js"></script>
</body>
</html>
