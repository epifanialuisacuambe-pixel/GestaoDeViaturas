<?php

include_once(__DIR__ . "/../model/utilizadoresModel.php");
include_once(__DIR__ . "/conexao.php");
include_once(__DIR__ . "/helpers.php");
include_once(__DIR__ . "/controllerLog.php");

function montarUtilizador($rs)
{
    return new Utilizador($rs['idUtilizador'], $rs['nome'], $rs['apelido'], $rs['contacto'], $rs['email'], $rs['username'], $rs['senha'], $rs['idPerfil'], $rs['nomePerfil']);
}

// Consulta base: só utilizadores activos, já com o nome do perfil
function consultarUtilizador($filtro = "")
{
    global $conexao;
    $lista = array();
    $sql = "select u.*, p.nomePerfil from utilizadores u
            join perfil p on p.idPerfil = u.idPerfil
            where u.activo=1" . $filtro . " order by u.idUtilizador desc";
    $result = mysqli_query($conexao, $sql);

    if ($result) {
        while ($rs = mysqli_fetch_assoc($result)) {
            array_push($lista, montarUtilizador($rs));
        }
    }
    return $lista;
}

function listarUtilizador()
{
    return consultarUtilizador();
}

function pesquisarUtilizador($pesquisa)
{
    $p = esc($pesquisa);
    return consultarUtilizador(" and (u.nome like '%{$p}%' or u.apelido like '%{$p}%' or u.contacto like '%{$p}%'
        or u.email like '%{$p}%' or u.username like '%{$p}%' or p.nomePerfil like '%{$p}%')");
}

function encontraUtilizador($id)
{
    $lista = consultarUtilizador(" and u.idUtilizador=" . (int) $id);
    return count($lista) > 0 ? $lista[0] : null;
}

// Quantos administradores activos existem (sem contar com o utilizador $idIgnorar)
function contarAdministradores($idIgnorar = 0)
{
    global $conexao;
    $sql = "select count(*) as total from utilizadores
            where activo=1 and idPerfil=" . ID_PERFIL_ADMIN . " and idUtilizador<>" . (int) $idIgnorar;
    $result = mysqli_query($conexao, $sql);
    $rs = $result ? mysqli_fetch_assoc($result) : null;
    return $rs ? (int) $rs['total'] : 0;
}

// Devolve "" se os dados são válidos, ou a mensagem de erro
function validarUtilizador($u, $idIgnorar = 0)
{
    global $conexao;

    if (trim($u->getNome()) == "" || trim($u->getApelido()) == "") {
        return "Preencha o nome e o apelido";
    }
    if (strlen(trim($u->getNome())) > 60 || strlen(trim($u->getApelido())) > 60) {
        return "O nome e o apelido aceitam no máximo 60 caracteres";
    }
    if (!preg_match('/^[0-9+ ]{9,15}$/', trim($u->getContacto()))) {
        return "O contacto deve ter entre 9 e 15 dígitos";
    }
    if (!filter_var(trim($u->getEmail()), FILTER_VALIDATE_EMAIL)) {
        return "Escreva um e-mail válido";
    }
    if (!preg_match('/^[A-Za-z0-9._-]{3,30}$/', trim($u->getUsername()))) {
        return "O username deve ter de 3 a 30 caracteres (letras, números, ponto, hífen ou _)";
    }
    if (!existePerfil($u->getIdPerfil())) {
        return "Seleccione o perfil";
    }
    if (!eAdministrador() && (int) $u->getIdPerfil() === ID_PERFIL_ADMIN) {
        return "Só o administrador pode atribuir o perfil Administrador";
    }

    // username único (inclui utilizadores removidos, porque o username nunca é reutilizado)
    $sql = "select idUtilizador from utilizadores where username='" . esc($u->getUsername()) . "' and idUtilizador<>" . (int) $idIgnorar;
    $result = mysqli_query($conexao, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        return "Esse username já está a ser usado";
    }

    $sql = "select idUtilizador from utilizadores where activo=1 and email='" . esc($u->getEmail()) . "' and idUtilizador<>" . (int) $idIgnorar;
    $result = mysqli_query($conexao, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        return "Esse e-mail já está a ser usado";
    }
    return "";
}

function existePerfil($idPerfil)
{
    global $conexao;
    $result = mysqli_query($conexao, "select 1 from perfil where idPerfil=" . (int) $idPerfil);
    return $result && mysqli_num_rows($result) > 0;
}

function criacaoUtilizador($u)
{
    global $conexao;

    $erro = validarUtilizador($u);
    if ($erro != "") {
        return array(false, $erro);
    }

    // A senha inicial é sempre a padrão; o utilizador troca-a no primeiro acesso
    $senhaEncriptada = password_hash(SENHA_PADRAO, PASSWORD_DEFAULT);

    $sql = "insert into utilizadores (nome, apelido, contacto, email, username, senha, idPerfil)
            values ('" . esc($u->getNome()) . "', '" . esc($u->getApelido()) . "', '" . esc($u->getContacto()) . "',
            '" . esc($u->getEmail()) . "', '" . esc($u->getUsername()) . "', '" . $senhaEncriptada . "', " . (int) $u->getIdPerfil() . ")";
    $result = mysqli_query($conexao, $sql);

    if ($result) {
        registarAccao("Cadastrou utilizador: " . trim($u->getUsername()));
        return array(true, "Utilizador cadastrado com sucesso. Senha inicial: " . SENHA_PADRAO);
    }
    return array(false, "Ocorreu um erro ao gravar os dados");
}

