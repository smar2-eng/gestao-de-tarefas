<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

if (session_status() === PHP_SESSION_NONE) {
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    ini_set('session.use_strict_mode', '1');
    session_name('sarick_tasks_session');
    session_set_cookie_params([
        'httponly' => true,
        'secure' => $https,
        'samesite' => $https ? 'None' : 'Lax',
    ]);
    session_start();
}

function usuario_logado(): ?int
{
    return empty($_SESSION['usuario_id']) ? null : (int) $_SESSION['usuario_id'];
}

function exigir_login(): void
{
    if (!usuario_logado()) {
        header('Location: login.php');
        exit;
    }
}

function entrar($usuario): void
{
    session_regenerate_id(true);
    $_SESSION['usuario_id'] = (int) $usuario->id;
    $_SESSION['usuario_nome'] = (string) $usuario->nome;
}

function token_csrf(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function exigir_csrf(): void
{
    $token = (string) ($_POST['csrf_token'] ?? '');
    if ($token === '' || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        exit('Requisição inválida. Atualize a página e tente novamente.');
    }
}
