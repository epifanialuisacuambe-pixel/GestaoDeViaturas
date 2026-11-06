<?php

include_once(__DIR__ . "/../model/Viatura.php");
include_once(__DIR__ . "/conexao.php");
include_once(__DIR__ . "/helpers.php");
include_once(__DIR__ . "/controllerLog.php");

function montarViatura($rs)
{
    return new Viatura($rs['idViatura'], $rs['matricula'], $rs['anoFabrico'], $rs['cilindrada'],
     $rs['numeroChassi'], $rs['preco'], $rs['idModelo'], $rs['idTipo'], $rs['idCor'], $rs['nomeModelo'],
      $rs['nomeMarca'], $rs['nomeFabricante'], $rs['nomeTipo'], $rs['nomeCor'], $rs['hexCor']);
}

function consultarViatura($filtro = "", $ordem = "v.idViatura desc")
{
    global $conexao;
    $lista = array();
    $sql = "select v.*, mo.nomeModelo, ma.nomeMarca, f.nomeFabricante, t.nomeTipo, c.nomeCor, c.hexCor 
                   from viatura v join modelo mo on mo.idModelo = v.idModelo 
                   join marca ma on ma.idMarca = mo.idMarca join fabricante f on f.idFabricante = ma.idFabricante 
                   join tipoviatura t on t.idTipo = v.idTipo join cor c on c.idCor = v.idCor 
                   where v.activo=1" . $filtro . " order by " . $ordem;
    $result = mysqli_query($conexao, $sql);

    if ($result) {
        while ($rs = mysqli_fetch_assoc($result)) {
            array_push($lista, montarViatura($rs));
        }
    }
    return $lista;
}

function listarViatura()
{
    return consultarViatura();
}

function pesquisarViatura($pesquisa)
{
    $p = esc($pesquisa);
    return consultarViatura(" and (v.matricula like '%{$p}%' or v.numeroChassi like '%{$p}%' or v.anoFabrico like '%{$p}%' 
                             or v.cilindrada like '%{$p}%' or v.preco like '%{$p}%' or mo.nomeModelo like '%{$p}%' 
                             or ma.nomeMarca like '%{$p}%' or f.nomeFabricante like '%{$p}%' or t.nomeTipo like '%{$p}%' 
                             or c.nomeCor like '%{$p}%')");
}

function encontraViatura($id)
{
    $lista = consultarViatura(" and v.idViatura=" . (int) $id);
    return count($lista) > 0 ? $lista[0] : null;
}

function validarViatura($obj, $idIgnorar = 0)//serve para indicar qual ID deve ser ignorado durante as verificações.
{
    global $conexao;

    if (trim($obj->getMatricula()) == "") {
        return "Preencha a matrícula";
    }
    if (strlen(trim($obj->getMatricula())) > 20) {
        return "O campo Matrícula aceita no máximo 20 caracteres";
    }
    if (!is_numeric($obj->getAnoFabrico())) {
        return "Preencha o ano de fabrico";
    }
    if (!is_numeric($obj->getCilindrada())) {
        return "Preencha a cilindrada";
    }
    if (trim($obj->getNumeroChassi()) == "") {
        return "Preencha o número de chassi";
    }//strlen() serve para contar o número de caracteres de um texto.
    if (strlen(trim($obj->getNumeroChassi())) > 30) {
        return "O campo Número de chassi aceita no máximo 30 caracteres";
    }//A função is_numeric() verifica se um valor é numérico,
    if (!is_numeric($obj->getPreco())) {
        return "Preencha o preço";
    }//!existeActivo() verifica se nao existe um registo activo na base de dados com os dados indicados.
    if (!existeActivo("modelo", "idModelo", (int) $obj->getIdModelo())) {
        return "Seleccione o modelo";
    }
    if (!existeActivo("tipoviatura", "idTipo", (int) $obj->getIdTipo())) {
        return "Seleccione o tipo de viatura";
    }
    if (!existeActivo("cor", "idCor", (int) $obj->getIdCor())) {
        return "Seleccione a cor";
    }

    $obj->setMatricula(strtoupper(trim($obj->getMatricula())));
    if (!preg_match('/^[A-Z0-9 -]{3,20}$/', $obj->getMatricula())) {
        return "A matrícula só pode ter letras, números, espaços e hífen (3 a 20 caracteres)";
    }
    $obj->setNumeroChassi(strtoupper(preg_replace('/\s+/', '', trim($obj->getNumeroChassi()))));
    if (!preg_match('/^[A-Z0-9]{5,30}$/', $obj->getNumeroChassi())) {
        return "O número de chassi só pode ter letras e números (5 a 30 caracteres)";
    }//A função date('Y') obtém o ano actual
    $anoActual = (int) date('Y');
    if ((int) $obj->getAnoFabrico() < 1900 || (int) $obj->getAnoFabrico() > $anoActual) {
        return "O ano de fabrico deve estar entre 1900 e " . $anoActual;
    }
    if ((int) $obj->getCilindrada() < 50 || (int) $obj->getCilindrada() > 20000) {
        return "A cilindrada deve estar entre 50 e 20000 cm³";
    }
    if ((float) $obj->getPreco() <= 0 || (float) $obj->getPreco() > 20000000000) {
        return "Escreva um preço válido (maior que zero)";
    }

    $sql = "select idViatura from viatura where activo=1 and matricula='" . esc($obj->getMatricula()) . "' and idViatura<>" . (int) $idIgnorar;
    $result = mysqli_query($conexao, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        return "Já existe uma viatura com essa matrícula";
    }
    $sql = "select idViatura from viatura where activo=1 and numeroChassi='" . esc($obj->getNumeroChassi()) . "' and idViatura<>" . (int) $idIgnorar;
    $result = mysqli_query($conexao, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        return "Já existe uma viatura com esse número de chassi";
    }
    return "";
}

