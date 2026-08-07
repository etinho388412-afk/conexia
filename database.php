<?php
function conexia_db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $host = 'sql101.infinityfree.com';
        $dbname = 'if0_42588643_conexia';
        $user = 'if0_42588643';
        $pass = 'Conexia2026'; // Se essa não for sua senha do painel, coloque a sua senha aqui
        $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
        try {
            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            die('Erro de conexão com o banco de dados: ' . $e->getMessage());
        }
    }
    return $pdo;
}
