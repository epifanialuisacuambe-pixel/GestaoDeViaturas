<?php
include_once __DIR__ . '/../controller/controllerLogin.php';
include_once __DIR__ . '/../controller/controllerTipoViatura.php';

exigirLogin();
exigirPermissao('tipoviatura:criar');

$mensagem = '';
$tipoMensagem = '';

if (isset($_POST['gravar'])) {
    $obj = new TipoViatura(null, $_POST['nomeTipo'] ?? '');
    list($ok, $mensagem) = criacaoTipoViatura($obj);
    $tipoMensagem = $ok ? 'sucesso' : 'erro';
    if ($ok) {
        $_POST = array();
    }
}


$paginaActiva = 'tipoviatura';
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CADASTRAR TIPO DE VIATURA</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>

<body>

    <?php include __DIR__ . '/partials/menu.php'; ?>

    <div class="mae">

        <div class="cabecalho">
            <div class="abas">
                <div class="aba abaAtiva">CADASTRO DE TIPO DE VIATURA</div>
                <?php if (temPermissao('tipoviatura:listar')) { ?><a href="ListarTipoViatura.php" class="aba">LISTA DE TIPOS DE VIATURA</a><?php } ?>
            </div>
        </div>

        <div class="dalheee">
            <div class="card">
                <form method="post">

                    <div class="topo">
                        <h1 class="titulo"><strong>Novo tipo de viatura</strong></h1>
                    </div>

                    <?php if ($mensagem != '') { ?>
                        <div class="mensagem <?= $tipoMensagem ?>"><?= e($mensagem) ?></div>
                    <?php } ?>

                    <div class="campo">
                        <div class="campoTipo">
                            <label class="label"><strong>Nome do tipo de viatura</strong></label>
                        </div>
                        <input type="text" name="nomeTipo" class="input" maxlength="60" value="<?= valorPost('nomeTipo') ?>" required>
                    </div>

                    <div class="acao">
                        <button type="submit" name="gravar" class="botao">Cadastrar tipo de viatura</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

</body>

</html>
