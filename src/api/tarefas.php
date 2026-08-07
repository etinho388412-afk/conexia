<?php
// Caminho: conexia/src/api/tarefas.php
// Endpoint JSON: GET retorna tarefas do aluno logado | POST cria tarefa (professor)

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../auth/rbac.php';
require_once __DIR__ . '/../controllers/TarefaController.php';

$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'GET') {
    $usuario = conexia_exigir_perfil('estudante');
    echo json_encode(TarefaController::listarParaAluno($usuario['id']));
    exit;
}

if ($metodo === 'POST') {
    $usuario = conexia_exigir_perfil('professor');
    $dados = json_decode(file_get_contents('php://input'), true);

    if (!$dados || empty($dados['turma_id']) || empty($dados['titulo']) || empty($dados['data_entrega'])) {
        http_response_code(422);
        echo json_encode(['erro' => 'Campos obrigatórios: turma_id, titulo, data_entrega.']);
        exit;
    }

    $id = TarefaController::criar($usuario['id'], $dados);
    echo json_encode(['sucesso' => true, 'tarefa_id' => $id]);
    exit;
}

http_response_code(405);
echo json_encode(['erro' => 'Método não permitido.']);
