<?php
// Caminho: conexia/src/controllers/BancoTempoController.php

require_once __DIR__ . '/../models/AgendamentoP2P.php';
require_once __DIR__ . '/TrilhaController.php';

class BancoTempoController {

    public static function agendarAula(int $solicitanteId, int $tutorId, int $habilidadeId, string $dataHora): array {
        $agendamentoId = AgendamentoP2P::agendar($solicitanteId, $tutorId, $habilidadeId, $dataHora);

        if ($agendamentoId === null) {
            return ['sucesso' => false, 'mensagem' => 'Saldo de Moedas de Tempo insuficiente.'];
        }

        return ['sucesso' => true, 'agendamento_id' => $agendamentoId];
    }

    public static function concluirAula(int $agendamentoId, int $tutorId): void {
        AgendamentoP2P::concluir($agendamentoId);
        // Tutor ganha XP por ensinar
        TrilhaController::concederXp($tutorId, 30, 'Aula ministrada no Banco de Tempo');
    }

    public static function registrarAvaliacao(int $agendamentoId, int $avaliadorId, array $checklist): void {
        AgendamentoP2P::avaliar(
            $agendamentoId,
            $avaliadorId,
            (bool) ($checklist['pontualidade'] ?? false),
            (bool) ($checklist['clareza'] ?? false),
            (bool) ($checklist['resolveu_duvida'] ?? false)
        );
    }

    public static function buscarTutoresDisponiveis(int $habilidadeId): array {
        return AgendamentoP2P::buscarTutoresPorHabilidade($habilidadeId);
    }
}
