<?php
include_once __DIR__ . '/../controller/controllerLogin.php';
include_once __DIR__ . '/../controller/controllerContinente.php';

exigirLogin();
exigirPermissao('continente:criar');

$mensagem = '';
$tipoMensagem = '';

if (isset($_POST['gravar'])) {
    $obj = new Continente(null, $_POST['nomeContinente'] ?? '');
    list($ok, $mensagem) = criacaoContinente($obj);
    $tipoMensagem = $ok ? 'sucesso' : 'erro';
    if ($ok) {
        $_POST = array();
    }
}


$paginaActiva = 'continente';
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CADASTRAR CONTINENTE</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>

<body>

    <?php include __DIR__ . '/partials/menu.php'; ?>

    <div class="mae">

        <div class="cabecalho">
            <div class="abas">
                <div class="aba abaAtiva">CADASTRO DE CONTINENTE</div>
                <?php if (temPermissao('continente:listar')) { ?><a href="ListarContinente.php" class="aba">LISTA DE CONTINENTES</a><?php } ?>
            </div>
        </div>

        <div class="dalheee">
            <div class="card">
                <form method="post">

                    <div class="topo">
                        <h1 class="titulo"><strong>Novo continente</strong></h1>
                    </div>

                    <?php if ($mensagem != '') { ?>
                        <div class="mensagem <?= $tipoMensagem ?>"><?= e($mensagem) ?></div>
                    <?php } ?>

                    <div class="campo">
                        <div class="campoTipo">
                            <label class="label"><strong>Nome do continente</strong></label>
                        </div>
                        <input type="text" name="nomeContinente" class="input" maxlength="60" value="<?= valorPost('nomeContinente') ?>" required>
                    </div>

                    <div class="acao">
                        <button type="submit" name="gravar" class="botao">Cadastrar continente</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

</body>

</html>
