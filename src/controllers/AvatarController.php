<?php
// Caminho: conexia/src/controllers/AvatarController.php

require_once __DIR__ . '/../config/database.php';

class AvatarController {

    public static function buscar(int $usuarioId): array {
        $pdo = conexia_db();
        $stmt = $pdo->prepare('SELECT * FROM avatares WHERE usuario_id = ?');
        $stmt->execute([$usuarioId]);
        return $stmt->fetch() ?: [];
    }

    public static function atualizar(int $usuarioId, array $dados): void {
        $pdo = conexia_db();
        $pdo->prepare(
            'UPDATE avatares SET cor_pele = ?, cabelo_estilo = ?, cabelo_cor = ?, roupa_id = ?, acessorio_id = ?
             WHERE usuario_id = ?'
        )->execute([
            $dados['cor_pele'],
            $dados['cabelo_estilo'],
            $dados['cabelo_cor'],
            $dados['roupa_id'] ?: null,
            $dados['acessorio_id'] ?: null,
            $usuarioId,
        ]);
    }

    /** Itens do inventário do usuário que já foram desbloqueados (XP suficiente) */
    public static function inventarioDisponivel(int $usuarioId): array {
        $pdo = conexia_db();
        $stmt = $pdo->prepare(
            'SELECT ic.* FROM inventario_usuario iu
             JOIN itens_cosmeticos ic ON ic.id = iu.item_id
             WHERE iu.usuario_id = ?'
        );
        $stmt->execute([$usuarioId]);
        return $stmt->fetchAll();
    }

    /** Verifica itens que o usuário desbloqueou pelo XP mas ainda não tem no inventário, e os adiciona */
    public static function desbloquearItensPorXp(int $usuarioId, int $xpAtual): void {
        $pdo = conexia_db();
        $stmt = $pdo->prepare(
            'SELECT id FROM itens_cosmeticos
             WHERE xp_necessario <= ?
             AND id NOT IN (SELECT item_id FROM inventario_usuario WHERE usuario_id = ?)'
        );
        $stmt->execute([$xpAtual, $usuarioId]);

        $insert = $pdo->prepare('INSERT INTO inventario_usuario (usuario_id, item_id) VALUES (?, ?)');
        foreach ($stmt->fetchAll() as $item) {
            $insert->execute([$usuarioId, $item['id']]);
        }
    }
}
