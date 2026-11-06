-- =====================================================================
--  SISTEMA DE GESTÃO DE VIATURAS  -  base de dados MySQL / MariaDB
--  Importar no phpMyAdmin (separador "Importar") ou no terminal:
--      mysql -u root < gestaoviaturas.sql
-- =====================================================================

SET NAMES utf8mb4;
DROP DATABASE IF EXISTS gestaoviaturas;
CREATE DATABASE gestaoviaturas CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE gestaoviaturas;

-- ---------------------------------------------------------------------
--  ENTIDADES
--  A coluna "activo" faz a remoção lógica: remover = activo 0.
--  O registo fica na tabela, por isso os registos que dependem dele não se perdem.
-- ---------------------------------------------------------------------

CREATE TABLE continente (
    idContinente   INT AUTO_INCREMENT PRIMARY KEY,
    nomeContinente VARCHAR(60) NOT NULL,
    activo         TINYINT(1) NOT NULL DEFAULT 1
);

CREATE TABLE pais (
    idPais       INT AUTO_INCREMENT PRIMARY KEY,
    nomePais     VARCHAR(80) NOT NULL,
    idContinente INT NOT NULL,
    activo       TINYINT(1) NOT NULL DEFAULT 1,
    FOREIGN KEY (idContinente) REFERENCES continente (idContinente)
);

CREATE TABLE fabricante (
    idFabricante   INT AUTO_INCREMENT PRIMARY KEY,
    nomeFabricante VARCHAR(80) NOT NULL,
    idPais         INT NOT NULL,
    activo         TINYINT(1) NOT NULL DEFAULT 1,
    FOREIGN KEY (idPais) REFERENCES pais (idPais)
);

CREATE TABLE marca (
    idMarca      INT AUTO_INCREMENT PRIMARY KEY,
    nomeMarca    VARCHAR(80) NOT NULL,
    idFabricante INT NOT NULL,
    activo       TINYINT(1) NOT NULL DEFAULT 1,
    FOREIGN KEY (idFabricante) REFERENCES fabricante (idFabricante)
);

CREATE TABLE modelo (
    idModelo   INT AUTO_INCREMENT PRIMARY KEY,
    nomeModelo VARCHAR(80) NOT NULL,
    idMarca    INT NOT NULL,
    activo     TINYINT(1) NOT NULL DEFAULT 1,
    FOREIGN KEY (idMarca) REFERENCES marca (idMarca)
);

CREATE TABLE tipoviatura (
    idTipo   INT AUTO_INCREMENT PRIMARY KEY,
    nomeTipo VARCHAR(60) NOT NULL,
    activo   TINYINT(1) NOT NULL DEFAULT 1
);

-- A descrição e o código hexadecimal vêm da paleta de cores (botão "Consultar cor")
CREATE TABLE cor (
    idCor   INT AUTO_INCREMENT PRIMARY KEY,
    nomeCor VARCHAR(40) NOT NULL,
    hexCor  VARCHAR(7)  NOT NULL,
    activo  TINYINT(1) NOT NULL DEFAULT 1
);

CREATE TABLE viatura (
    idViatura    INT AUTO_INCREMENT PRIMARY KEY,
    matricula    VARCHAR(20) NOT NULL,
    anoFabrico   SMALLINT NOT NULL,
    cilindrada   INT NOT NULL,
    numeroChassi VARCHAR(30) NOT NULL,
    preco        DECIMAL(12,2) NOT NULL,
    idModelo     INT NOT NULL,
    idTipo       INT NOT NULL,
    idCor        INT NOT NULL,
    activo       TINYINT(1) NOT NULL DEFAULT 1,
    FOREIGN KEY (idModelo) REFERENCES modelo (idModelo),
    FOREIGN KEY (idTipo)   REFERENCES tipoviatura (idTipo),
    FOREIGN KEY (idCor)    REFERENCES cor (idCor)
);

-- ---------------------------------------------------------------------
--  ADMINISTRAÇÃO: perfis, permissões, utilizadores e logs
-- ---------------------------------------------------------------------

CREATE TABLE perfil (
    idPerfil   INT AUTO_INCREMENT PRIMARY KEY,
    nomePerfil VARCHAR(40) NOT NULL UNIQUE
);

