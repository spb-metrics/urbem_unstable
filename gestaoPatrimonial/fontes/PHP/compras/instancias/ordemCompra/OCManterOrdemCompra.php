<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
    * Pagina Oculta para Formulário de Manter Ordem de Compra
    * Data de Criação   : 06/07/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @ignore

    $Id: OCManterOrdemCompra.php 64051 2015-11-24 17:55:39Z franver $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once CAM_GF_EMP_NEGOCIO."REmpenhoAutorizacaoEmpenho.class.php";
include_once TCOM."TComprasOrdem.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterOrdemCompra";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCtrl = $request->get('stCtrl');

$stAcao = $request->get('stAcao');
$stTipoOrdem = ( strpos($stAcao,'OS')==false ) ? 'C' : 'S';

//função que monta a lista de itens

function montaListaItens($arRecordSet , $boExecuta = true)
{
    $table = new TableTree();
    $js = "";

    for($i=0;$i<count($arRecordSet);$i++){
        if((array_key_exists("cod_item", $arRecordSet[$i])&&$arRecordSet[$i]['cod_item']==NULL)||($arRecordSet[$i]['bo_centro_marca']=='t')){
            $js .= "TableTreeReq ('".$table->getId()."_row_".($i+1)."' , 'OCManterOrdemCompra.php?componente=table_tree&cod_pre_empenho=".$arRecordSet[$i]['cod_pre_empenho'];
            $js .= "&num_item=".$arRecordSet[$i]['num_item']."&exercicio_empenho=".$arRecordSet[$i]['exercicio_empenho']."&stCtrl=detalharItem&linha_table_tree=".$table->getId()."_row_".($i+1)."');\n";

            $boItem = true;
        }
    }

    $inTotalItem = count($arRecordSet);

    $rsListaItens = new RecordSet;
    $rsListaItens->preenche( $arRecordSet );

    //formatando campos numéricos
    $rsListaItens->addFormatacao( 'quantidade'	, 'NUMERIC_BR_4' );
    $rsListaItens->addFormatacao( 'oc_saldo'	, 'NUMERIC_BR_4' );
    $rsListaItens->addFormatacao( 'qtde_oc' 	, 'NUMERIC_BR_4' );
    $rsListaItens->addFormatacao( 'vl_unitario'	, 'NUMERIC_BR' );
    $rsListaItens->addFormatacao( 'oc_vl_total' , '' );

    Sessao::write('stTableTreeId',$table->getId());
    $table->setArquivo( 'OCManterOrdemCompra.php' );
    $table->setParametros( array('cod_pre_empenho','num_item','exercicio_empenho') );
    $table->setComplementoParametros( 'stCtrl=detalharItem&stAcao='.$_REQUEST['stAcao'] );
    $table->setRecordset( $rsListaItens );
    $table->setSummary('Itens');

    $table->Head->addCabecalho('Item'            , 25);
    $table->Head->addCabecalho('Qtde. Emp.'      , 10);
    $table->Head->addCabecalho('Qtde. em OC'     , 10);
    $table->Head->addCabecalho('Qtde. Disponível', 10);
    $table->Head->addCabecalho('Valor Unitário'  , 10);
    $table->Head->addCabecalho('Qtde. da OC'     , 10);
    $table->Head->addCabecalho('Valor Total Item', 12);

    $stTitle = "";
    $table->Body->addCampo( 'nom_item'      , "E", $stTitle );
    $table->Body->addCampo( 'quantidade'    , "D", $stTitle );
    $table->Body->addCampo( 'qtde_oc'       , "D", $stTitle );
    $table->Body->addCampo( 'oc_disponivel' , "D", $stTitle );
    $table->Body->addCampo( 'vl_unitario'   , "D", $stTitle );

    if ( strpos($_REQUEST['stAcao'],'incluir') !== false || strpos($_REQUEST['stAcao'],'alterar') !== false ) {
        $obTextQtde = new Numerico();
        $obTextQtde->setName('qtdeOC');
        $obTextQtde->setDecimais(4);
        $obTextQtde->setSize (14);
        $obTextQtde->setMaxLength(13);
        $obTextQtde->setDefinicao('NUMERIC');
        
        $obTextQtde->obEvento->setOnChange ("floatDecimal(this, '4', event ); executaFuncaoAjax( 'calculaValorTotal', '&inTableId=".$table->getId()."&stId='+this.id+'&inQtde='+this.value );");

        $table->Body->addCampo( $obTextQtde, "C");
    } else {
        $table->Body->addCampo( 'quantidade_original', "D", $stTitle );
    }
    $table->Body->addCampo( 'oc_vl_total', "D", $stTitle );

    $inCountItem=0;
    $arItens = Sessao::read('arItens');
    foreach ($arItens as $item => $valor) {
        $stCampos.= "&qtdeOC_".(++$inCountItem)."='+document.frm.qtdeOC_".(++$inCount).".value+'";
    }

    if (strpos($_REQUEST['stAcao'],'incluir') !== false || strpos($_REQUEST['stAcao'],'alterar') !== false) {
        // Só permiti excluir o ítem se a ordem tiver mais de um (modo alterar).
        if ((strpos($_REQUEST['stAcao'],'alterar') !== false) || (count($arItens) > 0)) {
            if ($inTotalItem > 1) {
                $table->Body->addAcao( 'EXCLUIR' , "executaFuncaoAjax('delItem' , '&inTableId=".$table->getId()."&inNumItem=%s&stAcao=%s$stCampos')" , array( 'num_item', $_REQUEST['stAcao'] ) );
            }
        } else {
            $table->Body->addAcao( 'EXCLUIR' , "executaFuncaoAjax('delItem' , '&inTableId=".$table->getId()."&inNumItem=%s&stAcao=%s$stCampos')" , array( 'num_item', $_REQUEST['stAcao'] ) );
        }
    }

    $table->Foot->addSoma( 'oc_vl_total', 'D');

    $table->montaHTML();
    $stHTML = $table->getHtml();
    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );

    if ($boExecuta) {
        $stJs  = "document.getElementById('spnListaItens').innerHTML = '".$stHTML."';\n";
        $stJs .= $js;

        return $stJs;
    } else {
        return $stHTML;
    }
}
function BuscaOrdemCompraItens($stEmpenho, $inCodEntidade, $stExercicioOrdemCompra, $inCodOrdemCompra, $stTipoOrdem, $stAcao)
{
    $stJsPreenche="";
    $arEmpenho = explode('/',$stEmpenho);
    $obTComprasOrdemCompra = new TComprasOrdem();
    $obTComprasOrdemCompra->setDado('cod_entidade',$inCodEntidade);
    $obTComprasOrdemCompra->setDado('exercicio',$stExercicioOrdemCompra);
    $obTComprasOrdemCompra->setDado('cod_ordem', $inCodOrdemCompra);
    $obTComprasOrdemCompra->setDado('tipo'     , $stTipoOrdem);
    $obTComprasOrdemCompra->recuperaItensOrdemCompra($rsItens);

    Sessao::write('arItens', array());
    if ($rsItens->getCampo('nom_item') != '') {
        $arItens = array();
        $arItensAlmoxarifado = array();

        $inCount = 0;
        if ($rsItens->getNumLinhas() > 0) {
            while (!$rsItens->eof()) {
                $arItens[$inCount]['nom_item'] = $rsItens->getCampo('nom_item');
                $arItens[$inCount]['num_item'] = $rsItens->getCampo('num_item');
                if(!is_null($rsItens->getCampo('cod_item_ordem')))
                    $inCodItem = $rsItens->getCampo('cod_item_ordem');
                else
                    $inCodItem = $rsItens->getCampo('cod_item');
                $arItens[$inCount]['cod_item'] = $inCodItem;
                $arItens[$inCount]['exercicio_empenho'] = $rsItens->getCampo('exercicio');
                $arItens[$inCount]['cod_pre_empenho'] = $rsItens->getCampo('cod_pre_empenho');
                $arItens[$inCount]['quantidade']  = $rsItens->getCampo('qtde_empenhada');
                $arItens[$inCount]['qtde_oc']  = $rsItens->getCampo('qtde_em_oc');
                $arItens[$inCount]['vl_unitario'] = $rsItens->getCampo('vl_unitario');
                $arItens[$inCount]['oc_disponivel'] = number_format($rsItens->getCampo('qtde_disponivel'), 4, ",", ".");
                $arItens[$inCount]['oc_vl_total'] = number_format($rsItens->getCampo('vl_total_item'), 2, ',', '.');
                $arItens[$inCount]['oc_saldo'] = $rsItens->getCampo('oc_saldo');
                $arItens[$inCount]['quantidade_original'] = number_format($rsItens->getCampo('qtde_da_oc'), 4, ",", ".");
                
                if ( strpos($stAcao,'incluir') !== false || strpos($stAcao,'alterar') !== false )
                    $stJsPreenche.= "$('qtdeOC_".($inCount+1)."').value = '".number_format($rsItens->getCampo('qtde_da_oc'),4,',','.')."'; ";
                
                if(!is_null($rsItens->getCampo('cod_centro_ordem'))&&!is_null($rsItens->getCampo('cod_marca_ordem'))){
                    $arItensAlmoxarifado[$rsItens->getCampo('num_item')]['inCodItem'] = $inCodItem;
                    $arItensAlmoxarifado[$rsItens->getCampo('num_item')]['stNomItem'] = $rsItens->getCampo('nom_item');
                    $arItensAlmoxarifado[$rsItens->getCampo('num_item')]['inCodCentroCusto'] = $rsItens->getCampo('cod_centro_ordem');
                    $arItensAlmoxarifado[$rsItens->getCampo('num_item')]['stNomCentroCusto'] = $rsItens->getCampo('nom_centro_ordem');
                    $arItensAlmoxarifado[$rsItens->getCampo('num_item')]['inMarca'] = $rsItens->getCampo('cod_marca_ordem');
                    $arItensAlmoxarifado[$rsItens->getCampo('num_item')]['stNomMarca'] = $rsItens->getCampo('nom_marca_ordem');
                }else if(!is_null($rsItens->getCampo('cod_item_ordem'))){
                    $arItens[$inCount]['bo_centro_marca'] = 't';
                }
                
                $inCount++; 
                $rsItens->proximo();
            }
        }
        Sessao::write('arItens',$arItens);
        if(is_array($arItensAlmoxarifado)){
            Sessao::write('arItensAlmoxarifado', $arItensAlmoxarifado);
        }
    } else {
        Sessao::write('arItens', array());
    }
    $stJs  = montaListaItens( $arItens );
    $stJs .= $stJsPreenche;

    return $stJs;
}

