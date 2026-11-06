<?php

class Continente
{
    private $idContinente;
    private $nomeContinente;

    public function __construct($idContinente, $nomeContinente)
    {
        $this->idContinente = $idContinente;
        $this->nomeContinente = $nomeContinente;
    }

    public function getIdContinente()
    {
        return $this->idContinente;
    }

    public function setIdContinente($idContinente)
    {
        $this->idContinente = $idContinente;
        return $this;
    }

    public function getNomeContinente()
    {
        return $this->nomeContinente;
    }

    public function setNomeContinente($nomeContinente)
    {
        $this->nomeContinente = $nomeContinente;
        return $this;
    }
}
