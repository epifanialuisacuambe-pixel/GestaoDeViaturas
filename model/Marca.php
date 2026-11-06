<?php

class Marca
{
    private $idMarca;
    private $nomeMarca;
    private $idFabricante;
    private $nomeFabricante;

    public function __construct($idMarca, $nomeMarca, $idFabricante, $nomeFabricante = null)
    {
        $this->idMarca = $idMarca;
        $this->nomeMarca = $nomeMarca;
        $this->idFabricante = $idFabricante;
        $this->nomeFabricante = $nomeFabricante;
    }

    public function getIdMarca()
    {
        return $this->idMarca;
    }

    public function setIdMarca($idMarca)
    {
        $this->idMarca = $idMarca;
        return $this;
    }

    public function getNomeMarca()
    {
        return $this->nomeMarca;
    }

    public function setNomeMarca($nomeMarca)
    {
        $this->nomeMarca = $nomeMarca;
        return $this;
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
}
