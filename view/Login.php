
<?php
include_once __DIR__ . '/../controller/controllerLogin.php';

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

$erro = '';

if (isset($_POST['entrar'])) {
    $username = trim($_POST['username'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($username == '' || $senha == '') {
        $erro = 'Preencha o username e a senha';
    } else {
        list($estado, $erro) = login($username, $senha);

        if ($estado == 'ok') {
            header("Location: Dashboard.php");
            exit;
        }

        if ($estado == 'trocar') {
            header("Location: alterarSenha.php");
            exit;
        }
    }
} else {
    // Ao abrir o login sem enviar o formulário, termina a sessão activa.
    $sessao->logout();
    session_destroy();
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Gestão de Viaturas</title>

    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
    <link rel="stylesheet" href="css/estilo.css">
</head>

<body class="paginaLogin">

    <div class="mae maeLogin">

        <!-- Lado esquerdo: logótipo e mensagem de boas-vindas -->
        <section class="esquerda">

            <div class="logo">
                <img src="img/logo.svg" alt="Logótipo" class="marca-logo">
            </div>

            <div class="mensagemLogin">
                <h1>Bem-vindo ao Sistema de <span>Gestão de Viaturas</span></h1>

                <p>
                    A forma mais simples e eficiente de gerir
                    os seus veículos, modelos e tipos de viatura,
                    tudo organizado e seguro.
                </p>
            </div>

            <small></small>

        </section>

        <!-- Lado direito: formulário de autenticação -->
        <main class="direita">
            <div class="cartao">

            <form method="post">

                <div class="auth-logo">
                    <i class="bx bxs-car"></i>
                </div>

                <h2>Iniciar Sessão</h2>

                <p class="subtitulo">
                    Introduza as suas credenciais para aceder ao sistema.
                </p>

                <?php if ($erro != '') { ?>
                    <div class="mensagem erro">
                        <?= e($erro) ?>
                    </div>
                <?php } ?>

                <label for="username">Username</label>

                <div class="campoLogin">
                    <i class="bx bx-user"></i>

                    <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="O seu username"
                        value="<?= valorPost('username') ?>"
                        autocomplete="username"
                        required
                        autofocus>
                </div>

                <label for="senha">Senha</label>

                <div class="campoLogin">
                    <i class="bx bx-lock-alt"></i>

                    <input
                        type="password"
                        id="senha"
                        name="senha"
                        placeholder="Introduza a sua senha"
                        autocomplete="current-password"
                        required>
                </div>

                <button class="auth-botao" type="submit" name="entrar">
                    <i class="bx bx-log-in"></i>
                    Entrar
                </button>

            </form>

            <p class="auth-rodape">
                &copy; <?= date('Y') ?> Gestão de Viaturas.
                Todos os direitos reservados.
            </p>
            </div>

        </main>

    </div>

</body>
</html>
