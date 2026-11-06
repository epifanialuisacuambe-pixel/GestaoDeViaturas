<?php
include_once __DIR__ . '/../controller/controllerLogin.php';
include_once __DIR__ . '/../controller/controllerTipoViatura.php';

exigirLogin();
exigirPermissao('tipoviatura:listar');

if (isset($_POST['remover']) && temPermissao('tipoviatura:remover')) {
    list($ok, $msg) = remocaoTipoViatura($_POST['id'] ?? 0);
    definirMensagem($msg, $ok ? 'sucesso' : 'erro');
    header('Location: ListarTipoViatura.php');
    exit;
}

if (isset($_POST['guardar']) && temPermissao('tipoviatura:actualizar')) {
    $obj = new TipoViatura((int) ($_POST['id'] ?? 0), $_POST['nomeTipo'] ?? '');
    list($ok, $msg) = edicaoTipoViatura($obj);
    definirMensagem($msg, $ok ? 'sucesso' : 'erro');
    header('Location: ListarTipoViatura.php');
    exit;
}

$pesquisa = trim($_GET['pesquisa'] ?? '');
$lista = $pesquisa != '' ? pesquisarTipoViatura($pesquisa) : listarTipoViatura();


$podeEditar = temPermissao('tipoviatura:actualizar');
$podeRemover = temPermissao('tipoviatura:remover');
$paginaActiva = 'tipoviatura';
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LISTAR TIPOS DE VIATURA</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>

<body>

    <?php include __DIR__ . '/partials/menu.php'; ?>

    <div class="mae">

        <div class="cabecalho">
            <div class="abas">
                <?php if (temPermissao('tipoviatura:criar')) { ?><a href="CadastrarTipoViatura.php" class="aba">CADASTRO DE TIPO DE VIATURA</a><?php } ?>
                <div class="aba abaAtiva">LISTA DE TIPOS DE VIATURA</div>
            </div>
        </div>

        <div class="dalheee">
            <div class="card">

                <div class="topo">
                    <h1 class="titulo"><strong>Lista de tipos de viatura</strong></h1>
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
                                <a href="ListarTipoViatura.php" class="botao botaoCancelar">Limpar</a>
                            </div>
                        <?php } ?>
                    </div>
                </form>

                <div class="tabela">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tipo de viatura</th>
                                <th>Acções</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($lista) == 0) { ?>
                                <tr>
                                    <td colspan="3">SEM NENHUM TIPO DE VIATURA REGISTADO</td>
                                </tr>
                            <?php } else { ?>
                                <?php foreach ($lista as $item) { ?>
                                    <tr>
                                        <td><?= $item->getIdTipo() ?></td>
                                        <td><?= e($item->getNomeTipo()) ?></td>
                                        <td>
                                            <div class="acoes">
                                                <?php if ($podeEditar) { ?>
                                                    <a href="#editar-<?= $item->getIdTipo() ?>" class="botaoAtualizar">Editar</a>
                                                <?php } ?>
                                                <?php if ($podeRemover) { ?>
                                                    <a href="#remover-<?= $item->getIdTipo() ?>" class="botaoRemover">Remover</a>
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
                <div class="modalFundo" id="editar-<?= $item->getIdTipo() ?>">
                    <a href="#" class="modalOverlay" aria-label="Fechar"></a>
                    <div class="modalCaixa">
                        <div class="modalTopo">
                            <h2 class="titulo">Editar tipo de viatura</h2>
                            <a href="#" class="modalFechar">x</a>
                        </div>
                        <form method="post">
                            <input type="hidden" name="id" value="<?= $item->getIdTipo() ?>">

                            <div class="campo">
                                <div class="campoTipo">
                                    <label class="label"><strong>Nome do tipo de viatura</strong></label>
                                </div>
                                <input type="text" name="nomeTipo" class="input" maxlength="60" value="<?= e($item->getNomeTipo()) ?>" required>
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
                <div class="modalFundo" id="remover-<?= $item->getIdTipo() ?>">
                    <a href="#" class="modalOverlay" aria-label="Fechar"></a>
                    <div class="modalCaixa">
                        <div class="modalTopo">
                            <h2 class="titulo">Remover tipo de viatura</h2>
                            <a href="#" class="modalFechar">x</a>
                        </div>
                        <form method="post">
                            <input type="hidden" name="id" value="<?= $item->getIdTipo() ?>">

                            <div class="campo">
                                <div class="campoTipo">
                                    <label class="label"><strong>Nome do tipo de viatura</strong></label>
                                </div>
                                <input type="text" name="nomeTipo" class="input" maxlength="60" value="<?= e($item->getNomeTipo()) ?>" required disabled>
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
