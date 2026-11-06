<?php
include_once __DIR__ . '/../controller/controllerLogin.php';
include_once __DIR__ . '/../controller/controllerModelo.php';
include_once __DIR__ . '/../controller/controllerMarca.php';

exigirLogin();
exigirPermissao('modelo:listar');

if (isset($_POST['remover']) && temPermissao('modelo:remover')) {
    list($ok, $msg) = remocaoModelo($_POST['id'] ?? 0);
    definirMensagem($msg, $ok ? 'sucesso' : 'erro');
    header('Location: ListarModelo.php');
    exit;
}

if (isset($_POST['guardar']) && temPermissao('modelo:actualizar')) {
    $obj = new Modelo((int) ($_POST['id'] ?? 0), $_POST['nomeModelo'] ?? '', $_POST['idMarca'] ?? 0);
    list($ok, $msg) = edicaoModelo($obj);
    definirMensagem($msg, $ok ? 'sucesso' : 'erro');
    header('Location: ListarModelo.php');
    exit;
}

$pesquisa = trim($_GET['pesquisa'] ?? '');
$lista = $pesquisa != '' ? pesquisarModelo($pesquisa) : listarModelo();

$marcas = listarMarca();
$podeEditar = temPermissao('modelo:actualizar');
$podeRemover = temPermissao('modelo:remover');
$paginaActiva = 'modelo';
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LISTAR MODELOS</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>

<body>

    <?php include __DIR__ . '/partials/menu.php'; ?>

    <div class="mae">

        <div class="cabecalho">
            <div class="abas">
                <?php if (temPermissao('modelo:criar')) { ?><a href="CadastrarModelo.php" class="aba">CADASTRO DE MODELO</a><?php } ?>
                <div class="aba abaAtiva">LISTA DE MODELOS</div>
            </div>
        </div>

        <div class="dalheee">
            <div class="card">

                <div class="topo">
                    <h1 class="titulo"><strong>Lista de modelos</strong></h1>
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
                                <a href="ListarModelo.php" class="botao botaoCancelar">Limpar</a>
                            </div>
                        <?php } ?>
                    </div>
                </form>

                <div class="tabela">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Modelo</th>
                                <th>Marca</th>
                                <th>Fabricante</th>
                                <th>Acções</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($lista) == 0) { ?>
                                <tr>
                                    <td colspan="5">SEM NENHUM MODELO REGISTADO</td>
                                </tr>
                            <?php } else { ?>
                                <?php foreach ($lista as $item) { ?>
                                    <tr>
                                        <td><?= $item->getIdModelo() ?></td>
                                        <td><?= e($item->getNomeModelo()) ?></td>
                                        <td><?= e($item->getNomeMarca()) ?></td>
                                        <td><?= e($item->getNomeFabricante()) ?></td>
                                        <td>
                                            <div class="acoes">
                                                <?php if ($podeEditar) { ?>
                                                    <a href="#editar-<?= $item->getIdModelo() ?>" class="botaoAtualizar">Editar</a>
                                                <?php } ?>
                                                <?php if ($podeRemover) { ?>
                                                    <a href="#remover-<?= $item->getIdModelo() ?>" class="botaoRemover">Remover</a>
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
                <div class="modalFundo" id="editar-<?= $item->getIdModelo() ?>">
                    <a href="#" class="modalOverlay" aria-label="Fechar"></a>
                    <div class="modalCaixa">
                        <div class="modalTopo">
                            <h2 class="titulo">Editar modelo</h2>
                            <a href="#" class="modalFechar">x</a>
                        </div>
                        <form method="post">
                            <input type="hidden" name="id" value="<?= $item->getIdModelo() ?>">

                            <div class="campo">
                                <div class="campoTipo">
                                    <label class="label"><strong>Marca</strong></label>
                                </div>
                                <select name="idMarca" class="input" required>
                                    <option value="">Seleccione a marca</option>
                                    <?php foreach ($marcas as $o) { ?>
                                        <option value="<?= $o->getIdMarca() ?>" <?= ($o->getIdMarca() == $item->getIdMarca()) ? 'selected' : '' ?>><?= e($o->getNomeMarca()) ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="campo">
                                <div class="campoTipo">
                                    <label class="label"><strong>Nome do modelo</strong></label>
                                </div>
                                <input type="text" name="nomeModelo" class="input" maxlength="80" value="<?= e($item->getNomeModelo()) ?>" required>
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
                <div class="modalFundo" id="remover-<?= $item->getIdModelo() ?>">
                    <a href="#" class="modalOverlay" aria-label="Fechar"></a>
                    <div class="modalCaixa">
                        <div class="modalTopo">
                            <h2 class="titulo">Remover modelo</h2>
                            <a href="#" class="modalFechar">x</a>
                        </div>
                        <form method="post">
                            <input type="hidden" name="id" value="<?= $item->getIdModelo() ?>">

                            <div class="campo">
                                <div class="campoTipo">
                                    <label class="label"><strong>Marca</strong></label>
                                </div>
                                <select name="idMarca" class="input" required disabled>
                                    <option value="">Seleccione a marca</option>
                                    <?php foreach ($marcas as $o) { ?>
                                        <option value="<?= $o->getIdMarca() ?>" <?= ($o->getIdMarca() == $item->getIdMarca()) ? 'selected' : '' ?>><?= e($o->getNomeMarca()) ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="campo">
                                <div class="campoTipo">
                                    <label class="label"><strong>Nome do modelo</strong></label>
                                </div>
                                <input type="text" name="nomeModelo" class="input" maxlength="80" value="<?= e($item->getNomeModelo()) ?>" required disabled>
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
