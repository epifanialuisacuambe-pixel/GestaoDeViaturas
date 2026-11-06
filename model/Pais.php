<?php

class Pais
{
    private $idPais;
    private $nomePais;
    private $idContinente;
    private $nomeContinente;

    public function __construct($idPais, $nomePais, $idContinente, $nomeContinente = null)
    {
        $this->idPais = $idPais;
        $this->nomePais = $nomePais;
        $this->idContinente = $idContinente;
        $this->nomeContinente = $nomeContinente;
    }

    public function getIdPais()
    {
        return $this->idPais;
    }

    public function setIdPais($idPais)
    {
        $this->idPais = $idPais;
        return $this;
    }

    public function getNomePais()
    {
        return $this->nomePais;
    }

    public function setNomePais($nomePais)
    {
        $this->nomePais = $nomePais;
        return $this;
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
