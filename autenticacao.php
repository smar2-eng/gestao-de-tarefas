<?php
require_once __DIR__ . '/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function usuario_logado() {
    return $_SESSION['usuario_id'] ?? null;
}

function exigir_login() {
    if (!usuario_logado()) {
        header('Location: login.php');
        exit;
    }
}

function entrar($usuario) {
    session_regenerate_id(true);
    $_SESSION['usuario_id'] = (int) $usuario->id;
    $_SESSION['usuario_nome'] = $usuario->nome;
}
