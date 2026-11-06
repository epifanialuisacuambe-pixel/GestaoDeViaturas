<?php
include_once __DIR__ . '/../controller/controllerLogin.php';
include_once __DIR__ . '/../controller/controllerPerfil.php';

exigirLogin();
exigirAdministrador();

$mensagem = '';
$tipoMensagem = '';

// Criar um perfil novo
if (isset($_POST['guardar'])) {
    $perfilNovo = new Perfil(null, $_POST['nomePerfil'] ?? '');
    list($ok, $mensagem) = criacaoPerfil($perfilNovo, $_POST['permissoes'] ?? array());
    $tipoMensagem = $ok ? 'sucesso' : 'erro';
    if ($ok) {
        $_POST = array();
    }
}

// Editar um perfil (nome + permissões)
if (isset($_POST['actualizarPerfil'])) {
    $perfilEditado = new Perfil((int) ($_POST['idPerfil'] ?? 0), $_POST['nomePerfil'] ?? '');
    list($ok, $msg) = editarPerfil($perfilEditado, $_POST['permissoes'] ?? array());
    definirMensagem($msg, $ok ? 'sucesso' : 'erro');
    header('Location: Perfil.php');
    exit;
}

$perfis = listarPerfil();
$permissoes = listarPermissoesPorEntidade();
$todosCodigos = array();
foreach ($permissoes as $acoes) {
    foreach ($acoes as $codigo) {
        array_push($todosCodigos, (int) $codigo);
    }
}
$totalPermissoes = count($todosCodigos);
$paginaActiva = 'perfil';
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GERIR PERFIS</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>

<body>

    <?php include __DIR__ . '/partials/menu.php'; ?>

    <div class="mae">

        <div class="cabecalho">
            <div class="abas">
                <div class="aba abaAtiva">GESTÃO DE PERFIS</div>
            </div>
        </div>

        <div class="dalheee">
            <div class="card">

                <div class="topo">
                    <h1 class="titulo"><strong>Novo perfil</strong></h1>
                </div>

                <?php if ($mensagem != '') { ?>
                    <div class="mensagem <?= $tipoMensagem ?>"><?= e($mensagem) ?></div>
                <?php } ?>
                <?php mostrarMensagem(); ?>

                <form method="post">
                    <div class="campo">
                        <div class="campoTipo">
                            <label class="label"><strong>Nome do perfil</strong></label>
                        </div>
                        <input type="text" name="nomePerfil" class="input" maxlength="40" placeholder="Ex.: SUPERVISOR" value="<?= valorPost('nomePerfil') ?>" required>
                    </div>

                    <div class="campo">
                        <div class="campoTipo">
                            <label class="label"><strong>O que este perfil pode fazer</strong></label>
                        </div>
                        <?php
                        $matrizMarcadas = array_map('intval', $_POST['permissoes'] ?? array());
                        $matrizDesactivada = false;
                        include __DIR__ . '/partials/matrizPermissoes.php';
                        ?>
                    </div>

                    <div class="acao">
                        <button type="submit" name="guardar" class="botao">Guardar perfil</button>
                    </div>
                </form>

                <div class="topo topoSeparado">
                    <h1 class="titulo"><strong>Perfis existentes</strong></h1>
                </div>

                <div class="tabela">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Perfil</th>
                                <th>Permissões</th>
                                <th>Utilizadores</th>
                                <th>Acções</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($perfis as $p) {
                                $marcadas = permissoesDoPerfil($p->getIdPerfil());
                                $eAdmin = ((int) $p->getIdPerfil() === ID_PERFIL_ADMIN);
                            ?>
                                <tr>
                                    <td><?= $p->getIdPerfil() ?></td>
                                    <td><span class="perfilTag perfil-<?= $p->getIdPerfil() % 6 ?>"><?= e($p->getNomePerfil()) ?></span></td>
                                    <td><?= $eAdmin ? 'CRUD completo + utilizadores e perfis' : count($marcadas) . ' de ' . $totalPermissoes ?></td>
                                    <td><?= contarUtilizadoresDoPerfil($p->getIdPerfil()) ?></td>
                                    <td>
                                        <div class="acoes">
                                            <?php if ($eAdmin) { ?>
                                                <a href="#ver-<?= $p->getIdPerfil() ?>" class="botaoAtualizar">Ver</a>
                                            <?php } else { ?>
                                                <a href="#editar-<?= $p->getIdPerfil() ?>" class="botaoAtualizar">Editar</a>
                                            <?php } ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        <?php foreach ($perfis as $p) {
            $marcadas = permissoesDoPerfil($p->getIdPerfil());
            $eAdmin = ((int) $p->getIdPerfil() === ID_PERFIL_ADMIN);
        ?>
            <!-- MODAL EDITAR / VER -->
            <div class="modalFundo" id="<?= $eAdmin ? 'ver' : 'editar' ?>-<?= $p->getIdPerfil() ?>">
                <a href="#" class="modalOverlay" aria-label="Fechar"></a>
                <div class="modalCaixa modalLargo">
                    <div class="modalTopo">
                        <h2 class="titulo"><?= $eAdmin ? 'Perfil' : 'Editar perfil' ?></h2>
                        <a href="#" class="modalFechar">x</a>
                    </div>
                    <form method="post">
                        <input type="hidden" name="idPerfil" value="<?= $p->getIdPerfil() ?>">

                        <div class="campo">
                            <div class="campoTipo">
                                <label class="label"><strong>Nome do perfil</strong></label>
                            </div>
                            <input type="text" name="nomePerfil" class="input" maxlength="40" value="<?= e($p->getNomePerfil()) ?>" required <?= $eAdmin ? 'disabled' : '' ?>>
                        </div>

                        <?php if ($eAdmin) { ?>
                            <p class="aviso">O Administrador tem CRUD completo, gere utilizadores e perfis, e não pode ser alterado nem removido. Os logs são do Auditor.</p>
                        <?php } ?>

                        <div class="campo">
                            <?php
                            $matrizMarcadas = $marcadas;
                            $matrizDesactivada = $eAdmin;
                            include __DIR__ . '/partials/matrizPermissoes.php';
                            ?>
                        </div>

                        <div class="acao modalBotoes">
                            <a href="#" class="botao botaoCancelar"><?= $eAdmin ? 'Fechar' : 'Cancelar' ?></a>
                            <?php if (!$eAdmin) { ?>
                                <button type="submit" name="actualizarPerfil" class="botao botaoAzul">Actualizar</button>
                            <?php } ?>
                        </div>
                    </form>
                </div>
            </div>
        <?php } ?>

    </div>

</body>

</html>
