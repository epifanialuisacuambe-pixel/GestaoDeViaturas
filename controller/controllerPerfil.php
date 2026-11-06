<?php

include_once(__DIR__ . "/../model/modelPerfil.php");
include_once(__DIR__ . "/conexao.php");
include_once(__DIR__ . "/helpers.php");
include_once(__DIR__ . "/controllerLog.php");

function listarPerfil()
{
    global $conexao;
    $perfis = array();
    $result = mysqli_query($conexao, "select * from perfil order by idPerfil");
    if ($result) {
        while ($rs = mysqli_fetch_assoc($result)) {
            array_push($perfis, new Perfil($rs['idPerfil'], $rs['nomePerfil']));
        }
    }
    return $perfis;
}

function encontraPerfil($idPerfil)
{
    global $conexao;
    $result = mysqli_query($conexao, "select * from perfil where idPerfil=" . (int) $idPerfil);
    $rs = $result ? mysqli_fetch_assoc($result) : null;
    if (!$rs) {
        return null;
    }
    return new Perfil($rs['idPerfil'], $rs['nomePerfil']);
}

// Valida o nome do perfil (obrigatório e único)
function validarPerfil($perfil)
{
    global $conexao;
    $nome = trim($perfil->getNomePerfil());

    if ($nome == "") {
        return "Escreva o nome do perfil";
    }
    if (strlen($nome) > 40) {
        return "O nome do perfil aceita no máximo 40 caracteres";
    }

    $sql = "select idPerfil from perfil where nomePerfil='" . esc($nome) . "' and idPerfil<>" . (int) $perfil->getIdPerfil();
    $result = mysqli_query($conexao, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        return "Já existe um perfil com esse nome";
    }
    return "";
}

function criacaoPerfil($perfil, $codigos)
{
    global $conexao;

    $erro = validarPerfil($perfil);
    if ($erro != "") {
        return array(false, $erro);
    }

    $nome = strtoupper(trim($perfil->getNomePerfil()));
    $result = mysqli_query($conexao, "insert into perfil (nomePerfil) values ('" . esc($nome) . "')");

    if ($result) {
        gravarPermissoesPerfil(mysqli_insert_id($conexao), $codigos);
        registarAccao("Cadastrou perfil: " . $nome);
        return array(true, "Perfil criado com sucesso");
    }
    return array(false, "Ocorreu um erro ao gravar o perfil");
}

// O perfil Administrador não pode ser alterado. Os perfis não são removidos.
function editarPerfil($perfil, $codigos)
{
    global $conexao;

    if ((int) $perfil->getIdPerfil() === ID_PERFIL_ADMIN) {
        return array(false, "O perfil Administrador não pode ser alterado");
    }
    if (!encontraPerfil($perfil->getIdPerfil())) {
        return array(false, "Perfil não encontrado");
    }

    $erro = validarPerfil($perfil);
    if ($erro != "") {
        return array(false, $erro);
    }

    $nome = strtoupper(trim($perfil->getNomePerfil()));
    $result = mysqli_query($conexao, "update perfil set nomePerfil='" . esc($nome) . "' where idPerfil=" . (int) $perfil->getIdPerfil());

    if ($result) {
        gravarPermissoesPerfil($perfil->getIdPerfil(), $codigos);
        registarAccao("Editou perfil: " . $nome);
        return array(true, "Perfil actualizado com sucesso");
    }
    return array(false, "Ocorreu um erro ao actualizar o perfil");
}

// ---------- PERMISSÕES ----------

// Todas as permissões, organizadas por entidade: array(entidade => array(accao => codigo))
// A ordem segue o menu e os logs ficam no fim.
function listarPermissoesPorEntidade()
{
    global $conexao;
    $porEntidade = array();

    foreach (array_keys(entidades()) as $chave) {
        $porEntidade[$chave] = array();
    }
    $porEntidade['utilizador'] = array();
    $porEntidade['log'] = array();

    $result = mysqli_query($conexao, "select * from permissao order by codigoPermissao");
    if ($result) {
        while ($rs = mysqli_fetch_assoc($result)) {
            if (isset($porEntidade[$rs['entidade']])) {
                $porEntidade[$rs['entidade']][$rs['accao']] = $rs['codigoPermissao'];
            }
        }
    }
    return $porEntidade;
}

// Códigos das permissões que um perfil tem
function permissoesDoPerfil($idPerfil)
{
    global $conexao;
    $codigos = array();
    $result = mysqli_query($conexao, "select codigoPermissao from perfil_permissao where idPerfil=" . (int) $idPerfil);
    if ($result) {
        while ($rs = mysqli_fetch_assoc($result)) {
            array_push($codigos, (int) $rs['codigoPermissao']);
        }
    }
    return $codigos;
}

// Substitui as permissões do perfil pelas que foram marcadas
function gravarPermissoesPerfil($idPerfil, $codigos)
{
    global $conexao;
    $idPerfil = (int) $idPerfil;

    mysqli_query($conexao, "delete from perfil_permissao where idPerfil=" . $idPerfil);
    foreach ($codigos as $codigo) {
        mysqli_query($conexao, "insert into perfil_permissao (idPerfil, codigoPermissao)
                                select " . $idPerfil . ", codigoPermissao from permissao where codigoPermissao=" . (int) $codigo);
    }
}

// Perfis que o utilizador logado pode atribuir: só o administrador atribui o perfil Administrador
function listarPerfisAtribuiveis()
{
    $perfis = listarPerfil();
    if (eAdministrador()) {
        return $perfis;
    }
    $permitidos = array();
    foreach ($perfis as $p) {
        if ((int) $p->getIdPerfil() !== ID_PERFIL_ADMIN) {
            array_push($permitidos, $p);
        }
    }
    return $permitidos;
}

function contarUtilizadoresDoPerfil($idPerfil)
{
    global $conexao;
    $result = mysqli_query($conexao, "select count(*) as total from utilizadores where activo=1 and idPerfil=" . (int) $idPerfil);
    $rs = $result ? mysqli_fetch_assoc($result) : null;
    return $rs ? (int) $rs['total'] : 0;
}
