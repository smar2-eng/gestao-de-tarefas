<?php
declare(strict_types=1);

date_default_timezone_set('America/Sao_Paulo');
require_once __DIR__ . '/class/rb.php';

if (!class_exists('R') && class_exists('RedBeanPHP\\R')) {
    class_alias('RedBeanPHP\\R', 'R');
}

$databaseUrl = getenv('DATABASE_URL');
if ($databaseUrl) {
    $parts = parse_url($databaseUrl);
    $host = $parts['host'] ?? '127.0.0.1';
    $port = $parts['port'] ?? 3306;
    $name = ltrim($parts['path'] ?? '/todolist', '/');
    $user = urldecode($parts['user'] ?? 'root');
    $pass = urldecode($parts['pass'] ?? '');
} else {
    $host = getenv('DB_HOST') ?: '127.0.0.1';
    $port = getenv('DB_PORT') ?: '3306';
    $name = getenv('DB_NAME') ?: 'todolist';
    $user = getenv('DB_USER') ?: 'root';
    $pass = getenv('DB_PASS') ?: '';
}
R::setup("mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4", $user, $pass);

R::exec('CREATE TABLE IF NOT EXISTS usuario (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, nome VARCHAR(80) NOT NULL, email VARCHAR(190) NOT NULL, senha VARCHAR(255) NOT NULL, criado_em DATETIME NOT NULL, INDEX idx_usuario_email (email)) ENGINE=InnoDB');
R::exec('CREATE TABLE IF NOT EXISTS tarefa (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, usuario_id INT UNSIGNED NULL, descricao VARCHAR(255) NOT NULL, concluida TINYINT(1) NOT NULL DEFAULT 0, criada_em DATETIME NULL, concluida_em DATETIME NULL, INDEX idx_tarefa_usuario (usuario_id), INDEX idx_tarefa_status (concluida)) ENGINE=InnoDB');

foreach ([
    'usuario_id INT UNSIGNED NULL AFTER id',
    'concluida_em DATETIME NULL AFTER criada_em',
] as $column) {
    try {
        R::exec("ALTER TABLE tarefa ADD COLUMN {$column}");
    } catch (Throwable $e) {
        // A coluna já existe em instalações atualizadas.
    }
}
