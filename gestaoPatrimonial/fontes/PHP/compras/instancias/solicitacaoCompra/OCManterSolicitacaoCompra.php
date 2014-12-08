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
 * Pagina Oculta de Solicitação de compra
 * Data de Criação: 11/09/2006

 * @author Analista     : Diego Victoria
 * @author Desenvolvedor: Rodrigo

 * Casos de uso: uc-03.04.01

 $Id: OCManterSolicitacaoCompra.php 59612 2014-09-02 12:00:51Z gelson $

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

# Includes da GA.
include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php";
include_once CAM_FW_COMPONENTES."Table/TableTree.class.php";

# Includes da GP.
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCatalogoItem.class.php";
include_once CAM_GP_ALM_NEGOCIO."RAlmoxarifadoEstoqueItem.class.php";

include_once CAM_GP_COM_COMPONENTES."IMontaSolicitacao.class.php";

include_once CAM_GP_COM_MAPEAMENTO."TComprasSolicitacao.class.php";
include_once CAM_GP_COM_MAPEAMENTO."TComprasSolicitacaoItem.class.php";
include_once CAM_GP_COM_MAPEAMENTO."TComprasSolicitacaoEntrega.class.php";
include_once CAM_GP_COM_MAPEAMENTO."TComprasSolicitacaoConvenio.class.php";
include_once CAM_GP_COM_MAPEAMENTO."TComprasSolicitacaoItemDotacao.class.php";
include_once CAM_GP_COM_MAPEAMENTO."TComprasSolicitacaoItemAnulacao.class.php";
include_once CAM_GP_COM_MAPEAMENTO."TComprasMapaItem.class.php";
include_once CAM_GP_COM_MAPEAMENTO."TComprasMapaItemDotacao.class.php";
include_once CAM_GP_COM_MAPEAMENTO."TComprasConfiguracao.class.php";

# Includes da GF.
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoDespesa.class.php";
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoContaDespesa.class.php';
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecurso.class.php";
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoProjetoAtividade.class.php";
include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php";
include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoPreEmpenho.class.php";
include_once CAM_GF_EMP_NEGOCIO."REmpenhoAutorizacaoEmpenho.class.php";
include_once CAM_GP_COM_COMPONENTES."IMontaDotacaoDesdobramento.class.php";

function montaListaDotacoesAnular($arRecordSetItem , $boExecuta = true)
{
    $stPrograma = "ManterSolicitacaoCompra";
    $pgOcul     = "OC".$stPrograma.".php";

    $rsDotacoesItem = new RecordSet;
    $rsDotacoesItem->preenche( $arRecordSetItem );

    $table = new Table;
    $table->setRecordset( $rsDotacoesItem );
    $table->setSummary('Itens da Solicitação');

    //$table->setConditional( true , "#efefef" );

    $table->Head->addCabecalho('Item'                , 35);
    $table->Head->addCabecalho('Unidade'             , 15);
    $table->Head->addCabecalho('Centro de Custo'     , 25);
    $table->Head->addCabecalho('Quantidade Pendente' , 10);
    $table->Head->addCabecalho('Quantidade Anular'   , 15);
    $table->Head->addCabecalho('Valor Pendente'      , 10);
    $table->Head->addCabecalho('Valor Anular'        , 15);

    $obQuantidadeTotalAnulada = new TextBox;
    $obQuantidadeTotalAnulada->setName ( "nuQtTotalAnulada");
    $obQuantidadeTotalAnulada->setNull ( false );
    $obQuantidadeTotalAnulada->setSize ( 10 );
    $obQuantidadeTotalAnulada->setId   ( "" );
    $obQuantidadeTotalAnulada->obEvento->setOnKeyUp("mascaraMoeda(this,4,event,false);");
    $obQuantidadeTotalAnulada->obEvento->setOnBlur ("floatDecimal(this, '4', event ); ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&id='+this.id+'&valorAnular='+this.value, 'calculaValorAnular' );");

    $obValorTotalAnulada = new TextBox;
    $obValorTotalAnulada->setName ( "nuVlTotalAnulada" );
    $obValorTotalAnulada->setNull ( false );
    $obValorTotalAnulada->setSize ( 10 );
    $obValorTotalAnulada->setId   ( "" );
    $obValorTotalAnulada->obEvento->setOnKeyUp ("mascaraMoeda(this, 2, event, false);");
    $obValorTotalAnulada->obEvento->setOnBlur  ("floatDecimal(this, '2', event);");

    $table->Body->addCampo("[inCodItem] - [stNomItem]" , "E", "Item");
    $table->Body->addCampo('stNomUnidade'              , "E", "Unidade");
    $table->Body->addCampo('stNomCentroCusto'          , "E", "Centro de Custo");
    $table->Body->addCampo('nuQtTotalSolicitada'       , "D");
    $table->Body->addCampo($obQuantidadeTotalAnulada   , "C");
    $table->Body->addCampo('nuVlTotalSolicitada'       , "D");
    $table->Body->addCampo($obValorTotalAnulada        , "C");

    $table->montaHTML(true);
    $stHTML = $table->getHtml();

    if ($boExecuta) {
        return "jQuery('#spnListaSolicitacoes').html('".$stHTML."'); ";
    }
}

function calculaTotalizador($arItens)
{
    $nuTotalVlSolicitado  = 0.00;
    $nuTotalVlAnulado     = 0.00;
    $nuTotalVlMapa        = 0.00;

    foreach ($arItens as $value) {
        if ($value['bo_totalizar'] == "true") {
            $nuTotalVlSolicitado  = $nuTotalVlSolicitado  + str_replace("," , ".",  str_replace("." , "" , $value['nuVlTotalSolicitada'] ));
            $nuTotalVlAnulado     = $nuTotalVlAnulado     + str_replace("," , ".",  str_replace("." , "" , $value['nuVlTotalAnulada']    ));
            $nuTotalVlMapa        = $nuTotalVlMapa        + str_replace("," , ".",  str_replace("." , "" , $value['nuVlTotalMapa']       ));
        }
    }

    $arTotal = array();
    $arTotal['nuTotalVlSolicitado']  = number_format( $nuTotalVlSolicitado  , 2, ",", "." );
    $arTotal['nuTotalVlAnulado']     = number_format( $nuTotalVlAnulado     , 2, ",", "." );
    $arTotal['nuTotalVlMapa']        = number_format( $nuTotalVlMapa        , 2, ",", "." );

    $stJs = montaTotalizador($arTotal);

    return $stJs;
}

function montaTotalizador($arTotal)
{
    $obLblTotalSolicitado = new Label;
    $obLblTotalSolicitado->setId('nuTotalVlSolicitado');
    $obLblTotalSolicitado->setValue($arTotal['nuTotalVlSolicitado']);
    $obLblTotalSolicitado->setRotulo('Total Solicitado');

    $obLblTotalAnulado = new Label;
    $obLblTotalAnulado->setId('nuTotalVlAnulado');
    $obLblTotalAnulado->setValue($arTotal['nuTotalVlAnulado']);
    $obLblTotalAnulado->setRotulo('Total Anulado');

    $obLblTotalMapa = new Label;
    $obLblTotalMapa->setId('nuTotalVlMapa');
    $obLblTotalMapa->setValue($arTotal['nuTotalVlMapa']);
    $obLblTotalMapa->setRotulo('Valor Total em Mapas');

    $obFormulario = new Formulario();
    $obFormulario->addTitulo        ( "Valores Totais Atualizados" );
    $obFormulario->addComponente($obLblTotalSolicitado);
    $obFormulario->addComponente($obLblTotalAnulado);
    $obFormulario->addComponente($obLblTotalMapa);
    $obFormulario->montaInnerHTML();
    $stHtml .= $obFormulario->getHTML();

    $stHtml = str_replace( "\n", "", $stHtml);
    $stHtml = str_replace( "  ", "", $stHtml);
    $stHtml = str_replace( "'" , "\\'", $stHtml);

    $stJs = "jQuery('#spnTotalizador').html('".$stHtml."');";

    return $stJs;
}

function montaListaDotacoesConsulta($arRecordSetItem , $boExecuta = true)
{
    $stJs = "";

    $stPrograma = "ManterSolicitacaoCompra";
    $pgOcul     = "OC".$stPrograma.".php";

    $rsDotacoesItem = new RecordSet;
    $rsDotacoesItem->preenche( $arRecordSetItem );

    $table = new Table;

    $table->setRecordset( $rsDotacoesItem );
    $table->setSummary('Itens da Solicitação');

    //$table->setConditional( true , "#efefef" );

    $table->Head->addCabecalho('Item'             , 18);
    $table->Head->addCabecalho('Unidade'          , 10);
    $table->Head->addCabecalho('Centro de Custo'  , 10);
    $table->Head->addCabecalho('Qtde. Solicitada' , 10);
    $table->Head->addCabecalho('Qtde. Anulada'    , 10);
    $table->Head->addCabecalho('Qtde. Mapa'       ,  8);
    $table->Head->addCabecalho('Valor Solicitado' ,  8);
    $table->Head->addCabecalho('Valor Anulado'    ,  8);
    $table->Head->addCabecalho('Valor Mapa'       ,  8);

    $stTitle = "[stTitle]";

    $table->Body->addCampo("[inCodItem] - [stNomItem]" , "E" , $stTitle);
    $table->Body->addCampo('stNomUnidade'              , "E" , $stTitle);
    $table->Body->addCampo('stNomCentroCusto'          , "E" , $stTitle);
    $table->Body->addCampo('nuQtTotalSolicitada'       , "D" , $stTitle);
    $table->Body->addCampo('nuQtTotalAnulada'          , "D" , $stTitle);
    $table->Body->addCampo('nuQtTotalMapa'             , "D" , $stTitle);
    $table->Body->addCampo('nuVlTotalSolicitada'       , "D" , $stTitle);
    $table->Body->addCampo('nuVlTotalAnulada'          , "D" , $stTitle);
    $table->Body->addCampo('nuVlTotalMapa'             , "D" , $stTitle);

    $table->montaHTML(true);
    $stHTML = $table->getHtml();

    if ($boExecuta) {
        $stJs = "jQuery('#spnListaSolicitacoes').html('".$stHTML."');";
    }

    return $stJs;
}

