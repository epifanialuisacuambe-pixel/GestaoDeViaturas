<?php
include_once __DIR__ . '/../controller/controllerLogin.php';
include_once __DIR__ . '/../controller/controllerCor.php';

exigirLogin();
exigirPermissao('cor:criar');

$mensagem = '';
$tipoMensagem = '';
$paleta = '#c0392b';   // cor mostrada na paleta ao abrir a página
$hex = '';             // código hexadecimal já consultado
$nome = '';            // descrição já consultada

if (isset($_POST['consultar']) || isset($_POST['gravar'])) {
    $escolhida = strtolower(trim($_POST['corPaleta'] ?? ''));
    if (hexValido($escolhida)) {
        $paleta = $escolhida;
    }
}

// 1.º passo: "Consultar cor" preenche a descrição e o código hexadecimal da cor escolhida na paleta
if (isset($_POST['consultar'])) {
    if (hexValido($escolhida)) {
        $hex = $escolhida;
        $nome = nomeDaCor($hex);
    } else {
        $mensagem = 'Escolha uma cor na paleta';
        $tipoMensagem = 'erro';
    }
}

// 2.º passo: "Cadastrar cor" grava a cor que foi consultada
if (isset($_POST['gravar'])) {
    $hexConsultado = strtolower(trim($_POST['hexCor'] ?? ''));

    if ($hexConsultado == '') {
        $mensagem = 'Escolha a cor na paleta e carregue em "Consultar cor"';
        $tipoMensagem = 'erro';
    } elseif ($hexConsultado != $escolhida) {
        $hex = '';
        $mensagem = 'A cor da paleta mudou. Carregue em "Consultar cor" outra vez';
        $tipoMensagem = 'erro';
    } else {
        $hex = $hexConsultado;
        $nome = nomeDaCor($hex);
        $obj = new Cor(null, $nome, $hex);
        list($ok, $mensagem) = criacaoCor($obj);
        $tipoMensagem = $ok ? 'sucesso' : 'erro';
        if ($ok) {
            $hex = '';
            $nome = '';
        }
    }
}

$paginaActiva = 'cor';
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CADASTRAR COR</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>

<body>

    <?php include __DIR__ . '/partials/menu.php'; ?>

    <div class="mae">

        <div class="cabecalho">
            <div class="abas">
                <div class="aba abaAtiva">CADASTRO DE COR</div>
                <?php if (temPermissao('cor:listar')) { ?><a href="ListarCor.php" class="aba">LISTA DE CORES</a><?php } ?>
            </div>
        </div>

        <div class="dalheee">
            <div class="card">
                <form method="post">

                    <div class="topo">
                        <h1 class="titulo"><strong>Nova cor</strong></h1>
                    </div>

                    <?php if ($mensagem != '') { ?>
                        <div class="mensagem <?= $tipoMensagem ?>"><?= e($mensagem) ?></div>
                    <?php } ?>

                    <div class="linha linhaFiltro">
                        <div class="campo">
                            <button type="submit" name="consultar" value="1" class="botao botaoAzul">Consultar cor</button>
                        </div>
                        <div class="campo campoFlex">
                            <div class="campoTipo">
                                <label class="label"><strong>Cor (clique na caixa para abrir a paleta)</strong></label>
                            </div>
                            <input type="color" name="corPaleta" class="paleta" value="<?= e($paleta) ?>">
                        </div>
                    </div>

                    <div class="linha">
                        <div class="campo campoFlex">
                            <div class="campoTipo">
                                <label class="label"><strong>Descrição da cor</strong></label>
                            </div>
                            <input type="text" class="input" value="<?= e($nome) ?>" placeholder="Preenchida ao consultar a cor" disabled>
                            <input type="hidden" name="nomeCor" value="<?= e($nome) ?>">
                        </div>
                        <div class="campo campoFlex">
                            <div class="campoTipo">
                                <label class="label"><strong>Código hexadecimal</strong></label>
                            </div>
                            <input type="text" class="input" value="<?= e($hex) ?>" placeholder="Preenchido ao consultar a cor" disabled>
                            <input type="hidden" name="hexCor" value="<?= e($hex) ?>">
                        </div>
                    </div>

                    <div class="acao">
                        <button type="submit" name="gravar" class="botao" <?= $hex == '' ? 'disabled' : '' ?>>Cadastrar cor</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

</body>

</html>
