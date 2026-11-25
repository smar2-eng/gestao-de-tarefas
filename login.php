<?php
require_once __DIR__ . '/autenticacao.php';

if (usuario_logado()) {
    header('Location: index.php');
    exit;
}

$erro = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $senha = $_POST['senha'] ?? '';

    $usuario = R::findOne('usuario', 'email = ?', [$email]);

    if ($usuario && password_verify($senha, $usuario->senha)) {
        entrar($usuario);
        header('Location: index.php');
        exit;
    }
    $erro = 'E-mail ou senha incorretos.';
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Entrar · Sa&amp;Rick Tasks</title>
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

    <h1>Bem-vindo de volta</h1>

    <section class="cartao autenticacao">
        <?php if ($erro): ?>
            <div class="alerta"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <form method="post" class="form-autenticacao">
            <div class="grupo">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" placeholder="voce@exemplo.com" required autofocus>
            </div>
            <div class="grupo">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" placeholder="Sua senha" required>
            </div>
            <button type="submit" class="btn-add btn-cheio">Entrar</button>
        </form>

        <p class="troca">Ainda não tem conta? <a href="cadastro.php">Cadastre-se</a></p>
    </section>

</div>
</body>
</html>
