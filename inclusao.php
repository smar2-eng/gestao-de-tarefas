<?php
require_once __DIR__ . '/autenticacao.php';
exigir_login();

$descricao = trim($_POST['descricao'] ?? '');
$usuario = R::load('usuario', usuario_logado());

if ($descricao !== '' && $usuario->id) {
    $tarefa = R::dispense('tarefa');
    $tarefa->descricao = $descricao;
    $tarefa->concluida = 0;
    $tarefa->criada_em = date('Y-m-d H:i:s');
    $tarefa->usuario = $usuario; // a tarefa pertence ao usuário logado
    R::store($tarefa);
}

// Depois de criar, volta para "Todas" para o usuário ver a tarefa nova na lista
header('Location: index.php');
exit;
