<?php
include_once __DIR__ . '/../controller/controllerLogin.php';
include_once __DIR__ . '/../controller/controllerViatura.php';
include_once __DIR__ . '/../controller/controllerModelo.php';
include_once __DIR__ . '/../controller/controllerTipoViatura.php';
include_once __DIR__ . '/../controller/controllerCor.php';
include_once __DIR__ . '/../controller/controllerMarca.php';

exigirLogin();
exigirPermissao('viatura:listar');

//=================
//REMOVER
if (isset($_POST['remover']) && temPermissao('viatura:remover')) {
    list($ok, $msg) = remocaoViatura($_POST['id'] ?? 0);
    definirMensagem($msg, $ok ? 'sucesso' : 'erro');
    header('Location: ListarViatura.php');
    exit;
}
//=================
//EDIITAR
if (isset($_POST['guardar']) && temPermissao('viatura:actualizar')) {
    $obj = new Viatura((int) ($_POST['id'] ?? 0), $_POST['matricula'] ?? '', $_POST['anoFabrico'] ?? 0, $_POST['cilindrada'] ?? 0, $_POST['numeroChassi'] ?? '', $_POST['preco'] ?? 0, $_POST['idModelo'] ?? 0, $_POST['idTipo'] ?? 0, $_POST['idCor'] ?? 0);
    list($ok, $msg) = edicaoViatura($obj);
    definirMensagem($msg, $ok ? 'sucesso' : 'erro');
    header('Location: ListarViatura.php');
    exit;
}

$pesquisa = trim($_GET['pesquisa'] ?? '');
$lista = $pesquisa != '' ? pesquisarViatura($pesquisa) : listarViatura();

$modelos = listarModelo();
$tipos = listarTipoViatura();
$cores = listarCor();
$podeEditar = temPermissao('viatura:actualizar');
$podeRemover = temPermissao('viatura:remover');
$paginaActiva = 'viatura';
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LISTAR VIATURAS</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>

