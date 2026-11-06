<?php

// Ligação única à base de dados (todos os controllers usam esta variável $conexao)
mysqli_report(MYSQLI_REPORT_OFF);

$conexao = mysqli_connect("localhost", "root", "epif@maepa133fa", "gestaoviaturas");

if (!$conexao) {
    die("Não foi possível ligar à base de dados. Verifique se o MySQL está ligado e se a base 'gestaoviaturas' foi criada.");
}

mysqli_set_charset($conexao, "utf8mb4");
