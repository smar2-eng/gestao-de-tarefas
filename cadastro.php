<?php
require_once __DIR__ . '/autenticacao.php';

if (usuario_logado()) {
    header('Location: index.php');
    exit;
}

$erros = [];
$nome = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $senha = $_POST['senha'] ?? '';
    $confirma = $_POST['confirma'] ?? '';

    if ($nome === '') {
        $erros[] = 'Informe seu nome.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = 'Informe um e-mail válido.';
    }
    if (strlen($senha) < 6) {
        $erros[] = 'A senha precisa ter pelo menos 6 caracteres.';
    }
    if ($senha !== $confirma) {
        $erros[] = 'As senhas não conferem.';
    }
    if (!$erros && R::findOne('usuario', 'email = ?', [$email])) {
        $erros[] = 'Já existe uma conta com esse e-mail.';
    }

    if (!$erros) {
        $usuario = R::dispense('usuario');
        $usuario->nome = $nome;
        $usuario->email = $email;
        $usuario->senha = password_hash($senha, PASSWORD_DEFAULT); // nunca guardamos a senha em texto puro
        $usuario->criado_em = date('Y-m-d H:i:s');
        R::store($usuario);

        entrar($usuario);
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cadastro · Sa&amp;Rick Tasks</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Lora:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/styles/style.css">
</head>
<body>
<div class="pagina estreita">

    <header class="topo">
        <span class="logo" aria-hidden="true">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="5 12.5 10 17.5 19 7"/></svg>
        </span>
        <span class="marca">Sa&amp;Rick Tasks</span>
    </header>

    <h1>Crie seu cantinho</h1>

    <section class="cartao autenticacao">
        <?php if ($erros): ?>
            <div class="alerta">
                <?php foreach ($erros as $e): ?>
                    <div><?= htmlspecialchars($e) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" class="form-autenticacao">
            <div class="grupo">
                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($nome) ?>" placeholder="Como quer ser chamado?" maxlength="80" required autofocus>
            </div>
            <div class="grupo">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" placeholder="voce@exemplo.com" required>
            </div>
            <div class="grupo">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" placeholder="Mínimo de 6 caracteres" minlength="6" required>
            </div>
            <div class="grupo">
                <label for="confirma">Confirmar senha</label>
                <input type="password" id="confirma" name="confirma" placeholder="Repita a senha" minlength="6" required>
            </div>
            <button type="submit" class="btn-add btn-cheio">Criar conta</button>
        </form>

        <p class="troca">Já tem conta? <a href="login.php">Entrar</a></p>
    </section>

</div>
</body>
</html>
