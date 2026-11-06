<?php

class Fabricante
{
    private $idFabricante;
    private $nomeFabricante;
    private $idPais;
    private $nomePais;
    private $nomeContinente;

    public function __construct($idFabricante, $nomeFabricante, $idPais, $nomePais = null, $nomeContinente = null)
    {
        $this->idFabricante = $idFabricante;
        $this->nomeFabricante = $nomeFabricante;
        $this->idPais = $idPais;
        $this->nomePais = $nomePais;
        $this->nomeContinente = $nomeContinente;
    }

    public function getIdFabricante()
    {
        return $this->idFabricante;
    }

    public function setIdFabricante($idFabricante)
    {
        $this->idFabricante = $idFabricante;
        return $this;
    }

    public function getNomeFabricante()
    {
        return $this->nomeFabricante;
    }

    public function setNomeFabricante($nomeFabricante)
    {
        $this->nomeFabricante = $nomeFabricante;
        return $this;
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
