<?php
// Caminho: conexia/src/models/AgendamentoP2P.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Usuario.php';

class AgendamentoP2P {

    /**
     * Agenda uma aula com um colega tutor, debitando moedas do solicitante.
     * Retorna o ID do agendamento, ou null se saldo insuficiente.
     */
    public static function agendar(int $solicitanteId, int $tutorId, int $habilidadeId, string $dataHora, int $custo = 1): ?int {
        $debitou = Usuario::debitarMoedas($solicitanteId, $custo, 'Agendamento de aula P2P');
        if (!$debitou) {
            return null;
        }

        $pdo = conexia_db();
        $stmt = $pdo->prepare(
            'INSERT INTO agendamentos_p2p (aluno_solicitante_id, aluno_tutor_id, habilidade_id, data_hora, custo_moedas)
             VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([$solicitanteId, $tutorId, $habilidadeId, $dataHora, $custo]);

        return (int) $pdo->lastInsertId();
    }

    public static function concluir(int $agendamentoId): void {
        $pdo = conexia_db();
        $pdo->prepare('UPDATE agendamentos_p2p SET status = "concluido" WHERE id = ?')->execute([$agendamentoId]);

        $agendamento = $pdo->prepare('SELECT * FROM agendamentos_p2p WHERE id = ?');
        $agendamento->execute([$agendamentoId]);
        $dados = $agendamento->fetch();

        if ($dados) {
            // Credita moedas e XP para o tutor que ensinou a aula
            Usuario::creditarMoedas((int) $dados['aluno_tutor_id'], $dados['custo_moedas'], 'Aula ministrada no Banco de Tempo');
        }
    }

    /** Registra a avaliação objetiva pós-aula (checklist sim/não) */
    public static function avaliar(int $agendamentoId, int $avaliadorId, bool $pontualidade, bool $clareza, bool $resolveuDuvida): void {
        $pdo = conexia_db();
        $pdo->prepare(
            'INSERT INTO avaliacoes_p2p (agendamento_id, avaliador_id, pontualidade, clareza, resolveu_duvida)
             VALUES (?, ?, ?, ?, ?)'
        )->execute([$agendamentoId, $avaliadorId, $pontualidade, $clareza, $resolveuDuvida]);
    }

    public static function buscarTutoresPorHabilidade(int $habilidadeId): array {
        $pdo = conexia_db();
        $stmt = $pdo->prepare(
            'SELECT u.id, u.nome, uh.nivel
             FROM usuario_habilidades uh
             JOIN usuarios u ON u.id = uh.usuario_id
             WHERE uh.habilidade_id = ? AND uh.tipo = "ensina"
             ORDER BY uh.nivel DESC'
        );
        $stmt->execute([$habilidadeId]);
        return $stmt->fetchAll();
    }
}
