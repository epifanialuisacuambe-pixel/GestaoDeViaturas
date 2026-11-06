<?php
include_once __DIR__ . '/../controller/controllerLogin.php';
include_once __DIR__ . '/../controller/controllerCor.php';

exigirLogin();
exigirPermissao('cor:listar');

if (isset($_POST['remover']) && temPermissao('cor:remover')) {
    list($ok, $msg) = remocaoCor($_POST['id'] ?? 0);
    definirMensagem($msg, $ok ? 'sucesso' : 'erro');
    header('Location: ListarCor.php');
    exit;
}

// "Consultar cor" no pop-up de edição: volta à lista com o pop-up aberto e os campos preenchidos
if (isset($_POST['consultar']) && temPermissao('cor:actualizar')) {
    $id = (int) ($_POST['id'] ?? 0);
    $escolhida = strtolower(trim($_POST['corPaleta'] ?? ''));
    if (hexValido($escolhida)) {
        header('Location: ListarCor.php?consulta=' . $id . '&hex=' . substr($escolhida, 1) . '#editar-' . $id);
    } else {
        definirMensagem('Escolha uma cor na paleta', 'erro');
        header('Location: ListarCor.php');
    }
    exit;
}

if (isset($_POST['guardar']) && temPermissao('cor:actualizar')) {
    $escolhida = strtolower(trim($_POST['corPaleta'] ?? ''));
    $hexConsultado = strtolower(trim($_POST['hexCor'] ?? ''));

    if ($hexConsultado == '' || $hexConsultado != $escolhida) {
        definirMensagem('A cor da paleta mudou. Edite outra vez e carregue em "Consultar cor" antes de actualizar', 'erro');
    } else {
        $obj = new Cor((int) ($_POST['id'] ?? 0), nomeDaCor($hexConsultado), $hexConsultado);
        list($ok, $msg) = edicaoCor($obj);
        definirMensagem($msg, $ok ? 'sucesso' : 'erro');
    }
    header('Location: ListarCor.php');
    exit;
}

// Cor consultada num pop-up de edição (vem no endereço depois do "Consultar cor")
$consultaId = (int) ($_GET['consulta'] ?? 0);
$consultaHex = '#' . strtolower(ltrim($_GET['hex'] ?? '', '#'));
if (!hexValido($consultaHex)) {
    $consultaId = 0;
}

$pesquisa = trim($_GET['pesquisa'] ?? '');
$lista = $pesquisa != '' ? pesquisarCor($pesquisa) : listarCor();
$podeEditar = temPermissao('cor:actualizar');
$podeRemover = temPermissao('cor:remover');
$paginaActiva = 'cor';
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LISTAR CORES</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>

