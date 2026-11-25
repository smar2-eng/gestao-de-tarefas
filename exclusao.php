<?php
require_once __DIR__ . '/autenticacao.php';
exigir_login();

$filtro = $_POST['filtro'] ?? 'todas';
$id = (int) ($_POST['id'] ?? 0);

if ($id > 0) {
    $tarefa = R::load('tarefa', $id);
    // Só apaga se a tarefa for do usuário logado
    if ($tarefa->id && (int) $tarefa->usuario_id === (int) usuario_logado()) {
        R::trash($tarefa);
    }
}

// O resultado volta para a lista de tarefas
header('Location: index.php?filtro=' . urlencode($filtro));
exit;
