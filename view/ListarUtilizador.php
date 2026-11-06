<?php
include_once __DIR__ . '/../controller/controllerLogin.php';
include_once __DIR__ . '/../controller/utilizadorController.php';
include_once __DIR__ . '/../controller/controllerPerfil.php';

exigirLogin();
exigirPermissao('utilizador:listar');

if (isset($_POST['remover']) && temPermissao('utilizador:remover')) {
    list($ok, $msg) = remocaoUtilizador($_POST['id'] ?? 0);
    definirMensagem($msg, $ok ? 'sucesso' : 'erro');
    header('Location: ListarUtilizador.php');
    exit;
}

if (isset($_POST['resetar']) && temPermissao('utilizador:actualizar')) {
    list($ok, $msg) = resetarSenha($_POST['id'] ?? 0);
    definirMensagem($msg, $ok ? 'sucesso' : 'erro');
    header('Location: ListarUtilizador.php');
    exit;
}

if (isset($_POST['guardar']) && temPermissao('utilizador:actualizar')) {
    $utilizador = new Utilizador(
        (int) ($_POST['id'] ?? 0),
        $_POST['nome'] ?? '',
        $_POST['apelido'] ?? '',
        $_POST['contacto'] ?? '',
        $_POST['email'] ?? '',
        $_POST['username'] ?? '',
        null,
        $_POST['idPerfil'] ?? 0
    );
    list($ok, $msg) = edicaoUtilizador($utilizador);
    definirMensagem($msg, $ok ? 'sucesso' : 'erro');
    header('Location: ListarUtilizador.php');
    exit;
}

$pesquisa = trim($_GET['pesquisa'] ?? '');
$lista = $pesquisa != '' ? pesquisarUtilizador($pesquisa) : listarUtilizador();
$perfis = listarPerfisAtribuiveis();
$podeEditar = temPermissao('utilizador:actualizar');
$podeRemover = temPermissao('utilizador:remover');
$idLogado = (int) ($_SESSION['idUtilizador'] ?? 0);
$paginaActiva = 'utilizador';