function montaListaDotacoes($arRecordSet , $boExecuta = true)
{
    $stPrograma = "ManterSolicitacaoCompra";
    $pgOcul     = "OC".$stPrograma.".php";

    $rsDotacoesItem = new RecordSet;
    $rsDotacoesItem->preenche($arRecordSet);

    $rsDotacoesItem->setPrimeiroElemento();

    $table = new Table;
    $table->setRecordset($rsDotacoesItem);

    $table->setSummary('Itens da Solicitação');

    //$table->setConditional(true, "#efefef");

    $table->Head->addCabecalho('Item' 			   , 35);
    $table->Head->addCabecalho('Unidade de Medida' , 10);
    $table->Head->addCabecalho('Centro de Custo'   , 30);
    $table->Head->addCabecalho('Quantidade'		   , 10);
    $table->Head->addCabecalho('Valor Total'	   , 15);

    $stTitle = "[stTitle]";

    $table->Body->addCampo("[inCodItem] - [stNomItem]"				 ,"E", $stTitle);
    $table->Body->addCampo('stNomUnidade'							 ,"E", $stTitle);
    $table->Body->addCampo('[inCodCentroCusto] - [stNomCentroCusto]' ,"E", $stTitle);
    $table->Body->addCampo('nuQuantidade'							 ,"D", $stTitle);
    $table->Body->addCampo('nuVlTotal'								 ,"D", $stTitle);

    $table->Body->addAcao('excluir', 'excluirListaItens(%s)', array('id'));
    $table->Body->addAcao('alterar', 'alterarListaItens(%s)', array('id'));

    $table->Foot->addSoma('nuVlTotal', "D");

    $table->montaHTML();
    $stHTML = $table->getHtml();

    $stHTML = str_replace("\n" ,"" ,$stHTML);
    $stHTML = str_replace("  " ,"" ,$stHTML);
    $stHTML = str_replace("'","\\'",$stHTML);

    if ($boExecuta) {
        $js = "document.getElementById('spnListaSolicitacoes').innerHTML = '".$stHTML."';";
        if (count($arRecordSet) > 0) {
            $js .= "document.getElementById('inCodEntidade').disabled = true;";
            $js .= "document.getElementById('stNomEntidade').disabled = true;";
        } else {
            $js .= "document.getElementById('inCodEntidade').disabled = false;";
            $js .= "document.getElementById('stNomEntidade').disabled = false;";
        }

        return $js;
    } else {
        return $stHTML;
    }

}

function preencheSpnItensOutraSolicitacao()
{
    $pgOcul = "OCManterSolicitacaoCompra.php";
    //DEFINICAO DO FORM
    $obForm = new Form;
    $obForm->setAction ( $pgProc  );
    $obForm->setTarget ( "oculto" );

    //Objetos de itens de outra solicitação
    $obSolicitacao = new IMontaSolicitacao($obForm);
    $obSolicitacao->obPopUpSolicitacao->setRotulo("**Solicitação");
    $obSolicitacao->obExercicio->setRotulo('Exercício');
    $obSolicitacao->obExercicio->setObrigatorioBarra( true );
    $obSolicitacao->obExercicio->setNull( true );
    $obSolicitacao->obITextBoxSelectEntidade->setRotulo('*Entidade');
    if ($stAcao=="alterar") {
      $obSolicitacao->setCodSolicitacaoExcluida($_REQUEST['cod_solicitacao']);
    }

    // Define Objeto Button para Incluir Item
    $obBtnIncluirOutraSolicitacao = new Button;
    $obBtnIncluirOutraSolicitacao->setValue( "Incluir" );
    $obBtnIncluirOutraSolicitacao->obEvento->setOnClick( "goOcultoOutraSolicitacao('incluirItensOutraSolicitacao',true,'false');");

    // Define Objeto Button para Limpar Item
    $obBtnLimparOutraSolicitacao = new Button;
    $obBtnLimparOutraSolicitacao->setValue( "Limpar" );
    $obBtnLimparOutraSolicitacao->obEvento->setOnClick( "LimparOutraSolicitacao();" );

    $obFormulario = new Formulario();
    $obFormulario->addTitulo        ( "Incluir Itens de Outra Solicitação" );
    $obSolicitacao->geraFormulario  ( $obFormulario );
    $obFormulario->agrupaComponentes( array( $obBtnIncluirOutraSolicitacao, $obBtnLimparOutraSolicitacao ) );

    $obFormulario->montaInnerHTML();
    $stHtml .= $obFormulario->getHTML();

    $stJs = "document.getElementById('spnItensOutraSolicitacao').innerHTML = '".$stHtml."';";

    return $stJs;

}

