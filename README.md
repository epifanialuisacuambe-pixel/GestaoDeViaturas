# Sistema de Gestão de Viaturas (PHP + MySQL, MVC)

Adaptação do projecto "Gestão de Compras" ao tema **Gestão de Viaturas**, mantendo a mesma estrutura da ficha:
`model/` (classes), `controller/` (funções com mysqli) e `view/` (páginas). Sem JavaScript: os pop-ups usam `:target` (CSS).

## Como instalar (XAMPP)
1. Copie a pasta `gestaoViaturas` para `C:\xampp\htdocs\`.
2. Ligue o Apache e o MySQL e, no phpMyAdmin, importe `sql/gestaoviaturas.sql` (cria a base `gestaoviaturas` com dados de exemplo).
3. Abra `http://localhost/gestaoViaturas/`.
4. Se o seu MySQL tiver senha, altere-a em `controller/conexao.php` (é o único sítio onde existe a ligação).

## Perfis e utilizadores de exemplo (senha inicial: 12345678)
| Username | Perfil | O que pode fazer |
|---|---|---|
| admin | ADMINISTRADOR | CRUD de tudo + gere utilizadores e perfis (nunca pode ser alterado/removido) |
| auditor | AUDITOR | CRUD de tudo + acesso aos logs |
| superoperador | SUPER OPERADOR | Visualiza, cadastra, edita e remove |
| operador | OPERADOR | Visualiza e cadastra |

No primeiro acesso (ou depois de "Resetar senha") o sistema obriga a trocar a senha.
As permissões de cada perfil podem ser ajustadas em "Gerir perfis" (só o administrador).

## Entidades
Continente → País → Fabricante → Marca → Modelo; Tipo de viatura; Cor; **Viatura**.
- **Cor**: escolhe-se a cor na paleta, carrega-se em "Consultar cor" e a descrição (ex.: Magenta) e o código hexadecimal (ex.: #ff00ff) aparecem nos campos, que ficam desabilitados.
- **Viatura**: marca → modelo (só os modelos da marca escolhida, carregando em "Mostrar modelos") → tipo e ano de fabrico → matrícula e cilindrada → número de chassi → cor → preço. A lista mostra também o tempo de existência (calculado a partir do ano).
- Nos cadastros, o que é seleccionado vem sempre antes do que se cadastra (ex.: continente antes do país, marca antes do modelo).
Administração: Utilizadores, Perfis e Logs.

## Regras importantes
- **Remoção lógica**: remover põe `activo = 0`; o registo fica na tabela e os registos que dependem dele não se perdem.
- No cadastro de Fabricante escolhe-se o continente, carrega-se em "Mostrar países" e o país filtra por continente.
- Validações no controller (campos obrigatórios, duplicados, chaves estrangeiras, ano, matrícula, e-mail, username único).
- Permissões consultadas a cada pedido (`temPermissao('viatura:criar')`); páginas protegidas com `exigirLogin()`, `exigirPermissao()` e `exigirAdministrador()`.

## Correspondência com o projecto original (para a defesa)
| Gestão de Compras | Gestão de Viaturas |
|---|---|
| Produto (tem fornecedor) | Viatura (tem modelo, tipo e cor) |
| Fornecedor | Fabricante |
| Cliente (tem caixa) | Modelo (tem marca) |
| Caixa | Continente / Marca / Tipo / Cor (tabelas simples) |
| controllerLogin, controllerLog, controllerPerfil, utilizadorController | iguais, adaptados |

## Estrutura
```
index.php
sql/gestaoviaturas.sql
model/       Continente, Pais, Fabricante, Marca, Modelo, TipoViatura, Cor, Viatura, Utilizador, Perfil, Log
controller/  conexao, helpers, controllerLogin, controllerLog, controllerPerfil, utilizadorController,
             controllerDashboard e um controller por entidade
view/        Login, alterarSenha, logout, Dashboard, Cadastrar*/Listar* por entidade,
             CadastrarUtilizador, ListarUtilizador, Perfil, ListarLog
view/partials/  menu.php (menu lateral) e matrizPermissoes.php
view/css/estilo.css   (única folha de estilo; é só substituí-la pelo padrão)
```
