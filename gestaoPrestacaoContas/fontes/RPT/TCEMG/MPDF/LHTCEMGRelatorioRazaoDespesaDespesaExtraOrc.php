<div class='text_align_center font_weight_bold'>
    <h4>Razão da Despesa - <?= $stTipoRelatorio ?></h4>
</div>

<!-- DESPESA -->

<?php foreach($arData as $data): ?>
    <h5><?= $data ?></h5>
    
    <table class='border'>
        <thead>
            <tr>
                <th class='text_align_left border' style="width:15mm;">Data Pag.</th>
                <th class='text_align_left border' style="width:45mm;">Despesa</th>
                <th class='text_align_right border' style="width:15mm;">Valor Pag.</th>
                <th class='text_align_left border' style="width:30mm;">Banco</th>
                <th class='text_align_left border' style="width:35mm;">Conta Recurso</th>
            </tr>
        </thead>
        <tbody>
            
        <?php foreach($registros as $registro): ?>
            <?php if($registro['dt_pagamento'] == $data): ?>
            <tr>
                <td class='text_align_left border'><?= $registro['dt_pagamento'] ?></td>
                <td class='text_align_left border'><?= $registro['nome_despesa'] ?></td>
                <td class='text_align_right border'><?= number_format($registro['valor_pago'], '2', ',', '.') ?></td>
                <td class='text_align_left border'><?= $registro['banco'] ?></td>
                <td class='text_align_left border'><?= $registro['nom_recurso'] ?></td>
                <!-- <td class='text_align_left border'><?= $registro['cod_recurso_banco'] ?></td> -->
            </tr>
            <?php
                    $totalPago = $totalPago + $registro['valor_pago'];
                    endif;
                endforeach;
            ?>
        
        </tbody>
    </table>
    
    <p>
        Total Pago: <?= number_format($totalPago, '2', ',', '.') ?>
    </p>
    
    <?php
            $totalEmpenhado = 0;
            $totalPago      = 0;
        endforeach;
    ?>