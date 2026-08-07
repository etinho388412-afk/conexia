<?php
// Caminho: conexia/src/api/habilidade.php
// POST: cadastra que o aluno logado sabe ensinar ou quer aprender uma habilidade
// GET: lista as habilidades já cadastradas pelo aluno logado

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../auth/session.php';
require_once __DIR__ . '/../config/database.php';

$usuario = conexia_usuario_logado();
if (!$usuario) {
    http_response_code(401);
    echo json_encode(['erro' => 'Não autenticado.']);
    exit;
}

$pdo = conexia_db();
$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'GET') {
    $stmt = $pdo->prepare(
        'SELECT uh.id, uh.tipo, uh.nivel, h.nome AS habilidade
         FROM usuario_habilidades uh
         JOIN habilidades h ON h.id = uh.habilidade_id
         WHERE uh.usuario_id = ?
         ORDER BY uh.tipo, h.nome'
    );
    $stmt->execute([$usuario['id']]);
    echo json_encode($stmt->fetchAll());
    exit;
}

if ($metodo === 'POST') {
    $dados = json_decode(file_get_contents('php://input'), true);

    if (!$dados || empty($dados['habilidade_id']) || empty($dados['tipo'])) {
        http_response_code(422);
        echo json_encode(['erro' => 'Campos obrigatórios: habilidade_id, tipo.']);
        exit;
    }

    if (!in_array($dados['tipo'], ['ensina', 'aprende'], true)) {
        http_response_code(422);
        echo json_encode(['erro' => 'tipo deve ser "ensina" ou "aprende".']);
        exit;
    }

    $stmt = $pdo->prepare(
        'INSERT INTO usuario_habilidades (usuario_id, habilidade_id, tipo, nivel)
         VALUES (?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE tipo = VALUES(tipo)'
    );
    $stmt->execute([
        $usuario['id'],
        (int) $dados['habilidade_id'],
        $dados['tipo'],
        (int) ($dados['nivel'] ?? 1),
    ]);

    echo json_encode(['sucesso' => true]);
    exit;
}

http_response_code(405);
echo json_encode(['erro' => 'Método não permitido.']);