switch ($_REQUEST['stCtrl']) {

    case "alterarListaItens":

        $rsRecordSetItem = new RecordSet;
        $obTComprasSolicitacaoItem = new TComprasSolicitacaoItem;

        $arValores = Sessao::read('arValores');

        foreach ($arValores as $key => $value) {
            if (($arValores[$key]['id']) == $_REQUEST['id']) {
                $stJs .= "f.inCodItem.value                       = '".$arValores[$key]['inCodItem']."'; ";
                $stJs .= "d.getElementById('stNomItem').innerHTML = '".addslashes($arValores[$key]['stNomItem'])."'; ";
                $stJs .= "f.HdnNomItem.value       ='".addslashes($arValores[$key]['stNomItem'])."'; ";
                $stJs .= "d.getElementById('stUnidadeMedida').innerHTML ='".$arValores[$key]['stNomUnidade']."'; ";
                $stJs .= "f.stNomUnidade.value ='".$arValores[$key]['stNomUnidade']."';    ";

                $count=0;
                foreach ($arValores as $valor) {
                    if ($value['inCodItem'] == $valor['inCodItem']
                        and $value['inCodCentroCusto'] == $valor['inCodCentroCusto']) {
                        $count++;
                    }
                }

                if ($count > 1) {
                    $stJs .= "jq('#stComplemento').attr('readonly', 'readonly');";
                } else {
                    $stJs .= "jq('#stComplemento').attr('readonly', '');";
                }

                $stJs .= "f.stComplemento.value                         ='".nl2br(str_replace('\r\n', '\n', preg_replace('/(\r\n|\n|\r)/', ' ',addslashes($arValores[$key]['stComplemento']))))."';   ";
                $stJs .= "f.inCodCentroCusto.value                      ='".$arValores[$key]['inCodCentroCusto']."';";
                $stJs .= "d.getElementById('stNomCentroCusto').innerHTML='".$arValores[$key]['stNomCentroCusto']."';";
                $stJs .= "if (f.inCodDespesa.value=='') { f.inCodDespesa.focus(); }                                                    ";
                $stJs .= "f.inCodDespesa.value                          ='".$arValores[$key]['inCodDespesa']."';    ";
                $stJs .= "f.HdnCodClassificacao.value ='".$arValores[$key]['inCodEstrutural']."';";

                # Recupera valor da última compra.
                $obTAlmoxarifadoCatalogoItem = new TAlmoxarifadoCatalogoItem;
                $obTAlmoxarifadoCatalogoItem->setDado('cod_item'  , $arValores[$key]['inCodItem']);
                $obTAlmoxarifadoCatalogoItem->setDado('exercicio' , Sessao::getExercicio());
                $obTAlmoxarifadoCatalogoItem->recuperaValorItemUltimaCompra($rsRecordSetItem);
                $rsRecordSetItem->addFormatacao ('vl_unitario_ultima_compra', 'NUMERIC_BR');
                $vlUnitario = $rsRecordSetItem->getCampo('vl_unitario_ultima_compra');

                # Label para demonstrar o valor da última compra.
                $obLblValorReferenciaUltimaCompra = new Label;
                $obLblValorReferenciaUltimaCompra->setRotulo ('Valor da Última Compra');
                $obLblValorReferenciaUltimaCompra->setTitle  ('Valor Unitário de Referência da Última Compra.');
                $obLblValorReferenciaUltimaCompra->setValue  ($vlUnitario);

                $obFormulario = new Formulario;
                $obFormulario->addComponente( $obLblValorReferenciaUltimaCompra );
                $obFormulario->montaInnerHTML();
                $stHtml .= $obFormulario->getHTML();

                $stHtml = str_replace( "\n", "", $stHtml);
                $stHtml = str_replace( "  ", "", $stHtml);
                $stHtml = str_replace( "'" , "\\'", $stHtml);

                $stJs .= "jQuery('#spnVlrReferencia').html('".$stHtml."'); ";
                $stJs .= "d.getElementById('nuQuantidade').value = '".$arValores[$key]['nuQuantidade']."'; ";
                $stJs .= "d.getElementById('nuVlTotal').value = '".$arValores[$key]['nuVlTotal']."'; ";
                $stJs .= "d.getElementById('hdnValorTotalReservado').value='".$arValores[$key]['nuVlTotalReservado']."'; ";
                $stJs .= "f.nuVlTotalReservado.value = '".$arValores[$key]['nuVlTotalReservado']."'; ";
                $stJs .= "document.getElementById('incluiItem').value = 'Alterar';";
                $stJs .= "d.getElementById('incluiItem').setAttribute('onclick','JavaScript:goOculto(\'alteradoListaItens\',true,\'true\');');	 ";
                $stJs .= "f.HdnCodItem.value = '".$_REQUEST['id']."'; ";
                $stJs .= "f.inCodItem.focus(); ";
                $stJs .= "var vlUnitario = parseToMoeda(parseFloat(d.getElementById('nuVlTotal').value.replace('.','').replace(',','.'))	 ";
                $stJs .= "                 / parseFloat(d.getElementById('nuQuantidade').value.replace('.','').replace(',','.')).toFixed(4),4);   ";
                $stJs .= "d.getElementById('nuVlUnitario').value = vlUnitario;                                                       ";
                $stJs .= "f.stCtrl.value = 'alteradoListaItens'; ";

                if ($arValores[$key]['inCodDespesa'] != "") {
                    $stJs .= "var stTarget = document.frm.target; ";
                    $stJs .= "var stAction = document.frm.action; ";
                    $stJs .= "f.stCtrl.value = 'buscaDespesaDiverso'; ";
                    $stJs .= "d.getElementById('stNomDespesa').innerHTML   = '&nbsp;'; ";
                    $stJs .= "d.getElementById('nuSaldoDotacao').innerHTML = '&nbsp;'; ";
                    $stJs .= "f.target ='oculto';                                                                                 ";
                    $stJs .= "f.action ='../../instancias/processamento/OCIMontaDotacaoDesdobramento.php?".Sessao::getId()."&stCodEstrutural=".$arValores[$key]['stCodEstrutural']."&codClassificacao=".$arValores[$key]['inCodEstrutural']."';        ";
                    $stJs .= "f.submit();                                                                                         ";
                    $stJs .= "f.action = '".$pgOcul."?".Sessao::getId()."';                                                           ";
                    $stJs .= "f.action = stAction;                                                                                ";
                    $stJs .= "f.target = stTarget;                                                                                ";
                }

                $stJs .= "f.stCtrl.value = 'alteradoListaItens'; ";
            }
        }

    break;

    case "alteradoListaItens":

        $arValores = Sessao::read('arValores');

        $inCodItem        = $_REQUEST['inCodItem'];
        # Pega o id do array de itens.
        $inIdArrayItem    = $_REQUEST['HdnCodItem'];
        $inCodCentroCusto = $_REQUEST['inCodCentroCusto'];
        $inCodDespesa     = $_REQUEST['inCodDespesa'];
        $inCodConta       = $_REQUEST['stCodClassificacao'];
        $stJs 			  = "";

        $boErro    = false;
        $boAlterar = true;

        if (is_array($arValores)) {

            $obTConfiguracao = new TAdministracaoConfiguracao();
            $obTConfiguracao->setDado('exercicio', Sessao::getExercicio());
            $obTConfiguracao->pegaConfiguracao($boFormaExecucao,'forma_execucao_orcamento');

            if (is_numeric($inCodDespesa) && isset($inCodDespesa)) {
                if ((is_numeric($inCodConta) && isset($inCodConta) && $boFormaExecucao == true) || $boFormaExecucao == false) {
                    foreach ($arValores as $key => $arDados) {
                        if ($inIdArrayItem != $arDados['id']) {
                            if (($arDados['inCodItem']        == $inCodItem) 	    &&
                                ($arDados['inCodCentroCusto'] == $inCodCentroCusto) &&
                                ($arDados['inCodDespesa'] 	  == $inCodDespesa) 	&&
                                ($arDados['inCodEstrutural']  == $inCodConta)){
                                $stJs .= "alertaAviso('Este item já consta na lista com o mesmo Centro de Custo, Dotação e Desdobramento.','form','erro','".Sessao::getId()."');";
                                $boAlterar = false;
                                break;
                            } elseif (($arDados['inCodItem']        == $inCodItem) 	      &&
                                      ($arDados['inCodCentroCusto'] == $inCodCentroCusto) &&
                                      (empty($arDados['inCodDespesa']))) {
                                # Verifica se o item já não existe na lista porém sem dotação vinculada.
                                $stJs .= "alertaAviso('Esse item já consta na lista.','form','erro','".Sessao::getId()."');";
                                $boAlterar = false;
                                break;
                            }
                        }
                    }
                } else {
                    $boAlterar = false;
                    $stJs .= "alertaAviso('Necessário informar o Desdobramento para a Despesa !','form','erro','".Sessao::getId()."');";
                }
            } else {
                # Validação para não permitir incluir o mesmo item com o mesmo
                # centro de custo.
                foreach ($arValores as $key => $arDados) {
                    if (($arDados['inCodItem']        == $inCodItem) 	    &&
                        ($arDados['inCodCentroCusto'] == $inCodCentroCusto) &&
                        ($arDados['id'] != $inIdArrayItem)){
                        $boAlterar = false;
                        $stJs .= "alertaAviso('Esse item já consta na lista com o mesmo Centro de Custo.','form','erro','".Sessao::getId()."');";
                        break;
                    }
                }
            }
        }

        if ($boAlterar) {
            $rsConfiguracao = new RecordSet();
            $obTConfiguracao = new TAdministracaoConfiguracao();
            $obTConfiguracao->setDado('exercicio', Sessao::getExercicio());
            $obTConfiguracao->pegaConfiguracao($boReservaRigida      ,'reserva_rigida');
            $obTConfiguracao->pegaConfiguracao($boFormaExecucao      ,'forma_execucao_orcamento');
            $obTConfiguracao->pegaConfiguracao($boDotacaoObrigatoria ,'dotacao_obrigatoria_solicitacao');

            $boFormaExecucao 	  = ($boFormaExecucao == 1          ) ? true : false;
            $boReservaRigida      = ($boReservaRigida == 'true'     ) ? true : false;
            $boDotacaoObrigatoria = ($boDotacaoObrigatoria == 'true') ? true : false;

            $boDotacaoSaldo         = true;
            $boValorReservado       = true;
            $boDotacaoDesdobramento = true;

            if (!isset($arSaldoDotacoes['saldo'][$_REQUEST['inCodDespesa']])) {
                $arSaldoDotacoes['saldo'][$_REQUEST['inCodDespesa']] = $_REQUEST['nuHdnSaldoDotacao'];
                $arSaldoDotacoes['total'][$_REQUEST['inCodDespesa']] = $_REQUEST['nuHdnSaldoDotacao'];
            }

            if (($arSaldoDotacoes['saldo'][$_REQUEST['inCodDespesa']] + str_replace(',','.',str_replace('.','',$_REQUEST['hdnValorTotalReservado'])) ) < str_replace(',','.',str_replace('.','',$_REQUEST['nuVlTotalReservado'])) AND ( $boReservaRigida ) AND ( $_REQUEST['inCodDespesa'] !='')) {
                $boDotacaoSaldo = false;
            }

            if ($boFormaExecucao && $boDotacaoObrigatoria && ($_REQUEST[ "stCodClassificacao" ] == '')) {
                if ($_REQUEST["inCodDespesa"] != '') {
                    $boDotacaoDesdobramento = false;
                }
            }

            if (str_replace(',','.',str_replace('.','',$_REQUEST['nuVlTotalReservado'])) > str_replace(',','.',str_replace('.','',$_REQUEST['nuVlTotal']))) {
                $stJs .= "alertaAviso('Valor Reservado no exercício deve ser igual ou menor ao valor total do item!','form','erro','".Sessao::getId()."');";
                $boErro = true;
            }

            $nuVlUnitario = str_replace(',','.', str_replace('.','',$_REQUEST['nuVlUnitario']));
            $nuVlTotal    = str_replace(',','.', str_replace('.','',$_REQUEST['nuVlTotal']   ));
            $nuQuantidade = str_replace(',','.', str_replace('.','',$_REQUEST['nuQuantidade']));

            # Retirada a obrigatoriedade de o valor do item ser > 0
            if ($nuQuantidade == 0.0000) {
                $stJs .= "alertaAviso('A quantidade deve ser maior que zero!','form','erro','".Sessao::getId()."');";
                $boErro = true;
            }

            if ($nuVlTotal == 0.00) {
                $stJs .= "alertaAviso('O Valor Total dos itens deve ser maior que zero!','form','erro','".Sessao::getId()."');";
                $boErro = true;
            }

            if ($nuVlUnitario == 0.00) {
                $stJs .= "alertaAviso('O Valor Unitário dos itens deve ser maior que zero!','form','erro','".Sessao::getId()."');";
                $boErro = true;
            }

            if (($_REQUEST['inCodDespesa'] != '') AND ($_REQUEST['nuVlTotalReservado'] == '0,00' OR  $_REQUEST['nuVlTotalReservado'] == '')) {
                $boValorReservado = false;
            }

            if ($boErro == false) {
                if ($boDotacaoDesdobramento) {
                    if ($boDotacaoSaldo) {
                            foreach ($arValores as $key => $value) {
                                if ($value['id'] == $inIdArrayItem) {
                                    $arValores[$key]['id']                 = $inIdArrayItem;
                                    $arValores[$key]['inCodItem']          = $_REQUEST["inCodItem"];
                                    $arValores[$key]['stNomItem']          = stripslashes($_REQUEST["HdnNomItem"]);
                                    $arValores[$key]['inCodCentroCusto']   = $_REQUEST["inCodCentroCusto"];
                                    $arValores[$key]['stNomUnidade']       = stripslashes($_REQUEST["stNomUnidade"]);
                                    $arValores[$key]['stNomCentroCusto']   = stripslashes($_REQUEST["HdnNomCentroCusto"]);
                                    $arValores[$key]['stComplemento']      = stripslashes($_REQUEST["stComplemento"]);
                                    $arValores[$key]['nuQuantidade']       = $_REQUEST["nuQuantidade"];
                                    $arValores[$key]['nuVlTotal']          = $_REQUEST["nuVlTotal"];
                                    $arValores[$key]['inCodDespesa']       = $_REQUEST["inCodDespesa"];
                                    $arValores[$key]['nuVlTotalReservado'] = $_REQUEST["nuVlTotal"];

                                    if (is_numeric($inCodDespesa) && isset($inCodDespesa)) {
                                        if ($_REQUEST['stCodClassificacao'] == '') {
                                            $obTOrcamentoDespesa = new TOrcamentoDespesa;
                                            $stFiltro  = " AND D.cod_despesa = ".$_REQUEST['inCodDespesa'];
                                            $stFiltro .= " AND D.exercicio   = '".Sessao::getExercicio()."' ";
                                            $obTOrcamentoDespesa->recuperaListaDotacao( $rsDotacao, $stFiltro );
                                        } else {
                                            $obTOrcamentoContaDespesa = new TOrcamentoContaDespesa;
                                            $stFiltro  = " cod_conta     = ".$_REQUEST['stCodClassificacao'];
                                            $stFiltro .= " AND exercicio = '".Sessao::getExercicio()."' ";
                                            $obTOrcamentoContaDespesa->recuperaCodEstrutural( $rsDotacao, $stFiltro );
                                        }
                                        $arValores[$key]['inCodEstrutural'] = $rsDotacao->getCampo('cod_conta');
                                        $arValores[$key]['stCodEstrutural'] = $rsDotacao->getCampo('cod_estrutural');
                                        $arValores[$key]['stTitle'] = $_REQUEST[ "inCodDespesa" ].' - '.$rsDotacao->getCampo('descricao').' - '.$rsDotacao->getCampo('cod_estrutural');
                                    } else {
                                        $arValores[$key]['inCodEstrutural'] = '';
                                        $arValores[$key]['stCodEstrutural'] = '';
                                        $arValores[$key]['stTitle'] = '';
                                    }
                                    $arSaldoDotacoes['saldo'][$_REQUEST['inCodDespesa']] += str_replace(',','.',str_replace('.','',$_REQUEST['hdnValorTotalReservado']));
                                    $arSaldoDotacoes['saldo'][$_REQUEST['inCodDespesa']] -= str_replace(',','.',str_replace('.','',$_REQUEST['nuVlTotalReservado']));
                                }
                            }
                            $stJs .= "jQuery('#incluiItem').val('Incluir');";
                            $stJs .= "jQuery('#incluiItem').attr('onclick','JavaScript:goOculto(\'incluirListaItens\',true,\'false\');');	 ";
                            $stJs .= "LimparItensSolicitacao();";
                    } else {
                        $stJs .= "alertaAviso('Valor da Reserva é Superior ao Saldo da Dotação (".$_REQUEST['inCodDespesa'].").','form','erro','".Sessao::getId()."');";
                    }
                } else {
                    $stJs .= "alertaAviso('Você precisa selecionar um desdobramento.','form','erro','".Sessao::getId()."');";
                }
            }
            Sessao::write('arValores'       , $arValores      );
            Sessao::write('arSaldoDotacoes' , $arSaldoDotacoes);

            $stJs .= montaListaDotacoes($arValores);
        }

    break;

    case 'carregaConsultaSolicitacao' :

        $stAcao 		  = $_REQUEST['stAcao'];
        $inCodSolicitacao = $_REQUEST['cod_solicitacao'];
        $inCodEntidade    = $_REQUEST['cod_entidade'];
        $stExercicio      = $_REQUEST['exercicio'];

        if (is_numeric($inCodSolicitacao) && is_numeric($inCodEntidade) && is_numeric($stExercicio)) {

            $arValores = Sessao::read('arValores');

            $rsRecordSetItem = new RecordSet;
            $obTComprasSolicitacaoItem = new TComprasSolicitacaoItem;

            # Busca os dados dos itens da Solicitação de Compras.
            $obTComprasSolicitacaoItem->setDado('cod_solicitacao' , $inCodSolicitacao);
            $obTComprasSolicitacaoItem->setDado('cod_entidade'    , $inCodEntidade);
            $obTComprasSolicitacaoItem->setDado('exercicio'       , $stExercicio);
            $obTComprasSolicitacaoItem->recuperaItensConsulta($rsRecordSetItem);

            $inCount = 0;
            if (!($rsRecordSetItem->EOF())) {
                while (!($rsRecordSetItem->EOF())) {
                    $arValores[$inCount]['id']               = $inCount+1;
                    $arValores[$inCount]['inCodItem']        = $rsRecordSetItem->getCampo('cod_item');
                    $arValores[$inCount]['stNomItem']        = $rsRecordSetItem->getCampo('descricao_resumida');
                    $arValores[$inCount]['inCodCentroCusto'] = $rsRecordSetItem->getCampo('cod_centro');
                    $arValores[$inCount]['stNomUnidade']     = $rsRecordSetItem->getCampo('nom_unidade');
                    $arValores[$inCount]['stNomCentroCusto'] = $rsRecordSetItem->getCampo('descricao');
                    $arValores[$inCount]['stComplemento']    = $rsRecordSetItem->getCampo('complemento');
                    $arValores[$inCount]['cod_despesa']      = $rsRecordSetItem->getCampo('cod_despesa');
                    $arValores[$inCount]['cod_conta']        = $rsRecordSetItem->getCampo('cod_conta');
                    $arValores[$inCount]['cod_estrutural']   = $rsRecordSetItem->getCampo('hint_cod_estrutural');
                    $arValores[$inCount]['bo_totalizar']     = $rsRecordSetItem->getCampo('bo_totalizar');

                    if ($rsRecordSetItem->getCampo('hint_cod_despesa') != '') {
                        $stTitle  = $rsRecordSetItem->getCampo('hint_cod_despesa').' - '.$rsRecordSetItem->getCampo('hint_nom_despesa').' - ';
                        $stTitle .= $rsRecordSetItem->getCampo('hint_cod_estrutural').' - '.$rsRecordSetItem->getCampo('hint_num_pao').' - ';
                        $stTitle .= $rsRecordSetItem->getCampo('hint_nom_pao').' - '.$rsRecordSetItem->getCampo('hint_cod_recurso').' - ';
                        $stTitle .= $rsRecordSetItem->getCampo('hint_nom_recurso');
                    }
                    $arValores[$inCount]['stTitle']             = $stTitle;
                    $arValores[$inCount]['nuQtTotalSolicitada'] = number_format($rsRecordSetItem->getCampo('qnt_solicitada'),4,',','.');
                    $arValores[$inCount]['nuVlTotalSolicitada'] = number_format($rsRecordSetItem->getCampo('vl_solicitado'),2,',','.');
                    $arValores[$inCount]['nuQtTotalAnulada']    = number_format($rsRecordSetItem->getCampo('qnt_anulada'), 4, ",", ".");
                    $arValores[$inCount]['nuVlTotalAnulada']    = number_format($rsRecordSetItem->getCampo('vl_anulado'), 2, ",", ".");
                    $arValores[$inCount]['nuQtTotalMapa']       = number_format($rsRecordSetItem->getCampo('qnt_mapa'), 4, ",", ".");
                    $arValores[$inCount]['nuVlTotalMapa']       = number_format($rsRecordSetItem->getCampo('vl_mapa'), 2, "," ,".");

                    $inCount++;

                    $rsRecordSetItem->Proximo();
                }
            }

            Sessao::write('arValores', $arValores);

            if ($stAcao == "consultar") {
                $stJs .= montaListaDotacoesConsulta($arValores);
                $stJs .= calculaTotalizador($arValores);
            } elseif ($stAcao == "anular") {
                $stJs .= montaListaDotacoesAnular($arValores);
            }
        }

    break;

    case 'carregaSolicitacao':

        $rsRecordSet                      = new RecordSet;
        $rsRecordSetItem                  = new RecordSet;
        $rsRecordSetEntrega               = new RecordSet;

        $obTComprasSolicitacao            = new TComprasSolicitacao;
        $obTComprasSolicitacaoItem        = new TComprasSolicitacaoItem;
        $obTComprasSolicitacaoEntrega     = new TComprasSolicitacaoEntrega;
        $obTComprasSolicitacaoConvenio    = new TComprasSolicitacaoConvenio;
        $obTComprasSolicitacaoItemDotacao = new TComprasSolicitacaoItemDotacao;

        $inCodSolicitacao = $_REQUEST["cod_solicitacao"];
        $inCodEntidade    =	$_REQUEST["cod_entidade"];
        $stExercicio      =	$_REQUEST['exercicio'];

        if (is_numeric($inCodSolicitacao) && is_numeric($inCodEntidade) && !empty($stExercicio)) {
            $stFiltro  = " WHERE  cod_solicitacao = ".$inCodSolicitacao;
            $stFiltro .= "   AND  cod_entidade    = ".$inCodEntidade;
            $stFiltro .= "   AND  exercicio       = '".$stExercicio."'";

            $obTComprasSolicitacao->recuperaTodos($rsRecordSet, $stFiltro);
        }

        if (!($rsRecordSet->EOF())) {

            $stFiltroEntrega  = " WHERE  cod_solicitacao = ".$inCodSolicitacao;
            $stFiltroEntrega .= "   AND  cod_entidade    = ".$inCodEntidade;
            $stFiltroEntrega .= "   AND  exercicio       = '".$stExercicio."'";

            $obTComprasSolicitacaoEntrega->recuperaTodos($rsRecordSetEntrega, $stFiltroEntrega);

            $js  = "document.forms[0].inCodEntidade.value  ='".$rsRecordSet->getCampo("cod_entidade")."';";
            $js .= "document.forms[0].HdnCodEntidade.value ='".$rsRecordSet->getCampo("cod_entidade")."';";
            $js .= "document.forms[0].stNomEntidade.value  ='".$rsRecordSet->getCampo("cod_entidade")."';";
            $js .= "document.forms[0].stObjeto.value       ='".$rsRecordSet->getCampo("cod_objeto")."';";
            $js .= "buscaValorBscInner( '../../../../../../gestaoPatrimonial/fontes/PHP/compras/popups/objeto/OCProcuraObjeto.php?".Sessao::getId()."','frm','stObjeto','txtObjeto','');";
            $js .= "document.forms[0].inCGM.value          ='".$rsRecordSet->getCampo("cgm_solicitante")."';";
            $js .= "for (x=0;x<document.forms[0].inCodAlmoxarifado.options.length;x++) {";
            $js .= "   if (document.forms[0].inCodAlmoxarifado.options[x].value=='".$rsRecordSet->getCampo("cod_almoxarifado")."') {";
            $js .= "      document.forms[0].inCodAlmoxarifado.options[x].selected=true;";
            $js .= "   }		";
            $js .= "}		";
            $js .= "document.forms[0].inEntrega.value      ='".$rsRecordSetEntrega->getCampo("numcgm")."';";
            $js .= "ajaxJavaScript('../../../../../../gestaoAdministrativa/fontes/PHP/CGM/instancias/processamento/OCProcurarCgm.php?".Sessao::getId()."&inEntrega=".$rsRecordSetEntrega->getCampo("numcgm")."&stNomCampoCod=inEntrega&stIdCampoDesc=stNomEntrega&stTipoBusca=geral','buscaPopup');";
            $js .= "document.forms[0].stPrazoEntrega.value ='".$rsRecordSet->getCampo("prazo_entrega")."';";
            $js .= "document.forms[0].stObservacao.value   ='".preg_replace("/(\r?\n)|\r/",'\n',$rsRecordSet->getCampo("observacao"))."';";
            $js .= "ajaxJavaScript('../../../../../../gestaoAdministrativa/fontes/PHP/CGM/instancias/processamento/OCProcurarCgm.php?".Sessao::getId()."&inCGM=".$rsRecordSet->getCampo("cgm_solicitante")."&stNomCampoCod=inCGM&stIdCampoDesc=stNomCGM','buscaPopup');";

            $obTComprasSolicitacaoConvenio->recuperaTodos($rsRecordSetConvenio, $stFiltro);

            if (!($rsRecordSetConvenio->EOF())) {
                $js.="for (x=0;x<document.forms[0].inCodConvenio.options.length;x++) {";
                $stConvenio = $rsRecordSetConvenio->getCampo("exercicio_convenio")."-".$rsRecordSetConvenio->getCampo("num_convenio");
                $js.="   if (document.forms[0].inCodConvenio.options[x].value=='".$stConvenio."') {";
                $js.="      document.forms[0].inCodConvenio.options[x].selected=true;";
                $js.="   }		";
                $js.="}		";
            }
        }

        $obTComprasSolicitacaoItem->setDado('cod_solicitacao' , $inCodSolicitacao);
        $obTComprasSolicitacaoItem->setDado('cod_entidade'    , $inCodEntidade);
        $obTComprasSolicitacaoItem->setDado('exercicio'       , $stExercicio);
        $obTComprasSolicitacaoItem->recuperaItemSolicitacao($rsRecordSet);

        $arValores = array();

        if (!($rsRecordSet->EOF())) {
            $arValores = Sessao::read('arValores');

            while (!($rsRecordSet->EOF())) {
                $boDotacaoRepetida = false;
                foreach ($arValores as $arTEMP) {
                    if ($arTEMP['inCodItem']        == $rsRecordSet->getCampo('cod_item')    &&
                        $arTEMP['inCodCentroCusto'] == $rsRecordSet->getCampo('cod_centro')  &&
                        $arTEMP['inCodDespesa']     == $rsRecordSet->getCampo('cod_despesa') &&
                        $arTEMP['inCodEstrutural']  == $rsRecordSet->getCampo('cod_conta')){
                        $boDotacaoRepetida = true ;
                        break;
                    }
                }

                if (!($boDotacaoRepetida)) {

                    $inCount = sizeof($arValores);

                    $arValores[$inCount]['id']               = $inCount + 1;
                    $arValores[$inCount]['inCodItem']        = $rsRecordSet->getCampo('cod_item');
                    $arValores[$inCount]['stNomItem']        = stripslashes($rsRecordSet->getCampo('descricao_resumida'));
                    $arValores[$inCount]['inCodCentroCusto'] = $rsRecordSet->getCampo('cod_centro');
                    $arValores[$inCount]['stNomUnidade']     = stripslashes($rsRecordSet->getCampo('nom_unidade'));
                    $arValores[$inCount]['stNomCentroCusto'] = stripslashes($rsRecordSet->getCampo('descricao'));
                    $arValores[$inCount]['stComplemento']    = stripslashes($rsRecordSet->getCampo('complemento'));
                    $arValores[$inCount]['nuQuantidade']     = number_format($rsRecordSet->getCampo('quantidade'),4,',','.');
                    $arValores[$inCount]['nuVlTotal']        = number_format($rsRecordSet->getCampo('vl_total'),2,',','.');

                    $arValores[$inCount]['inCodDespesa']       = $rsRecordSet->getCampo('cod_despesa');
                    $arValores[$inCount]['inCodEstrutural']    = $rsRecordSet->getCampo('cod_conta');
                    $arValores[$inCount]['nuVlTotalReservado'] = number_format($rsRecordSet->getCampo('vl_reserva')?$rsRecordSet->getCampo('vl_reserva'):$rsRecordSet->getCampo('vl_total'),2,',','.');
                    $arValores[$inCount]['stCodEstrutural']    = $rsRecordSet->getCampo('desdobramento');
                    $arValores[$inCount]['stTitle']            = $rsRecordSet->getCampo('cod_despesa').' - '.$rsRecordSet->getCampo('nomdespesa').' - '.$rsRecordSet->getCampo('desdobramento');
                }
                $rsRecordSet->Proximo();
            }
            Sessao::write('arValores' , $arValores);
        }
        echo $js.montaListaDotacoes($arValores);
    break;

    case 'carregaListaItens' :
        $arValores = Sessao::read('arValores');
        echo montaListaDotacoes($arValores);
    break;

    case 'excluirListaItens':

        $arValores = Sessao::read('arValores');

        $boDotacaoRepetida = false;
        $arTEMP            = array();
        $inCount           = 0;
        $inCountE          = 0;

        $arExclui =  Sessao::read('arValoresExcluidos');
        $inCountE = count($arExclui);

        foreach ($arValores as $key => $value) {
            if (($key+1) != $_REQUEST['id']) {
                $arTEMP[$inCount]['id'                ] = $inCount + 1;
                $arTEMP[$inCount]['inCodItem'         ] = $value[ "inCodItem"         ];
                $arTEMP[$inCount]['stNomItem'         ] = $value[ "stNomItem"         ];
                $arTEMP[$inCount]['inCodCentroCusto'  ] = $value[ "inCodCentroCusto"  ];
                $arTEMP[$inCount]['stNomUnidade'      ] = $value[ "stNomUnidade"      ];
                $arTEMP[$inCount]['stNomCentroCusto'  ] = $value[ "stNomCentroCusto"  ];
                $arTEMP[$inCount]['stComplemento'     ] = $value[ "stComplemento"     ];
                $arTEMP[$inCount]['nuQuantidade'      ] = $value[ "nuQuantidade"      ];
                $arTEMP[$inCount]['nuVlTotal'         ] = $value[ "nuVlTotal"         ];
                $arTEMP[$inCount]['inCodDespesa'      ] = $value[ "inCodDespesa"      ];
                $arTEMP[$inCount]['inCodEstrutural'   ] = $value[ "inCodEstrutural"   ];
                $arTEMP[$inCount]['nuVlTotalReservado'] = $value[ "nuVlTotalReservado"];
                $arTEMP[$inCount]['stTitle'		      ] = $value[ "stTitle" 		  ];
                $inCount++;
            } else {
                $arSaldoDotacoes = Sessao::read('arSaldoDotacoes');
                $arSaldoDotacoes['saldo'][$value[ "inCodDespesa" ]] += str_replace(',','.',str_replace('.','',$value[ "nuVlTotal" ]));
                echo "alertaAviso('".$arSaldoDotacoes[$value[ "inCodDespesa" ]]."','form','erro','".Sessao::getId()."');";
                Sessao::write('arSaldoDotacoes', $arSaldoDotacoes);

                $arExclui[$inCountE]['id'                ] = $inCountE + 1;
                $arExclui[$inCountE]['inCodItem'         ] = $value[ "inCodItem"         ];
                $arExclui[$inCountE]['stNomItem'         ] = $value[ "stNomItem"         ];
                $arExclui[$inCountE]['inCodCentroCusto'  ] = $value[ "inCodCentroCusto"  ];
                $arExclui[$inCountE]['stNomUnidade'      ] = $value[ "stNomUnidade"      ];
                $arExclui[$inCountE]['stNomCentroCusto'  ] = $value[ "stNomCentroCusto"  ];
                $arExclui[$inCountE]['stComplemento'     ] = $value[ "stComplemento"     ];
                $arExclui[$inCountE]['nuQuantidade'      ] = $value[ "nuQuantidade"      ];
                $arExclui[$inCountE]['nuVlTotal'         ] = $value[ "nuVlTotal"         ];
                $arExclui[$inCountE]['inCodDespesa'      ] = $value[ "inCodDespesa"      ];
                $arExclui[$inCountE]['inCodEstrutural'   ] = $value[ "inCodEstrutural"   ];
                $arExclui[$inCountE]['nuVlTotalReservado'] = $value[ "nuVlTotalReservado"];
                $arExclui[$inCountE]['stTitle'		     ] = $value[ "stTitle" 		  ];
            }
        }
        Sessao::write('arValoresExcluidos', $arExclui);
        Sessao::write('arValores', $arTEMP);

        echo montaListaDotacoes($arTEMP);

    break;

    # Recurso que permite importar os itens de outra solicitação.
    case 'incluirItensOutraSolicitacao':
        $rsRecordSetItem           = new RecordSet;
        $obTComprasSolicitacaoItem = new TComprasSolicitacaoItem();
        $js                        = "";
        $obTComprasSolicitacaoItem->setDado( 'exercicio'      , $_REQUEST['stExercicioSolicitacao']   );
        $obTComprasSolicitacaoItem->setDado( 'cod_entidade'   , $_REQUEST['inCodEntidadeSolicitacao'] );
        $obTComprasSolicitacaoItem->setDado( 'cod_solicitacao', $_REQUEST['inCodSolicitacao']         );

        $stFiltro = " WHERE solicitacao_item.cod_entidade = ".$obTComprasSolicitacaoItem->getDado('cod_entidade')."      \n";
        $stFiltro.= "   AND solicitacao_item.exercicio    = '".$obTComprasSolicitacaoItem->getDado('exercicio'   )."'    \n";
        $stFiltro.= "   AND solicitacao_item.cod_solicitacao=".$obTComprasSolicitacaoItem->getDado('cod_solicitacao')."  \n";
        $stFiltro.= "   AND solicitacao_item.cod_item     = catalogo_item.cod_item                                       \n";
        $stFiltro.= "   AND solicitacao_item.cod_centro   = centro_custo.cod_centro                                      \n";
        $stFiltro.= "   AND catalogo_item.cod_unidade     = unidade_medida.cod_unidade                                   \n";
        $stFiltro.= "   AND catalogo_item.cod_grandeza    = unidade_medida.cod_grandeza                                  \n";

        $obTComprasSolicitacaoItem->recuperaRelacionamentoItem( $rsRecordSet, $stFiltro );

        $obTConfiguracao = new TAdministracaoConfiguracao();
        $obTConfiguracao->setDado('exercicio', Sessao::getExercicio());
        $obTConfiguracao->pegaConfiguracao( $boReservaRigida,'reserva_rigida' );
        $boReservaRigida      = ($boReservaRigida == 'true'     ) ? true : false;

        if (!($rsRecordSet->EOF())) {
            $arValores = Sessao::read('arValores');
            while (!($rsRecordSet->EOF())) {
                if ( !isset($arSaldoDotacoes['saldo'][$rsRecordSet->getCampo('cod_despesa')]) ) {
                $arSaldoDotacoes['saldo'][$rsRecordSet->getCampo('cod_despesa')] = $_REQUEST['nuHdnSaldoDotacao'];
                }

                $inCodDespesa = $rsRecordSet->getCampo('cod_despesa');

                if (is_numeric($inCodDespesa) && isset($inCodDespesa)) {
                    $obTEmpenhoPreEmpenho = new TEmpenhoPreEmpenho;
                    $obTEmpenhoPreEmpenho->setDado('exercicio'   , Sessao::getExercicio());
                    $obTEmpenhoPreEmpenho->setDado('cod_despesa' , $rsRecordSet->getCampo('cod_despesa'));
                    $obTEmpenhoPreEmpenho->recuperaSaldoAnterior($rsSaldoAnterior);

                    $nuSaldoDotacao = $rsSaldoAnterior->getCampo('saldo_anterior');
                }

                $boDotacaoSaldo = true;

                if (($nuSaldoDotacao < $rsRecordSet->getCampo('vl_reserva')) AND ($rsRecordSet->getCampo('cod_despesa') != '')) {
                    $boDotacaoSaldo = false;
                }

                if (!($boDotacaoRepetida)) {
                    $inCount = sizeof($arValores);
                    $arValores[$inCount]['id'              ] = $inCount + 1;
                    $arValores[$inCount]['inCodItem'       ] = $rsRecordSet->getCampo('cod_item'      );
                    $arValores[$inCount]['stNomItem'       ] = $rsRecordSet->getCampo('nomitem'       );
                    $arValores[$inCount]['inCodCentroCusto'] = $rsRecordSet->getCampo('cod_centro'    );
                    $arValores[$inCount]['stNomUnidade'    ] = $rsRecordSet->getCampo('nom_unidade'   );
                    $arValores[$inCount]['stNomCentroCusto'] = $rsRecordSet->getCampo('nomcentrocusto');
                    $arValores[$inCount]['stComplemento'   ] = $rsRecordSet->getCampo('complemento'   );
                    if ($rsRecordSet->getCampo('quantidade_dotacao')) {
                        $arValores[$inCount]['nuQuantidade'    ] = number_format($rsRecordSet->getCampo('quantidade_dotacao'),4,',','.');
                        $arValores[$inCount]['nuVlTotal'       ] = number_format($rsRecordSet->getCampo('vl_total_dotacao'),2,',','.');
                    } else {
                        $arValores[$inCount]['nuQuantidade'    ] = number_format($rsRecordSet->getCampo('quantidade'),4,',','.');
                        $arValores[$inCount]['nuVlTotal'       ] = number_format($rsRecordSet->getCampo('vl_total'),2,',','.');
                    }

                    if ($_REQUEST['inCodEntidade'] == $_REQUEST['inCodEntidadeSolicitacao']
                       and $_REQUEST['stExercicioSolicitacao'] == Sessao::getExercicio() ){
                        $arValores[$inCount]['inCodDespesa'    ] = $rsRecordSet->getCampo('cod_despesa'   );
                        $arValores[$inCount]['inNomDespesa'    ] = $rsRecordSet->getCampo('nomdespesa'   );
                        $arValores[$inCount]['inCodEstrutural' ] = $rsRecordSet->getCampo('cod_conta');
                        $arValores[$inCount]['nuVlTotalReservado'] = number_format($rsRecordSet->getCampo('vl_reserva'),2,',','.');
                    } elseif ($_REQUEST['inCodDespesaPadrao']) {
                       //incluir o padrão se existir
                        $arValores[$inCount]['inCodDespesa'    ]   = $_REQUEST['inCodDespesaPadrao'];
                        $arValores[$inCount]['inCodEstrutural' ]   = $_REQUEST['stCodClassificacaoPadrao'];
                        $arValores[$inCount]['nuVlTotalReservado'] = number_format( $_REQUEST['nuSaldoDotacaoPadrao'],2,',','.');

                    }

                    $rsDotacao = new RecordSet;
                    if ($arValores[$inCount]['inCodDespesa'] != null) {
                        $obTOrcamentoDespesa = new TOrcamentoDespesa;
                        $stFiltro  = " AND D.cod_despesa = ".$arValores[$inCount]['inCodDespesa'];
                        $stFiltro .= " AND D.exercicio   = '".Sessao::getExercicio()."' ";
                        $obTOrcamentoDespesa->recuperaListaDotacao( $rsDotacao, $stFiltro );
                        unset( $obTOrcamentoDespesa );
                    }

                    if ( $rsDotacao->Eof() ) {
                        $arValores[$inCount]['stTitle'] = '';
                    } else {
                        $arValores[$inCount]['stTitle'] = $arValores[$inCount]['inCodDespesa'].' - '.$rsDotacao->getCampo('descricao').' - '.$rsDotacao->getCampo('cod_estrutural');
                    }

                    unset( $rsDotacao );
                }

                if (!$boDotacaoSaldo) {
                    echo "alertaAviso('Existem Itens com Valor da Reserva superior ao Saldo da Dotação (Não foram efetuadas reservas para estes itens).','form','erro','".Sessao::getId()."');";
                }

                $rsRecordSet->Proximo();
            }

            if (isset($stMensagem)) {
                $js="alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');";
            }

            Sessao::write('arValores' , $arValores);
            echo $js.montaListaDotacoes($arValores);
        } else {
            echo "alertaAviso('Nenhum item consta nessa solicitacao.','form','erro','".Sessao::getId()."');";
            echo 'document.getElementById("stNomSolicitacao").innerHTML = "&nbsp;";';
        }

    break;

    # Realiza a inclusão dos itens na listagem da solicitação.
    # É possível incluir o mesmo item, mesmo centro de custo, mesma dotação
    # porém com desdobramentos diferentes, sendo o último nível de repetição.
    case 'incluirListaItens':

        # Busca algumas configurações.
        $obTConfiguracao = new TAdministracaoConfiguracao;
        $obTConfiguracao->setDado('exercicio', Sessao::getExercicio());
        $obTConfiguracao->pegaConfiguracao($boReservaRigida      , 'reserva_rigida');
        $obTConfiguracao->pegaConfiguracao($boFormaExecucao      , 'forma_execucao_orcamento');
        $obTConfiguracao->pegaConfiguracao($boDotacaoObrigatoria , 'dotacao_obrigatoria_solicitacao');

        $boFormaExecucao 	  = ($boFormaExecucao == 1) 		  ? true : false;
        $boReservaRigida 	  = ($boReservaRigida == 'true') 	  ? true : false;
        $boDotacaoObrigatoria = ($boDotacaoObrigatoria == 'true') ? true : false;

        # Inicializa TRUE.
        $boValorReservado  = $boDotacaoSaldo = $boDotacaoDesdobramento = true;
        # Inicializa FALSE.
        $boDotacaoRepetida = $boErro = $boItemRepetido = $boCentroCustoRepetido = false;

        $inCodItem        = $_REQUEST['inCodItem'];
        $inCodCentroCusto = $_REQUEST['inCodCentroCusto'];
        $inCodDespesa     = $_REQUEST['inCodDespesa'];
        $inCodConta       = $_REQUEST['stCodClassificacao'];
        $stJs 			  = "";

        $arValores = Sessao::read('arValores');

        if (is_array($arValores)) {

            $obTConfiguracao = new TAdministracaoConfiguracao();
            $obTConfiguracao->setDado('exercicio', Sessao::getExercicio());
            $obTConfiguracao->pegaConfiguracao($boFormaExecucao,'forma_execucao_orcamento');

            if (is_numeric($inCodDespesa) && isset($inCodDespesa)) {
                if ((is_numeric($inCodConta) && isset($inCodConta) && $boFormaExecucao == true) || $boFormaExecucao == false) {
                    foreach ($arValores as $key => $arDados) {
                        if (count($arValores) > 0) {
                            if (($arDados['inCodItem']        == $inCodItem) 	    &&
                                ($arDados['inCodCentroCusto'] == $inCodCentroCusto) &&
                                ($arDados['inCodDespesa'] 	  == $inCodDespesa) 	&&
                                ($arDados['inCodEstrutural']  == $inCodConta)  	    &&
                                ($_GET['alterar'] != 'true')){
                                $boDotacaoRepetida = true ;
                                break;
                            } elseif (($arDados['inCodItem']        == $inCodItem) 	      &&
                                      ($arDados['inCodCentroCusto'] == $inCodCentroCusto) &&
                                      (empty($arDados['inCodDespesa']))) {
                                # Verifica se o item já não existe na lista porém sem dotação vinculada.
                                $boItemRepetido = true;
                                break;
                            }
                        }
                    }
                } else {
                    $boErro = true;
                    $stJs .= "alertaAviso('Necessário informar o Desdobramento para a Despesa !','form','erro','".Sessao::getId()."');";
                }
            } else {
                # Validação para não permitir incluir o mesmo item com o mesmo
                # centro de custo.
                foreach ($arValores as $key => $arDados) {
                    if (($arDados['inCodItem']        == $inCodItem) 	   &&
                        ($arDados['inCodCentroCusto'] == $inCodCentroCusto) &&
                        ($_GET['alterar'] != 'true')){
                        $boCentroCustoRepetido = true ;
                        break;
                    }
                }
            }
        }

        $arSaldoDotacoes = Sessao::read('arSaldoDotacoes');

        if (!isset($arSaldoDotacoes['saldo'][$_REQUEST['inCodDespesa']])) {
            $arSaldoDotacoes['saldo'][$_REQUEST['inCodDespesa']] = $_REQUEST['nuHdnSaldoDotacao'];
            $arSaldoDotacoes['total'][$_REQUEST['inCodDespesa']] = $_REQUEST['nuHdnSaldoDotacao'];
        }

        if ($arSaldoDotacoes['saldo'][$_REQUEST['inCodDespesa']] < str_replace(',','.',str_replace('.','',$_REQUEST['nuVlTotalReservado'])) AND ($boReservaRigida) AND ($_REQUEST['inCodDespesa'] != '')) {
            $boDotacaoSaldo = false;
        }

        if ($boFormaExecucao && $boDotacaoObrigatoria && ($_REQUEST["stCodClassificacao"] == '')) {
            if (is_numeric($inCodDespesa) && isset($inCodDespesa)) {
                $boDotacaoDesdobramento = false;
            }
        }

        /**
         * Retirada a obrigatoriadade do valor do item ser > 0
         */
        if ($inCodCentroCusto == "") {
            $stJs .= "alertaAviso('O Centro de Custo deve ser informado!','form','erro','".Sessao::getId()."');";
            $boErro = true;
            break;
        }

        if (str_replace(',','.',str_replace('.','',$_REQUEST['nuVlTotal'])) <= 0.00) {
            $stJs .= "alertaAviso('O valor total dos itens deve ser maior que zero!','form','erro','".Sessao::getId()."');";
            $boErro = true;
        }

        if (str_replace(',','.',str_replace('.','',$_REQUEST['nuQuantidade'])) <= 0) {
            $stJs .= "alertaAviso('A quantidade deve ser maior que zero!','form','erro','".Sessao::getId()."');";
            $boErro = true;
        }

        if (str_replace(',','.',str_replace('.','',$_REQUEST['nuVlTotalReservado'])) > str_replace(',','.',str_replace('.','',$_REQUEST['nuVlTotal'])) ) {
            $stJs .= "alertaAviso('Valor Reservado no exercício deve ser igual ou menor ao valor total do item!','form','erro','".Sessao::getId()."');";
            $boErro = true;
        }

        if (($_REQUEST['inCodDespesa'] != '') && ($_REQUEST['nuVlTotalReservado'] == '0,00')) {
            $boValorReservado = false;
        }

        if ($boErro == false) {
            if ($boDotacaoDesdobramento) {
                if (!($boDotacaoRepetida)) {
                    if ($boCentroCustoRepetido == false) {
                        if ($boItemRepetido == false) {
                            if ($boDotacaoSaldo) {
                                    $inCount = count($arValores);
                                    $arValores[$inCount]['id'] 				   = $inCount + 1;
                                    $arValores[$inCount]['inCodItem']		   = $inCodItem;
                                    $arValores[$inCount]['stNomItem']		   = stripslashes($_REQUEST["stNomItem"]);
                                    $arValores[$inCount]['inCodCentroCusto']   = $_REQUEST["inCodCentroCusto"];
                                    $arValores[$inCount]['stNomUnidade']	   = stripslashes($_REQUEST["stNomUnidade"]);
                                    $arValores[$inCount]['stNomCentroCusto']   = stripslashes($_REQUEST["stNomCentroCusto"]);
                                    $arValores[$inCount]['stComplemento']	   = stripslashes($_REQUEST["stComplemento"]);
                                    $arValores[$inCount]['nuQuantidade']	   = $_REQUEST["nuQuantidade"];
                                    $arValores[$inCount]['nuVlTotal']		   = $_REQUEST["nuVlTotal"];
                                    $arValores[$inCount]['inCodDespesa']	   = $_REQUEST["inCodDespesa"];
                                    $arValores[$inCount]['nuVlTotalReservado'] = $_REQUEST["nuVlTotalReservado"];
                                    $arValores[$inCount]['nuVlReferencia']     = Sessao::read('vl_unitario_ultima_compra');

                                    if (is_numeric($inCodDespesa) && isset($inCodDespesa)) {

                                        if ($_REQUEST['stCodClassificacao'] == '') {
                                            $obTOrcamentoDespesa = new TOrcamentoDespesa();
                                            $stFiltro = " AND D.cod_despesa = ".$_REQUEST['inCodDespesa']." AND D.exercicio = '".Sessao::getExercicio()."' ";
                                            $obTOrcamentoDespesa->recuperaListaDotacao( $rsDotacao, $stFiltro );
                                        } else {
                                            $obTOrcamentoContaDespesa = new TOrcamentoContaDespesa();
                                            $stFiltro = " cod_conta = ".$_REQUEST['stCodClassificacao']." AND exercicio = '".Sessao::getExercicio()."' ";
                                            $obTOrcamentoContaDespesa->recuperaCodEstrutural( $rsDotacao, $stFiltro );
                                        }
                                        $arValores[$inCount]['inCodEstrutural'] = $rsDotacao->getCampo('cod_conta');
                                        $arValores[$inCount]['stCodEstrutural'] = $rsDotacao->getCampo('cod_estrutural');
                                        $arValores[$inCount]['stTitle'		  ] = $_REQUEST[ "inCodDespesa" ].' - '.$rsDotacao->getCampo('descricao').' - '.$rsDotacao->getCampo('cod_estrutural');
                                    }

                                    $stJs .= "LimparItensSolicitacao();";

                                    $arSaldoDotacoes['saldo'][$_REQUEST['inCodDespesa']] -= str_replace(',','.',str_replace('.','',$_REQUEST['nuVlTotalReservado']));
                                    if (($arSaldoDotacoes['saldo'][$_REQUEST['inCodDespesa']] < 0 ) AND ( $_REQUEST['inCodDespesa'] != '')) {
                                        $stJs .= "alertaAviso('Valor da Reserva é Superior ao Saldo da Dotação (Não foi efetuada reserva para este item).','form','erro','".Sessao::getId()."');";
                                    }
                            } else {
                                $stJs .= "alertaAviso('Valor da Reserva é Superior ao Saldo da Dotação (".$_REQUEST['inCodDespesa'].").','form','erro','".Sessao::getId()."');";
                            }
                        } else {
                            $stJs .= "alertaAviso('Esse item já consta na lista.','form','erro','".Sessao::getId()."');";
                        }
                    } else {
                        $stJs .= "alertaAviso('Esse item já consta na lista com o mesmo Centro de Custo.','form','erro','".Sessao::getId()."');";
                    }
                } else {
                  $stJs .= "alertaAviso('Este item já consta na lista com o mesmo Centro de Custo, Dotação e Desdobramento.','form','erro','".Sessao::getId()."');";
                }
            } else {
                $stJs .= "alertaAviso('É necessário informar um desdobramento.','form','erro','".Sessao::getId()."');";
            }
        }

        Sessao::write('arValores'       , $arValores);
        Sessao::write('arSaldoDotacoes' , $arSaldoDotacoes);
        if ($boErro == false) {
            $stJs .= montaListaDotacoes($arValores);
        }
    break;

    case "BuscaComplemento":
        $stJs .= "jq('#stComplemento').attr('readonly', '');";
        $arValores 	  = Sessao::read('arValores');
        $arValoresAux = $arValores;

        if (is_array($arValores)) {
            $count=1;
            foreach ($arValores as $valor) {
                foreach ($arValoresAux as $valorAux) {
                    if ($_REQUEST['inCodItem'] == $valorAux['inCodItem'] && $_REQUEST['inCodCentroCusto'] == $valorAux['inCodCentroCusto']) {
                        if ($valorAux['id'] == ($count)) {
                            if ($valor['stComplemento'] == '') {
                                $stJs .= "jQuery('#stComplemento').val('');";
                            } else {
                                $stComplemento = str_replace( "\n", " ", $valor['stComplemento']);
                                $stJs .= "jQuery('#stComplemento').val('".$stComplemento."');";
                            }
                            $stJs .= "jq('#stComplemento').attr('readonly', 'readonly');";
                        }
                    }
                }
                $count++;
            }
        }
    break;

    case "saldoEstoque":

        $inCodItem 		  = $_REQUEST['inCodItem'];
        $inCodCentroCusto = $_REQUEST['inCodCentroCusto'];

        if ((is_numeric($inCodItem) && isset($inCodItem)) &&
            (is_numeric($inCodCentroCusto) && isset($inCodCentroCusto))){

            $obEstoqueItem = new RAlmoxarifadoEstoqueItem();
            $obEstoqueItem->obRCentroDeCustos->setCodigo($inCodCentroCusto);
            $obEstoqueItem->obRCatalogoItem->setCodigo($inCodItem);

            $obEstoqueItem->retornaSaldoEstoque($inSaldoEstoque);
                                      //$arTmp[$inContador]['saldo_formatado'] = number_format(str_replace('.',',',$rsRequisicaoItens->getCampo('saldo_estoque')),4,',','.');
            $inSaldoEstoque = (is_numeric($inSaldoEstoque)) ? $inSaldoEstoque : '0.00';
            $stJs .= "jQuery('#lblSaldoEstoque').html('".number_format($inSaldoEstoque,4,',','.')."'); ";
        } else {
            $stJs .= "jQuery('#inCodCentroCusto').val('');";
            $stJs .= "jQuery('#lblSaldoEstoque').html('&nbsp;');";
            $stJs .= "setTimeout(\"document.getElementById('stNomCentroCusto').innerHTML = '&nbsp;'\", 500);";
        }
    break;

    case "valorUnitarioUltimaCompra":
        if ($_REQUEST['inCodItem']) {

            $obTAlmoxarifadoCatalogoItem = new TAlmoxarifadoCatalogoItem();

            $obTAlmoxarifadoCatalogoItem->setDado('cod_item'  , $_REQUEST['inCodItem']);
            $obTAlmoxarifadoCatalogoItem->setDado('exercicio' , Sessao::getExercicio());
            $obTAlmoxarifadoCatalogoItem->recuperaValorItemUltimaCompra($rsRecordSetItem);

            $rsRecordSetItem->addFormatacao('vl_unitario_ultima_compra', 'NUMERIC_BR');

            $vlUnitario = $rsRecordSetItem->getCampo('vl_unitario_ultima_compra');
            Sessao::write('vl_unitario_ultima_compra', $vlUnitario);

            //cria um label para demonstrar o valor de referência da última compra
            $obLblValorReferenciaUltimaCompra = new Label;
            $obLblValorReferenciaUltimaCompra->setRotulo ( 'Valor da Última Compra' );
            $obLblValorReferenciaUltimaCompra->setTitle  ( 'Valor Unitário de Referência da Última Compra.' );
            $obLblValorReferenciaUltimaCompra->setValue  ( $vlUnitario );

            $obFormulario = new Formulario;
            $obFormulario->addComponente( $obLblValorReferenciaUltimaCompra );
            $obFormulario->montaInnerHTML();
            $stHtml .= $obFormulario->getHTML();

            $stHtml = str_replace( "\n", "", $stHtml);
            $stHtml = str_replace( "  ", "", $stHtml);
            $stHtml = str_replace( "'" , "\\'", $stHtml);

            $stJs .= "jQuery('#spnVlrReferencia').html('".$stHtml."'); ";
            $stJs .= "jQuery('#nuVlUnitario').val('".$vlUnitario."');";

            $vlQuantidade = str_replace( ".", "", $_REQUEST["nuQuantidade"] );
            $vlQuantidade = str_replace( ",", ".", $vlQuantidade );

            $vlTotal = number_format( $vlQuantidade * $vlUnitario, 2, ",", "." );
            $stJs .= "jQuery('#nuVlTotal').val('".$vlUnitario."');";
        }

    break;

    case "deletarListaItens":
        Sessao::remove('arValores');
    break;

    case "verificaEntidadeDotacao":
        if ($_REQUEST['inCodEntidade'] == "") {
            $obTOrcamentoDespesa = new TOrcamentoDespesa();
            $stFiltro = " WHERE cod_despesa = ".$_REQUEST['inCodDespesaPadrao']." AND exercicio = '".Sessao::getExercicio()."' ";
            $obTOrcamentoDespesa->recuperaTodos( $rsDados, $stFiltro );

            $stJs .= "jQuery('#inCodEntidade').focus();";
            $stJs .= "jQuery('#inCodEntidade').val('".$rsDados->getCampo("cod_entidade") ."');";
            $stJs .= "jQuery('#stCodClassificacaoPadrao').focus();";
        }
    break;

    case "montaDotacao" :
        if ((($_REQUEST['inCodEntidade'] != "") AND ($_REQUEST['inCodCentroCusto'] != "")) OR ($_REQUEST['alterar'])) {

            //Define o objeto de configuracao
            $obTConfiguracao = new TAdministracaoConfiguracao();
            $obTConfiguracao->setDado( "parametro" ,"dotacao_obrigatoria_solicitacao" );
            $obTConfiguracao->setDado( "exercicio" , Sessao::getExercicio()                );
            $obTConfiguracao->recuperaPorChave( $rsConfiguracao );

            $obHdnConfiguracao = new Hidden;
            $obHdnConfiguracao->setName  ( "boConfiguracao"                  );
            $obHdnConfiguracao->setValue ( $rsConfiguracao->getCampo("valor") );

            $obMontaDotacao = new IMontaDotacaoDesdobramento();

            if ($rsConfiguracao->getCampo("valor") == 'true') {
                $obMontaDotacao->obBscDespesa->setRotulo("*Dotação Orçamentária");
                $obMontaDotacao->obCmbClassificacao->setNull(false);
            }

            $obMontaDotacao->setMostraSintetico( false );

            $obValorTotal = new ValorTotal();
            $obValorTotal->setValue ( $_REQUEST["nuVlTotal"] );
            $obValorTotal->setNull  ( true                           );
            $obValorTotal->setId    ( "inVlTotal"                    );
            $obValorTotal->setSize  ( 10                             );
            $obValorTotal->setRotulo( "Valor a ser Reservado" );
            $obValorTotal->setDisabled( true                         );
            $obValorTotal->setName  ( "nuVlTotalReservado"           );

            $obHdnValorTotal = new Hidden();
            $obHdnValorTotal->setId( 'hdnValorTotalReservado' );
            $obHdnValorTotal->setName( 'hdnValorTotalReservado' );

            $obForm = new Form;
            $obForm->setAction ( $pgProc  );
            $obForm->setTarget ( "oculto" );

            $obFormulario = new Formulario();
            $obFormulario->addForm( $obForm );
            $obMontaDotacao->geraFormulario($obFormulario);
            $obFormulario->addComponente( $obValorTotal );
            $obFormulario->addHidden( $obHdnValorTotal );
            $obFormulario->montaInnerHTML();
            $stHTML = $obFormulario->getHTML();

            $js.= "d.getElementById('spnDotacao').innerHTML = '".$stHTML."';";
            if ($_REQUEST['alterar'] != 'true') {
                $js.= "montaParametrosGET( 'preencheDotacaoPadrao');";
            }

            echo $js;
        } else {
            echo "d.getElementById('spnDotacao').innerHTML = '';";
        }

        break;

    case 'preencheDotacaoPadrao':
        if ($_REQUEST['inCodDespesaPadrao'] != '') {
            if ($_REQUEST['stCodClassificacaoPadrao'] == '') {
                $obTOrcamentoDespesa = new TOrcamentoDespesa();
                $stFiltro = " AND D.cod_despesa = ".$_REQUEST['inCodDespesaPadrao']." AND D.exercicio = '".Sessao::getExercicio()."' ";
                $obTOrcamentoDespesa->recuperaListaDotacao( $rsDotacao, $stFiltro );
            } else {
                $obTOrcamentoContaDespesa = new TOrcamentoContaDespesa();
                $stFiltro = " cod_conta = ".$_REQUEST['stCodClassificacaoPadrao']." AND exercicio = '".Sessao::getExercicio()."' ";
                $obTOrcamentoContaDespesa->recuperaCodEstrutural( $rsDotacao, $stFiltro );
            }

            $stJs .= "jQuery('#inCodDespesa').val('".$_REQUEST['inCodDespesaPadrao']."');";
            $stJs .= "jQuery('#HdnCodClassificacao').val('".$_REQUEST['stCodClassificacaoPadrao']."');";
            $stJs .= "var stTarget   = document.frm.target;";
            $stJs .= "var stAction   = document.frm.action;";
            $stJs .= "f.stCtrl.value = 'buscaDespesaDiverso';";
            $stJs .= "d.getElementById('stNomDespesa').innerHTML   = '&nbsp;';";
            $stJs .= "d.getElementById('nuSaldoDotacao').innerHTML = '&nbsp;';";
            $stJs .= "f.target ='oculto';";
            $stJs .= "f.action ='../../instancias/processamento/OCIMontaDotacaoDesdobramento.php?".Sessao::getId()."&codClassificacao=".$rsDotacao->getCampo('cod_conta')."';";
            $stJs .= "f.submit();";
            $stJs .= "f.action = '".$pgOcul."?".Sessao::getId()."';";
            $stJs .= "f.action = stAction;";
            $stJs .= "f.target = stTarget;";

            $stJs .= "jQuery('#nuVlUnitario').focus();";
        }
    break;

    case 'calculaValorAnular':

        $inId = explode('_',$_REQUEST['id']);
        $arValores = Sessao::read('arValores');

        $inVlTotal   = str_replace(',','.',str_replace('.','',$arValores[$inId[1]-1]['nuVlTotalSolicitada']));
        $inQtdeTotal = str_replace(',','.',str_replace('.','',$arValores[$inId[1]-1]['nuQtTotalSolicitada']));

        if ($inQtdeTotal > 0) {
            $inVlUnit = $inVlTotal / $inQtdeTotal;
        } else {
            $inVlUnit = 0.00;
        }

        $inVlAnular = str_replace(',','.',str_replace('.','',$_REQUEST['valorAnular']));

        if ($inVlAnular <= $inQtdeTotal) {
            $stJs .= "d.getElementById('nuVlTotalAnulada_".$inId[1]."').value='".number_format($inVlUnit*$inVlAnular,2,',','.')."';";
        } else {
            $stJs .= "d.getElementById('nuVlTotalAnulada_".$inId[1]."').value='0,00';";
            $stJs .= "d.getElementById('nuQtTotalAnulada_".$inId[1]."').value='0,0000';";
            $stJs .= "alertaAviso('A Quantidade a ser anulada deve ser menor ou igual a quantidade pendente!','form','erro','".Sessao::getId()."');";
        }
    break;

    case 'preencheSpnItensOutraSolicitacao':
        $stJs = preencheSpnItensOutraSolicitacao();
    break;

    case 'limparSpnItensOutraSolicitacao':
        $stJs = "jQuery('#spnItensOutraSolicitacao').html('');";
    break;

    case 'recuperaDataContabil':
        if (!empty($_REQUEST['inCodEntidade'])) {

            $obTEmpenhoEmpenho = new TEmpenhoEmpenho;

            $stFiltro  = " AND empenho.cod_entidade = ".$_REQUEST['inCodEntidade']." \n";
            $stFiltro .= " AND empenho.exercicio = '".Sessao::getExercicio()."' \n";
            $stOrdem   = " ORDER BY empenho.dt_empenho DESC LIMIT 1 \n";

            $obTEmpenhoEmpenho->recuperaUltimaDataEmpenho( $rsRecordSet,$stFiltro,$stOrdem );

            if ($dataUltimoEmpenho != "") {
                $dataUltimoEmpenho = SistemaLegado::dataToBr($rsRecordSet->getCampo('dt_empenho'));
            }

            /*
                Rotina que serve para preencher a data da compra direta com
                a última data do lançamento contábil.
            */
            $obREmpenhoAutorizacaoEmpenho = new REmpenhoAutorizacaoEmpenho;

            $obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade']);
            $obREmpenhoAutorizacaoEmpenho->setExercicio( Sessao::getExercicio() );
            $obErro = $obREmpenhoAutorizacaoEmpenho->listarMaiorData( $rsMaiorData );

            if (($rsMaiorData->getCampo("data_autorizacao") !="" )) {
                $stDtAutorizacao = $rsMaiorData->getCampo( "data_autorizacao" );
                $stExercicioDtAutorizacao = substr($stDtAutorizacao, 6, 4);
            } elseif (($dataUltimoEmpenho !="")) {
                $stDtAutorizacao = $dataUltimoEmpenho;
                $stExercicioDtAutorizacao = substr($dataUltimoEmpenho, 6, 4);
            } else {
                $stDtAutorizacao = "01/01/".Sessao::getExercicio();
                $stExercicioDtAutorizacao = Sessao::getExercicio();
            }

            if ($stDtAutorizacao) {
                $stJs .= "if (jQuery('#stDtSolicitacao')) { jQuery('#stDtSolicitacao').val(''); }";
                $stJs .= "if (jQuery('#stDtSolicitacao')) { jQuery('#stDtSolicitacao').val('".$stDtAutorizacao."'); }";
                $stJs .= "jQuery('#HdnDtSolicitacao').val('".$stDtAutorizacao."');";
            } else {
                $stJs .= "if (jQuery('#stDtSolicitacao')) { jQuery('#stDtSolicitacao').val(''); }";
                $stJs .= "jQuery('#HdnDtSolicitacao').val('');";
            }
        }
    break;
}

if (!empty($stJs)) {
    echo $stJs;
}

?>
