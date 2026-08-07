<?php
// Caminho: conexia/public/auth/google-callback.php
// Recebe o retorno do Google OAuth, valida o domínio institucional
// e cria/loga o usuário no sistema.

require_once __DIR__ . '/../../database.php';
require_once __DIR__ . '/../../session.php';
$oAuthConfig = require __DIR__ . '/../../src/config/oauth.php';

if (!isset($_GET['code'])) {
    http_response_code(400);
    die('Código de autorização não recebido.');
}

// 1) Troca o "code" pelo token de acesso do Google
$tokenResponse = file_get_contents('https://oauth2.googleapis.com/token', false, stream_context_create([
    'http' => [
        'method'  => 'POST',
        'header'  => 'Content-Type: application/x-www-form-urlencoded',
        'content' => http_build_query([
            'code'          => $_GET['code'],
            'client_id'     => $oAuthConfig['client_id'],
            'client_secret' => $oAuthConfig['client_secret'],
            'redirect_uri'  => $oAuthConfig['redirect_uri'],
            'grant_type'    => 'authorization_code',
        ]),
    ],
]));

$tokenData = json_decode($tokenResponse, true);

if (!isset($tokenData['access_token'])) {
    http_response_code(401);
    die('Falha ao obter token de acesso do Google.');
}

// 2) Busca os dados do perfil do usuário
$perfilResponse = file_get_contents('https://www.googleapis.com/oauth2/v2/userinfo?access_token=' . $tokenData['access_token']);
$perfil = json_decode($perfilResponse, true);

if (!isset($perfil['email'])) {
    http_response_code(401);
    die('Não foi possível obter o e-mail do Google.');
}

// 3) Valida domínio institucional
if (!str_ends_with($perfil['email'], $oAuthConfig['dominio_permitido'])) {
    http_response_code(403);
    die('Acesso restrito a e-mails institucionais (' . $oAuthConfig['dominio_permitido'] . ').');
}

// 4) Cria ou recupera o usuário no banco
$pdo = conexia_db();

$stmt = $pdo->prepare('SELECT * FROM usuarios WHERE email = ?');
$stmt->execute([$perfil['email']]);
$usuario = $stmt->fetch();

if (!$usuario) {
    // Novo usuário: por padrão entra como 'estudante'.
    $stmt = $pdo->prepare(
        'INSERT INTO usuarios (nome, email, google_id, foto, tipo_perfil) VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $perfil['name'],
        $perfil['email'],
        $perfil['id'],
        $perfil['picture'] ?? null,
        'estudante',
    ]);

    $novoId = $pdo->lastInsertId();

    // Inicializa registros dependentes
    $pdo->prepare('INSERT INTO moedas_tempo (usuario_id, saldo) VALUES (?, 0)')->execute([$novoId]);
    $pdo->prepare('INSERT INTO trilha_progresso (usuario_id) VALUES (?)')->execute([$novoId]);
    $pdo->prepare('INSERT INTO avatares (usuario_id) VALUES (?)')->execute([$novoId]);

    $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE id = ?');
    $stmt->execute([$novoId]);
    $usuario = $stmt->fetch();
}

// 5) Loga o usuário e redireciona conforme o perfil
conexia_logar_usuario($usuario);

$destino = (($usuario['tipo_perfil'] ?? 'estudante') === 'professor')
    ? '../../public/professor/dashboard.php'
    : '../../public/aluno/dashboard.php';

header("Location: {$destino}");
exit;

