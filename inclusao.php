<?php
require_once __DIR__ . '/db.php';

$descricao = trim($_POST['descricao'] ?? '');

if ($descricao !== '') {
    $tarefa = R::dispense('tarefa');
    $tarefa->descricao = $descricao;
    $tarefa->concluida = 0;
    $tarefa->criada_em = date('Y-m-d H:i:s');
    R::store($tarefa);
}

header('Location: index.php');
exit;
