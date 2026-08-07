<?php
// Caminho: conexia/src/api/trilha.php
// Endpoint JSON: retorna progresso (XP, nível, patente) e conquistas do usuário logado

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../auth/session.php';
require_once __DIR__ . '/../controllers/TrilhaController.php';

$usuario = conexia_usuario_logado();
if (!$usuario) {
    http_response_code(401);
    echo json_encode(['erro' => 'Não autenticado.']);
    exit;
}

echo json_encode([
    'progresso'   => TrilhaController::buscarProgresso($usuario['id']),
    'conquistas'  => TrilhaController::buscarConquistas($usuario['id']),
]);