function BuscaEmpenhoItens($stEmpenho, $inCodEntidade, $stTipoOrdem, $stAcao)
{
    if ( ($stEmpenho != "") and ($inCodEntidade != "") ) {
        $stJsPreenche="";
        $arEmpenho = explode('/',$stEmpenho);
        $arEmpenho[1] = ( trim($arEmpenho[1]) == '' ) ? Sessao::getExercicio() : $arEmpenho[1];

        $obTComprasOrdemCompra = new TComprasOrdem();
        $obTComprasOrdemCompra->setDado( 'cod_entidade', $inCodEntidade);
        $obTComprasOrdemCompra->setDado( 'cod_empenho', $arEmpenho[0] );
        $obTComprasOrdemCompra->setDado( 'exercicio', $arEmpenho[1] );
        $obTComprasOrdemCompra->setDado( 'tipo'     , $stTipoOrdem );
        $obTComprasOrdemCompra->recuperaItensEmpenho( $rsItens );

        if ( $rsItens->getNumLinhas() > 0 ) {
            Sessao::write('arItens', array());
            $arItensAlmoxarifado = array();
            $inCount = 0;

            while (!$rsItens->eof()) {
                $ocVlTotal = $rsItens->getCampo('vl_unitario') * $rsItens->getCampo('oc_saldo');
                $ocVlTotal = number_format($ocVlTotal, 2, ',', '.');

                $inNumItem = $rsItens->getCampo('num_item');
                $arItens[$inCount]['nom_item']          = $rsItens->getCampo('nom_item');
                $arItens[$inCount]['num_item']          = $inNumItem;
                if(!is_null($rsItens->getCampo('cod_item_ordem')))
                    $inCodItem = $rsItens->getCampo('cod_item_ordem');
                else
                    $inCodItem = $rsItens->getCampo('cod_item');
                $arItens[$inCount]['cod_item']          = $inCodItem;
                $arItens[$inCount]['exercicio_empenho'] = $rsItens->getCampo('exercicio');
                $arItens[$inCount]['cod_pre_empenho']   = $rsItens->getCampo('cod_pre_empenho');
                $arItens[$inCount]['quantidade']        = $rsItens->getCampo('quantidade');
                $arItens[$inCount]['qtde_oc']           = $rsItens->getCampo('oc_quantidade_atendido');
                $arItens[$inCount]['vl_unitario']       = $rsItens->getCampo('vl_unitario');
                $arItens[$inCount]['oc_vl_total']       = $ocVlTotal;
                $arItens[$inCount]['oc_saldo']          = $rsItens->getCampo('oc_saldo');
                $arItens[$inCount]['oc_disponivel']     = '0,0000';
                $arItens[$inCount]['bo_centro_marca']   = $rsItens->getCampo('bo_centro_marca');
                $inCount++;
                // Preenche a quantidade da OC com a quantidade do Empenho, para facilitar na operação.
                $stJsPreenche.= "$('qtdeOC_".$inCount."').value = '".number_format($rsItens->getCampo('oc_saldo'),4,',','.')."'; ";

                if((!is_null($rsItens->getCampo('cod_centro_ordem'))&&!is_null($rsItens->getCampo('cod_marca_ordem'))) || !is_null($rsItens->getCampo('cod_centro_empenho'))){
                    $arItensAlmoxarifado[$inNumItem]['inCodItem'] = $inCodItem;
                    $arItensAlmoxarifado[$inNumItem]['stNomItem'] = (!is_null($inCodItem)) ? $rsItens->getCampo('nom_item') : '';

                    if(!is_null($rsItens->getCampo('cod_centro_empenho'))){
                        $inCodCentro = $rsItens->getCampo('cod_centro_empenho');
                        $stNomCentro = $rsItens->getCampo('nom_centro_empenho');
                    }else{
                        $inCodCentro = $rsItens->getCampo('cod_centro_ordem');
                        $stNomCentro = $rsItens->getCampo('nom_centro_ordem');
                    }

                    $arItensAlmoxarifado[$inNumItem]['inCodCentroCusto']    = $inCodCentro;
                    $arItensAlmoxarifado[$inNumItem]['stNomCentroCusto']    = $stNomCentro;
                    $arItensAlmoxarifado[$inNumItem]['inMarca']             = $rsItens->getCampo('cod_marca_ordem');
                    $arItensAlmoxarifado[$inNumItem]['stNomMarca']          = $rsItens->getCampo('nom_marca_ordem');
                }

                $rsItens->proximo();
            }
            Sessao::write('arItens', $arItens);
            $stJs = montaListaItens( $arItens );

            if(is_array($arItensAlmoxarifado)){
                Sessao::write('arItensAlmoxarifado', $arItensAlmoxarifado);
            }
        } else {
            Sessao::write('arItens', array());
        $stJs .= "document.getElementById('spnListaItens').innerHTML = '';";
        }

        $stJs .= $stJsPreenche;

    return $stJs;
    }
}

