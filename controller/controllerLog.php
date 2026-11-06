<?php

include_once(__DIR__ . "/../model/LogModel.php");
include_once(__DIR__ . "/conexao.php");
include_once(__DIR__ . "/helpers.php");

function registarLog($idUtilizador, $nome, $perfil, $accao)
{
    global $conexao;

    $sql = "insert into log (idUtilizador, nome, perfil, accao, dataAccao)
            values (" . (int) $idUtilizador . ", '" . esc($nome) . "', '" . esc($perfil) . "', '" . esc($accao) . "', now())";
    mysqli_query($conexao, $sql);
}

// Regista uma acção feita pelo utilizador que está com a sessão iniciada
function registarAccao($accao)
{
    if (!isset($_SESSION['idUtilizador'])) {
        return;
    }
    registarLog($_SESSION['idUtilizador'], $_SESSION['nome'] ?? '', $_SESSION['perfil'] ?? '', $accao);
}

function consultarLog($filtro = "")
{
    global $conexao;
    $logs = array();

    $sql = "select l.*, u.username from log l
            left join utilizadores u on u.idUtilizador = l.idUtilizador
            {$filtro}
            order by l.dataAccao desc, l.idLog desc";
    $result = mysqli_query($conexao, $sql);

    if ($result) {
        while ($rs = mysqli_fetch_assoc($result)) {
            array_push($logs, new Log($rs['idLog'], $rs['idUtilizador'], $rs['nome'], $rs['perfil'], $rs['accao'], $rs['dataAccao'], $rs['username']));
        }
    }
    return $logs;
}

function listaDeLogs()
{
    return consultarLog();
}

function pesquisarLog($pesquisa)
{
    $p = esc($pesquisa);
    return consultarLog("where l.nome like '%{$p}%' or l.perfil like '%{$p}%' or l.accao like '%{$p}%'
                         or u.username like '%{$p}%' or l.dataAccao like '%{$p}%'");
}
