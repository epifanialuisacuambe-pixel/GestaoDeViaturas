<?php
include_once __DIR__ . '/../controller/controllerLogin.php';
include_once __DIR__ . '/../controller/controllerModelo.php';
include_once __DIR__ . '/../controller/controllerMarca.php';

exigirLogin();
exigirPermissao('modelo:criar');

$mensagem = '';
$tipoMensagem = '';

if (isset($_POST['gravar'])) {
    $obj = new Modelo(null, $_POST['nomeModelo'] ?? '', $_POST['idMarca'] ?? 0);
    list($ok, $mensagem) = criacaoModelo($obj);
    $tipoMensagem = $ok ? 'sucesso' : 'erro';
    if ($ok) {
        $_POST = array();
    }
}

$marcas = listarMarca();
$paginaActiva = 'modelo';
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CADASTRAR MODELO</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>

<body>

    <?php include __DIR__ . '/partials/menu.php'; ?>

    <div class="mae">

        <div class="cabecalho">
            <div class="abas">
                <div class="aba abaAtiva">CADASTRO DE MODELO</div>
                <?php if (temPermissao('modelo:listar')) { ?><a href="ListarModelo.php" class="aba">LISTA DE MODELOS</a><?php } ?>
            </div>
        </div>

        <div class="dalheee">
            <div class="card">
                <form method="post">

                    <div class="topo">
                        <h1 class="titulo"><strong>Novo modelo</strong></h1>
                    </div>

                    <?php if ($mensagem != '') { ?>
                        <div class="mensagem <?= $tipoMensagem ?>"><?= e($mensagem) ?></div>
                    <?php } ?>

                    <div class="campo">
                        <div class="campoTipo">
                            <label class="label"><strong>Marca</strong></label>
                        </div>
                        <select name="idMarca" class="input" required>
                            <option value="">Seleccione a marca</option>
                            <?php foreach ($marcas as $o) { ?>
                                <option value="<?= $o->getIdMarca() ?>" <?= (($_POST['idMarca'] ?? '') == $o->getIdMarca()) ? 'selected' : '' ?>><?= e($o->getNomeMarca()) ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="campo">
                        <div class="campoTipo">
                            <label class="label"><strong>Nome do modelo</strong></label>
                        </div>
                        <input type="text" name="nomeModelo" class="input" maxlength="80" value="<?= valorPost('nomeModelo') ?>" required>
                    </div>

                    <div class="acao">
                        <button type="submit" name="gravar" class="botao">Cadastrar modelo</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

</body>

</html>
