<?php
// Caminho: conexia/src/api/mascote-mensagem.php
// GET: retorna mensagens não lidas do mascote | POST: marca mensagem como lida

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../auth/session.php';
require_once __DIR__ . '/../models/Mascote.php';

$usuario = conexia_usuario_logado();
if (!$usuario) {
    http_response_code(401);
    echo json_encode(['erro' => 'Não autenticado.']);
    exit;
}

$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'GET') {
    // Gera alertas automáticos de prazo antes de listar (só relevante para estudantes)
    if ($usuario['tipo_perfil'] === 'estudante') {
        Mascote::gerarAlertasDePrazo($usuario['id']);
    }
    echo json_encode(Mascote::mensagensNaoLidas($usuario['id']));
    exit;
}

if ($metodo === 'POST') {
    $dados = json_decode(file_get_contents('php://input'), true);
    if (empty($dados['mensagem_id'])) {
        http_response_code(422);
        echo json_encode(['erro' => 'mensagem_id é obrigatório.']);
        exit;
    }
    Mascote::marcarComoLida((int) $dados['mensagem_id']);
    echo json_encode(['sucesso' => true]);
    exit;
}

http_response_code(405);
echo json_encode(['erro' => 'Método não permitido.']);
