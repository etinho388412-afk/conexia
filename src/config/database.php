<?php
// Caminho: conexia/src/config/database.php
// Conexão PDO com o banco de dados MySQL/MariaDB

function conexia_db(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        // >>> AJUSTE ESTES 4 VALORES COM OS DADOS DO SEU BANCO <<<
        $host = 'localhost';
        $dbname = 'conexia';
        $user = 'usuario_do_banco';
        $pass = 'senha_do_banco';

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
