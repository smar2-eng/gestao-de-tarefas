<?php
require_once __DIR__ . '/autenticacao.php';
exigir_login();
exigir_csrf();

$filtro = $_POST['filtro'] ?? 'todas';
$id = (int) ($_POST['id'] ?? 0);

if ($id > 0) {
    $tarefa = R::findOne('tarefa', 'id = ? AND usuario_id = ?', [$id, usuario_logado()]);
    
    if ($tarefa && $tarefa->id) {
        R::trash($tarefa);
    }
}

header('Location: index.php?filtro=' . urlencode($filtro));
exit;
