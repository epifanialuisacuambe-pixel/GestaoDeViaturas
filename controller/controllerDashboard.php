<?php

include_once(__DIR__ . "/conexao.php");
include_once(__DIR__ . "/helpers.php");
include_once(__DIR__ . "/controllerViatura.php");

// Total de registos de cada entidade que o utilizador pode consultar
function dashboardTotais()
{
    $totais = array();
    foreach (entidades() as $chave => $entidade) {
        if (temPermissao($chave . ':listar')) {
            $totais[$chave] = contarRegistos($entidade['tabela']);
        }
    }
    return $totais;
}

// As últimas viaturas cadastradas (mais recentes primeiro)
function dashboardUltimasViaturas($quantidade)
{
    $viaturas = listarViatura();
    return array_slice($viaturas, 0, $quantidade);
    }
