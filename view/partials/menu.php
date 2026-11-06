<?php
// Menu lateral. Cada página define $paginaActiva antes de incluir este ficheiro:
// 'dashboard', a chave de uma entidade (ex.: 'viatura'), 'utilizador', 'perfil' ou 'log'
if (!isset($paginaActiva)) {
    $paginaActiva = '';
}
?>
<div class="navbar">
    <div class="marca">
        <img src="img/logo.svg" alt="Logo" class="marca-logo">
        <div class="titulo-marca">SISTEMA DE<br>GESTÃO DE VIATURAS</div>
    </div>

    <nav class="navlinks">
        <a class="<?= $paginaActiva == 'dashboard' ? 'on' : '' ?>" href="Dashboard.php">Dashboard</a>

        <?php
        $primeiro = true;
        foreach (entidades() as $chave => $entidade) {
            $podeCriar = temPermissao($chave . ':criar');
            $podeListar = temPermissao($chave . ':listar');

            if ($podeCriar || $podeListar) {
                if ($primeiro) {
                    echo '<div class="grupoMenu">Cadastros</div>';
                    $primeiro = false;
                }
                $pagina = $podeCriar ? $entidade['criar'] : $entidade['listar'];
        ?>
                <a class="<?= $paginaActiva == $chave ? 'on' : '' ?>" href="<?= $pagina ?>"><?= e($entidade['menu']) ?></a>
        <?php
            }
        }
        ?>

        <?php if (temPermissao('utilizador:criar') || temPermissao('utilizador:listar') || eAdministrador() || temPermissao('log:listar')) { ?>
            <div class="grupoMenu grupoAdmin">Administração</div>
        <?php } ?>

        <?php if (temPermissao('utilizador:criar') || temPermissao('utilizador:listar')) {
            $paginaUtilizadores = temPermissao('utilizador:criar') ? 'CadastrarUtilizador.php' : 'ListarUtilizador.php';
        ?>
            <a class="<?= $paginaActiva == 'utilizador' ? 'on' : '' ?>" href="<?= $paginaUtilizadores ?>">Utilizadores</a>
        <?php } ?>
        <?php if (eAdministrador()) { ?>
            <a class="<?= $paginaActiva == 'perfil' ? 'on' : '' ?>" href="Perfil.php">Gerir perfis</a>
        <?php } ?>

        <?php if (temPermissao('log:listar')) { ?>
            <a class="<?= $paginaActiva == 'log' ? 'on' : '' ?>" href="ListarLog.php">Logs</a>
        <?php } ?>

     <div class="menuRodape">
        <a href="alterarSenha.php">Alterar senha</a>
        <a class="sair" href="logout.php">Terminar sessão</a>
    </div>
    </nav>
</div>

<?php
$titulosTopo = array('dashboard' => 'Início', 'utilizador' => 'Utilizadores', 'perfil' => 'Gerir perfis', 'log' => 'Logs');
$tituloTopo = $titulosTopo[$paginaActiva] ?? nomeEntidade($paginaActiva);
$nomeTopo = trim($_SESSION['nome'] ?? '');
?>
<div class="topbar">
    <div class="topbarTitulo"><?= e($tituloTopo) ?></div>
    <div class="topbarUtilizador">
        <div class="avatar"><?= e(strtoupper(substr($nomeTopo, 0, 1))) ?></div>
        <div class="topbarDados">
            <strong><?= e($nomeTopo) ?></strong>
            <span class="perfilTag perfil-<?= (int) ($_SESSION['idPerfil'] ?? 0) % 6 ?>"><?= e($_SESSION['perfil'] ?? '') ?></span>
        </div>
    </div>
</div>
