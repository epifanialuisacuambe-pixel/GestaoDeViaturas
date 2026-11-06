<?php

class Viatura
{
    private $idViatura;
    private $matricula;
    private $anoFabrico;
    private $cilindrada;
    private $numeroChassi;
    private $preco;
    private $idModelo;
    private $idTipo;
    private $idCor;
    private $nomeModelo;
    private $nomeMarca;
    private $nomeFabricante;
    private $nomeTipo;
    private $nomeCor;
    private $hexCor;

    public function __construct($idViatura, $matricula, $anoFabrico, $cilindrada, $numeroChassi, $preco, $idModelo, 
                               $idTipo, $idCor, $nomeModelo = null, $nomeMarca = null, $nomeFabricante = null, $nomeTipo = null, $nomeCor = null, $hexCor = null)
    {
        $this->idViatura = $idViatura;
        $this->matricula = $matricula;
        $this->anoFabrico = $anoFabrico;
        $this->cilindrada = $cilindrada;
        $this->numeroChassi = $numeroChassi;
        $this->preco = $preco;
        $this->idModelo = $idModelo;
        $this->idTipo = $idTipo;
        $this->idCor = $idCor;
        $this->nomeModelo = $nomeModelo;
        $this->nomeMarca = $nomeMarca;
        $this->nomeFabricante = $nomeFabricante;
        $this->nomeTipo = $nomeTipo;
        $this->nomeCor = $nomeCor;
        $this->hexCor = $hexCor;
    }

    //get serve para obter os valores/ set serve para definir ou alterar um valor.
    public function getIdViatura()
    {
        return $this->idViatura;
    }

    public function setIdViatura($idViatura)
    {
        $this->idViatura = $idViatura;
        return $this;
    }

    public function getMatricula()
    {
        return $this->matricula;
    }

    public function setMatricula($matricula)
    {
        $this->matricula = $matricula;
        return $this;
    }

    public function getAnoFabrico()
    {
        return $this->anoFabrico;
    }

    public function setAnoFabrico($anoFabrico)
    {
        $this->anoFabrico = $anoFabrico;
        return $this;
    }

    public function getCilindrada()
    {
        return $this->cilindrada;
    }

    public function setCilindrada($cilindrada)
    {
        $this->cilindrada = $cilindrada;
        return $this;
    }

    public function getNumeroChassi()
    {
        return $this->numeroChassi;
    }

    public function setNumeroChassi($numeroChassi)
    {
        $this->numeroChassi = $numeroChassi;
        return $this;
    }

    public function getPreco()
    {
        return $this->preco;
    }

    public function setPreco($preco)
    {
        $this->preco = $preco;
        return $this;
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

    public function getIdTipo()
    {
        return $this->idTipo;
    }

    public function setIdTipo($idTipo)
    {
        $this->idTipo = $idTipo;
        return $this;
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

    public function getNomeModelo()
    {
        return $this->nomeModelo;
    }

    public function setNomeModelo($nomeModelo)
    {
        $this->nomeModelo = $nomeModelo;
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

    public function getNomeTipo()
    {
        return $this->nomeTipo;
    }

    public function setNomeTipo($nomeTipo)
    {
        $this->nomeTipo = $nomeTipo;
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

    // Tempo de existência da viatura, calculado a partir do ano de fabrico
    public function getTempoExistencia()
    {
        $anos = (int) date('Y') - (int) $this->anoFabrico;
        if ($anos <= 0) {
            return "Menos de 1 ano";
        }
        if ($anos == 1) {
            return "1 ano";
        }
        return $anos . " anos";
    }
}
