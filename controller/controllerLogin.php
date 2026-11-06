<?php

include_once(__DIR__ . "/conexao.php");
include_once(__DIR__ . "/helpers.php");
include_once(__DIR__ . "/controllerLog.php");

class Sessao
{
    private $logado = false;
    public $idUtilizador;
    public $username;

    function __construct()
    {
        session_start();
        $this->verificar_login();
    }

    private function verificar_login()
    {
        if (isset($_SESSION['idUtilizador']) && isset($_SESSION['username'])) {
            $this->idUtilizador = $_SESSION['idUtilizador'];
            $this->username = $_SESSION['username'];
            $this->logado = true;
        } else {
            unset($this->idUtilizador);
            unset($this->username);
            $this->logado = false;
        }
    }

    public function esta_logado()
    {
        return $this->logado;
    }

    public function login($utilizador)
    {
        if ($utilizador) {
            $_SESSION['idUtilizador'] = $utilizador['idUtilizador'];
            $_SESSION['username'] = $utilizador['username'];
            $_SESSION['nome'] = $utilizador['nome'];
             $_SESSION['apelido'] = $utilizador['apelido'];
            $_SESSION['idPerfil'] = $utilizador['idPerfil'];
            $_SESSION['perfil'] = $utilizador['nomePerfil'];

            $this->idUtilizador = $_SESSION['idUtilizador'];
            $this->username = $_SESSION['username'];
            $this->logado = true;
        }
    }

    public function logout()
    {
        unset($_SESSION['idUtilizador']);
        unset($_SESSION['username']);
        unset($_SESSION['nome']);
        unset($_SESSION['apelido']);
        unset($_SESSION['idPerfil']);
        unset($_SESSION['perfil']);
        unset($this->idUtilizador);
        unset($this->username);

        $this->logado = false;
    }
}

$sessao = new Sessao();

// Valida o username e a senha. Devolve array(estado, mensagem):
// 'ok' = entrou | 'trocar' = tem de trocar a senha padrão | 'erro' = dados errados
function login($username, $senha)
{
    global $conexao, $sessao;

    $sql = "select u.*, p.nomePerfil from utilizadores u
            join perfil p on p.idPerfil = u.idPerfil
            where u.username='" . esc($username) . "' and u.activo=1";
    $result = mysqli_query($conexao, $sql);

    if (!$result || mysqli_num_rows($result) == 0) {
        return array('erro', 'Username ou senha incorrectos');
    }

    $utilizador = mysqli_fetch_assoc($result);

    if (!password_verify($senha, $utilizador['senha'])) {
        return array('erro', 'User name achado, mas senha incorrecta');
    }

    // Primeiro acesso (ou senha resetada): não entra no sistema sem trocar a senha
    if ($senha === SENHA_PADRAO) {
        $_SESSION['username_trocar_senha'] = $utilizador['username'];
        return array('trocar', '');
    }

    $sessao->login($utilizador);
    registarAccao("Log in");
    return array('ok', '');
}

// ---------- Protecção das páginas ----------

// Use no topo de cada página: sem sessão, volta para o login
function exigirLogin()
{
    global $sessao;
    if (!$sessao->esta_logado()) {
        header("Location: Login.php");
        exit;
    }
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Pragma: no-cache");
}

function eAdministrador()
{
    return isset($_SESSION['idPerfil']) && (int) $_SESSION['idPerfil'] === ID_PERFIL_ADMIN;
}

// Devolve true se o perfil do utilizador tem a permissão (ex.: 'viatura:criar' ou 'log:listar').
// A consulta é feita a cada pedido, por isso uma alteração no perfil aplica-se logo.
function temPermissao($chave)
{
    global $conexao;
    static $chaves = null;

    // O administrador tem CRUD de todas as entidades e gere utilizadores e perfis; os logs são do auditor
    if (eAdministrador() && strpos($chave, 'log:') !== 0) {
        return true;
    }

    if ($chaves === null) {
        $chaves = array();
        $idPerfil = (int) ($_SESSION['idPerfil'] ?? 0);
        $sql = "select p.chave from perfil_permissao pp
                join permissao p on p.codigoPermissao = pp.codigoPermissao
                where pp.idPerfil=" . $idPerfil;
        $result = mysqli_query($conexao, $sql);
        if ($result) {
            while ($rs = mysqli_fetch_assoc($result)) {
                array_push($chaves, $rs['chave']);
            }
        }
    }
    return in_array($chave, $chaves);
}

// Sem a permissão, volta para o Dashboard
function exigirPermissao($chave)
{
    if (!temPermissao($chave)) {
        header("Location: Dashboard.php");
        exit;
    }
}

// Páginas só do Administrador (utilizadores e perfis)
function exigirAdministrador()
{
    if (!eAdministrador()) {
        header("Location: Dashboard.php");
        exit;
    }
}
