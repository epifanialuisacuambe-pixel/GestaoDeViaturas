<?php

include_once __DIR__ . '/../controller/controllerLogin.php';

registarAccao("Log out");
$sessao->logout();
session_destroy();
header("Location: Login.php");
exit;
