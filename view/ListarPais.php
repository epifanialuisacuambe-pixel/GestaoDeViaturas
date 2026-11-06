<?php
include_once __DIR__ . '/../controller/controllerLogin.php';
include_once __DIR__ . '/../controller/controllerPais.php';
include_once __DIR__ . '/../controller/controllerContinente.php';

exigirLogin();
exigirPermissao('pais:listar');

if (isset($_POST['remover']) && temPermissao('pais:remover')) {
    list($ok, $msg) = remocaoPais($_POST['id'] ?? 0);
    definirMensagem($msg, $ok ? 'sucesso' : 'erro');
    header('Location: ListarPais.php');
    exit;
}

if (isset($_POST['guardar']) && temPermissao('pais:actualizar')) {
    $obj = new Pais((int) ($_POST['id'] ?? 0), $_POST['nomePais'] ?? '', $_POST['idContinente'] ?? 0);
    list($ok, $msg) = edicaoPais($obj);
    definirMensagem($msg, $ok ? 'sucesso' : 'erro');
    header('Location: ListarPais.php');
    exit;
}

$pesquisa = trim($_GET['pesquisa'] ?? '');
$lista = $pesquisa != '' ? pesquisarPais($pesquisa) : listarPais();

$continentes = listarContinente();
$podeEditar = temPermissao('pais:actualizar');
$podeRemover = temPermissao('pais:remover');
$paginaActiva = 'pais';
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LISTAR PAÍSES</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>

<body>

    <?php include __DIR__ . '/partials/menu.php'; ?>

    <div class="mae">

        <div class="cabecalho">
            <div class="abas">
                <?php if (temPermissao('pais:criar')) { ?><a href="CadastrarPais.php" class="aba">CADASTRO DE PAÍS</a><?php } ?>
                <div class="aba abaAtiva">LISTA DE PAÍSES</div>
            </div>
        </div>

        <div class="dalheee">
            <div class="card">

                <div class="topo">
                    <h1 class="titulo"><strong>Lista de países</strong></h1>
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
                                <a href="ListarPais.php" class="botao botaoCancelar">Limpar</a>
                            </div>
                        <?php } ?>
                    </div>
                </form>

                <div class="tabela">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>País</th>
                                <th>Continente</th>
                                <th>Acções</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($lista) == 0) { ?>
                                <tr>
                                    <td colspan="4">SEM NENHUM PAÍS REGISTADO</td>
                                </tr>
                            <?php } else { ?>
                                <?php foreach ($lista as $item) { ?>
                                    <tr>
                                        <td><?= $item->getIdPais() ?></td>
                                        <td><?= e($item->getNomePais()) ?></td>
                                        <td><?= e($item->getNomeContinente()) ?></td>
                                        <td>
                                            <div class="acoes">
                                                <?php if ($podeEditar) { ?>
                                                    <a href="#editar-<?= $item->getIdPais() ?>" class="botaoAtualizar">Editar</a>
                                                <?php } ?>
                                                <?php if ($podeRemover) { ?>
                                                    <a href="#remover-<?= $item->getIdPais() ?>" class="botaoRemover">Remover</a>
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
                <div class="modalFundo" id="editar-<?= $item->getIdPais() ?>">
                    <a href="#" class="modalOverlay" aria-label="Fechar"></a>
                    <div class="modalCaixa">
                        <div class="modalTopo">
                            <h2 class="titulo">Editar país</h2>
                            <a href="#" class="modalFechar">x</a>
                        </div>
                        <form method="post">
                            <input type="hidden" name="id" value="<?= $item->getIdPais() ?>">

                            <div class="campo">
                                <div class="campoTipo">
                                    <label class="label"><strong>Continente</strong></label>
                                </div>
                                <select name="idContinente" class="input" required>
                                    <option value="">Seleccione o continente</option>
                                    <?php foreach ($continentes as $o) { ?>
                                        <option value="<?= $o->getIdContinente() ?>" <?= ($o->getIdContinente() == $item->getIdContinente()) ? 'selected' : '' ?>><?= e($o->getNomeContinente()) ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="campo">
                                <div class="campoTipo">
                                    <label class="label"><strong>Nome do país</strong></label>
                                </div>
                                <input type="text" name="nomePais" class="input" maxlength="80" value="<?= e($item->getNomePais()) ?>" required>
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
                <div class="modalFundo" id="remover-<?= $item->getIdPais() ?>">
                    <a href="#" class="modalOverlay" aria-label="Fechar"></a>
                    <div class="modalCaixa">
                        <div class="modalTopo">
                            <h2 class="titulo">Remover país</h2>
                            <a href="#" class="modalFechar">x</a>
                        </div>
                        <form method="post">
                            <input type="hidden" name="id" value="<?= $item->getIdPais() ?>">

                            <div class="campo">
                                <div class="campoTipo">
                                    <label class="label"><strong>Continente</strong></label>
                                </div>
                                <select name="idContinente" class="input" required disabled>
                                    <option value="">Seleccione o continente</option>
                                    <?php foreach ($continentes as $o) { ?>
                                        <option value="<?= $o->getIdContinente() ?>" <?= ($o->getIdContinente() == $item->getIdContinente()) ? 'selected' : '' ?>><?= e($o->getNomeContinente()) ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="campo">
                                <div class="campoTipo">
                                    <label class="label"><strong>Nome do país</strong></label>
                                </div>
                                <input type="text" name="nomePais" class="input" maxlength="80" value="<?= e($item->getNomePais()) ?>" required disabled>
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