function delItem($inNumItem)
{
    echo "BloqueiaFrames(true,false);\n";

    $arItens = Sessao::read('arItens');
    $stTableTreeId = Sessao::read('stTableTreeId');
    if (is_array($arItens)) {
        $inCount=0;
        $inCountItem=0;
        foreach ($arItens as $item => $valor) {
            $inCountItem++;
            if ($arItens[$item]['num_item'] == $inNumItem) {
                $arItensExcluidos[] = $arItens[$item];
            } else {
                $arTMP[] = $arItens[$item];
            }
        }

        Sessao::write('arItens', $arItens);
        Sessao::write('arItensExcluidos', $arItensExcluidos);
        Sessao::write('arItens', $arTMP);

        if (empty($arTMP)) {
            unset($arItens);
            Sessao::remove('arItens');
            unset($arTMP);
            $stJs .= " d.getElementById('spnListaItens').innerHTML = ''; ";
        } else {
            $stJs .= montaListaItens( $arTMP );

            $inCount=0;
            $inCountItem=0;
            foreach ($arItens as $item => $valor) {
                $inCountItem++;
                if ($arItens[$item]['num_item'] != $inNumItem) {
                    $inCount++;
                    $nuValor = str_replace(',','.',str_replace('.','',$_REQUEST['qtdeOC_'.($inCountItem)] ));
                    $stJsPreenche.= "$('qtdeOC_".($inCount)."').value = '".number_format( $nuValor ,4,',','.')."'; ";

                    $inVlUnitario = $arItens[$item]['vl_unitario'];
                    $stValor = number_format($nuValor * $inVlUnitario, 2, ',', '.');
                    $stJsPreenche.= "if ($('".$stTableTreeId."_row_".($inCount)."_cell_9')) { $('".$stTableTreeId."_row_".($inCount)."_cell_9').innerHTML = '".$stValor."';} ";

                    $flQtdeOriginal = $arItens[$item]['quantidade'];
                    $stValorDisponivel = number_format( $flQtdeOriginal - $nuValor  ,4,',','.');
                    $stJsPreenche.= "if ($('".$stTableTreeId."_row_".($inCount)."_cell_6')) { $('".$stTableTreeId."_row_".($inCount)."_cell_6').innerHTML = '".$stValorDisponivel."';} ";

                }
            }

            $stJs .= $stJsPreenche;
            echo "LiberaFrames(true,false);\n";
        }
    }

    return $stJs;
}

