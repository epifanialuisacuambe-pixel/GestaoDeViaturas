<?php
include_once __DIR__ . '/../controller/controllerLogin.php';
include_once __DIR__ . '/../controller/controllerFabricante.php';
include_once __DIR__ . '/../controller/controllerPais.php';
include_once __DIR__ . '/../controller/controllerContinente.php';

exigirLogin();
exigirPermissao('fabricante:listar');

if (isset($_POST['remover']) && temPermissao('fabricante:remover')) {
    list($ok, $msg) = remocaoFabricante($_POST['id'] ?? 0);
    definirMensagem($msg, $ok ? 'sucesso' : 'erro');
    header('Location: ListarFabricante.php');
    exit;
}

if (isset($_POST['guardar']) && temPermissao('fabricante:actualizar')) {
    $obj = new Fabricante((int) ($_POST['id'] ?? 0), $_POST['nomeFabricante'] ?? '', $_POST['idPais'] ?? 0);
    list($ok, $msg) = edicaoFabricante($obj);
    definirMensagem($msg, $ok ? 'sucesso' : 'erro');
    header('Location: ListarFabricante.php');
    exit;
}

$pesquisa = trim($_GET['pesquisa'] ?? '');
$lista = $pesquisa != '' ? pesquisarFabricante($pesquisa) : listarFabricante();

$paises = listarPais();
$podeEditar = temPermissao('fabricante:actualizar');
$podeRemover = temPermissao('fabricante:remover');
$paginaActiva = 'fabricante';
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LISTAR FABRICANTES</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>

<body>

    <?php include __DIR__ . '/partials/menu.php'; ?>

    <div class="mae">

        <div class="cabecalho">
            <div class="abas">
                <?php if (temPermissao('fabricante:criar')) { ?><a href="CadastrarFabricante.php" class="aba">CADASTRO DE FABRICANTE</a><?php } ?>
                <div class="aba abaAtiva">LISTA DE FABRICANTES</div>
            </div>
        </div>

        <div class="dalheee">
            <div class="card">

                <div class="topo">
                    <h1 class="titulo"><strong>Lista de fabricantes</strong></h1>
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
                                <a href="ListarFabricante.php" class="botao botaoCancelar">Limpar</a>
                            </div>
                        <?php } ?>
                    </div>
                </form>

                <div class="tabela">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Fabricante</th>
                                <th>País</th>
                                <th>Continente</th>
                                <th>Acções</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($lista) == 0) { ?>
                                <tr>
                                    <td colspan="5">SEM NENHUM FABRICANTE REGISTADO</td>
                                </tr>
                            <?php } else { ?>
                                <?php foreach ($lista as $item) { ?>
                                    <tr>
                                        <td><?= $item->getIdFabricante() ?></td>
                                        <td><?= e($item->getNomeFabricante()) ?></td>
                                        <td><?= e($item->getNomePais()) ?></td>
                                        <td><?= e($item->getNomeContinente()) ?></td>
                                        <td>
                                            <div class="acoes">
                                                <?php if ($podeEditar) { ?>
                                                    <a href="#editar-<?= $item->getIdFabricante() ?>" class="botaoAtualizar">Editar</a>
                                                <?php } ?>
                                                <?php if ($podeRemover) { ?>
                                                    <a href="#remover-<?= $item->getIdFabricante() ?>" class="botaoRemover">Remover</a>
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
                <div class="modalFundo" id="editar-<?= $item->getIdFabricante() ?>">
                    <a href="#" class="modalOverlay" aria-label="Fechar"></a>
                    <div class="modalCaixa">
                        <div class="modalTopo">
                            <h2 class="titulo">Editar fabricante</h2>
                            <a href="#" class="modalFechar">x</a>
                        </div>
                        <form method="post">
                            <input type="hidden" name="id" value="<?= $item->getIdFabricante() ?>">

                            <div class="campo">
                                <div class="campoTipo">
                                    <label class="label"><strong>País</strong></label>
                                </div>
                                <select name="idPais" class="input" required>
                                    <option value="">Seleccione o país</option>
                                    <?php $grupo = ''; foreach ($paises as $o) {
                                        if ($grupo != $o->getNomeContinente()) {
                                            if ($grupo != '') { echo '</optgroup>'; }
                                            $grupo = $o->getNomeContinente();
                                            echo '<optgroup label="' . e($grupo) . '">';
                                        } ?>
                                        <option value="<?= $o->getIdPais() ?>" <?= ($o->getIdPais() == $item->getIdPais()) ? 'selected' : '' ?>><?= e($o->getNomePais()) ?></option>
                                    <?php }
                                    if ($grupo != '') { echo '</optgroup>'; } ?>
                                </select>
                            </div>

                            <div class="campo">
                                <div class="campoTipo">
                                    <label class="label"><strong>Nome do fabricante</strong></label>
                                </div>
                                <input type="text" name="nomeFabricante" class="input" maxlength="80" value="<?= e($item->getNomeFabricante()) ?>" required>
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
                <div class="modalFundo" id="remover-<?= $item->getIdFabricante() ?>">
                    <a href="#" class="modalOverlay" aria-label="Fechar"></a>
                    <div class="modalCaixa">
                        <div class="modalTopo">
                            <h2 class="titulo">Remover fabricante</h2>
                            <a href="#" class="modalFechar">x</a>
                        </div>
                        <form method="post">
                            <input type="hidden" name="id" value="<?= $item->getIdFabricante() ?>">

                            <div class="campo">
                                <div class="campoTipo">
                                    <label class="label"><strong>País</strong></label>
                                </div>
                                <select name="idPais" class="input" required disabled>
                                    <option value="">Seleccione o país</option>
                                    <?php $grupo = ''; foreach ($paises as $o) {
                                        if ($grupo != $o->getNomeContinente()) {
                                            if ($grupo != '') { echo '</optgroup>'; }
                                            $grupo = $o->getNomeContinente();
                                            echo '<optgroup label="' . e($grupo) . '">';
                                        } ?>
                                        <option value="<?= $o->getIdPais() ?>" <?= ($o->getIdPais() == $item->getIdPais()) ? 'selected' : '' ?>><?= e($o->getNomePais()) ?></option>
                                    <?php }
                                    if ($grupo != '') { echo '</optgroup>'; } ?>
                                </select>
                            </div>

                            <div class="campo">
                                <div class="campoTipo">
                                    <label class="label"><strong>Nome do fabricante</strong></label>
                                </div>
                                <input type="text" name="nomeFabricante" class="input" maxlength="80" value="<?= e($item->getNomeFabricante()) ?>" required disabled>
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
