<?php

include_once(__DIR__ . "/conexao.php");

// Senha dada a todos os utilizadores novos (ou quando o administrador reseta a senha)
define("SENHA_PADRAO", "12345678");

// O perfil 1 é o Administrador do sistema: gere tudo e não pode ser alterado nem removido
define("ID_PERFIL_ADMIN", 1);

// Entidades do sistema (usadas no menu, no dashboard e na configuração de perfis)
function entidades()
{
    return array(
        'continente'  => array('menu' => 'Continentes',      'tabela' => 'continente',  'criar' => 'CadastrarContinente.php',  'listar' => 'ListarContinente.php'),
        'pais'        => array('menu' => 'Países',           'tabela' => 'pais',        'criar' => 'CadastrarPais.php',        'listar' => 'ListarPais.php'),
        'fabricante'  => array('menu' => 'Fabricantes',      'tabela' => 'fabricante',  'criar' => 'CadastrarFabricante.php',  'listar' => 'ListarFabricante.php'),
        'marca'       => array('menu' => 'Marcas',           'tabela' => 'marca',       'criar' => 'CadastrarMarca.php',       'listar' => 'ListarMarca.php'),
        'modelo'      => array('menu' => 'Modelos',          'tabela' => 'modelo',      'criar' => 'CadastrarModelo.php',      'listar' => 'ListarModelo.php'),
        'tipoviatura' => array('menu' => 'Tipos de viatura', 'tabela' => 'tipoviatura', 'criar' => 'CadastrarTipoViatura.php', 'listar' => 'ListarTipoViatura.php'),
        'cor'         => array('menu' => 'Cores',            'tabela' => 'cor',         'criar' => 'CadastrarCor.php',         'listar' => 'ListarCor.php'),
        'viatura'     => array('menu' => 'Viaturas',         'tabela' => 'viatura',     'criar' => 'CadastrarViatura.php',     'listar' => 'ListarViatura.php'),
    );
}

// Nome da entidade como aparece nas telas
function nomeEntidade($chave)
{
    if ($chave == 'log') {
        return 'Logs (auditoria)';
    }
    if ($chave == 'utilizador') {
        return 'Utilizadores';
    }
    $entidades = entidades();
    return isset($entidades[$chave]) ? $entidades[$chave]['menu'] : $chave;
}

// Escreve texto na página sem deixar HTML passar
function e($texto)
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

// Prepara texto para ser usado numa instrução SQL
function esc($texto)
{
    global $conexao;
    return mysqli_real_escape_string($conexao, trim((string) $texto));
}

// Valor que o utilizador escreveu no formulário (para não perder os dados quando há erro)
function valorPost($nome)
{
    return e($_POST[$nome] ?? '');
}

// Confirma que existe um registo activo com aquele id (para chaves estrangeiras)
function existeActivo($tabela, $colunaId, $id)
{
    global $conexao;
    $sql = "select 1 from {$tabela} where activo=1 and {$colunaId}=" . (int) $id;
    $result = mysqli_query($conexao, $sql);
    return $result && mysqli_num_rows($result) > 0;
}

// Número de registos activos de uma tabela
function contarRegistos($tabela)
{
    global $conexao;
    $result = mysqli_query($conexao, "select count(*) as total from {$tabela} where activo=1");
    if ($result) {
        $rs = mysqli_fetch_assoc($result);
        return (int) $rs['total'];
    }
    return 0;
}

// ---------- Mensagens que sobrevivem a um redirecionamento ----------

function definirMensagem($texto, $tipo = 'sucesso')
{
    $_SESSION['mensagem'] = array($tipo, $texto);
}

function mostrarMensagem()
{
    if (isset($_SESSION['mensagem'])) {
        $tipo = $_SESSION['mensagem'][0];
        $texto = $_SESSION['mensagem'][1];
        unset($_SESSION['mensagem']);
        echo '<div class="mensagem ' . e($tipo) . '">' . e($texto) . '</div>';
    }
}