switch ($stCtrl) {

// FAZ A BUSCA DOS ITENS RELACIONADOS AO EMPENHO SELECIONADO
case 'calculaValorTotal':

    $arItens = Sessao::read('arItens');
    $arPosicao = explode('_',$_REQUEST['stId']);
    $inQtde = str_replace(',','.',str_replace('.','',$_REQUEST['inQtde']));
    $flQtdeOriginal = str_replace(',','.', str_replace(".", "", $arItens[$arPosicao[1]-1]['quantidade_original']));
    $saldo = $arItens[$arPosicao[1]-1]['oc_saldo'] + $flQtdeOriginal;

    if ( ( $inQtde ) <= ( $saldo ) ) {

        $inVlUnitario = $arItens[$arPosicao[1]-1]['vl_unitario'];
        $stValor = number_format($inQtde * $inVlUnitario, 2, ',', '.');
        $stJs.= "$('".$_REQUEST['inTableId']."_row_".$arPosicao[1]."_cell_9').innerHTML = '".$stValor."';";
        $stValorDisponivel = number_format($arItens[$arPosicao[1]-1]['oc_saldo'] + $flQtdeOriginal - str_replace(',','.',str_replace('.','',$_REQUEST['inQtde'])),4,',','.');
        $stJs.= "$('".$_REQUEST['inTableId']."_row_".$arPosicao[1]."_cell_6').innerHTML = '".$stValorDisponivel."';";

    } else {

        $stValorDisponivel = number_format($arItens[$arPosicao[1]-1]['oc_saldo'] + $flQtdeOriginal,4,',','.');
        $stJs.= "$('".$_REQUEST['inTableId']."_row_".$arPosicao[1]."_cell_6').innerHTML = '".$stValorDisponivel."';";
        $stJs.= "$('".$_REQUEST['inTableId']."_row_".$arPosicao[1]."_cell_9').innerHTML = '0,00';";
        $stJs.= "$('".$_REQUEST['stId']."').value = '0,0000';";
        $stJs.= "alertaAviso('A quantidade do item deve ser menor ou igual ao saldo.','form','erro','".Sessao::getId()."');";
    }

    // calcula o total da listagem
    $inCount = count($arItens);

    // soma os valores da listagem
    $stJs.= "
    var vlTotal = 0;
    var vlLinha;
    for (var i=0; i<".$inCount."; i++) {
        vlLinha = $('".$_REQUEST['inTableId']."_row_'+(i+1)+'_cell_9').innerHTML.replace('.', '').replace(',', '.');
        vlTotal = parseFloat(vlTotal) + parseFloat(vlLinha);
    }";
    // pega o total e separa o centavos do valor para que possa ser montado o valor sem perder as casas decimais
    $stJs.= "
        vlCentavos = Math.floor((vlTotal*100+0.5)%100);
        if (vlCentavos < 10) vlCentavos = '0'+vlCentavos;
        vlTotal = Math.floor((vlTotal*100+0.5)/100).toString();
    ";
    $stTableTreeId = Sessao::read('stTableTreeId');
    $stJs.= "$('".$stTableTreeId."_foot_1_cell_2').innerHTML = vlTotal+','+vlCentavos;";

    break;

case 'detalharItem' :
    $obTComprasOrdemCompra = new TComprasOrdem();
    $obTComprasOrdemCompra->setDado( 'exercicio'		, $_REQUEST['exercicio_empenho'] );
    $obTComprasOrdemCompra->setDado( 'cod_pre_empenho'	, $_REQUEST['cod_pre_empenho'] 	 );
    $obTComprasOrdemCompra->setDado( 'num_item'			, $_REQUEST['num_item'] 		 );
    $obTComprasOrdemCompra->recuperaDetalheItem( $rsDetalheItem );

    $obForm = new Form();
    $obForm->setName("frm2");

    if ( ($rsDetalheItem->getCampo('bo_centro_marca')=='f') || $_REQUEST['stAcao'] == 'consultar' || $_REQUEST['stAcao'] == 'anular' ) {
        $stTitulo = 'Detalhe do Item';
        $obLblCodItem = new Label();
        $obLblCodItem->setRotulo( 'Código do Item' );
        if(!is_null($rsDetalheItem->getCampo('cod_item_ordem')))
            $obLblCodItem->setValue( $rsDetalheItem->getCampo('cod_item_ordem') );
        else
            $obLblCodItem->setValue( $rsDetalheItem->getCampo('cod_item') );
    }else{
        include_once CAM_GP_ALM_COMPONENTES."IPopUpItem.class.php";
        include_once CAM_GP_ALM_COMPONENTES."IPopUpCentroCustoUsuario.class.php";
        require_once CAM_GP_ALM_COMPONENTES."IPopUpMarca.class.php";

        $stTituloItem = '';
        $linha = explode('row_', $_REQUEST['linha_table_tree']);
        if( !is_null($linha[1]) )
            $stTituloItem = ' - Item '.$linha[1];
        $stTitulo = 'Vínculo do Almoxarifado'.$stTituloItem;

        $arItensAlmoxarifado = is_array(Sessao::read('arItensAlmoxarifado')) ? Sessao::read('arItensAlmoxarifado') : array();

        $obForm = new Form;
        $obForm->setAction( $pgOcul );
        $obForm->setTarget( "oculto" );

        if( !is_null($rsDetalheItem->getCampo('cod_item')) || !is_null($rsDetalheItem->getCampo('cod_item_ordem')) ){
            if( !is_null($rsDetalheItem->getCampo('cod_item')) )
                $inCodItem = $rsDetalheItem->getCampo('cod_item');
            else
                $inCodItem = $rsDetalheItem->getCampo('cod_item_ordem');
            $stNomItem = $rsDetalheItem->getCampo('descricao');
            $boCentroMarca = 'f';
        }else{
            $inCodItem = $arItensAlmoxarifado[$_REQUEST['num_item']]['inCodItem'];
            $stNomItem = $arItensAlmoxarifado[$_REQUEST['num_item']]['stNomItem'];
            $boCentroMarca = $rsDetalheItem->getCampo('bo_centro_marca');
        }

        $obIPopUpCatalogoItem = new IPopUpItem($obForm);
        $obIPopUpCatalogoItem->setRotulo            ( '*Código do Item'                 );
        $obIPopUpCatalogoItem->setNull              ( true                              );
        $obIPopUpCatalogoItem->setRetornaUnidade    ( false                             );
        $obIPopUpCatalogoItem->setId                ( 'stNomItem'.$_REQUEST['num_item'] );
        $obIPopUpCatalogoItem->obCampoCod->setName  ( 'inCodItem'.$_REQUEST['num_item'] );
        $obIPopUpCatalogoItem->obCampoCod->setId    ( 'inCodItem'.$_REQUEST['num_item'] );
        $obIPopUpCatalogoItem->obImagem->setId      ( 'imgBuscar'.$_REQUEST['num_item'] );
        $obIPopUpCatalogoItem->obCampoCod->setValue ( $inCodItem                        );
        $obIPopUpCatalogoItem->setValue             ( $stNomItem                        );

        if( !is_null($rsDetalheItem->getCampo('cod_item')) || !is_null($rsDetalheItem->getCampo('cod_item_ordem')) ){
            $js .= "jQuery('#inCodItem".$_REQUEST['num_item']."').attr('readonly'   , 'readonly');";
            $js .= "jQuery('#imgBuscar".$_REQUEST['num_item']."').css('visibility'  , 'hidden'  );";
        }

        $boLimparCentroCusto = 't';
        if( !is_null($arItensAlmoxarifado[$_REQUEST['num_item']]['inCodCentroCusto']) ){
            $inCodCentroCusto = $arItensAlmoxarifado[$_REQUEST['num_item']]['inCodCentroCusto'];
            $stNomCentroCusto = $arItensAlmoxarifado[$_REQUEST['num_item']]['stNomCentroCusto'];
        }else{
            if( !is_null($rsDetalheItem->getCampo('cod_centro_empenho')) ){
                if( !is_null($rsDetalheItem->getCampo('cod_item_ordem')) || !is_null($rsDetalheItem->getCampo('cod_centro_solicitacao')) ){
                    $js .= "jQuery('#inCodCentroCusto".$_REQUEST['num_item']."').attr('readonly'        , 'readonly');";
                    $js .= "jQuery('#imgBuscarCentroCusto".$_REQUEST['num_item']."').css('visibility'   , 'hidden'  );";
                    $boLimparCentroCusto = 'f';
                }
            }
            $inCodCentroCusto = $rsDetalheItem->getCampo('cod_centro_empenho');
            $stNomCentroCusto = $rsDetalheItem->getCampo('nom_centro_empenho');
        }

        $obCentroCustoUsuario = new IPopUpCentroCustoUsuario($obForm);
        $obCentroCustoUsuario->setNull              ( true                                          );
        $obCentroCustoUsuario->setRotulo            ( '*Centro de Custo'                            );
        $obCentroCustoUsuario->obCampoCod->setId    ( 'inCodCentroCusto'.$_REQUEST['num_item']      );
        $obCentroCustoUsuario->obCampoCod->setName  ( 'inCodCentroCusto'.$_REQUEST['num_item']      );
        $obCentroCustoUsuario->setId                ( 'stNomCentroCusto'.$_REQUEST['num_item']      );
        $obCentroCustoUsuario->obImagem->setId      ( 'imgBuscarCentroCusto'.$_REQUEST['num_item']  );
        $obCentroCustoUsuario->obCampoCod->setValue ( $inCodCentroCusto                             );
        $obCentroCustoUsuario->setValue             ( $stNomCentroCusto                             );

        $inMarca    = ( !is_null($arItensAlmoxarifado[$_REQUEST['num_item']]['inMarca'])    ) ? $arItensAlmoxarifado[$_REQUEST['num_item']]['inMarca']      : $rsDetalheItem->getCampo('cod_marca_ordem');
        $stNomMarca = ( !is_null($arItensAlmoxarifado[$_REQUEST['num_item']]['stNomMarca']) ) ? $arItensAlmoxarifado[$_REQUEST['num_item']]['stNomMarca']   : $rsDetalheItem->getCampo('nom_marca_ordem');
 
        $obMarca = new IPopUpMarca( new Form);
        $obMarca->setNull               ( true                                  );
        $obMarca->setRotulo             ( '*Marca'                              );
        $obMarca->setId                 ( 'stNomMarca'.$_REQUEST['num_item']    );
        $obMarca->obCampoCod->setName   ( 'inMarca'.$_REQUEST['num_item']       );
        $obMarca->obCampoCod->setId     ( 'inMarca'.$_REQUEST['num_item']       );
        $obMarca->obCampoCod->setValue  ( $inMarca                              );
        $obMarca->setValue              ( $stNomMarca                           );

        $obBtnIncluir = new Button;
        $obBtnIncluir->setName      ( "btnIncluir".$_REQUEST['num_item']        );
        $obBtnIncluir->setValue     ( "Incluir"                                 );
        $obBtnIncluir->setTipo      ( "button"                                  );
        $obBtnIncluir->setDisabled  ( false                                     );
        $obBtnIncluir->obEvento->setOnClick ( "incluirItem(".$_REQUEST['num_item'].", '".$_REQUEST['linha_table_tree']."');" );

        $obBtnLimpar = new Button;
        $obBtnLimpar->setName       ( "btnLimpar".$_REQUEST['num_item']         );
        $obBtnLimpar->setValue      ( "Limpar"                                  );
        $obBtnLimpar->setTipo       ( "button"                                  );
        $obBtnLimpar->obEvento->setOnClick ( "limpaItem(".$_REQUEST['num_item'].", '".$boCentroMarca."', '".$boLimparCentroCusto."');" );
    }

    $obLblItem = new Label();
    $obLblItem->setRotulo( 'Descrição' );
    $obLblItem->setValue( $rsDetalheItem->getCampo('descricao') );

    $obLblGrandeza = new Label();
    $obLblGrandeza->setRotulo( 'Grandeza' );
    $obLblGrandeza->setValue( $rsDetalheItem->getCampo('nom_grandeza') );

    $obLblUnidade = new Label();
    $obLblUnidade->setRotulo( 'Unidade' );
    $obLblUnidade->setValue( $rsDetalheItem->getCampo('nom_unidade') );

    $obFormulario = new Formulario();
    $obFormulario->addForm( $obForm );
    $obFormulario->addTitulo( $stTitulo );

    if ( ($rsDetalheItem->getCampo('bo_centro_marca')=='f')||$_REQUEST['stAcao'] == 'consultar'||$_REQUEST['stAcao'] == 'anular' ) {
        $obFormulario->addComponente( $obLblCodItem );
    }else{
        $obFormulario->addComponente( $obIPopUpCatalogoItem );
        $obFormulario->addComponente( $obCentroCustoUsuario );
        $obFormulario->addComponente( $obMarca );
    }

    $obFormulario->addComponente( $obLblItem );
    $obFormulario->addComponente( $obLblGrandeza );
    $obFormulario->addComponente( $obLblUnidade );

    if ( ($rsDetalheItem->getCampo('bo_centro_marca')=='t') && $_REQUEST['stAcao'] != 'consultar' && $_REQUEST['stAcao'] != 'anular' )
        $obFormulario->defineBarra ( array( $obBtnIncluir , $obBtnLimpar ) );

    $obFormulario->show();

    break;

    case 'delItem':
        $stJs = delItem( $_REQUEST['inNumItem'] );
    break;

    case 'BuscaOrdemCompraItens':
        $stJs = BuscaOrdemCompraItens($_REQUEST['inCodEmpenho'], $_REQUEST['inCodEntidade'], $_REQUEST['stExercicioOrdemCompra'],$_REQUEST['inCodOrdemCompra'],$_REQUEST['stTipoOrdem'],$_REQUEST['stAcao']);
    break;

    case 'BuscaEmpenhoItens':
        $stJs = BuscaEmpenhoItens( $_REQUEST['inCodEmpenho'],$_REQUEST['inCodEntidade'],$stTipoOrdem, $stAcao);
    break;

    case 'incluirItem':
        if($_REQUEST['idItem']){
            $idItem = $_REQUEST['idItem'];

            $arItens = Sessao::read('arItens');

            foreach ($arItens as $key => $value) {
                if($value['num_item']==$idItem)
                    $stNomItem = $value['nom_item'];
            }

            $arItensAlmoxarifado = is_array(Sessao::read('arItensAlmoxarifado')) ? Sessao::read('arItensAlmoxarifado') : array();

            $arItensAlmoxarifado[$idItem]['inCodItem']          = $_REQUEST['inCodItem'.$idItem];
            $arItensAlmoxarifado[$idItem]['stNomItem']          = $_REQUEST['stNomItem'.$idItem];
            $arItensAlmoxarifado[$idItem]['inCodCentroCusto']   = $_REQUEST['inCodCentroCusto'.$idItem];
            $arItensAlmoxarifado[$idItem]['stNomCentroCusto']   = $_REQUEST['stNomCentroCusto'.$idItem];
            $arItensAlmoxarifado[$idItem]['inMarca']            = $_REQUEST['inMarca'.$idItem];
            $arItensAlmoxarifado[$idItem]['stNomMarca']         = $_REQUEST['stNomMarca'.$idItem];

            Sessao::write('arItensAlmoxarifado', $arItensAlmoxarifado);

            $js = "alertaAviso('Código do Item (".$_REQUEST['inCodItem'.$idItem].") vinculado ao Item - ".$stNomItem." ','form','erro','".Sessao::getId()."', '../');";
        }
    break;

    case 'excluirItem':
        if($_REQUEST['idItem']){
            $idItem = $_REQUEST['idItem'];
            $arItensTemp = array();

            $arItens = Sessao::read('arItens');

            foreach ($arItens as $key => $value) {
                if($value['num_item']==$idItem)
                    $stNomItem = $value['nom_item'];
            }

            $arItensAlmoxarifado = Sessao::read('arItensAlmoxarifado');

            foreach ($arItensAlmoxarifado as $key => $value) {
                if($key!=$idItem)
                    $arItensTemp[$key] = $value;
                else
                    $inCodItem = $arItensAlmoxarifado[$key]['inCodItem'];
            }

            Sessao::write('arItensAlmoxarifado', $arItensTemp);
            if($inCodItem)
                $js = "alertaAviso('Código do Item (".$inCodItem.") desvinculado do Item - ".$stNomItem." ','form','erro','".Sessao::getId()."', '../');";
        }    
    break;

} // fim switch

if (isset($stJs))
   echo($stJs);

if (isset($js))
   sistemaLegado::executaFrameOculto( $js );

?>