<body>

    <?php include __DIR__ . '/partials/menu.php'; ?>

    <div class="mae">

        <div class="cabecalho">
            <div class="abas">
                <?php if (temPermissao('viatura:criar')) { ?><a href="CadastrarViatura.php" class="aba">CADASTRO DE VIATURA</a><?php } ?>
                <div class="aba abaAtiva">LISTA DE VIATURAS</div>
            </div>
        </div>

        <div class="dalheee">
            <div class="card">

                <div class="topo">
                    <h1 class="titulo"><strong>Lista de viaturas</strong></h1>
                </div>

                <?php mostrarMensagem(); ?>
                <p class="totalRegistos">Registos: <strong><?= count($lista) ?></strong></p>

                <form method="get">
                    <div class="linha">
                        <div class="campo campoBusca">
                            <input type="text" name="pesquisa" class="input" placeholder="Pesquisar" value="<?= e($pesquisa) ?>">
                        </div>
                        <div class="campo">
                            <button type="submit" class="botao">Pesquisar</button>
                        </div>
                        <?php if ($pesquisa != '') { ?>
                            <div class="campo">
                                <a href="ListarViatura.php" class="botao botaoCancelar">Limpar</a>
                            </div>
                        <?php } ?>
                    </div>
                </form>

                <div class="tabela">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Matrícula</th>
                                <th>Marca</th>
                                <th>Modelo</th>
                                <th>Tipo</th>
                                <th>Ano</th>
                                <th>Tempo de existência</th>
                                <th>Cilindrada</th>
                                <th>Nº de chassi</th>
                                <th>Cor</th>
                                <th>Preço</th>
                                <th>Acções</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($lista) == 0) { ?>
                                <tr>
                                    <td colspan="12">SEM NENHUMA VIATURA REGISTADA</td>
                                </tr>
                            <?php } else { ?>
                                <?php foreach ($lista as $item) { ?>
                                    <tr>
                                        <td><?= $item->getIdViatura() ?></td>
                                        <td><?= e($item->getMatricula()) ?></td>
                                        <td><?= e($item->getNomeMarca()) ?></td>
                                        <td><?= e($item->getNomeModelo()) ?></td>
                                        <td><?= e($item->getNomeTipo()) ?></td>
                                        <td><?= e($item->getAnoFabrico()) ?></td>
                                        <td><?= e($item->getTempoExistencia()) ?></td>
                                        <td><?= e($item->getCilindrada() . ' cm³') ?></td>
                                        <td><?= e($item->getNumeroChassi()) ?></td>
                                        <td><span class="amostra" style="background: <?= e($item->getHexCor()) ?>"></span><?= e($item->getNomeCor()) ?></td>
                                        <td><?= e(number_format((float) $item->getPreco(), 2, ',', '.') . ' MT') ?></td>
                                        <td>
                                            <div class="acoes">
                                                <?php if ($podeEditar) { ?>
                                                    <a href="#editar-<?= $item->getIdViatura() ?>" class="botaoAtualizar">Editar</a>
                                                <?php } ?>
                                                <?php if ($podeRemover) { ?>
                                                    <a href="#remover-<?= $item->getIdViatura() ?>" class="botaoRemover">Remover</a>
                                                <?php } ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        <?php if ($podeEditar) { ?>
            <?php foreach ($lista as $item) { ?>
                <!-- MODAL EDITAR -->
                <div class="modalFundo" id="editar-<?= $item->getIdViatura() ?>">
                    <a href="#" class="modalOverlay" aria-label="Fechar"></a>
                    <div class="modalCaixa">
                        <div class="modalTopo">
                            <h2 class="titulo">Editar viatura</h2>
                            <a href="#" class="modalFechar">x</a>
                        </div>
                        <form method="post">
                            <input type="hidden" name="id" value="<?= $item->getIdViatura() ?>">

                            <div class="campo">
                                <div class="campoTipo">
                                    <label class="label"><strong>Modelo</strong></label>
                                </div>
                                <select name="idModelo" class="input" required>
                                    <option value="">Seleccione o modelo</option>

                                    <?php $grupo = ''; //$grupo é uma variável que guarda o nome da marca que estamos a tratar naquele momento.

                                    foreach ($modelos as $o) {
                                        if ($grupo != $o->getNomeMarca()) {
                                            if ($grupo != '')
                                                 { echo '</optgroup>';
                                            }
                                            $grupo = $o->getNomeMarca();
                                            echo '<optgroup label="' . e($grupo) . '">';
                                        } ?>
                                        <option value="<?= $o->getIdModelo() ?>" <?= ($o->getIdModelo() == $item->getIdModelo()) ? 'selected' : '' ?>><?= e($o->getNomeModelo()) ?></option>
                                    <?php }
                                    if ($grupo != '') { echo '</optgroup>'; } ?>
                                </select>
                            </div>

                            <div class="linha">
                                <div class="campo campoFlex">
                                    <div class="campoTipo">
                                        <label class="label"><strong>Tipo de viatura</strong></label>
                                    </div>
                                    <select name="idTipo" class="input" required>
                                        <option value="">Seleccione o tipo de viatura</option>
                                        <?php foreach ($tipos as $o) { ?>
                                            <option value="<?= $o->getIdTipo() ?>" <?= ($o->getIdTipo() == $item->getIdTipo()) ? 'selected' : '' ?>><?= e($o->getNomeTipo()) ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="campo campoFlex">
                                    <div class="campoTipo">
                                        <label class="label"><strong>Ano de fabrico</strong></label>
                                    </div>
                                    <input type="number" name="anoFabrico" class="input" min="1900" max="<?= date('Y') ?>" step="1" value="<?= e($item->getAnoFabrico()) ?>" required>
                                </div>
                            </div>

                            <div class="linha">
                                <div class="campo campoFlex">
                                    <div class="campoTipo">
                                        <label class="label"><strong>Matrícula</strong></label>
                                    </div>
                                    <input type="text" name="matricula" class="input" maxlength="20" value="<?= e($item->getMatricula()) ?>" required>
                                </div>
                                <div class="campo campoFlex">
                                    <div class="campoTipo">
                                        <label class="label"><strong>Cilindrada (cm³)</strong></label>
                                    </div>
                                    <input type="number" name="cilindrada" class="input" min="50" max="20000" step="1" value="<?= e($item->getCilindrada()) ?>" required>
                                </div>
                            </div>

                            <div class="campo">
                                <div class="campoTipo">
                                    <label class="label"><strong>Número de chassi</strong></label>
                                </div>
                                <input type="text" name="numeroChassi" class="input" maxlength="30" value="<?= e($item->getNumeroChassi()) ?>" required>
                            </div>

                            <div class="campo">
                                <div class="campoTipo">
                                    <label class="label"><strong>Cor</strong></label>
                                </div>
                                <select name="idCor" class="input" required>
                                    <option value="">Seleccione a cor</option>
                                    <?php foreach ($cores as $o) { ?>
                                        <option value="<?= $o->getIdCor() ?>" <?= ($o->getIdCor() == $item->getIdCor()) ? 'selected' : '' ?>><?= e($o->getNomeCor()) ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="campo">
                                <div class="campoTipo">
                                    <label class="label"><strong>Preço (MT)</strong></label>
                                </div>
                                <input type="number" name="preco" class="input" min="0" step="0.01" placeholder="0.00" value="<?= e($item->getPreco()) ?>" required>
                            </div>

                            <div class="acao modalBotoes">
                                <a href="#" class="botao botaoCancelar">Cancelar</a>
                                <button type="submit" name="guardar" class="botao botaoAzul">Actualizar</button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>

        <?php if ($podeRemover) { ?>
            <?php foreach ($lista as $item) { ?>
                <!-- MODAL REMOVER (mesmos campos, só para consulta) -->
                <div class="modalFundo" id="remover-<?= $item->getIdViatura() ?>">
                    <a href="#" class="modalOverlay" aria-label="Fechar"></a>
                    <div class="modalCaixa">
                        <div class="modalTopo">
                            <h2 class="titulo">Remover viatura</h2>
                            <a href="#" class="modalFechar">x</a>
                        </div>
                        <form method="post">
                            <input type="hidden" name="id" value="<?= $item->getIdViatura() ?>">

                            <div class="campo">
                                <div class="campoTipo">
                                    <label class="label"><strong>Modelo</strong></label>
                                </div>
                                <select name="idModelo" class="input" required disabled>
                                    <option value="">Seleccione o modelo</option>
                                    <?php $grupo = ''; foreach ($modelos as $o) {
                                        if ($grupo != $o->getNomeMarca()) {
                                            if ($grupo != '') { echo '</optgroup>'; }
                                            $grupo = $o->getNomeMarca();
                                            echo '<optgroup label="' . e($grupo) . '">';
                                        } ?>
                                        <option value="<?= $o->getIdModelo() ?>" <?= ($o->getIdModelo() == $item->getIdModelo()) ? 'selected' : '' ?>><?= e($o->getNomeModelo()) ?></option>
                                    <?php }
                                    if ($grupo != '') { echo '</optgroup>'; } ?>
                                </select>
                            </div>

                            <div class="linha">
                                <div class="campo campoFlex">
                                    <div class="campoTipo">
                                        <label class="label"><strong>Tipo de viatura</strong></label>
                                    </div>
                                    <select name="idTipo" class="input" required disabled>
                                        <option value="">Seleccione o tipo de viatura</option>
                                        <?php foreach ($tipos as $o) { ?>
                                            <option value="<?= $o->getIdTipo() ?>" <?= ($o->getIdTipo() == $item->getIdTipo()) ? 'selected' : '' ?>><?= e($o->getNomeTipo()) ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="campo campoFlex">
                                    <div class="campoTipo">
                                        <label class="label"><strong>Ano de fabrico</strong></label>
                                    </div>
                                    <input type="number" name="anoFabrico" class="input" min="1900" max="<?= date('Y') ?>" step="1" value="<?= e($item->getAnoFabrico()) ?>" required disabled>
                                </div>
                            </div>

                            <div class="linha">
                                <div class="campo campoFlex">
                                    <div class="campoTipo">
                                        <label class="label"><strong>Matrícula</strong></label>
                                    </div>
                                    <input type="text" name="matricula" class="input" maxlength="20" value="<?= e($item->getMatricula()) ?>" required disabled>
                                </div>
                                <div class="campo campoFlex">
                                    <div class="campoTipo">
                                        <label class="label"><strong>Cilindrada (cm³)</strong></label>
                                    </div>
                                    <input type="number" name="cilindrada" class="input" min="50" max="20000" step="1" value="<?= e($item->getCilindrada()) ?>" required disabled>
                                </div>
                            </div>

                            <div class="campo">
                                <div class="campoTipo">
                                    <label class="label"><strong>Número de chassi</strong></label>
                                </div>
                                <input type="text" name="numeroChassi" class="input" maxlength="30" value="<?= e($item->getNumeroChassi()) ?>" required disabled>
                            </div>

                            <div class="campo">
                                <div class="campoTipo">
                                    <label class="label"><strong>Cor</strong></label>
                                </div>
                                <select name="idCor" class="input" required disabled>
                                    <option value="">Seleccione a cor</option>
                                    <?php foreach ($cores as $o) { ?>
                                        <option value="<?= $o->getIdCor() ?>" <?= ($o->getIdCor() == $item->getIdCor()) ? 'selected' : '' ?>><?= e($o->getNomeCor()) ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="campo">
                                <div class="campoTipo">
                                    <label class="label"><strong>Preço (MT)</strong></label>
                                </div>
                                <input type="number" name="preco" class="input" min="0" step="0.01" placeholder="0.00" value="<?= e($item->getPreco()) ?>" required disabled>
                            </div>

                            <p class="aviso">Tem a certeza que deseja remover este registo?</p>

                            <div class="acao modalBotoes">
                                <a href="#" class="botao botaoCancelar">Cancelar</a>
                                <button type="submit" name="remover" class="botao botaoVermelho">Remover</button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>

    </div>

</body>

</html>
