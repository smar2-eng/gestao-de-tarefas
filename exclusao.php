<?php
require_once __DIR__ . '/db.php';

$filtro = $_POST['filtro'] ?? 'todas';
$id = (int) ($_POST['id'] ?? 0);

if ($id > 0) {
    $tarefa = R::load('tarefa', $id);
    if ($tarefa->id) {
        R::trash($tarefa);
    }
}

header('Location: index.php?filtro=' . urlencode($filtro));
exit;
