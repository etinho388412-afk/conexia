<?php
// Caminho: conexia/src/models/Tarefa.php

require_once __DIR__ . '/../config/database.php';

class Tarefa {

    public static function criar(int $professorId, int $turmaId, string $titulo, string $descricao, ?string $anexoUrl, ?string $rubrica, string $dataEntrega): int {
        $pdo = conexia_db();
        $stmt = $pdo->prepare(
            'INSERT INTO tarefas (professor_id, turma_id, titulo, descricao, anexo_url, rubrica, data_entrega)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$professorId, $turmaId, $titulo, $descricao, $anexoUrl, $rubrica, $dataEntrega]);

        $tarefaId = (int) $pdo->lastInsertId();

        // Cria automaticamente o registro de entrega pendente para cada aluno da turma
        $alunos = $pdo->prepare('SELECT aluno_id FROM turma_alunos WHERE turma_id = ?');
        $alunos->execute([$turmaId]);

        $insertEntrega = $pdo->prepare(
            'INSERT INTO tarefa_entregas (tarefa_id, aluno_id, status) VALUES (?, ?, "pendente")'
        );
        foreach ($alunos->fetchAll() as $aluno) {
            $insertEntrega->execute([$tarefaId, $aluno['aluno_id']]);
        }

        return $tarefaId;
    }

    public static function listarPorAluno(int $alunoId): array {
        $pdo = conexia_db();
        $stmt = $pdo->prepare(
            'SELECT t.id, t.titulo, t.data_entrega, te.status, te.nota
             FROM tarefa_entregas te
             JOIN tarefas t ON t.id = te.tarefa_id
             WHERE te.aluno_id = ?
             ORDER BY t.data_entrega ASC'
        );
        $stmt->execute([$alunoId]);
        return $stmt->fetchAll();
    }

    public static function corrigir(int $tarefaEntregaId, float $nota, string $feedback): void {
        $pdo = conexia_db();
        $pdo->prepare(
            'UPDATE tarefa_entregas SET status = "corrigida", nota = ?, feedback = ?, corrigida_em = NOW() WHERE id = ?'
        )->execute([$nota, $feedback, $tarefaEntregaId]);
    }
}