-- Uma permissão = uma acção (listar, criar, actualizar, remover) sobre uma entidade.
-- chave: 'entidade:accao', por exemplo 'viatura:criar' ou 'log:listar'
CREATE TABLE permissao (
    codigoPermissao INT AUTO_INCREMENT PRIMARY KEY,
    entidade        VARCHAR(30) NOT NULL,
    accao           VARCHAR(20) NOT NULL,
    chave           VARCHAR(60) NOT NULL UNIQUE
);

CREATE TABLE perfil_permissao (
    idPerfil        INT NOT NULL,
    codigoPermissao INT NOT NULL,
    PRIMARY KEY (idPerfil, codigoPermissao),
    FOREIGN KEY (idPerfil) REFERENCES perfil (idPerfil),
    FOREIGN KEY (codigoPermissao) REFERENCES permissao (codigoPermissao)
);

CREATE TABLE utilizadores (
    idUtilizador INT AUTO_INCREMENT PRIMARY KEY,
    nome         VARCHAR(60)  NOT NULL,
    apelido      VARCHAR(60)  NOT NULL,
    contacto     VARCHAR(15)  NOT NULL,
    email        VARCHAR(100) NOT NULL,
    username     VARCHAR(30)  NOT NULL UNIQUE,
    senha        VARCHAR(255) NOT NULL,
    idPerfil     INT NOT NULL,
    activo       TINYINT(1) NOT NULL DEFAULT 1,
    FOREIGN KEY (idPerfil) REFERENCES perfil (idPerfil)
);

CREATE TABLE log (
    idLog        INT AUTO_INCREMENT PRIMARY KEY,
    idUtilizador INT NOT NULL,
    nome         VARCHAR(60) NOT NULL,
    perfil       VARCHAR(40) NOT NULL,
    accao        VARCHAR(200) NOT NULL,
    dataAccao    DATETIME NOT NULL
);

-- ---------------------------------------------------------------------
--  DADOS INICIAIS
-- ---------------------------------------------------------------------

INSERT INTO permissao (entidade, accao, chave) VALUES
    ('continente', 'listar', 'continente:listar'),
    ('continente', 'criar', 'continente:criar'),
    ('continente', 'actualizar', 'continente:actualizar'),
    ('continente', 'remover', 'continente:remover'),
    ('pais', 'listar', 'pais:listar'),
    ('pais', 'criar', 'pais:criar'),
    ('pais', 'actualizar', 'pais:actualizar'),
    ('pais', 'remover', 'pais:remover'),
    ('fabricante', 'listar', 'fabricante:listar'),
    ('fabricante', 'criar', 'fabricante:criar'),
    ('fabricante', 'actualizar', 'fabricante:actualizar'),
    ('fabricante', 'remover', 'fabricante:remover'),
    ('marca', 'listar', 'marca:listar'),
    ('marca', 'criar', 'marca:criar'),
    ('marca', 'actualizar', 'marca:actualizar'),
    ('marca', 'remover', 'marca:remover'),
    ('modelo', 'listar', 'modelo:listar'),
    ('modelo', 'criar', 'modelo:criar'),
    ('modelo', 'actualizar', 'modelo:actualizar'),
    ('modelo', 'remover', 'modelo:remover'),
    ('tipoviatura', 'listar', 'tipoviatura:listar'),
    ('tipoviatura', 'criar', 'tipoviatura:criar'),
    ('tipoviatura', 'actualizar', 'tipoviatura:actualizar'),
    ('tipoviatura', 'remover', 'tipoviatura:remover'),
    ('cor', 'listar', 'cor:listar'),
    ('cor', 'criar', 'cor:criar'),
    ('cor', 'actualizar', 'cor:actualizar'),
    ('cor', 'remover', 'cor:remover'),
    ('viatura', 'listar', 'viatura:listar'),
    ('viatura', 'criar', 'viatura:criar'),
    ('viatura', 'actualizar', 'viatura:actualizar'),
    ('viatura', 'remover', 'viatura:remover'),
    ('utilizador', 'listar', 'utilizador:listar'),
    ('utilizador', 'criar', 'utilizador:criar'),
    ('utilizador', 'actualizar', 'utilizador:actualizar'),
    ('utilizador', 'remover', 'utilizador:remover'),
    ('log', 'listar', 'log:listar');

-- Os 4 perfis. O perfil 1 é sempre o Administrador do sistema (não pode ser alterado nem removido).
INSERT INTO perfil (idPerfil, nomePerfil) VALUES
    (1, 'ADMINISTRADOR'),
    (2, 'AUDITOR'),
    (3, 'OPERADOR'),
    (4, 'SUPER OPERADOR');

