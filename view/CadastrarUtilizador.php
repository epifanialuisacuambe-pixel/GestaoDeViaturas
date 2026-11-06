<?php
include_once __DIR__ . '/../controller/controllerLogin.php';
include_once __DIR__ . '/../controller/utilizadorController.php';
include_once __DIR__ . '/../controller/controllerPerfil.php';

exigirLogin();
exigirPermissao('utilizador:criar');

$mensagem = '';
$tipoMensagem = '';

if (isset($_POST['cadastrar'])) {
    $utilizador = new Utilizador(
        null,
        $_POST['nome'] ?? '',
        $_POST['apelido'] ?? '',
        $_POST['contacto'] ?? '',
        $_POST['email'] ?? '',
        $_POST['username'] ?? '',
        null,
        $_POST['idPerfil'] ?? 0
    );
    list($ok, $mensagem) = criacaoUtilizador($utilizador);
    $tipoMensagem = $ok ? 'sucesso' : 'erro';
    if ($ok) {
        $_POST = array();
    }
}

$perfis = listarPerfisAtribuiveis();
$paginaActiva = 'utilizador';
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CADASTRAR UTILIZADOR</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>

<body>

    <?php include __DIR__ . '/partials/menu.php'; ?>

    <div class="mae">

        <div class="cabecalho">
            <div class="abas">
                <div class="aba abaAtiva">CADASTRO DE UTILIZADOR</div>
                <?php if (temPermissao('utilizador:listar')) { ?><a href="ListarUtilizador.php" class="aba">LISTA DE UTILIZADORES</a><?php } ?>
            </div>
        </div>

        <div class="dalheee">
            <div class="card">
                <form method="post">

                    <div class="topo">
                        <h1 class="titulo"><strong>Novo utilizador</strong></h1>
                    </div>

                    <?php if ($mensagem != '') { ?>
                        <div class="mensagem <?= $tipoMensagem ?>"><?= e($mensagem) ?></div>
                    <?php } ?>

                    <div class="linha">
                        <div class="campo campoFlex">
                            <div class="campoTipo">
                                <label class="label"><strong>Nome</strong></label>
                            </div>
                            <input type="text" name="nome" class="input" maxlength="60" value="<?= valorPost('nome') ?>" required>
                        </div>
                        <div class="campo campoFlex">
                            <div class="campoTipo">
                                <label class="label"><strong>Apelido</strong></label>
                            </div>
                            <input type="text" name="apelido" class="input" maxlength="60" value="<?= valorPost('apelido') ?>" required>
                        </div>
                    </div>

                    <div class="linha">
                        <div class="campo campoFlex">
                            <div class="campoTipo">
                                <label class="label"><strong>Contacto</strong></label>
                            </div>
                            <input type="text" name="contacto" class="input" maxlength="15" placeholder="Ex.: 841234567" value="<?= valorPost('contacto') ?>" required>
                        </div>
                        <div class="campo campoFlex">
                            <div class="campoTipo">
                                <label class="label"><strong>E-mail</strong></label>
                            </div>
                            <input type="email" name="email" class="input" maxlength="100" value="<?= valorPost('email') ?>" required>
                        </div>
                    </div>

                    <div class="linha">
                        <div class="campo campoFlex">
                            <div class="campoTipo">
                                <label class="label"><strong>Username (usado para entrar)</strong></label>
                            </div>
                            <input type="text" name="username" class="input" maxlength="30" value="<?= valorPost('username') ?>" required>
                        </div>
                        <div class="campo campoFlex">
                            <div class="campoTipo">
                                <label class="label"><strong>Perfil</strong></label>
                            </div>
                            <select name="idPerfil" class="input" required>
                                <option value="">Seleccione o perfil</option>
                                <?php foreach ($perfis as $p) { ?>
                                    <option value="<?= $p->getIdPerfil() ?>" <?= (($_POST['idPerfil'] ?? '') == $p->getIdPerfil()) ? 'selected' : '' ?>><?= e($p->getNomePerfil()) ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <p class="nota">A senha inicial é <strong><?= SENHA_PADRAO ?></strong>. O utilizador terá de a trocar no primeiro acesso.</p>

                    <div class="acao">
                        <button type="submit" name="cadastrar" class="botao">Cadastrar utilizador</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

</body>

</html>
