<?php
include_once __DIR__ . '/../controller/controllerLogin.php';
include_once __DIR__ . '/../controller/controllerMarca.php';
include_once __DIR__ . '/../controller/controllerFabricante.php';

exigirLogin();
exigirPermissao('marca:listar');

if (isset($_POST['remover']) && temPermissao('marca:remover')) {
    list($ok, $msg) = remocaoMarca($_POST['id'] ?? 0);
    definirMensagem($msg, $ok ? 'sucesso' : 'erro');
    header('Location: ListarMarca.php');
    exit;
}

if (isset($_POST['guardar']) && temPermissao('marca:actualizar')) {
    $obj = new Marca((int) ($_POST['id'] ?? 0), $_POST['nomeMarca'] ?? '', $_POST['idFabricante'] ?? 0);
    list($ok, $msg) = edicaoMarca($obj);
    definirMensagem($msg, $ok ? 'sucesso' : 'erro');
    header('Location: ListarMarca.php');
    exit;
}

$pesquisa = trim($_GET['pesquisa'] ?? '');
$lista = $pesquisa != '' ? pesquisarMarca($pesquisa) : listarMarca();

$fabricantes = listarFabricante();
$podeEditar = temPermissao('marca:actualizar');
$podeRemover = temPermissao('marca:remover');
$paginaActiva = 'marca';
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LISTAR MARCAS</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>

<body>

    <?php include __DIR__ . '/partials/menu.php'; ?>

    <div class="mae">

        <div class="cabecalho">
            <div class="abas">
                <?php if (temPermissao('marca:criar')) { ?><a href="CadastrarMarca.php" class="aba">CADASTRO DE MARCA</a><?php } ?>
                <div class="aba abaAtiva">LISTA DE MARCAS</div>
            </div>
        </div>

        <div class="dalheee">
            <div class="card">

                <div class="topo">
                    <h1 class="titulo"><strong>Lista de marcas</strong></h1>
                </div>

                <?php mostrarMensagem(); ?>
                <p class="totalRegistos">Registos: <strong><?= count($lista) ?></strong></p>

                <form method="get">
                    <div class="linha">s
                        <div class="campo campoBusca">
                            <input type="text" name="pesquisa" class="input" placeholder="Pesquisar" value="<?= e($pesquisa) ?>">
                        </div>
                        <div class="campo">
                            <button type="submit" class="botao ">Pesquisar</button>
                        </div>
                        <?php if ($pesquisa != '') { ?>
                            <div class="campo">
                                <a href="ListarMarca.php" class="botao botaoCancelar">Limpar</a>
                            </div>
                        <?php } ?>
                    </div>
                </form>

                <div class="tabela">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Marca</th>
                                <th>Fabricante</th>
                                <th>Acções</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($lista) == 0) { ?>
                                <tr>
                                    <td colspan="4">SEM NENHUMA MARCA REGISTADA</td>
                                </tr>
                            <?php } else { ?>
                                <?php foreach ($lista as $item) { ?>
                                    <tr>
                                        <td><?= $item->getIdMarca() ?></td>
                                        <td><?= e($item->getNomeMarca()) ?></td>
                                        <td><?= e($item->getNomeFabricante()) ?></td>
                                        <td>
                                            <div class="acoes">
                                                <?php if ($podeEditar) { ?>
                                                    <a href="#editar-<?= $item->getIdMarca() ?>" class="botaoAtualizar">Editar</a>
                                                <?php } ?>
                                                <?php if ($podeRemover) { ?>
                                                    <a href="#remover-<?= $item->getIdMarca() ?>" class="botaoRemover">Remover</a>
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
                <div class="modalFundo" id="editar-<?= $item->getIdMarca() ?>">
                    <a href="#" class="modalOverlay" aria-label="Fechar"></a>
                    <div class="modalCaixa">
                        <div class="modalTopo">
                            <h2 class="titulo">Editar marca</h2>
                            <a href="#" class="modalFechar">x</a>
                        </div>
                        <form method="post">
                            <input type="hidden" name="id" value="<?= $item->getIdMarca() ?>">

                            <div class="campo">
                                <div class="campoTipo">
                                    <label class="label"><strong>Fabricante</strong></label>
                                </div>
                                <select name="idFabricante" class="input" required>
                                    <option value="">Seleccione o fabricante</option>
                                    <?php foreach ($fabricantes as $o) { ?>
                                        <option value="<?= $o->getIdFabricante() ?>" <?= ($o->getIdFabricante() == $item->getIdFabricante()) ? 'selected' : '' ?>><?= e($o->getNomeFabricante()) ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="campo">
                                <div class="campoTipo">
                                    <label class="label"><strong>Nome da marca</strong></label>
                                </div>
                                <input type="text" name="nomeMarca" class="input" maxlength="80" value="<?= e($item->getNomeMarca()) ?>" required>
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
                <div class="modalFundo" id="remover-<?= $item->getIdMarca() ?>">
                    <a href="#" class="modalOverlay" aria-label="Fechar"></a>
                    <div class="modalCaixa">
                        <div class="modalTopo">
                            <h2 class="titulo">Remover marca</h2>
                            <a href="#" class="modalFechar">x</a>
                        </div>
                        <form method="post">
                            <input type="hidden" name="id" value="<?= $item->getIdMarca() ?>">

                            <div class="campo">
                                <div class="campoTipo">
                                    <label class="label"><strong>Fabricante</strong></label>
                                </div>
                                <select name="idFabricante" class="input" required disabled>
                                    <option value="">Seleccione o fabricante</option>
                                    <?php foreach ($fabricantes as $o) { ?>
                                        <option value="<?= $o->getIdFabricante() ?>" <?= ($o->getIdFabricante() == $item->getIdFabricante()) ? 'selected' : '' ?>><?= e($o->getNomeFabricante()) ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="campo">
                                <div class="campoTipo">
                                    <label class="label"><strong>Nome da marca</strong></label>
                                </div>
                                <input type="text" name="nomeMarca" class="input" maxlength="80" value="<?= e($item->getNomeMarca()) ?>" required disabled>
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
