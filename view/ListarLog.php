<?php
include_once __DIR__ . '/../controller/controllerLogin.php';
include_once __DIR__ . '/../controller/controllerLog.php';

exigirLogin();
exigirPermissao('log:listar');

$pesquisa = trim($_GET['pesquisa'] ?? '');
$logs = $pesquisa != '' ? pesquisarLog($pesquisa) : listaDeLogs();
$paginaActiva = 'log';
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LISTAR LOGS</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>

<body>

    <?php include __DIR__ . '/partials/menu.php'; ?>

    <div class="mae">

        <div class="cabecalho">
            <div class="abas">
                <div class="aba abaAtiva">LISTA DE LOGS</div>
            </div>
        </div>

        <div class="dalheee">
            <div class="card">

                <div class="topo">
                    <h1 class="titulo"><strong>Lista de logs</strong></h1>
                </div>

                <p class="totalRegistos">Registos: <strong><?= count($logs) ?></strong></p>

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
                                <a href="ListarLog.php" class="botao botaoCancelar">Limpar</a>
                            </div>
                        <?php } ?>
                    </div>
                </form>

                <div class="tabela">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Nome</th>
                                <th>Perfil</th>
                                <th>Acção</th>
                                <th>Data da acção</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($logs) == 0) { ?>
                                <tr>
                                    <td colspan="6">SEM NENHUM LOG REGISTADO</td>
                                </tr>
                            <?php } else { ?>
                                <?php foreach ($logs as $log) { ?>
                                    <tr>
                                        <td><?= $log->getIdLog() ?></td>
                                        <td><?= e($log->getUsername()) ?></td>
                                        <td><?= e($log->getNome()) ?></td>
                                        <td><?= e($log->getPerfil()) ?></td>
                                        <td><?= e($log->getAccao()) ?></td>
                                        <td><?= e($log->getDataAccao()) ?></td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>

</body>

</html>
