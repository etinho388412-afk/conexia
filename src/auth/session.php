<?php
// Caminho: conexia/src/auth/session.php
// Funções auxiliares de sessão

function conexia_iniciar_sessao(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function conexia_logar_usuario(array $usuario): void {
    conexia_iniciar_sessao();
    $_SESSION['usuario_id']    = $usuario['id'];
    $_SESSION['usuario_nome']  = $usuario['nome'];
    $_SESSION['usuario_email'] = $usuario['email'];
    $_SESSION['tipo_perfil']   = $usuario['tipo_perfil'];
}

function conexia_usuario_logado(): ?array {
    conexia_iniciar_sessao();
    if (!isset($_SESSION['usuario_id'])) {
        return null;
    }
    return [
        'id'          => $_SESSION['usuario_id'],
        'nome'        => $_SESSION['usuario_nome'],
        'email'       => $_SESSION['usuario_email'],
        'tipo_perfil' => $_SESSION['tipo_perfil'],
    ];
}

function conexia_logout(): void {
    conexia_iniciar_sessao();
    session_unset();
    session_destroy();
}
