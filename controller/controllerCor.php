<?php

include_once(__DIR__ . "/../model/Cor.php");
include_once(__DIR__ . "/conexao.php");
include_once(__DIR__ . "/helpers.php");
include_once(__DIR__ . "/controllerLog.php");

// Converte uma linha da base de dados num objecto Cor
function montarCor($rs)
{
    return new Cor($rs['idCor'], $rs['nomeCor'], $rs['hexCor']);
}

// Consulta base (com as junções). Só devolve registos activos.
function consultarCor($filtro = "", $ordem = "c.idCor desc")
{
    global $conexao;
    $lista = array();
    $sql = "select c.* from cor c where c.activo=1" . $filtro . " order by " . $ordem;
    $result = mysqli_query($conexao, $sql);

    if ($result) {
        while ($rs = mysqli_fetch_assoc($result)) {
            array_push($lista, montarCor($rs));
        }
    }
    return $lista;
}

function listarCor()
{
    return consultarCor();
}

function pesquisarCor($pesquisa)
{
    $p = esc($pesquisa);
    return consultarCor(" and (c.nomeCor like '%{$p}%' or c.hexCor like '%{$p}%')");
}

function encontraCor($id)
{
    $lista = consultarCor(" and c.idCor=" . (int) $id);
    return count($lista) > 0 ? $lista[0] : null;
}

// Devolve "" se os dados são válidos, ou a mensagem de erro
function validarCor($obj, $idIgnorar = 0)
{
    global $conexao;

    if (trim($obj->getNomeCor()) == "") {
        return "Preencha a descrição da cor";
    }
    if (strlen(trim($obj->getNomeCor())) > 40) {
        return "O campo Descrição da cor aceita no máximo 40 caracteres";
    }
    if (trim($obj->getHexCor()) == "") {
        return "Preencha o código hexadecimal";
    }
    if (strlen(trim($obj->getHexCor())) > 7) {
        return "O campo Código hexadecimal aceita no máximo 7 caracteres";
    }

    $obj->setHexCor(strtolower(trim($obj->getHexCor())));
    if (!hexValido($obj->getHexCor())) {
        return "O código hexadecimal deve ter o formato #rrggbb";
    }

    $sql = "select idCor from cor where activo=1 and hexCor='" . esc($obj->getHexCor()) . "' and idCor<>" . (int) $idIgnorar;
    $result = mysqli_query($conexao, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        return "Já existe uma cor com esse código hexadecimal";
    }
    return "";
}

function criacaoCor($obj)
{
    global $conexao;

    $erro = validarCor($obj);
    if ($erro != "") {
        return array(false, $erro);
    }

    $sql = "insert into cor (nomeCor, hexCor) values ('" . esc($obj->getNomeCor()) . "', '" . esc($obj->getHexCor()) . "')";
    $result = mysqli_query($conexao, $sql);

    if ($result) {
        registarAccao("Cadastrou cor: " . trim($obj->getNomeCor()));
        return array(true, "Cor cadastrada com sucesso");
    }
    return array(false, "Ocorreu um erro ao gravar os dados");
}

function edicaoCor($obj)
{
    global $conexao;

    if (!encontraCor($obj->getIdCor())) {
        return array(false, "Cor não encontrada");
    }

    $erro = validarCor($obj, $obj->getIdCor());
    if ($erro != "") {
        return array(false, $erro);
    }

    $sql = "update cor set nomeCor='" . esc($obj->getNomeCor()) . "', hexCor='" . esc($obj->getHexCor()) . "' where idCor=" . (int) $obj->getIdCor();
    $result = mysqli_query($conexao, $sql);

    if ($result) {
        registarAccao("Editou cor: " . trim($obj->getNomeCor()));
        return array(true, "Cor actualizada com sucesso");
    }
    return array(false, "Ocorreu um erro ao actualizar os dados");
}

// Remoção lógica: o registo fica na tabela (activo=0) para não afectar os registos que dependem dele
function remocaoCor($id)
{
    global $conexao;

    $antigo = encontraCor($id);
    if (!$antigo) {
        return array(false, "Cor não encontrada");
    }

    $sql = "update cor set activo=0 where idCor=" . (int) $id;
    $result = mysqli_query($conexao, $sql);

    if ($result) {
        registarAccao("Removeu cor: " . trim($antigo->getNomeCor()));
        return array(true, "Cor removida com sucesso");
    }
    return array(false, "Ocorreu um erro ao remover");
}

// Confirma o formato #rrggbb
function hexValido($hex)
{
    return (bool) preg_match('/^#[0-9a-fA-F]{6}$/', (string) $hex);
}

// Descrição (em português) da cor mais parecida com o código hexadecimal escolhido na paleta
function nomeDaCor($hex)
{
    $conhecidas = array(
        'Preto' => '000000', 'Branco' => 'ffffff', 'Cinzento' => '808080', 'Cinzento claro' => 'd3d3d3',
        'Cinzento escuro' => '404040', 'Prata' => 'c0c0c0', 'Vermelho' => 'ff0000', 'Vermelho escuro' => '8b0000',
        'Bordô' => '800020', 'Laranja' => 'ffa500', 'Laranja escuro' => 'ff8c00', 'Amarelo' => 'ffff00',
        'Dourado' => 'ffd700', 'Creme' => 'fffdd0', 'Bege' => 'f5f5dc', 'Castanho' => '8b4513',
        'Castanho claro' => 'cd853f', 'Verde' => '008000', 'Verde claro' => '90ee90', 'Verde escuro' => '006400',
        'Verde lima' => '00ff00', 'Verde azeitona' => '808000', 'Turquesa' => '40e0d0', 'Ciano' => '00ffff',
        'Azul' => '0000ff', 'Azul escuro' => '00008b', 'Azul marinho' => '000080', 'Azul claro' => '87ceeb',
        'Azul royal' => '4169e1', 'Roxo' => '800080', 'Violeta' => '8a2be2', 'Lilás' => 'c8a2c8',
        'Magenta' => 'ff00ff', 'Rosa' => 'ffc0cb', 'Rosa choque' => 'ff1493', 'Salmão' => 'fa8072',
    );

    $hex = ltrim(strtolower((string) $hex), '#');
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));

    $melhorNome = 'Cor';
    $melhorDistancia = null;
    foreach ($conhecidas as $nome => $codigo) {
        $dr = $r - hexdec(substr($codigo, 0, 2));
        $dg = $g - hexdec(substr($codigo, 2, 2));
        $db = $b - hexdec(substr($codigo, 4, 2));
        $distancia = $dr * $dr + $dg * $dg + $db * $db;
        if ($melhorDistancia === null || $distancia < $melhorDistancia) {
            $melhorDistancia = $distancia;
            $melhorNome = $nome;
        }
    }
    return $melhorNome;
}
