<?php
// Caminho: conexia/src/api/avatar.php
// POST: salva a customização do avatar do aluno logado

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../auth/session.php';
require_once __DIR__ . '/../controllers/AvatarController.php';

$usuario = conexia_usuario_logado();
if (!$usuario) {
    http_response_code(401);
    echo json_encode(['erro' => 'Não autenticado.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido.']);
    exit;
}

$dados = json_decode(file_get_contents('php://input'), true);

if (!$dados || empty($dados['cor_pele']) || empty($dados['cabelo_estilo']) || empty($dados['cabelo_cor'])) {
    http_response_code(422);
    echo json_encode(['erro' => 'Campos obrigatórios: cor_pele, cabelo_estilo, cabelo_cor.']);
    exit;
}

AvatarController::atualizar($usuario['id'], [
    'cor_pele'      => $dados['cor_pele'],
    'cabelo_estilo' => $dados['cabelo_estilo'],
    'cabelo_cor'    => $dados['cabelo_cor'],
    'roupa_id'      => $dados['roupa_id'] ?? null,
    'acessorio_id'  => $dados['acessorio_id'] ?? null,
]);

echo json_encode(['sucesso' => true]);
