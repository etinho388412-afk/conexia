<?php
// Bypassing Google login temporarily for preview
if (session_status() === PHP_SESSION_SUCCESS || session_status() === PHP_SESSION_NONE) {
    if (!isset($_SESSION)) {
        session_start();
    }
}
$_SESSION['user'] = [
    'name' => 'Usuário Teste',
    'email' => 'teste@escola.com'
];

