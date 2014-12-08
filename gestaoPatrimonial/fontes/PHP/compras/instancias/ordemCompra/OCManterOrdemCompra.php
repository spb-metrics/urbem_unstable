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

    $Id: OCManterOrdemCompra.php 59612 2014-09-02 12:00:51Z gelson $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoAutorizacaoEmpenho.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterOrdemCompra";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCtrl = $_REQUEST['stCtrl'];

$stAcao = $request->get('stAcao');
$stTipoOrdem = ( strpos($stAcao,'OS')==false ) ? 'C' : 'S';

//função que monta a lista de itens

function montaListaItens($arRecordSet , $boExecuta = true)
{

    $inTotalItem = count($arRecordSet);

    $rsListaItens = new RecordSet;
    $rsListaItens->preenche( $arRecordSet );

    //formatando campos numéricos
    $rsListaItens->addFormatacao( 'quantidade'	, 'NUMERIC_BR_4' );
    $rsListaItens->addFormatacao( 'oc_saldo'	, 'NUMERIC_BR_4' );
    $rsListaItens->addFormatacao( 'qtde_oc' 	, 'NUMERIC_BR_4' );
    $rsListaItens->addFormatacao( 'vl_unitario'	, 'NUMERIC_BR' );
    $rsListaItens->addFormatacao( 'oc_vl_total' , '' );

    $table = new TableTree();
    Sessao::write('stTableTreeId',$table->getId());
    $table->setArquivo( 'OCManterOrdemCompra.php' );
    $table->setParametros( array('cod_pre_empenho','num_item','exercicio_empenho') );
    $table->setComplementoParametros( 'stCtrl=detalharItem' );
    $table->setRecordset( $rsListaItens );
    $table->setSummary('Itens');

    $table->Head->addCabecalho( 'Item' , 25 			);
    $table->Head->addCabecalho( 'Qtde. Emp.' , 8 		);
    $table->Head->addCabecalho( 'Qtde. em OC' , 10 		);
    $table->Head->addCabecalho( 'Qtde. Disponível' , 10 );
    $table->Head->addCabecalho( 'Valor Unitário' , 10 	);
    $table->Head->addCabecalho( 'Qtde. da OC' , 8 		);
    $table->Head->addCabecalho( 'Valor Total Item' , 12 );

    $stTitle = "";
    $table->Body->addCampo( 'nom_item'      , "E", $stTitle );
    $table->Body->addCampo( 'quantidade'    , "D", $stTitle );
    $table->Body->addCampo( 'qtde_oc'       , "D", $stTitle );
    $table->Body->addCampo( 'oc_disponivel' , "D", $stTitle );
    $table->Body->addCampo( 'vl_unitario'   , "D", $stTitle );

    if ( strpos($_REQUEST['stAcao'],'incluir') !== false || strpos($_REQUEST['stAcao'],'alterar') !== false ) {
        $obTextQtde = new TextBox();
        $obTextQtde->setName('qtdeOC');
        $obTextQtde->obEvento->setOnKeyUp("mascaraMoeda(this,4,event,false);");
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
        $stJs = "document.getElementById('spnListaItens').innerHTML = '".$stHTML."';";

        return $stJs;
    } else {
        return $stHTML;
    }
}
function BuscaOrdemCompraItens($stEmpenho, $inCodEntidade, $stExercicioOrdemCompra, $inCodOrdemCompra, $stTipoOrdem, $stAcao)
{
    include_once( TCOM."TComprasOrdem.class.php" );
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

        $inCount = 0;
        if ($rsItens->getNumLinhas() > 0) {
            while (!$rsItens->eof()) {
                $arItens[$inCount]['nom_item'] = $rsItens->getCampo('nom_item');
                $arItens[$inCount]['num_item'] = $rsItens->getCampo('num_item');
                $arItens[$inCount]['exercicio_empenho'] = $rsItens->getCampo('exercicio');
                $arItens[$inCount]['cod_pre_empenho'] = $rsItens->getCampo('cod_pre_empenho');
                $arItens[$inCount]['quantidade']  = $rsItens->getCampo('qtde_empenhada');
                $arItens[$inCount]['qtde_oc']  = $rsItens->getCampo('qtde_em_oc');
                $arItens[$inCount]['vl_unitario'] = $rsItens->getCampo('vl_unitario');
                $arItens[$inCount]['oc_disponivel'] = number_format($rsItens->getCampo('qtde_disponivel'), 4, ",", ".");
                $arItens[$inCount]['oc_vl_total'] = number_format($rsItens->getCampo('vl_total_item'), 2, ',', '.');
                $arItens[$inCount]['oc_saldo'] = $rsItens->getCampo('oc_saldo');
                $arItens[$inCount]['quantidade_original'] = number_format($rsItens->getCampo('qtde_da_oc'), 4, ",", ".");
                $inCount++;
                if ( strpos($stAcao,'incluir') !== false || strpos($stAcao,'alterar') !== false )
                    $stJsPreenche.= "$('qtdeOC_".$inCount."').value = '".number_format($rsItens->getCampo('qtde_da_oc'),4,',','.')."'; ";
                $rsItens->proximo();
            }
        }
        Sessao::write('arItens',$arItens);
    } else {
        Sessao::write('arItens', array());
    }
    $stJs = montaListaItens( $arItens);
    $stJs .= $stJsPreenche;

    return $stJs;
}

