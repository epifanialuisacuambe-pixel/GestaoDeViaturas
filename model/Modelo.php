<?php

class Modelo
{
    private $idModelo;
    private $nomeModelo;
    private $idMarca;
    private $nomeMarca;
    private $nomeFabricante;

    public function __construct($idModelo, $nomeModelo, $idMarca, $nomeMarca = null, $nomeFabricante = null)
    {
        $this->idModelo = $idModelo;
        $this->nomeModelo = $nomeModelo;
        $this->idMarca = $idMarca;
        $this->nomeMarca = $nomeMarca;
        $this->nomeFabricante = $nomeFabricante;
    }

    public function getIdModelo()
    {
        return $this->idModelo;
    }

    public function setIdModelo($idModelo)
    {
        $this->idModelo = $idModelo;
        return $this;
    }

    public function getNomeModelo()
    {
        return $this->nomeModelo;
    }

    public function setNomeModelo($nomeModelo)
    {
        $this->nomeModelo = $nomeModelo;
        return $this;
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
