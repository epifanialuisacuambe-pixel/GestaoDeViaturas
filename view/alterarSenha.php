<?php
include_once __DIR__ . '/../controller/controllerLogin.php';
include_once __DIR__ . '/../controller/utilizadorController.php';

$mensagem = '';
$tipoMensagem = '';
$concluido = false;

// Username já preenchido: vem do primeiro acesso ou da sessão iniciada
$usernameSugerido = $_SESSION['username_trocar_senha'] ?? ($_SESSION['username'] ?? '');

if (isset($_POST['alterar'])) {
    list($ok, $mensagem) = alterarSenha(
        trim($_POST['username'] ?? ''),
        $_POST['senha_antiga'] ?? '',
        $_POST['nova_senha'] ?? '',
        $_POST['confirma_senha'] ?? ''
    );
    $tipoMensagem = $ok ? 'sucesso' : 'erro';
    $concluido = $ok;

    if ($ok) {
        unset($_SESSION['username_trocar_senha']);
    }
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar senha</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>

<body class="paginaLogin">
    <div class="mae maeLogin maeCentro">

        <div class="direita cartaoCentro">
            <form method="post">
                <div class="logo logoEscura">
                    <img src="img/logo.svg" alt="Logo" class="marca-logo">
                    <span>SISTEMA DE<br>GESTÃO DE VIATURAS</span>
                </div>

                <h2>Alterar senha</h2>
                <p class="subtitulo">Escolha uma nova senha com pelo menos 8 caracteres.</p>

                <?php if ($mensagem != '') { ?>
                    <div class="mensagem <?= $tipoMensagem ?>"><?= e($mensagem) ?></div>
                <?php } ?>

                <?php if ($concluido) { ?>
                    <a href="Dashboard.php" class="botaoLink">Ir para o Dashboard</a>
                <?php } else { ?>
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?= e($_POST['username'] ?? $usernameSugerido) ?>" required>

                    <label for="senha_antiga">Senha actual</label>
                    <input type="password" id="senha_antiga" name="senha_antiga" placeholder="Senha actual" required>

                    <label for="nova_senha">Nova senha</label>
                    <input type="password" id="nova_senha" name="nova_senha" placeholder="••••••••" required>

                    <label for="confirma_senha">Confirmar nova senha</label>
                    <input type="password" id="confirma_senha" name="confirma_senha" placeholder="••••••••" required>

                    <button type="submit" name="alterar">Alterar senha</button>
                    <a href="Login.php" class="ligacaoVoltar">Voltar ao login</a>
                <?php } ?>
            </form>
        </div>

    </div>
</body>

</html>
