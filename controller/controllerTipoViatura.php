<?php

include_once(__DIR__ . "/../model/TipoViatura.php");
include_once(__DIR__ . "/conexao.php");
include_once(__DIR__ . "/helpers.php");
include_once(__DIR__ . "/controllerLog.php");

// Converte uma linha da base de dados num objecto TipoViatura
function montarTipoViatura($rs)
{
    return new TipoViatura($rs['idTipo'], $rs['nomeTipo']);
}

// Consulta base (com as junções). Só devolve registos activos.
function consultarTipoViatura($filtro = "", $ordem = "t.idTipo desc")
{
    global $conexao;
    $lista = array();
    $sql = "select t.* from tipoviatura t where t.activo=1" . $filtro . " order by " . $ordem;
    $result = mysqli_query($conexao, $sql);

    if ($result) {
        while ($rs = mysqli_fetch_assoc($result)) {
            array_push($lista, montarTipoViatura($rs));
        }
    }
    return $lista;
}

function listarTipoViatura()
{
    return consultarTipoViatura();
}

function pesquisarTipoViatura($pesquisa)
{
    $p = esc($pesquisa);
    return consultarTipoViatura(" and (t.nomeTipo like '%{$p}%')");
}

function encontraTipoViatura($id)
{
    $lista = consultarTipoViatura(" and t.idTipo=" . (int) $id);
    return count($lista) > 0 ? $lista[0] : null;
}

// Devolve "" se os dados são válidos, ou a mensagem de erro
function validarTipoViatura($obj, $idIgnorar = 0)
{
    global $conexao;

    if (trim($obj->getNomeTipo()) == "") {
        return "Preencha o nome do tipo de viatura";
    }
    if (strlen(trim($obj->getNomeTipo())) > 60) {
        return "O campo Nome do tipo de viatura aceita no máximo 60 caracteres";
    }

    $sql = "select idTipo from tipoviatura where activo=1 and nomeTipo='" . esc($obj->getNomeTipo()) . "' and idTipo<>" . (int) $idIgnorar;
    $result = mysqli_query($conexao, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        return "Já existe um tipo de viatura com esse nome";
    }
    return "";
}

function criacaoTipoViatura($obj)
{
    global $conexao;

    $erro = validarTipoViatura($obj);
    if ($erro != "") {
        return array(false, $erro);
    }

    $sql = "insert into tipoviatura (nomeTipo) values ('" . esc($obj->getNomeTipo()) . "')";
    $result = mysqli_query($conexao, $sql);

    if ($result) {
        registarAccao("Cadastrou tipo de viatura: " . trim($obj->getNomeTipo()));
        return array(true, "Tipo de viatura cadastrado com sucesso");
    }
    return array(false, "Ocorreu um erro ao gravar os dados");
}

function edicaoTipoViatura($obj)
{
    global $conexao;

    if (!encontraTipoViatura($obj->getIdTipo())) {
        return array(false, "Tipo de viatura não encontrado");
    }

    $erro = validarTipoViatura($obj, $obj->getIdTipo());
    if ($erro != "") {
        return array(false, $erro);
    }

    $sql = "update tipoviatura set nomeTipo='" . esc($obj->getNomeTipo()) . "' where idTipo=" . (int) $obj->getIdTipo();
    $result = mysqli_query($conexao, $sql);

    if ($result) {
        registarAccao("Editou tipo de viatura: " . trim($obj->getNomeTipo()));
        return array(true, "Tipo de viatura actualizado com sucesso");
    }
    return array(false, "Ocorreu um erro ao actualizar os dados");
}

// Remoção lógica: o registo fica na tabela (activo=0) para não afectar os registos que dependem dele
function remocaoTipoViatura($id)
{
    global $conexao;

    $antigo = encontraTipoViatura($id);
    if (!$antigo) {
        return array(false, "Tipo de viatura não encontrado");
    }

    $sql = "update tipoviatura set activo=0 where idTipo=" . (int) $id;
    $result = mysqli_query($conexao, $sql);

    if ($result) {
        registarAccao("Removeu tipo de viatura: " . trim($antigo->getNomeTipo()));
        return array(true, "Tipo de viatura removido com sucesso");
    }
    return array(false, "Ocorreu um erro ao remover");
}
