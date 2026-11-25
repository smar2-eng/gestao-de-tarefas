<?php
require_once __DIR__ . '/autenticacao.php';
exigir_login();
exigir_csrf();

$filtro = $_POST['filtro'] ?? 'todas';
$id = (int) ($_POST['id'] ?? 0);
$descricao = trim($_POST['descricao'] ?? '');

if ($id > 0 && $descricao !== '') {
    $tarefa = R::load('tarefa', $id);
    
    if ($tarefa->id && (int) $tarefa->usuario_id === (int) usuario_logado()) {
        $tarefa->descricao = $descricao;
        R::store($tarefa);
    }
}

header('Location: index.php?filtro=' . urlencode($filtro));
exit;
