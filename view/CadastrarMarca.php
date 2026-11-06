<?php
include_once __DIR__ . '/../controller/controllerLogin.php';
include_once __DIR__ . '/../controller/controllerMarca.php';
include_once __DIR__ . '/../controller/controllerFabricante.php';

exigirLogin();
exigirPermissao('marca:criar');

$mensagem = '';
$tipoMensagem = '';

if (isset($_POST['gravar'])) {
    $obj = new Marca(null, $_POST['nomeMarca'] ?? '', $_POST['idFabricante'] ?? 0);
    list($ok, $mensagem) = criacaoMarca($obj);
    $tipoMensagem = $ok ? 'sucesso' : 'erro';
    if ($ok) {
        $_POST = array();
    }
}

$fabricantes = listarFabricante();
$paginaActiva = 'marca';
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CADASTRAR MARCA</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>

<body>

    <?php include __DIR__ . '/partials/menu.php'; ?>

    <div class="mae">

        <div class="cabecalho">
            <div class="abas">
                <div class="aba abaAtiva">CADASTRO DE MARCA</div>
                <?php if (temPermissao('marca:listar')) { ?><a href="ListarMarca.php" class="aba">LISTA DE MARCAS</a><?php } ?>
            </div>
        </div>

        <div class="dalheee">
            <div class="card">
                <form method="post">

                    <div class="topo">
                        <h1 class="titulo"><strong>Nova marca</strong></h1>
                    </div>

                    <?php if ($mensagem != '') { ?>
                        <div class="mensagem <?= $tipoMensagem ?>"><?= e($mensagem) ?></div>
                    <?php } ?>

                    <div class="campo">
                        <div class="campoTipo">
                            <label class="label"><strong>Fabricante</strong></label>
                        </div>
                        <select name="idFabricante" class="input" required>
                            <option value="">Seleccione o fabricante</option>
                            <?php foreach ($fabricantes as $o) { ?>
                                <option value="<?= $o->getIdFabricante() ?>" <?= (($_POST['idFabricante'] ?? '') == $o->getIdFabricante()) ? 'selected' : '' ?>><?= e($o->getNomeFabricante()) ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="campo">
                        <div class="campoTipo">
                            <label class="label"><strong>Nome da marca</strong></label>
                        </div>
                        <input type="text" name="nomeMarca" class="input" maxlength="80" value="<?= valorPost('nomeMarca') ?>" required>
                    </div>

                    <div class="acao">
                        <button type="submit" name="gravar" class="botao">Cadastrar marca</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

</body>

</html>
