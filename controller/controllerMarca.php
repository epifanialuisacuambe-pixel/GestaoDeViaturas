<?php

include_once(__DIR__ . "/../model/Marca.php");
include_once(__DIR__ . "/conexao.php");
include_once(__DIR__ . "/helpers.php");
include_once(__DIR__ . "/controllerLog.php");

// Converte uma linha da base de dados num objecto Marca
function montarMarca($rs)
{
    return new Marca($rs['idMarca'], $rs['nomeMarca'], $rs['idFabricante'], $rs['nomeFabricante']);
}

// Consulta base (com as junções). Só devolve registos activos.
function consultarMarca($filtro = "", $ordem = "m.idMarca desc")
{
    global $conexao;
    $lista = array();
    $sql = "select m.*, f.nomeFabricante from marca m join fabricante f on f.idFabricante = m.idFabricante where m.activo=1" . $filtro . " order by " . $ordem;
    $result = mysqli_query($conexao, $sql);

    if ($result) {
        while ($rs = mysqli_fetch_assoc($result)) {
            array_push($lista, montarMarca($rs));
        }
    }
    return $lista;
}

function listarMarca()
{
    return consultarMarca();
}

function pesquisarMarca($pesquisa)
{
    $p = esc($pesquisa);
    return consultarMarca(" and (m.nomeMarca like '%{$p}%' or f.nomeFabricante like '%{$p}%')");
}

function encontraMarca($id)
{
    $lista = consultarMarca(" and m.idMarca=" . (int) $id);
    return count($lista) > 0 ? $lista[0] : null;
}

// Devolve "" se os dados são válidos, ou a mensagem de erro
function validarMarca($obj, $idIgnorar = 0)
{
    global $conexao;

    if (trim($obj->getNomeMarca()) == "") {
        return "Preencha o nome da marca";
    }
    if (strlen(trim($obj->getNomeMarca())) > 80) {
        return "O campo Nome da marca aceita no máximo 80 caracteres";
    }
    if (!existeActivo("fabricante", "idFabricante", (int) $obj->getIdFabricante())) {
        return "Seleccione o fabricante";
    }

    $sql = "select idMarca from marca where activo=1 and nomeMarca='" . esc($obj->getNomeMarca()) . "' and idMarca<>" . (int) $idIgnorar;
    $result = mysqli_query($conexao, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        return "Já existe uma marca com esse nome";
    }
    return "";
}

function criacaoMarca($obj)
{
    global $conexao;

    $erro = validarMarca($obj);
    if ($erro != "") {
        return array(false, $erro);
    }

    $sql = "insert into marca (nomeMarca, idFabricante) values ('" . esc($obj->getNomeMarca()) . "', " . (int) $obj->getIdFabricante() . ")";
    $result = mysqli_query($conexao, $sql);

    if ($result) {
        registarAccao("Cadastrou marca: " . trim($obj->getNomeMarca()));
        return array(true, "Marca cadastrada com sucesso");
    }
    return array(false, "Ocorreu um erro ao gravar os dados");
}

function edicaoMarca($obj)
{
    global $conexao;

    if (!encontraMarca($obj->getIdMarca())) {
        return array(false, "Marca não encontrada");
    }

    $erro = validarMarca($obj, $obj->getIdMarca());
    if ($erro != "") {
        return array(false, $erro);
    }

    $sql = "update marca set nomeMarca='" . esc($obj->getNomeMarca()) . "', idFabricante=" . (int) $obj->getIdFabricante() . " where idMarca=" . (int) $obj->getIdMarca();
    $result = mysqli_query($conexao, $sql);

    if ($result) {
        registarAccao("Editou marca: " . trim($obj->getNomeMarca()));
        return array(true, "Marca actualizada com sucesso");
    }
    return array(false, "Ocorreu um erro ao actualizar os dados");
}

// Remoção lógica: o registo fica na tabela (activo=0) para não afectar os registos que dependem dele
function remocaoMarca($id)
{
    global $conexao;

    $antigo = encontraMarca($id);
    if (!$antigo) {
        return array(false, "Marca não encontrada");
    }

    $sql = "update marca set activo=0 where idMarca=" . (int) $id;
    $result = mysqli_query($conexao, $sql);

    if ($result) {
        registarAccao("Removeu marca: " . trim($antigo->getNomeMarca()));
        return array(true, "Marca removida com sucesso");
    }
    return array(false, "Ocorreu um erro ao remover");
}