function BuscaEmpenhoItens($stEmpenho, $inCodEntidade, $stTipoOrdem, $stAcao)
{
    if ( ($stEmpenho != "") and ($inCodEntidade != "") ) {
    $stJsPreenche="";
        include_once( TCOM."TComprasOrdem.class.php" );
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
            $inCount = 0;
            while (!$rsItens->eof()) {
                $ocVlTotal = $rsItens->getCampo('vl_unitario') * $rsItens->getCampo('oc_saldo');
                $ocVlTotal = number_format($ocVlTotal, 2, ',', '.');

        $arItens[$inCount]['nom_item'] = $rsItens->getCampo('nom_item');
                $arItens[$inCount]['num_item'] = $rsItens->getCampo('num_item');
                $arItens[$inCount]['exercicio_empenho'] = $rsItens->getCampo('exercicio');
                $arItens[$inCount]['cod_pre_empenho'] = $rsItens->getCampo('cod_pre_empenho');
                $arItens[$inCount]['quantidade']  = $rsItens->getCampo('quantidade');
                $arItens[$inCount]['qtde_oc']  = $rsItens->getCampo('oc_quantidade_atendido');
                $arItens[$inCount]['vl_unitario'] = $rsItens->getCampo('vl_unitario');
                $arItens[$inCount]['oc_vl_total'] = $ocVlTotal;
                $arItens[$inCount]['oc_saldo'] = $rsItens->getCampo('oc_saldo');
                $arItens[$inCount]['oc_disponivel'] = '0,0000';
                $inCount++;
                // Preenche a quantidade da OC com a quantidade do Empenho, para facilitar na operação.
        $stJsPreenche.= "$('qtdeOC_".$inCount."').value = '".number_format($rsItens->getCampo('oc_saldo'),4,',','.')."'; ";
                $rsItens->proximo();
            }
            Sessao::write('arItens', $arItens);
            $stJs = montaListaItens( $arItens);
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

    include_once( TCOM."TComprasOrdem.class.php" );
    $obTComprasOrdemCompra = new TComprasOrdem();
    $obTComprasOrdemCompra->setDado( 'exercicio'		, $_REQUEST['exercicio_empenho'] );
    $obTComprasOrdemCompra->setDado( 'cod_pre_empenho'	, $_REQUEST['cod_pre_empenho'] 	 );
    $obTComprasOrdemCompra->setDado( 'num_item'			, $_REQUEST['num_item'] 		 );
    $obTComprasOrdemCompra->recuperaDetalheItem( $rsDetalheItem );

    $obForm = new Form();
    $obForm->setName("frm2");

    if ( !is_null($rsDetalheItem->getCampo('cod_item')) ) {
        $obLblCodItem = new Label();
        $obLblCodItem->setRotulo( 'Código do Item' );
        $obLblCodItem->setValue( $rsDetalheItem->getCampo('cod_item') );
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
    $obFormulario->addTitulo( 'Detalhe do Item' );

    if ( !is_null($rsDetalheItem->getCampo('cod_item')) ) {
        $obFormulario->addComponente( $obLblCodItem );
    }

    $obFormulario->addComponente( $obLblItem );
    $obFormulario->addComponente( $obLblGrandeza );
    $obFormulario->addComponente( $obLblUnidade );
    $obFormulario->show();

    break;

    case 'delItem':
        $stJs = delItem( $_GET['inNumItem'] );
    break;

    case 'BuscaOrdemCompraItens':
        $stJs = BuscaOrdemCompraItens($_REQUEST['inCodEmpenho'], $_REQUEST['inCodEntidade'], $_REQUEST['stExercicioOrdemCompra'],$_REQUEST['inCodOrdemCompra'],$_REQUEST['stTipoOrdem'],$_REQUEST['stAcao']);
    break;

    case 'BuscaEmpenhoItens':
    $stJs = BuscaEmpenhoItens( $_REQUEST['inCodEmpenho'],$_REQUEST['inCodEntidade'],$stTipoOrdem, $stAcao);
    break;

} // fim switch

if (isset($stJs)) {
   echo($stJs);
}
?>