-- ADMINISTRADOR: CRUD de todas as entidades (+ gere utilizadores e perfis, na aplicação)
INSERT INTO perfil_permissao (idPerfil, codigoPermissao)
    SELECT 1, codigoPermissao FROM permissao WHERE entidade <> 'log';

-- AUDITOR: CRUD de todas as entidades + acesso aos logs
INSERT INTO perfil_permissao (idPerfil, codigoPermissao)
    SELECT 2, codigoPermissao FROM permissao WHERE entidade <> 'utilizador';

-- OPERADOR: visualiza e cadastra
INSERT INTO perfil_permissao (idPerfil, codigoPermissao)
    SELECT 3, codigoPermissao FROM permissao WHERE entidade NOT IN ('log', 'utilizador') AND accao IN ('listar', 'criar');

-- SUPER OPERADOR: visualiza, cadastra, edita e remove
INSERT INTO perfil_permissao (idPerfil, codigoPermissao)
    SELECT 4, codigoPermissao FROM permissao WHERE entidade NOT IN ('log', 'utilizador');

-- Utilizadores de exemplo. Senha de todos: 12345678 (pedem para a trocar no primeiro acesso)
INSERT INTO utilizadores (nome, apelido, contacto, email, username, senha, idPerfil) VALUES
    ('Administrador', 'Sistema', '840000001', 'admin@gestaoviaturas.co.mz',         'admin',         '$2y$10$YJn.fMXyyRCkX6pa28OcvOl60uU8MbxO.pHhXmtsJbPS25F3Z7wje', 1),
    ('Auditor',       'Sistema', '840000002', 'auditor@gestaoviaturas.co.mz',       'auditor',       '$2y$10$YJn.fMXyyRCkX6pa28OcvOl60uU8MbxO.pHhXmtsJbPS25F3Z7wje', 2),
    ('Operador',      'Sistema', '840000003', 'operador@gestaoviaturas.co.mz',      'operador',      '$2y$10$YJn.fMXyyRCkX6pa28OcvOl60uU8MbxO.pHhXmtsJbPS25F3Z7wje', 3),
    ('Super',         'Operador', '840000004', 'superoperador@gestaoviaturas.co.mz', 'superoperador', '$2y$10$YJn.fMXyyRCkX6pa28OcvOl60uU8MbxO.pHhXmtsJbPS25F3Z7wje', 4);

INSERT INTO continente (nomeContinente) VALUES
    ('África'), ('América'), ('Ásia'), ('Europa'), ('Oceânia');

INSERT INTO pais (nomePais, idContinente) VALUES
    ('África do Sul', 1), ('Estados Unidos', 2), ('Japão', 3), ('Coreia do Sul', 3), ('Alemanha', 4), ('França', 4);

INSERT INTO fabricante (nomeFabricante, idPais) VALUES
    ('Toyota Motor Corporation', 3), ('Volkswagen AG', 5), ('Ford Motor Company', 2), ('Hyundai Motor Company', 4), ('Renault Group', 6);

INSERT INTO marca (nomeMarca, idFabricante) VALUES
    ('Toyota', 1), ('Volkswagen', 2), ('Ford', 3), ('Hyundai', 4), ('Renault', 5);

INSERT INTO modelo (nomeModelo, idMarca) VALUES
    ('Hilux', 1), ('Corolla', 1), ('Golf', 2), ('Ranger', 3), ('i10', 4), ('Clio', 5);

INSERT INTO tipoviatura (nomeTipo) VALUES
    ('Ligeiro de passageiros'), ('Pick-up'), ('Pesado de mercadorias'), ('Autocarro'), ('Motociclo');

INSERT INTO cor (nomeCor, hexCor) VALUES
    ('Branco', '#ffffff'), ('Preto', '#000000'), ('Cinzento', '#808080'), ('Vermelho', '#ff0000'), ('Azul', '#0000ff');

INSERT INTO viatura (matricula, anoFabrico, cilindrada, numeroChassi, preco, idModelo, idTipo, idCor) VALUES
    ('ABC-123-MC', 2019, 2800, 'MR0FB22G200123456', 1850000.00, 1, 2, 1),
    ('ADF-456-MP', 2022, 1600, 'JTDBR32E720654321',  920000.00, 2, 1, 3),
    ('AFG-789-MC', 2015, 3200, 'MNCLMFF50FW123987', 1450000.00, 4, 2, 2);
