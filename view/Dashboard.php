<?php
include_once __DIR__ . '/../controller/controllerLogin.php';
include_once __DIR__ . '/../controller/controllerDashboard.php';

exigirLogin();

$totais = dashboardTotais();
$entidades = entidades();
$ultimasViaturas = temPermissao('viatura:listar') ? dashboardUltimasViaturas(5) : array();
$paginaActiva = 'dashboard';
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>INÍCIO</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>

<body>

    <?php include __DIR__ . '/partials/menu.php'; ?>

    <div class="mae">

<div class="topoDashboard">
    <h1>Olá, <?= e($_SESSION['nome'] ?? '') ?> <?= e($_SESSION['apelido'] ?? '') ?></h1>

    <p>Perfil: <?= e($_SESSION['perfil'] ?? '') ?></p>

    <p class="dataDashboard"><span>📅</span><?= date('d/m/Y') ?></p>
</div>

        <?php mostrarMensagem(); ?>

        <div class="dashboard-conteudo">

            <?php if (temPermissao('viatura:listar')) { ?>
                <div class="dashboard">
                    <a class="quadrado" href="ListarViatura.php">
                        <p class="legenda">Viaturas cadastradas</p>
                        <p class="numero"><?= contarRegistos('viatura') ?></p>
                    </a>
                </div>
            <?php } ?>

        </div>

        <?php if (count($ultimasViaturas) > 0) { ?>
            <div class="card cardDashboard">
                <div class="topo">
                    <h1 class="titulo"><strong>Últimas viaturas cadastradas</strong></h1>
                </div>

                <div class="tabela">
                    <table>
                        <thead>
                            <tr>
                                <th>Matrícula</th>
                                <th>Marca</th>
                                <th>Modelo</th>
                                <th>Cor</th>
                                <th>Ano de fabrico</th>
                                <th>Tempo de existência</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($ultimasViaturas as $v) { ?>
                                <tr>
                                    <td><?= e($v->getMatricula()) ?></td>
                                    <td><?= e($v->getNomeMarca()) ?></td>
                                    <td><?= e($v->getNomeModelo()) ?></td>
                                    <td><?= e($v->getNomeCor()) ?></td>
                                    <td><?= e($v->getAnoFabrico()) ?></td>
                                    <td><?= e($v->getTempoExistencia()) ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php } ?>

    </div>

</body>
</html>
