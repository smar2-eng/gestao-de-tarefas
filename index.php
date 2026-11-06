<?php
require_once __DIR__ . '/db.php';

$permitidos = ['todas', 'concluidas', 'nao-concluidas'];
$filtro = $_GET['filtro'] ?? 'todas';
if (!in_array($filtro, $permitidos, true)) {
    $filtro = 'todas';
}

$tarefas = [];
$total = $feitas = 0;
if (in_array('tarefa', R::inspect())) {
    $total  = R::count('tarefa');
    $feitas = R::count('tarefa', 'concluida = 1');

    if ($filtro === 'concluidas') {
        $tarefas = R::find('tarefa', 'concluida = 1 ORDER BY id DESC');
    } elseif ($filtro === 'nao-concluidas') {
        $tarefas = R::find('tarefa', 'concluida = 0 ORDER BY id DESC');
    } else {
        $tarefas = R::findAll('tarefa', 'ORDER BY id DESC');
    }
}
$pendentes = $total - $feitas;

function quando($datahora) {
    if (!$datahora) return '';
    $ts = strtotime($datahora);
    $dia = date('Y-m-d', $ts);
    if ($dia === date('Y-m-d')) {
        $d = 'hoje';
    } elseif ($dia === date('Y-m-d', strtotime('-1 day'))) {
        $d = 'ontem';
    } else {
        $d = date('d/m', $ts);
    }
    $hora = date('G', $ts) . 'h' . (date('i', $ts) !== '00' ? date('i', $ts) : '');
    return [$d, $hora];
}

function legenda($t) {
    if ($t->concluida) {
        $q = quando($t->concluida_em ?? null);
        return $q ? "Concluída {$q[0]} · {$q[1]}" : 'Concluída';
    }
    $q = quando($t->criada_em ?? null);
    return $q ? ucfirst($q[0]) . " · {$q[1]}" : '';
}

function aba($atual, $link) { return $atual === $link ? 'ativa' : ''; }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sa&amp;Rick Tasks</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Lora:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/styles/style.css">
</head>
<body>
<div class="pagina">

    <header class="topo">
        <span class="logo" aria-hidden="true">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="5 12.5 10 17.5 19 7"/></svg>
        </span>
        <span class="marca">Sa&amp;Rick Tasks</span>
    </header>

    <h1>Seu espaço para organizar o dia</h1>

    <section class="cartao">

        <form action="inclusao.php" method="post" class="nova">
            <label for="descricao">Nova tarefa</label>
            <div class="nova-linha">
                <div class="campo">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                    <input type="text" id="descricao" name="descricao" placeholder="O que você quer fazer hoje?" maxlength="255" required autofocus autocomplete="off">
                </div>
                <button type="submit" class="btn-add">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                    Adicionar tarefa
                </button>
            </div>
        </form>

        <div class="barra">
            <nav class="abas">
                <a href="index.php?filtro=todas" class="<?= aba($filtro, 'todas') ?>">Todas <span><?= $total ?></span></a>
                <a href="index.php?filtro=concluidas" class="<?= aba($filtro, 'concluidas') ?>">Concluídas <span><?= $feitas ?></span></a>
                <a href="index.php?filtro=nao-concluidas" class="<?= aba($filtro, 'nao-concluidas') ?>">Não concluídas <span><?= $pendentes ?></span></a>
            </nav>
            <span class="resumo"><?= $total ?> <?= $total === 1 ? 'tarefa' : 'tarefas' ?> no caderno</span>
        </div>

        <ul class="lista">
            <?php if (empty($tarefas)): ?>
                <li class="vazio">Nenhuma tarefa por aqui.</li>
            <?php endif; ?>

            <?php foreach ($tarefas as $t): ?>
                <li>
                    <form action="concluir.php" method="post" class="cel-check">
                        <input type="hidden" name="id" value="<?= $t->id ?>">
                        <input type="hidden" name="filtro" value="<?= $filtro ?>">
                        <button type="submit" class="check <?= $t->concluida ? 'marcada' : '' ?>"
                                aria-label="<?= $t->concluida ? 'Marcar como não concluída' : 'Marcar como concluída' ?>">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="4.5 12.5 10 18 19.5 6.5"/></svg>
                        </button>
                    </form>

                    <div class="texto <?= $t->concluida ? 'feita' : '' ?>">
                        <strong><?= htmlspecialchars($t->descricao) ?></strong>
                        <small><?= htmlspecialchars(legenda($t)) ?></small>
                    </div>

                    <form action="exclusao.php" method="post" class="cel-lixo" onsubmit="return confirm('Excluir esta tarefa?')">
                        <input type="hidden" name="id" value="<?= $t->id ?>">
                        <input type="hidden" name="filtro" value="<?= $filtro ?>">
                        <button type="submit" class="lixeira" title="Excluir" aria-label="Excluir tarefa">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M6 6l1 14h10l1-14"/><path d="M10 10v6M14 10v6"/>
                            </svg>
                        </button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>

    <footer class="rodape">
        <span>Menos pressa. Mais presença.</span>
        <span>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1-1.1a5.5 5.5 0 0 0-7.8 7.8l1 1.1L12 21l7.8-7.5 1-1.1a5.5 5.5 0 0 0 0-7.8Z"/></svg>
            Um cantinho só seu.
        </span>
    </footer>

</div>
</body>
</html>
