<?php

include_once(__DIR__ . "/../model/Continente.php");
include_once(__DIR__ . "/conexao.php");
include_once(__DIR__ . "/helpers.php");
include_once(__DIR__ . "/controllerLog.php");

// Converte uma linha da base de dados num objecto Continente
function montarContinente($rs)
{
    return new Continente($rs['idContinente'], $rs['nomeContinente']);
}

// Consulta base (com as junções). Só devolve registos activos.
function consultarContinente($filtro = "", $ordem = "c.idContinente desc")
{
    global $conexao;
    $lista = array();
    $sql = "select c.* from continente c where c.activo=1" . $filtro . " order by " . $ordem;
    $result = mysqli_query($conexao, $sql);

    if ($result) {
        while ($rs = mysqli_fetch_assoc($result)) {
            array_push($lista, montarContinente($rs));
        }
    }
    return $lista;
}

function listarContinente()
{
    return consultarContinente();
}

function pesquisarContinente($pesquisa)
{
    $p = esc($pesquisa);
    return consultarContinente(" and (c.nomeContinente like '%{$p}%')");
}

function encontraContinente($id)
{
    $lista = consultarContinente(" and c.idContinente=" . (int) $id);
    return count($lista) > 0 ? $lista[0] : null;
}

// Devolve "" se os dados são válidos, ou a mensagem de erro
function validarContinente($obj, $idIgnorar = 0)
{
    global $conexao;

    if (trim($obj->getNomeContinente()) == "") {
        return "Preencha o nome do continente";
    }
    if (strlen(trim($obj->getNomeContinente())) > 60) {
        return "O campo Nome do continente aceita no máximo 60 caracteres";
    }

    $sql = "select idContinente from continente where activo=1 and nomeContinente='" . esc($obj->getNomeContinente()) . "' and idContinente<>" . (int) $idIgnorar;
    $result = mysqli_query($conexao, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        return "Já existe um continente com esse nome";
    }
    return "";
}

function criacaoContinente($obj)
{
    global $conexao;

    $erro = validarContinente($obj);
    if ($erro != "") {
        return array(false, $erro);
    }

    $sql = "insert into continente (nomeContinente) values ('" . esc($obj->getNomeContinente()) . "')";
    $result = mysqli_query($conexao, $sql);

    if ($result) {
        registarAccao("Cadastrou continente: " . trim($obj->getNomeContinente()));
        return array(true, "Continente cadastrado com sucesso");
    }
    return array(false, "Ocorreu um erro ao gravar os dados");
}

function edicaoContinente($obj)
{
    global $conexao;

    if (!encontraContinente($obj->getIdContinente())) {
        return array(false, "Continente não encontrado");
    }

    $erro = validarContinente($obj, $obj->getIdContinente());
    if ($erro != "") {
        return array(false, $erro);
    }

    $sql = "update continente set nomeContinente='" . esc($obj->getNomeContinente()) . "' where idContinente=" . (int) $obj->getIdContinente();
    $result = mysqli_query($conexao, $sql);

    if ($result) {
        registarAccao("Editou continente: " . trim($obj->getNomeContinente()));
        return array(true, "Continente actualizado com sucesso");
    }
    return array(false, "Ocorreu um erro ao actualizar os dados");
}

// Remoção lógica: o registo fica na tabela (activo=0) para não afectar os registos que dependem dele
function remocaoContinente($id)
{
    global $conexao;

    $antigo = encontraContinente($id);
    if (!$antigo) {
        return array(false, "Continente não encontrado");
    }

    $sql = "update continente set activo=0 where idContinente=" . (int) $id;
    $result = mysqli_query($conexao, $sql);

    if ($result) {
        registarAccao("Removeu continente: " . trim($antigo->getNomeContinente()));
        return array(true, "Continente removido com sucesso");
    }
    return array(false, "Ocorreu um erro ao remover");
}