// Campos do formulário de edição / remoção (o mesmo desenho para os dois pop-ups)
function camposUtilizador($item, $perfis, $desactivado)
{
    $dis = $desactivado ? 'disabled' : '';
?>
    <div class="linha">
        <div class="campo campoFlex">
            <div class="campoTipo"><label class="label"><strong>Nome</strong></label></div>
            <input type="text" name="nome" class="input" maxlength="60" value="<?= e($item->getNome()) ?>" required <?= $dis ?>>
        </div>
        <div class="campo campoFlex">
            <div class="campoTipo"><label class="label"><strong>Apelido</strong></label></div>
            <input type="text" name="apelido" class="input" maxlength="60" value="<?= e($item->getApelido()) ?>" required <?= $dis ?>>
        </div>
    </div>
    <div class="linha">
        <div class="campo campoFlex">
            <div class="campoTipo"><label class="label"><strong>Contacto</strong></label></div>
            <input type="text" name="contacto" class="input" maxlength="15" value="<?= e($item->getContacto()) ?>" required <?= $dis ?>>
        </div>
        <div class="campo campoFlex">
            <div class="campoTipo"><label class="label"><strong>E-mail</strong></label></div>
            <input type="email" name="email" class="input" maxlength="100" value="<?= e($item->getEmail()) ?>" required <?= $dis ?>>
        </div>
    </div>
    <div class="linha">
        <div class="campo campoFlex">
            <div class="campoTipo"><label class="label"><strong>Username</strong></label></div>
            <input type="text" name="username" class="input" maxlength="30" value="<?= e($item->getUsername()) ?>" required <?= $dis ?>>
        </div>
        <div class="campo campoFlex">
            <div class="campoTipo"><label class="label"><strong>Perfil</strong></label></div>
            <select name="idPerfil" class="input" required <?= $dis ?>>
                <?php foreach ($perfis as $p) { ?>
                    <option value="<?= $p->getIdPerfil() ?>" <?= $p->getIdPerfil() == $item->getIdPerfil() ? 'selected' : '' ?>><?= e($p->getNomePerfil()) ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
<?php
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LISTAR UTILIZADORES</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>

<body>

    <?php include __DIR__ . '/partials/menu.php'; ?>

    <div class="mae">

        <div class="cabecalho">
            <div class="abas">
                <?php if (temPermissao('utilizador:criar')) { ?><a href="CadastrarUtilizador.php" class="aba">CADASTRO DE UTILIZADOR</a><?php } ?>
                <div class="aba abaAtiva">LISTA DE UTILIZADORES</div>
            </div>
        </div>

        <div class="dalheee">
            <div class="card">

                <div class="topo">
                    <h1 class="titulo"><strong>Lista de utilizadores</strong></h1>
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
                                <a href="ListarUtilizador.php" class="botao botaoCancelar">Limpar</a>
                            </div>
                        <?php } ?>
                    </div>
                </form>

                <div class="tabela">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Apelido</th>
                                <th>Contacto</th>
                                <th>E-mail</th>
                                <th>Username</th>
                                <th>Perfil</th>
                                <th>Acções</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($lista) == 0) { ?>
                                <tr>
                                    <td colspan="8">SEM NENHUM UTILIZADOR REGISTADO</td>
                                </tr>
                            <?php } else { ?>
                                <?php foreach ($lista as $item) { ?>
                                    <tr>
                                        <td><?= $item->getIdUtilizador() ?></td>
                                        <td><?= e($item->getNome()) ?></td>
                                        <td><?= e($item->getApelido()) ?></td>
                                        <td><?= e($item->getContacto()) ?></td>
                                        <td><?= e($item->getEmail()) ?></td>
                                        <td>
                                            <?= e($item->getUsername()) ?>
                                            <?php if (!jaEntrouNoSistema($item->getIdUtilizador())) { ?>
                                                <br>
                                                <span class="estadoTag estadoPrimeiro">Primeiro acesso</span>
                                            <?php } ?>
                                        </td>
                                        <td><span class="perfilTag perfil-<?= $item->getIdPerfil() % 6 ?>"><?= e($item->getNomePerfil()) ?></span></td>
                                       <td>
                                        <?php if ($item->getIdUtilizador() == $idLogado) { ?>
                                            <span class="estadoTag estadoAtual">Sessão actual</span>
                                        <?php } else { ?>
                                            <div class="acoes">
                                                <?php if ($podeEditar) { ?>
                                                    <a href="#editar-<?= $item->getIdUtilizador() ?>" class="botaoAtualizar">Editar</a>
                                                    <a href="#resetar-<?= $item->getIdUtilizador() ?>" class="botaoResetar">Resetar senha</a>
                                                <?php } ?>
                                                <?php if ($podeRemover) { ?>
                                                    <a href="#remover-<?= $item->getIdUtilizador() ?>" class="botaoRemover">Remover</a>
                                                <?php } ?>
                                            </div>
                                            
                                        <?php } ?>
                                    </td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        <?php foreach ($lista as $item) { ?>
            <?php if ($podeEditar) { ?>
            <!-- MODAL EDITAR -->
            <div class="modalFundo" id="editar-<?= $item->getIdUtilizador() ?>">
                <a href="#" class="modalOverlay" aria-label="Fechar"></a>
                <div class="modalCaixa">
                    <div class="modalTopo">
                        <h2 class="titulo">Editar utilizador</h2>
                        <a href="#" class="modalFechar">x</a>
                    </div>
                    <form method="post">
                        <input type="hidden" name="id" value="<?= $item->getIdUtilizador() ?>">
                        <?php camposUtilizador($item, $perfis, false); ?>
                        <div class="acao modalBotoes">
                            <a href="#" class="botao botaoCancelar">Cancelar</a>
                            <button type="submit" name="guardar" class="botao botaoAzul">Actualizar</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- MODAL RESETAR SENHA -->
            <div class="modalFundo" id="resetar-<?= $item->getIdUtilizador() ?>">
                <a href="#" class="modalOverlay" aria-label="Fechar"></a>
                <div class="modalCaixa modalPequeno">
                    <div class="modalTopo">
                        <h2 class="titulo">Resetar senha</h2>
                        <a href="#" class="modalFechar">x</a>
                    </div>
                    <form method="post">
                        <input type="hidden" name="id" value="<?= $item->getIdUtilizador() ?>">
                        <p class="aviso">A senha de <strong><?= e($item->getUsername()) ?></strong> volta a <strong><?= SENHA_PADRAO ?></strong> e terá de ser trocada no próximo acesso.</p>
                        <div class="acao modalBotoes">
                            <a href="#" class="botao botaoCancelar">Cancelar</a>
                            <button type="submit" name="resetar" class="botao botaoAzul">Resetar</button>
                        </div>
                    </form>
                </div>
            </div>

            <?php } ?>
            <?php if ($podeRemover) { ?>
            <!-- MODAL REMOVER (mesmos campos, só para consulta) -->
            <div class="modalFundo" id="remover-<?= $item->getIdUtilizador() ?>">
                <a href="#" class="modalOverlay" aria-label="Fechar"></a>
                <div class="modalCaixa">
                    <div class="modalTopo">
                        <h2 class="titulo">Remover utilizador</h2>
                        <a href="#" class="modalFechar">x</a>
                    </div>
                    <form method="post">
                        <input type="hidden" name="id" value="<?= $item->getIdUtilizador() ?>">
                        <?php camposUtilizador($item, $perfis, true); ?>
                        <p class="aviso">Tem a certeza que deseja remover este utilizador?</p>
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
