<?php
include_once __DIR__ . '/../controller/controllerLogin.php';
include_once __DIR__ . '/../controller/controllerPais.php';
include_once __DIR__ . '/../controller/controllerContinente.php';

exigirLogin();
exigirPermissao('pais:criar');

$mensagem = '';
$tipoMensagem = '';

if (isset($_POST['gravar'])) {
    $obj = new Pais(null, $_POST['nomePais'] ?? '', $_POST['idContinente'] ?? 0);
    list($ok, $mensagem) = criacaoPais($obj);
    $tipoMensagem = $ok ? 'sucesso' : 'erro';
    if ($ok) {
        $_POST = array();
    }
}

$continentes = listarContinente();
$paginaActiva = 'pais';
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CADASTRAR PAÍS</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>

<body>

    <?php include __DIR__ . '/partials/menu.php'; ?>

    <div class="mae">

        <div class="cabecalho">
            <div class="abas">
                <div class="aba abaAtiva">CADASTRO DE PAÍS</div>
                <?php if (temPermissao('pais:listar')) { ?><a href="ListarPais.php" class="aba">LISTA DE PAÍSES</a><?php } ?>
            </div>
        </div>

        <div class="dalheee">
            <div class="card">
                <form method="post">

                    <div class="topo">
                        <h1 class="titulo"><strong>Novo país</strong></h1>
                    </div>

                    <?php if ($mensagem != '') { ?>
                        <div class="mensagem <?= $tipoMensagem ?>"><?= e($mensagem) ?></div>
                    <?php } ?>

                    <div class="campo">
                        <div class="campoTipo">
                            <label class="label"><strong>Continente</strong></label>
                        </div>
                        <select name="idContinente" class="input" required>
                            <option value="">Seleccione o continente</option>
                            <?php foreach ($continentes as $o) { ?>
                                <option value="<?= $o->getIdContinente() ?>" <?= (($_POST['idContinente'] ?? '') == $o->getIdContinente()) ? 'selected' : '' ?>><?= e($o->getNomeContinente()) ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="campo">
                        <div class="campoTipo">
                            <label class="label"><strong>Nome do país</strong></label>
                        </div>
                        <input type="text" name="nomePais" class="input" maxlength="80" value="<?= valorPost('nomePais') ?>" required>
                    </div>

                    <div class="acao">
                        <button type="submit" name="gravar" class="botao">Cadastrar país</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

</body>

</html>
