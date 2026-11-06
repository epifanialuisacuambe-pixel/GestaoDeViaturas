<?php

$permissoes = $permissoes ?? array();
$matrizMarcadas = $matrizMarcadas ?? array();
$matrizDesactivada = $matrizDesactivada ?? false;
// Tabela de permissões por entidade (listar / adicionar / editar / remover).
// Variáveis esperadas: $permissoes (entidade => accao => codigo), $matrizMarcadas (códigos marcados)
// e $matrizDesactivada (true = só consulta).
$colunasMatriz = array('listar' => 'Listar', 'criar' => 'Adicionar', 'actualizar' => 'Editar', 'remover' => 'Remover');
?>
<div class="tabela">
    <table class="matriz">
        <thead>
            <tr>
                <th>Entidade</th>
                <?php foreach ($colunasMatriz as $titulo) { ?>
                    <th class="centro"><?= $titulo ?></th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($permissoes as $entidade => $acoes) { ?>
                <tr>
                    <td><?= e(nomeEntidade($entidade)) ?></td>
                    <?php foreach ($colunasMatriz as $accao => $titulo) { ?>
                        <td class="centro">
                            <?php if (isset($acoes[$accao])) { ?>
                                <input type="checkbox" name="permissoes[]" value="<?= $acoes[$accao] ?>"
                                    <?= in_array((int) $acoes[$accao], $matrizMarcadas) ? 'checked' : '' ?>
                                    <?= $matrizDesactivada ? 'disabled' : '' ?>>
                            <?php } else { ?>
                                <span class="semAccao">—</span>
                            <?php } ?>
                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
