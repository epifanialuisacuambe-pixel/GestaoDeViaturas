<?php

include_once(__DIR__ . "/../model/Modelo.php");
include_once(__DIR__ . "/conexao.php");
include_once(__DIR__ . "/helpers.php");
include_once(__DIR__ . "/controllerLog.php");

// Converte uma linha da base de dados num objecto Modelo
function montarModelo($rs)
{
    return new Modelo($rs['idModelo'], $rs['nomeModelo'], $rs['idMarca'], $rs['nomeMarca'], $rs['nomeFabricante']);
}

// Consulta base (com as junções). Só devolve registos activos.
function consultarModelo($filtro = "", $ordem = "f.idFabricante desc, ma.nomeMarca, mo.nomeModelo")
{
    global $conexao;
    $lista = array();
    $sql = "select mo.*, ma.nomeMarca, f.nomeFabricante from modelo mo join marca ma on ma.idMarca = mo.idMarca join fabricante f on f.idFabricante = ma.idFabricante where mo.activo=1" . $filtro . " order by " . $ordem;
    $result = mysqli_query($conexao, $sql);

    if ($result) {
        while ($rs = mysqli_fetch_assoc($result)) {
            array_push($lista, montarModelo($rs));
        }
    }
    return $lista;
}

function listarModelo()
{
    return consultarModelo();
}

function pesquisarModelo($pesquisa)
{
    $p = esc($pesquisa);
    return consultarModelo(" and (mo.nomeModelo like '%{$p}%' or ma.nomeMarca like '%{$p}%' or f.nomeFabricante like '%{$p}%')");
}

function encontraModelo($id)
{
    $lista = consultarModelo(" and mo.idModelo=" . (int) $id);
    return count($lista) > 0 ? $lista[0] : null;
}

// Devolve "" se os dados são válidos, ou a mensagem de erro
function validarModelo($obj, $idIgnorar = 0)
{
    global $conexao;

    if (trim($obj->getNomeModelo()) == "") {
        return "Preencha o nome do modelo";
    }
    if (strlen(trim($obj->getNomeModelo())) > 80) {
        return "O campo Nome do modelo aceita no máximo 80 caracteres";
    }
    if (!existeActivo("marca", "idMarca", (int) $obj->getIdMarca())) {
        return "Seleccione a marca";
    }

    $sql = "select idModelo from modelo where activo=1 and nomeModelo='" . esc($obj->getNomeModelo()) . "' and idMarca=" . (int) $obj->getIdMarca() . " and idModelo<>" . (int) $idIgnorar;
    $result = mysqli_query($conexao, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        return "Já existe um modelo com esse nome nesta marca";
    }
    return "";
}

function criacaoModelo($obj)
{
    global $conexao;

    $erro = validarModelo($obj);
    if ($erro != "") {
        return array(false, $erro);
    }

    $sql = "insert into modelo (nomeModelo, idMarca) values ('" . esc($obj->getNomeModelo()) . "', " . (int) $obj->getIdMarca() . ")";
    $result = mysqli_query($conexao, $sql);

    if ($result) {
        registarAccao("Cadastrou modelo: " . trim($obj->getNomeModelo()));
        return array(true, "Modelo cadastrado com sucesso");
    }
    return array(false, "Ocorreu um erro ao gravar os dados");
}

function edicaoModelo($obj)
{
    global $conexao;

    if (!encontraModelo($obj->getIdModelo())) {
        return array(false, "Modelo não encontrado");
    }

    $erro = validarModelo($obj, $obj->getIdModelo());
    if ($erro != "") {
        return array(false, $erro);
    }

    $sql = "update modelo set nomeModelo='" . esc($obj->getNomeModelo()) . "', idMarca=" . (int) $obj->getIdMarca() . " where idModelo=" . (int) $obj->getIdModelo();
    $result = mysqli_query($conexao, $sql);

    if ($result) {
        registarAccao("Editou modelo: " . trim($obj->getNomeModelo()));
        return array(true, "Modelo actualizado com sucesso");
    }
    return array(false, "Ocorreu um erro ao actualizar os dados");
}

// Remoção lógica: o registo fica na tabela (activo=0) para não afectar os registos que dependem dele
function remocaoModelo($id)
{
    global $conexao;

    $antigo = encontraModelo($id);
    if (!$antigo) {
        return array(false, "Modelo não encontrado");
    }

    $sql = "update modelo set activo=0 where idModelo=" . (int) $id;
    $result = mysqli_query($conexao, $sql);

    if ($result) {
        registarAccao("Removeu modelo: " . trim($antigo->getNomeModelo()));
        return array(true, "Modelo removido com sucesso");
    }
    return array(false, "Ocorreu um erro ao remover");
}

function listarModeloPorMarca($idMarca)
{
    return consultarModelo(" and mo.idMarca=" . (int) $idMarca);
}
