<?php

class Utilizador
{
    private $idUtilizador;
    private $nome;
    private $apelido;
    private $contacto;
    private $email;
    private $username;
    private $senha;
    private $idPerfil;
    private $nomePerfil;

    public function __construct($idUtilizador, $nome, $apelido, $contacto, $email, $username, $senha, $idPerfil, $nomePerfil = null)
    {
        $this->idUtilizador = $idUtilizador;
        $this->nome = $nome;
        $this->apelido = $apelido;
        $this->contacto = $contacto;
        $this->email = $email;
        $this->username = $username;
        $this->senha = $senha;
        $this->idPerfil = $idPerfil;
        $this->nomePerfil = $nomePerfil;
    }

    public function getIdUtilizador()
    {
        return $this->idUtilizador;
    }

    public function setIdUtilizador($idUtilizador)
    {
        $this->idUtilizador = $idUtilizador;
        return $this;
    }

    public function getNome()
    {
        return $this->nome;
    }

    public function setNome($nome)
    {
        $this->nome = $nome;
        return $this;
    }

    public function getApelido()
    {
        return $this->apelido;
    }

    public function setApelido($apelido)
    {
        $this->apelido = $apelido;
        return $this;
    }

    public function getContacto()
    {
        return $this->contacto;
    }

    public function setContacto($contacto)
    {
        $this->contacto = $contacto;
        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    public function getSenha()
    {
        return $this->senha;
    }

    public function setSenha($senha)
    {
        $this->senha = $senha;
        return $this;
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
