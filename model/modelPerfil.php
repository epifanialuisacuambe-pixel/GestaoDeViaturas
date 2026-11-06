<?php

class Perfil
{
    private $idPerfil;
    private $nomePerfil;

    public function __construct($idPerfil, $nomePerfil)
    {
        $this->idPerfil = $idPerfil;
        $this->nomePerfil = $nomePerfil;
    }

    public function getIdPerfil()
    {
        return $this->idPerfil;
    }

    public function setIdPerfil($idPerfil)
    {
        $this->idPerfil = $idPerfil;
        return $this;
    }

    public function getNomePerfil()
    {
        return $this->nomePerfil;
    }

    public function setNomePerfil($nomePerfil)
    {
        $this->nomePerfil = $nomePerfil;
        return $this;
    }
}
