<?php
include_once __DIR__ . '/../controller/controllerLogin.php';
include_once __DIR__ . '/../controller/controllerViatura.php';
include_once __DIR__ . '/../controller/controllerModelo.php';
include_once __DIR__ . '/../controller/controllerTipoViatura.php';
include_once __DIR__ . '/../controller/controllerCor.php';
include_once __DIR__ . '/../controller/controllerMarca.php';

exigirLogin();
exigirPermissao('viatura:criar');

$mensagem = '';
$tipoMensagem = '';

if (isset($_POST['gravar'])) {
    $obj = new Viatura(null, $_POST['matricula'] ?? '',
     $_POST['anoFabrico'] ?? 0, $_POST['cilindrada'] ?? 0, $_POST['numeroChassi'] ?? '', $_POST['preco'] ?? 0,
      $_POST['idModelo'] ?? 0, $_POST['idTipo'] ?? 0, $_POST['idCor'] ?? 0);
    list($ok, $mensagem) = criacaoViatura($obj);
    $tipoMensagem = $ok ? 'sucesso' : 'erro';
    if ($ok) {
        $_POST = array();
    }
}//a tabela viatura guarda o identificador do modelo, e é através desse modelo que o sistema consegue chegar à marca correspondente.
$marcas = listarMarca();
$idMarca = (int) ($_POST['idMarca'] ?? 0);
$modelos = $idMarca > 0 ? listarModeloPorMarca($idMarca) : array();
$tipos = listarTipoViatura();
$cores = listarCor();
$paginaActiva = 'viatura';
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CADASTRAR VIATURA</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>

<body>

    <?php include __DIR__ . '/partials/menu.php'; ?>

    <div class="mae">

        <div class="cabecalho">
            <div class="abas">
                <div class="aba abaAtiva">CADASTRO DE VIATURA</div>
                <?php if (temPermissao('viatura:listar')) { ?><a href="ListarViatura.php" class="aba">LISTA DE VIATURAS</a><?php } ?>
            </div>
        </div>

        <div class="dalheee">
            <div class="card">
                <form method="post">

                    <div class="topo">
                        <h1 class="titulo"><strong>Nova viatura</strong></h1>
                    </div>

                    <?php if ($mensagem != '') { ?>//e e uma funcao que apresenta o resultado de forma segura no HTML escapando caracteres especiais.
                        <div class="mensagem <?= $tipoMensagem ?>"><?= e($mensagem) ?></div>
                    <?php } ?>

                    <div class="linha linhaFiltro">
                        <div class="campo campoFlex">
                            <div class="campoTipo">
                                <label class="label"><strong>Marca</strong></label>
                            </div>
                            <select name="idMarca" class="input">
                                <option value="">Seleccione a marca.....</option>
                                <?php foreach ($marcas as $o) { ?>                                                                 //e()e evitar problemas de segurança ou caracteres     
                                    <option value="<?= $o->getIdMarca() ?>" <?= $idMarca == $o->getIdMarca() ? 'selected' : '' ?>><?= e($o->getNomeMarca()) ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="campo">
                            <button type="submit" name="filtrar" value="1" class="botao botaoCancelar" formnovalidate>Mostrar modelos</button>
                        </div>
                    </div>

                    <div class="campo">
                        <div class="campoTipo">
                            <label class="label"><strong>Modelo</strong></label>
                        </div>
                        <select name="idModelo" class="input" required>
                            <option value=""><?= $idMarca > 0 ? (count($modelos) > 0 ? 'Seleccione o modelo.....' : 'Esta marca ainda não tem modelos') : 'Seleccione o modelo.....' ?></option>
                            <?php foreach ($modelos as $o) { ?>
                                <option value="<?= $o->getIdModelo() ?>" <?= (($_POST['idModelo'] ?? '') == $o->getIdModelo()) ? 'selected' : '' ?>><?= e($o->getNomeModelo()) ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="linha">
                        <div class="campo campoFlex">
                            <div class="campoTipo">
                                <label class="label"><strong>Tipo de viatura</strong></label>
                            </div>
                            <select name="idTipo" class="input" required>
                                <option value="">Seleccione o tipo de viatura.....</option>
                                <?php foreach ($tipos as $o) { ?>
                                    <option value="<?= $o->getIdTipo() ?>" <?= (($_POST['idTipo'] ?? '') == $o->getIdTipo()) ? 'selected' : '' ?>><?= e($o->getNomeTipo()) ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="campo campoFlex">
                            <div class="campoTipo">
                                <label class="label"><strong>Ano de fabrico</strong></label>
                            </div>
                            <input type="number" name="anoFabrico" class="input" min="1900" max="<?= date('Y') ?>" step="1" value="<?= valorPost('anoFabrico') ?>" required>
                        </div>
                    </div>

                    <div class="linha">
                        <div class="campo campoFlex">
                            <div class="campoTipo">
                                <label class="label"><strong>Matrícula</strong></label>
                            </div>
                            <input type="text" name="matricula" class="input" maxlength="20" value="<?= valorPost('matricula') ?>" required>
                        </div>
                        <div class="campo campoFlex">
                            <div class="campoTipo">
                                <label class="label"><strong>Cilindrada (cm³)</strong></label>
                            </div>
                            <input type="number" name="cilindrada" class="input" min="50" max="20000" step="1" value="<?= valorPost('cilindrada') ?>" required>
                        </div>
                    </div>

                    <div class="campo">
                        <div class="campoTipo">
                            <label class="label"><strong>Número de chassi</strong></label>
                        </div>
                        <input type="text" name="numeroChassi" class="input" maxlength="30" value="<?= valorPost('numeroChassi') ?>" required>
                    </div>

                    <div class="campo">
                        <div class="campoTipo">
                            <label class="label"><strong>Cor</strong></label>
                        </div>
                        <select name="idCor" class="input" required>
                            <option value="">Seleccione a cor....</option>
                            <?php foreach ($cores as $o) { ?>
                                <option value="<?= $o->getIdCor() ?>" <?= (($_POST['idCor'] ?? '') == $o->getIdCor()) ? 'selected' : '' ?>><?= e($o->getNomeCor()) ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="campo">
                        <div class="campoTipo">
                            <label class="label"><strong>Preço (MT)</strong></label>
                        </div>
                        <input type="number" name="preco" class="input" min="0" step="0.01" placeholder="0.00" value="<?= valorPost('preco') ?>" required>
                    </div>

                    <div class="acao">
                        <button type="submit" name="gravar" class="botao">Cadastrar viatura</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

</body>

</html>