function criacaoViatura($obj)
{
    global $conexao;

    $erro = validarViatura($obj);
    if ($erro != "") {
        return array(false, $erro);
    }
    $sql = "insert into viatura (matricula, anoFabrico, cilindrada, numeroChassi, preco, idModelo, idTipo, idCor) values (
        '". esc($obj->getMatricula()) . "', 
        " . (int) $obj->getAnoFabrico() . ", 
        " . (int) $obj->getCilindrada() . ", 
        '". esc($obj->getNumeroChassi()) . "', 
        " . (float) $obj->getPreco() . ", 
        " . (int) $obj->getIdModelo() . ", 
        " . (int) $obj->getIdTipo() . ",
        " . (int) $obj->getIdCor() . ")";
    $result = mysqli_query($conexao, $sql);

    if ($result) {
        registarAccao("Cadastrou viatura: " . trim($obj->getMatricula()));
        return array(true, "Viatura cadastrada com sucesso");
    }
    return array(false, "Ocorreu um erro ao gravar os dados");
}

function edicaoViatura($obj)
{
    global $conexao;

    if (!encontraViatura($obj->getIdViatura())) {
        return array(false, "Viatura não encontrada");
    }

    $erro = validarViatura($obj, $obj->getIdViatura());
    if ($erro != "") {
        return array(false, $erro);
    }

    $sql = "update viatura set matricula='" . esc($obj->getMatricula()) . 
           "', anoFabrico=" . (int) $obj->getAnoFabrico() . 
           ", cilindrada=" . (int) $obj->getCilindrada() . 
           ", numeroChassi='" . esc($obj->getNumeroChassi()) . 
           "', preco=" . (float) $obj->getPreco() . 
           ", idModelo=" . (int) $obj->getIdModelo() . 
           ", idTipo=" . (int) $obj->getIdTipo() . 
           ", idCor=" . (int) $obj->getIdCor() . 
           " where idViatura=" . (int) $obj->getIdViatura();
    $result = mysqli_query($conexao, $sql);

    if ($result) {
        registarAccao("Editou viatura: " . trim($obj->getMatricula()));
        return array(true, "Viatura actualizada com sucesso");
    }
    return array(false, "Ocorreu um erro ao actualizar os dados");
}

// Remoção lógica: o registo fica na tabela (activo=0) para não afectar os registos que dependem dele
function remocaoViatura($id)
{
    global $conexao;

    $antigo = encontraViatura($id);
    if (!$antigo) {
        return array(false, "Viatura não encontrada");
    }

    $sql = "update viatura set activo=0 where idViatura=" . (int) $id;
    $result = mysqli_query($conexao, $sql);

    if ($result) {
        registarAccao("Removeu viatura: " . trim($antigo->getMatricula()));
        return array(true, "Viatura removida com sucesso");
    }
    return array(false, "Ocorreu um erro ao remover");
}