function edicaoUtilizador($u)
{
    global $conexao;

    $antigo = encontraUtilizador($u->getIdUtilizador());
    if (!$antigo) {
        return array(false, "Utilizador não encontrado");
    }

    if (!eAdministrador() && (int) $antigo->getIdPerfil() === ID_PERFIL_ADMIN) {
        return array(false, "Só o administrador pode alterar um administrador");
    }

    $erro = validarUtilizador($u, $u->getIdUtilizador());
    if ($erro != "") {
        return array(false, $erro);
    }

    // Tem de existir sempre pelo menos um administrador
    if ((int) $antigo->getIdPerfil() === ID_PERFIL_ADMIN && (int) $u->getIdPerfil() !== ID_PERFIL_ADMIN
        && contarAdministradores($u->getIdUtilizador()) == 0) {
        return array(false, "Tem de existir sempre pelo menos um administrador");
    }

    $sql = "update utilizadores set nome='" . esc($u->getNome()) . "', apelido='" . esc($u->getApelido()) . "',
            contacto='" . esc($u->getContacto()) . "', email='" . esc($u->getEmail()) . "',
            username='" . esc($u->getUsername()) . "', idPerfil=" . (int) $u->getIdPerfil() . "
            where idUtilizador=" . (int) $u->getIdUtilizador();
    $result = mysqli_query($conexao, $sql);

    if ($result) {
        registarAccao("Editou utilizador: " . trim($u->getUsername()));
        return array(true, "Utilizador actualizado com sucesso");
    }
    return array(false, "Ocorreu um erro ao actualizar os dados");
}

// Remoção lógica (activo=0). Ninguém remove a própria conta nem o último administrador.
function remocaoUtilizador($id)
{
    global $conexao;

    $antigo = encontraUtilizador($id);
    if (!$antigo) {
        return array(false, "Utilizador não encontrado");
    }
    if ((int) $id === (int) ($_SESSION['idUtilizador'] ?? 0)) {
        return array(false, "Não pode remover a sua própria conta");
    }
    if (!eAdministrador() && (int) $antigo->getIdPerfil() === ID_PERFIL_ADMIN) {
        return array(false, "Só o administrador pode remover um administrador");
    }
    if ((int) $antigo->getIdPerfil() === ID_PERFIL_ADMIN && contarAdministradores($id) == 0) {
        return array(false, "Tem de existir sempre pelo menos um administrador");
    }

    $result = mysqli_query($conexao, "update utilizadores set activo=0 where idUtilizador=" . (int) $id);

    if ($result) {
        registarAccao("Removeu utilizador: " . $antigo->getUsername());
        return array(true, "Utilizador removido com sucesso");
    }
    return array(false, "Ocorreu um erro ao remover");
}

function resetarSenha($id)
{
    global $conexao;

    $utilizador = encontraUtilizador($id);
    if (!$utilizador) {
        return array(false, "Utilizador não encontrado");
    }

    if (!eAdministrador() && (int) $utilizador->getIdPerfil() === ID_PERFIL_ADMIN) {
        return array(false, "Só o administrador pode resetar a senha de um administrador");
    }

    $senhaPadrao = password_hash(SENHA_PADRAO, PASSWORD_DEFAULT);
    $result = mysqli_query($conexao, "update utilizadores set senha='" . $senhaPadrao . "' where idUtilizador=" . (int) $id);

    if ($result) {
        registarAccao("Resetou a senha de: " . $utilizador->getUsername());
        return array(true, "Senha de " . $utilizador->getUsername() . " resetada para " . SENHA_PADRAO . " (terá de a trocar no próximo acesso)");
    }
    return array(false, "Ocorreu um erro ao resetar a senha");
}

// Troca de senha (usada no primeiro acesso e no menu "Alterar senha")
function alterarSenha($username, $senhaAntiga, $novaSenha, $confirmaSenha)
{
    global $conexao;

    $sql = "select u.*, p.nomePerfil from utilizadores u join perfil p on p.idPerfil = u.idPerfil
            where u.activo=1 and u.username='" . esc($username) . "'";
    $result = mysqli_query($conexao, $sql);
    $rs = $result ? mysqli_fetch_assoc($result) : null;

    if (!$rs || !password_verify($senhaAntiga, $rs['senha'])) {
        return array(false, "Username ou senha actual incorrectos");
    }
    if (strlen($novaSenha) < 8) {
        return array(false, "A nova senha deve ter pelo menos 8 caracteres");
    }
    if ($novaSenha === SENHA_PADRAO) {
        return array(false, "A nova senha não pode ser a senha padrão");
    }
    if ($novaSenha !== $confirmaSenha) {
        return array(false, "As senhas não são iguais");
    }

    $hash = password_hash($novaSenha, PASSWORD_DEFAULT);
    $result = mysqli_query($conexao, "update utilizadores set senha='" . $hash . "' where idUtilizador=" . (int) $rs['idUtilizador']);

    if ($result) {
        registarLog($rs['idUtilizador'], $rs['nome'], $rs['nomePerfil'], "Alterou a sua senha");
        return array(true, "Senha alterada com sucesso. Já pode entrar.");
    }
    return array(false, "Ocorreu um erro ao alterar a senha");
}
// true se o utilizador já entrou alguma vez no sistema (existe um "Log in" registado)
function jaEntrouNoSistema($idUtilizador)
{
    global $conexao;
    $result = mysqli_query($conexao, "select 1 from log where idUtilizador=" . (int) $idUtilizador . " and accao='Log in' limit 1");
    return $result && mysqli_num_rows($result) > 0;
}