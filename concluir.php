<?php
require_once __DIR__ . '/autenticacao.php';
exigir_login();

$filtro = $_POST['filtro'] ?? 'todas';
$id = (int) ($_POST['id'] ?? 0);

if ($id > 0) {
    $tarefa = R::load('tarefa', $id);
    // Só mexe na tarefa se ela for do usuário logado
    if ($tarefa->id && (int) $tarefa->usuario_id === (int) usuario_logado()) {
        if ($tarefa->concluida) {
            $tarefa->concluida = 0;
            $tarefa->concluida_em = null;
        } else {
            $tarefa->concluida = 1;
            $tarefa->concluida_em = date('Y-m-d H:i:s');
        }
        R::store($tarefa);
    }
}

header('Location: index.php?filtro=' . urlencode($filtro));
exit;