<body>

    <?php include __DIR__ . '/partials/menu.php'; ?>

    <div class="mae">

        <div class="cabecalho">
            <div class="abas">
                <?php if (temPermissao('cor:criar')) { ?><a href="CadastrarCor.php" class="aba">CADASTRO DE COR</a><?php } ?>
                <div class="aba abaAtiva">LISTA DE CORES</div>
            </div>
        </div>

        <div class="dalheee">
            <div class="card">

                <div class="topo">
                    <h1 class="titulo"><strong>Lista de cores</strong></h1>
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
                                <a href="ListarCor.php" class="botao botaoCancelar">Limpar</a>
                            </div>
                        <?php } ?>
                    </div>
                </form>

                <div class="tabela">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cor</th>
                                <th>Código hexadecimal</th>
                                <th>Acções</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($lista) == 0) { ?>
                                <tr>
                                    <td colspan="4">SEM NENHUMA COR REGISTADA</td>
                                </tr>
                            <?php } else { ?>
                                <?php foreach ($lista as $item) { ?>
                                    <tr>
                                        <td><?= $item->getIdCor() ?></td>
                                        <td><span class="amostra" style="background: <?= e($item->getHexCor()) ?>"></span><?= e($item->getNomeCor()) ?></td>
                                        <td><?= e($item->getHexCor()) ?></td>
                                        <td>
                                            <div class="acoes">
                                                <?php if ($podeEditar) { ?>
                                                    <a href="#editar-<?= $item->getIdCor() ?>" class="botaoAtualizar">Editar</a>
                                                <?php } ?>
                                                <?php if ($podeRemover) { ?>
                                                    <a href="#remover-<?= $item->getIdCor() ?>" class="botaoRemover">Remover</a>
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
            <?php foreach ($lista as $item) {
                // Se acabou de consultar uma cor nova neste pop-up, mostra essa; senão a cor guardada
                $hexItem = ($consultaId == $item->getIdCor()) ? $consultaHex : $item->getHexCor();
                $nomeItem = ($consultaId == $item->getIdCor()) ? nomeDaCor($consultaHex) : $item->getNomeCor();
            ?>
                <!-- MODAL EDITAR -->
                <div class="modalFundo" id="editar-<?= $item->getIdCor() ?>">
                    <a href="#" class="modalOverlay" aria-label="Fechar"></a>
                    <div class="modalCaixa">
                        <div class="modalTopo">
                            <h2 class="titulo">Editar cor</h2>
                            <a href="#" class="modalFechar">x</a>
                        </div>
                        <form method="post">
                            <input type="hidden" name="id" value="<?= $item->getIdCor() ?>">

                            <div class="linha linhaFiltro">
                                <div class="campo">
                                    <button type="submit" name="consultar" value="1" class="botao botaoAzul">Consultar cor</button>
                                </div>
                                <div class="campo campoFlex">
                                    <div class="campoTipo">
                                        <label class="label"><strong>Cor (clique na caixa para abrir a paleta)</strong></label>
                                    </div>
                                    <input type="color" name="corPaleta" class="paleta" value="<?= e($hexItem) ?>">
                                </div>
                            </div>

                            <div class="linha">
                                <div class="campo campoFlex">
                                    <div class="campoTipo">
                                        <label class="label"><strong>Descrição da cor</strong></label>
                                    </div>
                                    <input type="text" class="input" value="<?= e($nomeItem) ?>" disabled>
                                </div>
                                <div class="campo campoFlex">
                                    <div class="campoTipo">
                                        <label class="label"><strong>Código hexadecimal</strong></label>
                                    </div>
                                    <input type="text" class="input" value="<?= e($hexItem) ?>" disabled>
                                    <input type="hidden" name="hexCor" value="<?= e($hexItem) ?>">
                                </div>
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
                <div class="modalFundo" id="remover-<?= $item->getIdCor() ?>">
                    <a href="#" class="modalOverlay" aria-label="Fechar"></a>
                    <div class="modalCaixa">
                        <div class="modalTopo">
                            <h2 class="titulo">Remover cor</h2>
                            <a href="#" class="modalFechar">x</a>
                        </div>
                        <form method="post">
                            <input type="hidden" name="id" value="<?= $item->getIdCor() ?>">

                            <div class="linha linhaFiltro">
                                <div class="campo campoFlex">
                                    <div class="campoTipo">
                                        <label class="label"><strong>Cor</strong></label>
                                    </div>
                                    <input type="color" class="paleta" value="<?= e($item->getHexCor()) ?>" disabled>
                                </div>
                            </div>

                            <div class="linha">
                                <div class="campo campoFlex">
                                    <div class="campoTipo">
                                        <label class="label"><strong>Descrição da cor</strong></label>
                                    </div>
                                    <input type="text" class="input" value="<?= e($item->getNomeCor()) ?>" disabled>
                                </div>
                                <div class="campo campoFlex">
                                    <div class="campoTipo">
                                        <label class="label"><strong>Código hexadecimal</strong></label>
                                    </div>
                                    <input type="text" class="input" value="<?= e($item->getHexCor()) ?>" disabled>
                                </div>
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
