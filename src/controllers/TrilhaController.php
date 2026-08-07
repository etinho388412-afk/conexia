<?php
// Caminho: conexia/src/controllers/TrilhaController.php

require_once __DIR__ . '/../config/database.php';

class TrilhaController {

    // XP necessário para cada nível (curva simples: 100 * nível)
    private static function xpParaProximoNivel(int $nivelAtual): int {
        return 100 * $nivelAtual;
    }

    private static function patentePorNivel(int $nivel): string {
        return match (true) {
            $nivel >= 20 => 'Mestre Conexia',
            $nivel >= 10 => 'Guardião do Conhecimento',
            $nivel >= 5  => 'Explorador Dedicado',
            default      => 'Aprendiz Conexia',
        };
    }

    public static function concederXp(int $usuarioId, int $xp, string $motivo = ''): void {
        $pdo = conexia_db();

        $stmt = $pdo->prepare('SELECT xp_total, nivel_atual FROM trilha_progresso WHERE usuario_id = ?');
        $stmt->execute([$usuarioId]);
        $progresso = $stmt->fetch();

        if (!$progresso) {
            $pdo->prepare('INSERT INTO trilha_progresso (usuario_id, xp_total) VALUES (?, ?)')
                ->execute([$usuarioId, $xp]);
            return;
        }

        $novoXp = (int) $progresso['xp_total'] + $xp;
        $nivelAtual = (int) $progresso['nivel_atual'];

        // Sobe de nível enquanto o XP acumulado ultrapassar o limite do nível
        while ($novoXp >= self::xpParaProximoNivel($nivelAtual)) {
            $novoXp -= self::xpParaProximoNivel($nivelAtual);
            $nivelAtual++;
        }

        $novaPatente = self::patentePorNivel($nivelAtual);

        $pdo->prepare(
            'UPDATE trilha_progresso SET xp_total = ?, nivel_atual = ?, patente_atual = ? WHERE usuario_id = ?'
        )->execute([$novoXp, $nivelAtual, $novaPatente, $usuarioId]);
    }

    public static function buscarProgresso(int $usuarioId): array {
        $pdo = conexia_db();
        $stmt = $pdo->prepare('SELECT * FROM trilha_progresso WHERE usuario_id = ?');
        $stmt->execute([$usuarioId]);
        $progresso = $stmt->fetch() ?: [
            'xp_total' => 0, 'nivel_atual' => 1, 'patente_atual' => 'Aprendiz Conexia',
        ];

        $progresso['xp_proximo_nivel'] = self::xpParaProximoNivel((int) $progresso['nivel_atual']);
        return $progresso;
    }

    public static function buscarConquistas(int $usuarioId): array {
        $pdo = conexia_db();
        $stmt = $pdo->prepare(
            'SELECT c.nome, c.descricao, c.icone, uc.obtida_em
             FROM usuario_conquistas uc
             JOIN conquistas c ON c.id = uc.conquista_id
             WHERE uc.usuario_id = ?
             ORDER BY uc.obtida_em DESC'
        );
        $stmt->execute([$usuarioId]);
        return $stmt->fetchAll();
    }
}
