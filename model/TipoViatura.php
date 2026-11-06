<?php

class TipoViatura
{
    private $idTipo;
    private $nomeTipo;

    public function __construct($idTipo, $nomeTipo)
    {
        $this->idTipo = $idTipo;
        $this->nomeTipo = $nomeTipo;
    }

    public function getIdTipo()
    {
        return $this->idTipo;
    }

    public function setIdTipo($idTipo)
    {
        $this->idTipo = $idTipo;
        return $this;
    }

    public function getNomeTipo()
    {
        return $this->nomeTipo;
    }

    public function setNomeTipo($nomeTipo)
    {
        $this->nomeTipo = $nomeTipo;
        return $this;
    }
}
