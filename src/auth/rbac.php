<?php
// Caminho: conexia/src/auth/rbac.php
// Middleware simples de controle de acesso por perfil.
// Inclua este arquivo no topo de qualquer página protegida.

require_once __DIR__ . '/session.php';

/**
 * Garante que o usuário está logado e tem o perfil esperado.
 * @param string $perfilEsperado 'professor' ou 'estudante'
 */
function conexia_exigir_perfil(string $perfilEsperado): array {
    $usuario = conexia_usuario_logado();

    if ($usuario === null) {
        header('Location: /index.php');
        exit;
    }

    if ($usuario['tipo_perfil'] !== $perfilEsperado) {
        // Redireciona para o dashboard correto do perfil real do usuário
        $destino = $usuario['tipo_perfil'] === 'professor'
            ? '/professor/dashboard.php'
            : '/aluno/dashboard.php';
        header("Location: {$destino}");
        exit;
    }

    return $usuario;
}
