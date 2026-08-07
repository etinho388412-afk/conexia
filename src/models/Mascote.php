<?php
// Caminho: conexia/src/models/Mascote.php

require_once __DIR__ . '/../config/database.php';

class Mascote {

    public static function buscarPorUsuario(int $usuarioId): ?array {
        $pdo = conexia_db();
        $stmt = $pdo->prepare('SELECT * FROM mascotes WHERE usuario_id = ?');
        $stmt->execute([$usuarioId]);
        $resultado = $stmt->fetch();
        return $resultado ?: null;
    }

    public static function escolher(int $usuarioId, string $tipo, string $nomeCustomizado): int {
        $pdo = conexia_db();
        $stmt = $pdo->prepare(
            'INSERT INTO mascotes (usuario_id, tipo, nome_customizado) VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE tipo = VALUES(tipo), nome_customizado = VALUES(nome_customizado)'
        );
        $stmt->execute([$usuarioId, $tipo, $nomeCustomizado]);
        return (int) $pdo->lastInsertId();
    }

    /** Gera e salva uma mensagem do mascote (dica, alerta de prazo, motivacional, etc.) */
    public static function enviarMensagem(int $usuarioId, string $texto, string $tipo): void {
        $pdo = conexia_db();
        $pdo->prepare(
            'INSERT INTO mensagens_mascote (usuario_id, texto, tipo) VALUES (?, ?, ?)'
        )->execute([$usuarioId, $texto, $tipo]);
    }

    public static function mensagensNaoLidas(int $usuarioId): array {
        $pdo = conexia_db();
        $stmt = $pdo->prepare(
            'SELECT * FROM mensagens_mascote WHERE usuario_id = ? AND lida = FALSE ORDER BY criado_em DESC'
        );
        $stmt->execute([$usuarioId]);
        return $stmt->fetchAll();
    }

    public static function marcarComoLida(int $mensagemId): void {
        $pdo = conexia_db();
        $pdo->prepare('UPDATE mensagens_mascote SET lida = TRUE WHERE id = ?')->execute([$mensagemId]);
    }

    /** Verifica tarefas próximas do prazo e gera alertas automáticos do mascote */
    public static function gerarAlertasDePrazo(int $usuarioId): void {
        $pdo = conexia_db();
        $stmt = $pdo->prepare(
            "SELECT t.titulo, t.data_entrega
             FROM tarefa_entregas te
             JOIN tarefas t ON t.id = te.tarefa_id
             WHERE te.aluno_id = ? AND te.status = 'pendente'
             AND t.data_entrega BETWEEN NOW() AND NOW() + INTERVAL 2 DAY"
        );
        $stmt->execute([$usuarioId]);

        foreach ($stmt->fetchAll() as $tarefa) {
            self::enviarMensagem(
                $usuarioId,
                "Ei! A tarefa \"{$tarefa['titulo']}\" vence em breve. Bora terminar? 🐾",
                'alerta_prazo'
            );
        }
    }
}
