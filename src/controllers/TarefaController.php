<?php
// Caminho: conexia/src/controllers/TarefaController.php

require_once __DIR__ . '/../models/Tarefa.php';
require_once __DIR__ . '/TrilhaController.php';

class TarefaController {

    public static function criar(int $professorId, array $dados): int {
        return Tarefa::criar(
            $professorId,
            (int) $dados['turma_id'],
            $dados['titulo'],
            $dados['descricao'] ?? '',
            $dados['anexo_url'] ?? null,
            $dados['rubrica'] ?? null,
            $dados['data_entrega']
        );
    }

    public static function listarParaAluno(int $alunoId): array {
        return Tarefa::listarPorAluno($alunoId);
    }

    /** Professor corrige a entrega; aluno ganha XP e moedas automaticamente */
    public static function corrigir(int $tarefaEntregaId, int $alunoId, float $nota, string $feedback): void {
        Tarefa::corrigir($tarefaEntregaId, $nota, $feedback);

        TrilhaController::concederXp($alunoId, 20, 'Tarefa corrigida');

        require_once __DIR__ . '/../models/Usuario.php';
        Usuario::creditarMoedas($alunoId, 5, 'Recompensa por entrega de tarefa');
    }
}
