<?php
require_once __DIR__ . '/autenticacao.php';
exigir_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }
exigir_csrf();
$descricao = trim($_POST['descricao'] ?? '');
$usuario = R::load('usuario', usuario_logado());

if ($descricao !== '' && mb_strlen($descricao) <= 255 && $usuario->id) {
    $tarefa = R::dispense('tarefa');
    $tarefa->descricao = $descricao;
    $tarefa->concluida = 0;
    $tarefa->criada_em = date('Y-m-d H:i:s');
    $tarefa->usuario = $usuario; 
    R::store($tarefa);
}

header('Location: index.php');
exit;
