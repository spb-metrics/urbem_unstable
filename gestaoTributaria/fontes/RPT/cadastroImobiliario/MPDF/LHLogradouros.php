<table style="font-size: 13px;">
    <thead>        
        <tr>
            <td class="font_weight_bold text_align_right"> CÓDIGO              </td>
            <td class="font_weight_bold text_align_left" > TIPO                 </td>
            <td class="font_weight_bold text_align_left" > NOME DO LOGRADOURO   </td>
            <td class="font_weight_bold text_align_left" > BAIRROS              </td>
            <td class="font_weight_bold text_align_left" > CEP                  </td>
            <td class="font_weight_bold text_align_left" > UF                   </td>
            <td class="font_weight_bold text_align_left" > MUNICÍPIO            </td>
            <?php 
                if ($boMostrarHistorico == 'true') {
                    $inTamanhCss = " width: 100mm; ";
                    $inColSpan = "8";
                    echo "<td class=\"font_weight_bold text_align_left\" > DATA LOGRADOURO      </td>";
                }else{
                    $inColSpan = "7";
                    $inTamanhCss = "width: 130mm;";
                }
            ?>
            
        </tr>
    </thead>

    <tbody>
        <tr>
            <td colspan="<?=$inColSpan?>"> <hr> </td>
        </tr>
        <?php foreach($arDadosLogradouro as $logradouro) { 
            if ($logradouro["grupo"] == 3)
                $stCss .= "font-style: italic;";
            else
                $stCss .= "";
        ?>
        <tr>
            <td style="width: 12mm; <?= $stCss ?>"       class="text_align_right tabulacao_nivel_<?= $logradouro["grupo"] ?>" ><?= $logradouro["cod_logradouro"]    ?></td>
            <td style="width: 14mm; <?= $stCss ?>"       class="text_align_left tabulacao_nivel_<?= $logradouro["grupo"] ?>"><?= $logradouro["nom_tipo"]            ?></td>
            <td style="<?= $inTamanhCss ?><?= $stCss ?>" class="text_align_left tabulacao_nivel_<?= $logradouro["grupo"] ?>" ><?= $logradouro["nom_logradouro"] ?></td>
            <td style="width: 30mm;  font-size: 11px;"   class="text_align_left "><?= $logradouro["nom_bairro"] ?></td>
            <td style="width: 12mm; <?= $stCss ?>"       class="text_align_left "><?= $logradouro["cep"]                 ?></td>
            <td style="width: 10mm; <?= $stCss ?>"       class="text_align_left "><?= $logradouro["sigla_uf"]            ?></td>
            <td style="width: 40mm; <?= $stCss ?>"       class="text_align_left "><?= $logradouro["nom_municipio"]       ?></td>
            <?php 
                if ($boMostrarHistorico == 'true')
                    echo "<td style=\"width: 15mm; ".$stCss."\" class=\"text_align_left\">".$logradouro["data_logradouro"]."</td>";
            ?>
        </tr>
        <?php } ?>
    </tbody>
    
    <tfoot>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </tfoot>
</table>
