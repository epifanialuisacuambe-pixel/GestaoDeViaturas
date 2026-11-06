<?php
include_once __DIR__ . '/../controller/controllerLogin.php';
include_once __DIR__ . '/../controller/controllerFabricante.php';
include_once __DIR__ . '/../controller/controllerPais.php';
include_once __DIR__ . '/../controller/controllerContinente.php';

exigirLogin();
exigirPermissao('fabricante:criar');

$mensagem = '';
$tipoMensagem = '';

if (isset($_POST['gravar'])) {
    $obj = new Fabricante(null, $_POST['nomeFabricante'] ?? '', $_POST['idPais'] ?? 0);
    list($ok, $mensagem) = criacaoFabricante($obj);
    $tipoMensagem = $ok ? 'sucesso' : 'erro';
    if ($ok) {
        $_POST = array();
    }
}

$continentes = listarContinente();
$idContinente = (int) ($_POST['idContinente'] ?? 0);
$paises = $idContinente > 0 ? listarPaisPorContinente($idContinente) : array();
$paginaActiva = 'fabricante';
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CADASTRAR FABRICANTE</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>

<body>

    <?php include __DIR__ . '/partials/menu.php'; ?>

    <div class="mae">

        <div class="cabecalho">
            <div class="abas">
                <div class="aba abaAtiva">CADASTRO DE FABRICANTE</div>
                <?php if (temPermissao('fabricante:listar')) { ?><a href="ListarFabricante.php" class="aba">LISTA DE FABRICANTES</a><?php } ?>
            </div>
        </div>

        <div class="dalheee">
            <div class="card">
                <form method="post">

                    <div class="topo">
                        <h1 class="titulo"><strong>Novo fabricante</strong></h1>
                    </div>

                    <?php if ($mensagem != '') { ?>
                        <div class="mensagem <?= $tipoMensagem ?>"><?= e($mensagem) ?></div>
                    <?php } ?>

                    <div class="linha linhaFiltro">
                        <div class="campo campoFlex">
                            <div class="campoTipo">
                                <label class="label"><strong>Continente</strong></label>
                            </div>
                            <select name="idContinente" class="input">
                                <option value="">Seleccione o continente</option>
                                <?php foreach ($continentes as $o) { ?>
                                    <option value="<?= $o->getIdContinente() ?>" <?= $idContinente == $o->getIdContinente() ? 'selected' : '' ?>><?= e($o->getNomeContinente()) ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="campo">
                            <button type="submit" name="filtrar" value="1" class="botao botaoCancelar" formnovalidate>Mostrar países</button>
                        </div>
                    </div>

                    <div class="campo">
                        <div class="campoTipo">
                            <label class="label"><strong>País</strong></label>
                        </div>
                        <select name="idPais" class="input" required>
                            <option value=""><?= $idContinente > 0 ? (count($paises) > 0 ? 'Seleccione o país' : 'Este continente ainda não tem países') : 'Escolha primeiro o continente e carregue em "Mostrar países"' ?></option>
                            <?php foreach ($paises as $o) { ?>
                                <option value="<?= $o->getIdPais() ?>" <?= (($_POST['idPais'] ?? '') == $o->getIdPais()) ? 'selected' : '' ?>><?= e($o->getNomePais()) ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="campo">
                        <div class="campoTipo">
                            <label class="label"><strong>Nome do fabricante</strong></label>
                        </div>
                        <input type="text" name="nomeFabricante" class="input" maxlength="80" value="<?= valorPost('nomeFabricante') ?>" required>
                    </div>

                    <div class="acao">
                        <button type="submit" name="gravar" class="botao">Cadastrar fabricante</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

</body>

</html>
