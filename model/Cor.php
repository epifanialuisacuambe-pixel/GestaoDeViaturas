<?php

class Cor
{
    private $idCor;
    private $nomeCor;
    private $hexCor;

    public function __construct($idCor, $nomeCor, $hexCor)
    {
        $this->idCor = $idCor;
        $this->nomeCor = $nomeCor;
        $this->hexCor = $hexCor;
    }

    public function getIdCor()
    {
        return $this->idCor;
    }

    public function setIdCor($idCor)
    {
        $this->idCor = $idCor;
        return $this;
    }

    public function getNomeCor()
    {
        return $this->nomeCor;
    }

    public function setNomeCor($nomeCor)
    {
        $this->nomeCor = $nomeCor;
        return $this;
    }

    public function getHexCor()
    {
        return $this->hexCor;
    }

    public function setHexCor($hexCor)
    {
        $this->hexCor = $hexCor;
        return $this;
    }
}
