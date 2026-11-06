<?php

include_once(__DIR__ . "/../model/Pais.php");
include_once(__DIR__ . "/conexao.php");
include_once(__DIR__ . "/helpers.php");
include_once(__DIR__ . "/controllerLog.php");

// Converte uma linha da base de dados num objecto Pais
function montarPais($rs)
{
    return new Pais($rs['idPais'], $rs['nomePais'], $rs['idContinente'], $rs['nomeContinente']);
}

// Consulta base (com as junções). Só devolve registos activos.
function consultarPais($filtro = "", $ordem = "c.nomeContinente, p.idPais desc")
{
    global $conexao;
    $lista = array();
    $sql = "select p.*, c.nomeContinente from pais p join continente c on c.idContinente = p.idContinente where p.activo=1" . $filtro . " order by " . $ordem;
    $result = mysqli_query($conexao, $sql);

    if ($result) {
        while ($rs = mysqli_fetch_assoc($result)) {
            array_push($lista, montarPais($rs));
        }
    }
    return $lista;
}

function listarPais()
{
    return consultarPais();
}

function pesquisarPais($pesquisa)
{
    $p = esc($pesquisa);
    return consultarPais(" and (p.nomePais like '%{$p}%' or c.nomeContinente like '%{$p}%')");
}

function encontraPais($id)
{
    $lista = consultarPais(" and p.idPais=" . (int) $id);
    return count($lista) > 0 ? $lista[0] : null;
}

// Devolve "" se os dados são válidos, ou a mensagem de erro
function validarPais($obj, $idIgnorar = 0)
{
    global $conexao;

    if (trim($obj->getNomePais()) == "") {
        return "Preencha o nome do país";
    }
    if (strlen(trim($obj->getNomePais())) > 80) {
        return "O campo Nome do país aceita no máximo 80 caracteres";
    }
    if (!existeActivo("continente", "idContinente", (int) $obj->getIdContinente())) {
        return "Seleccione o continente";
    }

    $sql = "select idPais from pais where activo=1 and nomePais='" . esc($obj->getNomePais()) . "' and idContinente=" . (int) $obj->getIdContinente() . " and idPais<>" . (int) $idIgnorar;
    $result = mysqli_query($conexao, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        return "Já existe um país com esse nome neste continente";
    }
    return "";
}

function criacaoPais($obj)
{
    global $conexao;

    $erro = validarPais($obj);
    if ($erro != "") {
        return array(false, $erro);
    }

    $sql = "insert into pais (nomePais, idContinente) values ('" . esc($obj->getNomePais()) . "', " . (int) $obj->getIdContinente() . ")";
    $result = mysqli_query($conexao, $sql);

    if ($result) {
        registarAccao("Cadastrou país: " . trim($obj->getNomePais()));
        return array(true, "País cadastrado com sucesso");
    }
    return array(false, "Ocorreu um erro ao gravar os dados");
}

function edicaoPais($obj)
{
    global $conexao;

    if (!encontraPais($obj->getIdPais())) {
        return array(false, "País não encontrado");
    }

    $erro = validarPais($obj, $obj->getIdPais());
    if ($erro != "") {
        return array(false, $erro);
    }

    $sql = "update pais set nomePais='" . esc($obj->getNomePais()) . "', idContinente=" . (int) $obj->getIdContinente() . " where idPais=" . (int) $obj->getIdPais();
    $result = mysqli_query($conexao, $sql);

    if ($result) {
        registarAccao("Editou país: " . trim($obj->getNomePais()));
        return array(true, "País actualizado com sucesso");
    }
    return array(false, "Ocorreu um erro ao actualizar os dados");
}

// Remoção lógica: o registo fica na tabela (activo=0) para não afectar os registos que dependem dele
function remocaoPais($id)
{
    global $conexao;

    $antigo = encontraPais($id);
    if (!$antigo) {
        return array(false, "País não encontrado");
    }

    $sql = "update pais set activo=0 where idPais=" . (int) $id;
    $result = mysqli_query($conexao, $sql);

    if ($result) {
        registarAccao("Removeu país: " . trim($antigo->getNomePais()));
        return array(true, "País removido com sucesso");
    }
    return array(false, "Ocorreu um erro ao remover");
}

function listarPaisPorContinente($idContinente)
{
    return consultarPais(" and p.idContinente=" . (int) $idContinente);
}
