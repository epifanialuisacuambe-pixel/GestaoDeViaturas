<?php

include_once(__DIR__ . "/../model/Fabricante.php");
include_once(__DIR__ . "/conexao.php");
include_once(__DIR__ . "/helpers.php");
include_once(__DIR__ . "/controllerLog.php");

// Converte uma linha da base de dados num objecto Fabricante
function montarFabricante($rs)
{
    return new Fabricante($rs['idFabricante'], $rs['nomeFabricante'], $rs['idPais'], $rs['nomePais'], $rs['nomeContinente']);
}

// Consulta base (com as junções). Só devolve registos activos.
function consultarFabricante($filtro = "", $ordem = "f.idFabricante desc")
{
    global $conexao;
    $lista = array();
    $sql = "select f.*, p.nomePais, c.nomeContinente from fabricante f join pais p on p.idPais = f.idPais join continente c on c.idContinente = p.idContinente where f.activo=1" . $filtro . " order by " . $ordem;
    $result = mysqli_query($conexao, $sql);

    if ($result) {
        while ($rs = mysqli_fetch_assoc($result)) {
            array_push($lista, montarFabricante($rs));
        }
    }
    return $lista;
}

function listarFabricante()
{
    return consultarFabricante();
}

function pesquisarFabricante($pesquisa)
{
    $p = esc($pesquisa);
    return consultarFabricante(" and (f.nomeFabricante like '%{$p}%' or p.nomePais like '%{$p}%' or c.nomeContinente like '%{$p}%')");
}

function encontraFabricante($id)
{
    $lista = consultarFabricante(" and f.idFabricante=" . (int) $id);
    return count($lista) > 0 ? $lista[0] : null;
}

// Devolve "" se os dados são válidos, ou a mensagem de erro
function validarFabricante($obj, $idIgnorar = 0)
{
    global $conexao;

    if (trim($obj->getNomeFabricante()) == "") {
        return "Preencha o nome do fabricante";
    }
    if (strlen(trim($obj->getNomeFabricante())) > 80) {
        return "O campo Nome do fabricante aceita no máximo 80 caracteres";
    }
    if (!existeActivo("pais", "idPais", (int) $obj->getIdPais())) {
        return "Seleccione o país";
    }

    $sql = "select idFabricante from fabricante where activo=1 and nomeFabricante='" . esc($obj->getNomeFabricante()) . "' and idFabricante<>" . (int) $idIgnorar;
    $result = mysqli_query($conexao, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        return "Já existe um fabricante com esse nome";
    }
    return "";
}

function criacaoFabricante($obj)
{
    global $conexao;

    $erro = validarFabricante($obj);
    if ($erro != "") {
        return array(false, $erro);
    }

    $sql = "insert into fabricante (nomeFabricante, idPais) values ('" . esc($obj->getNomeFabricante()) . "', " . (int) $obj->getIdPais() . ")";
    $result = mysqli_query($conexao, $sql);

    if ($result) {
        registarAccao("Cadastrou fabricante: " . trim($obj->getNomeFabricante()));
        return array(true, "Fabricante cadastrado com sucesso");
    }
    return array(false, "Ocorreu um erro ao gravar os dados");
}

function edicaoFabricante($obj)
{
    global $conexao;

    if (!encontraFabricante($obj->getIdFabricante())) {
        return array(false, "Fabricante não encontrado");
    }

    $erro = validarFabricante($obj, $obj->getIdFabricante());
    if ($erro != "") {
        return array(false, $erro);
    }

    $sql = "update fabricante set nomeFabricante='" . esc($obj->getNomeFabricante()) . "', idPais=" . (int) $obj->getIdPais() . " where idFabricante=" . (int) $obj->getIdFabricante();
    $result = mysqli_query($conexao, $sql);

    if ($result) {
        registarAccao("Editou fabricante: " . trim($obj->getNomeFabricante()));
        return array(true, "Fabricante actualizado com sucesso");
    }
    return array(false, "Ocorreu um erro ao actualizar os dados");
}

// Remoção lógica: o registo fica na tabela (activo=0) para não afectar os registos que dependem dele
function remocaoFabricante($id)
{
    global $conexao;

    $antigo = encontraFabricante($id);
    if (!$antigo) {
        return array(false, "Fabricante não encontrado");
    }

    $sql = "update fabricante set activo=0 where idFabricante=" . (int) $id;
    $result = mysqli_query($conexao, $sql);

    if ($result) {
        registarAccao("Removeu fabricante: " . trim($antigo->getNomeFabricante()));
        return array(true, "Fabricante removido com sucesso");
    }
    return array(false, "Ocorreu um erro ao remover");
}
