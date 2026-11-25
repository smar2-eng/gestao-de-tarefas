<?php
// Sessão e funções de autenticação usadas por todas as páginas
require_once __DIR__ . '/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function usuario_logado() {
    return $_SESSION['usuario_id'] ?? null;
}

// Páginas protegidas chamam esta função: sem login, vai para login.php
function exigir_login() {
    if (!usuario_logado()) {
        header('Location: login.php');
        exit;
    }
}

// Guarda o usuário na sessão (e troca o id da sessão por segurança)
function entrar($usuario) {
    session_regenerate_id(true);
    $_SESSION['usuario_id'] = (int) $usuario->id;
    $_SESSION['usuario_nome'] = $usuario->nome;
}
