<?php

class Log
{
    private $idLog;
    private $idUtilizador;
    private $nome;
    private $perfil;
    private $accao;
    private $dataAccao;
    private $username;

    public function __construct($idLog, $idUtilizador, $nome, $perfil, $accao, $dataAccao, $username)
    {
        $this->idLog = $idLog;
        $this->idUtilizador = $idUtilizador;
        $this->nome = $nome;
        $this->perfil = $perfil;
        $this->accao = $accao;
        $this->dataAccao = $dataAccao;
        $this->username = $username;
    }

    public function getIdLog()
    {
        return $this->idLog;
    }

    public function getIdUtilizador()
    {
        return $this->idUtilizador;
    }

    public function getNome()
    {
        return $this->nome;
    }

    public function getPerfil()
    {
        return $this->perfil;
    }

    public function getAccao()
    {
        return $this->accao;
    }

    public function getDataAccao()
    {
        return $this->dataAccao;
    }

    public function getUsername()
    {
        return $this->username;
    }
}
