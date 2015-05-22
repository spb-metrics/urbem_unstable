<div class='text_align_center font_weight_bold'>
    <h4>Razão da Despesa - <?= $stTipoRelatorio ?></h4>
</div>


<?php $boPrimeiroRegistroDespesa = true;?>
<?php $inCount = 0;?>
<?php $orgao = '';?>
<?php $unidade = '';?>

<?php foreach($registros as $registro): ?>
<?php $inCount ++;?>
    <!-- ANALISA OS ORGAOS E UNIDADES -->
    <?php if((($registroAnterior['cod_orgao'] != $registro['cod_orgao']) || ($registroAnterior['cod_unidade'] != $registro['cod_unidade'])) || (!isset($registroAnterior))): ?>
            <h5>
                Orgão : <?= $registro['cod_orgao'] ?> - <?= $registro['nom_orgao'] ?> <br />
                Unidade: <?=  $registro['cod_unidade'] ?> - <?= $registro['nom_unidade'] ?><br />
            </h5>
      <?php $orgao = $registro['cod_orgao'] ;
            $unidade =  $registro['cod_unidade'];
            $subtotal = 0;
            $subTotalAnulado = 0;
         endif; ?>
    
    <!-- DESPESA -->    
    <?php  if(($registroAnterior['despesa'] != $registro['despesa']) || (!isset($registroAnterior))): ?>
        <h5><?= $registro['despesa'] ?></h5>
    <?php $boPrimeiroRegistroDespesa = true;
        endif;
    ?>

    <!-- SE FOR O PRIMEIRO REGISTRO DA DESPESA, ORGAO E UNIDADE, CRIA UMA NOVA TABLE -->  
     <?php if($boPrimeiroRegistroDespesa):
                $boPrimeiroRegistroDespesa = false;
              
     ?>
        <table class='border'>
            <thead>
                <tr>
                    <th class='text_align_center border'style="width:10mm;">Empenho</th>
                    <th class='text_align_left border' style="width:15mm;">Data Emp.</th>
                    <th class='text_align_left border' style="width:35mm;">Credor</th>
                    <th class='text_align_left border' style="width:10mm;">Recurso</th>
                    <th class='text_align_left border' style="width:15mm;">Data Pag.</th>
                    <th class='text_align_left border' style="width:40mm;">Banco / Ag. / Cc.</th>
                    <th class='text_align_left border' style="width:15mm;">Documento</th>
                    <th class='text_align_right border'style="width:15mm;">Valor</th>
                    <th class='text_align_right border'style="width:15mm;">Valor Anulado</th>
                    <th class='text_align_left border' style="width:45mm;">Dotação</th>
                </tr>
            </thead>
        <tbody>
        <?php endif; ?>
        
         <?php if($orgao == $registro['cod_orgao'] and $unidade == $registro['cod_unidade']): ?>
                <tr>
                    <td class='text_align_center border'style="width:10mm;"><?= $registro['empenho'] ?></td>
                    <td class='text_align_left border' style="width:15mm;"><?= $registro['dt_empenho'] ?></td>
                    <td class='text_align_left border' style="width:35mm;"><?= $registro['credor'] ?></td>
                    <td class='text_align_left border' style="width:10mm;"><?= $registro['cod_recurso'] ?></td>
                    <td class='text_align_left border' style="width:15mm;"><?= $registro['stData'] ?></td>
                    <td class='text_align_left border' style="width:40mm;"><?= $registro['banco'] ?></td>
                    <td class='text_align_left border' style="width:15mm;"><?= $registro['num_documento'] ?></td>            
                    <td class='text_align_right border'style="width:15mm;"><?= number_format($registro['valor'], '2', ',', '.') ?></td>
                    <td class='text_align_right border'style="width:15mm;"><?= number_format($registro['valor_anulado'], '2', ',', '.') ?></td>
                    <td class='text_align_left border' style="width:45mm;"><?= $registro['dotacao'] ?></td>
                </tr>
            <?php
                $registroAnterior = $registro;
                $subtotal = $subtotal + $registro['valor'];
                $subTotalAnulado = $subTotalAnulado + $registro['valor_anulado'];
            endif; ?>
   
   <!-- SE A DESPESA DO PROXIMO FOR DIFERENTE INSERE OS SUBTOTAIS-->   
   <?php if(($registroAnterior['despesa'] != $registros[$inCount]['despesa'])): ?>
        </tbody>
    </table>
    <?php endif;  ?>
   
    <?php if( ($registro['cod_orgao'] != $registros[$inCount]['cod_orgao']) || ($registro['cod_unidade'] != $registros[$inCount]['cod_unidade']) ): ?>
        <p> 
               SubTotal : <?= number_format($subtotal, '2', ',', '.') ?> <br />
               SubTotal Anulado: <?= number_format($subTotalAnulado, '2', ',', '.') ?> <br />
        </p>
    <?php endif;  ?>
    <?php
        $totalGeral         = $registro['vl_total'];
        $totalGeralAnulado  = $registro['vl_total_anulado'];
    endforeach;
    ?>
    
    <p>
        <h5>TOTAL DO PERÍODO:  <?= number_format($totalGeral, '2', ',', '.') ?> <br /> </h5>
        <h5>TOTAL DO PERÍODO ANULADO:  <?= number_format($totalGeralAnulado, '2', ',', '.') ?> <br /> </h5> 
    </p>