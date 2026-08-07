<?php
// Caminho: conexia/src/models/Usuario.php

require_once __DIR__ . '/../config/database.php';

class Usuario {

    public static function buscarPorId(int $id): ?array {
        $pdo = conexia_db();
        $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE id = ?');
        $stmt->execute([$id]);
        $resultado = $stmt->fetch();
        return $resultado ?: null;
    }

    public static function saldoMoedas(int $usuarioId): int {
        $pdo = conexia_db();
        $stmt = $pdo->prepare('SELECT saldo FROM moedas_tempo WHERE usuario_id = ?');
        $stmt->execute([$usuarioId]);
        $resultado = $stmt->fetch();
        return $resultado ? (int) $resultado['saldo'] : 0;
    }

    public static function creditarMoedas(int $usuarioId, int $quantidade, string $motivo, string $tipo = 'ganho'): void {
        $pdo = conexia_db();
        $pdo->beginTransaction();

        try {
            $pdo->prepare('UPDATE moedas_tempo SET saldo = saldo + ? WHERE usuario_id = ?')
                ->execute([$quantidade, $usuarioId]);

            $pdo->prepare('INSERT INTO transacoes_moedas (usuario_id, tipo, quantidade, motivo) VALUES (?, ?, ?, ?)')
                ->execute([$usuarioId, $tipo, $quantidade, $motivo]);

            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function debitarMoedas(int $usuarioId, int $quantidade, string $motivo): bool {
        $saldoAtual = self::saldoMoedas($usuarioId);
        if ($saldoAtual < $quantidade) {
            return false; // saldo insuficiente
        }

        $pdo = conexia_db();
        $pdo->beginTransaction();

        try {
            $pdo->prepare('UPDATE moedas_tempo SET saldo = saldo - ? WHERE usuario_id = ?')
                ->execute([$quantidade, $usuarioId]);

            $pdo->prepare('INSERT INTO transacoes_moedas (usuario_id, tipo, quantidade, motivo) VALUES (?, "gasto", ?, ?)')
                ->execute([$usuarioId, $quantidade, $motivo]);

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
