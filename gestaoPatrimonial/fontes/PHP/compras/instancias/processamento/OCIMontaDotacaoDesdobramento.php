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

 $Id: OCIMontaDotacaoDesdobramento.php 61949 2015-03-18 14:06:06Z evandro $

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GF_EMP_NEGOCIO."REmpenhoAutorizacaoEmpenho.class.php";
include_once CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php";
include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoPreEmpenho.class.php";

$stCtrl        = $_REQUEST['stCtrl'];
$inCodEntidade = $_REQUEST["inCodEntidade"];
$obREmpenhoAutorizacaoEmpenho = new REmpenhoPreEmpenho;
$obREmpenhoEmpenho            = new REmpenhoEmpenho;
$obREmpenhoAutorizacaoEmpenho->setExercicio( Sessao::getExercicio() );
$obREmpenhoEmpenho->setExercicio( Sessao::getExercicio() );

if (isset($_REQUEST['HdnCodEntidade'])) {
    $_REQUEST['inCodEntidade'] = $_REQUEST['HdnCodEntidade'];
}

switch ($_REQUEST['stCtrl']) {

    case 'buscaDespesaDiverso':
        if ($_REQUEST['inCodDespesa'] != '') {
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa( $_REQUEST["inCodDespesa"] );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodCentroCusto( $_REQUEST["inCodCentroCusto"] );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST["inCodEntidade"] );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setExercicio( Sessao::getExercicio() );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->listarDespesaUsuario( $rsDespesa );
            if ( $rsDespesa->getNumLinhas() > -1 ) {
                $js .= 'd.getElementById("stNomDespesa").innerHTML = "'.$rsDespesa->getCampo('descricao').'";';

                $obTEmpenhoPreEmpenho = new TEmpenhoPreEmpenho();
                $obTEmpenhoPreEmpenho->setDado( 'exercicio'  ,Sessao::getExercicio() );
                $obTEmpenhoPreEmpenho->setDado( 'cod_despesa',$_REQUEST["inCodDespesa"] );
                $obTEmpenhoPreEmpenho->recuperaSaldoDotacaoCompra( $rsSaldoAnterior );

                $nuSaldoDotacao = $rsSaldoAnterior->getCampo('saldo_anterior');

                $js	.= "var saldoDotacao = '".number_format($nuSaldoDotacao,2,',','.')."';";
                $js .= "d.getElementById('nuSaldoDotacao').innerHTML=saldoDotacao;";
                $js .= "d.getElementById('nuHdnSaldoDotacao').value=".number_format($nuSaldoDotacao,2,'.','').";";

                $js .= montaComboDiverso();

            } else {
                $js .= "alertaAviso('@Dotação inválida. (".$_REQUEST["inCodDespesa"].")','form','erro','".Sessao::getId()."');";
                $js .= 'd.getElementById("stNomDespesa").innerHTML = "&nbsp;";';
                $js .= 'd.getElementById("inCodDespesa").value = "";';
                $js .= 'd.getElementById("nuSaldoDotacao").innerHTML= "&nbsp;" ;';
                $js .= "limpaSelect(f.stCodClassificacao,0); \n";
                $js .= "f.stCodClassificacao.options[0] = new Option('Selecione','', 'selected');\n";
            }
        } else {
            $js .= 'd.getElementById("stNomDespesa").innerHTML = "&nbsp;";';
            $js .= 'd.getElementById("inCodDespesa").value= "" ;';
            $js .= 'd.getElementById("nuSaldoDotacao").innerHTML= "&nbsp;" ;';
            $js .= "limpaSelect(f.stCodClassificacao,0); \n";
            $js .= "f.stCodClassificacao.options[0] = new Option('Selecione','', 'selected');\n";
        }
        SistemaLegado::executaFrameOculto($js);
        break;
}

function montaComboDiverso()
{
    global $obREmpenhoAutorizacaoEmpenho;
    $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa( $_REQUEST['inCodDespesa'] );
    $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setExercicio( Sessao::getExercicio() );
    $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->listarRelacionamentoContaDespesa( $rsConta );
    $stCodClassificacao = $rsConta->getCampo( "cod_estrutural" );
    $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascClassificacao( $stCodClassificacao );
    $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setExercicio( Sessao::getExercicio() );
    $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa( "" );
    $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->listarCodEstruturalDespesa( $rsClassificacao );
    if ( $rsClassificacao->getNumLinhas() > -1 ) {
        $inContador = 1;
        $js .= "limpaSelect(f.stCodClassificacao,0); \n";
        if ($_REQUEST['boMostraSintetico'] == 'true') {
            $js .= "f.stCodClassificacao.options[0] = new Option('".$rsClassificacao->arElementos[0]['cod_estrutural'].' - '.$rsClassificacao->arElementos[0]['descricao']."','".$rsClassificacao->arElementos[0]['cod_conta']."', 'selected');\n";
        } else {
            $js .= "f.stCodClassificacao.options[0] = new Option('Selecione','', 'selected');\n";
        }
        while ( !$rsClassificacao->eof() ) {
            $selected = "";
            $stMascaraReduzida = $rsClassificacao->getCampo("mascara_reduzida");
            if ($stMascaraReduzidaOld) {
                if ( $stMascaraReduzidaOld != substr($stMascaraReduzida,0,strlen($stMascaraReduzidaOld)) ) {
                    if ($inCodContaOld == $_REQUEST["codClassificacao"] or $inCodContaOld == $_REQUEST["stCodClassificacao"]) {
                        $selected = "selected";
                    } else {
                        $selected = "";
                    }
                    $arOptions[]['reduzido']                  = $stMascaraReduzidaOld;
                    $arOptions[count($arOptions)-1]['option'] = "'".$stCodEstruturalOld.' - '.$stDescricaoOld."','".$inCodContaOld."','".$selected."'";
                    $inContador++;
                }
            }
            $inCodContaOld        = $rsClassificacao->getCampo("cod_conta");
            $stCodEstruturalOld   = $rsClassificacao->getCampo("cod_estrutural");
            $stDescricaoOld       = $rsClassificacao->getCampo("descricao");
            $stMascaraReduzidaOld = $stMascaraReduzida;
            $stMascaraReduzida    = "";
            $rsClassificacao->proximo();
        }
        if ($stMascaraReduzidaOld) {
            if ($inCodContaOld == $_REQUEST['codClassificacao']) {
                $selected = "selected";
            } else {
                $selected = "";
            }
            $arOptions[]['reduzido'] = $stMascaraReduzidaOld;
            $arOptions[count($arOptions)-1]['option'] = "'".$stCodEstruturalOld.' - '.$stDescricaoOld."','".$inCodContaOld."','".$selected."'";

        }

        // Remove Contas Sintéticas
        if (is_array($arOptions)) {
            $count = 0;
            for ( $x=0 ; $x<count($arOptions) ; $x++ ) {
                for ( $y=0 ; $y<count($arOptions) ; $y++ ) {
                    $estruturalX = str_replace( '.', '', $arOptions[$x]['reduzido'] );
                    $estruturalY = str_replace( '.', '', $arOptions[$y]['reduzido'] );

                    if ((strpos($estruturalY,$estruturalX)!==false) && ($estruturalX !== $estruturalY) ) {
                        $count++;
                    }
                }
                if ($count>=1) {
                    unset($arOptions[$x]);
                }
                $count = 0;
            }
            $inContador = ( count($arOptions)>1 )? 1 : 0;
            asort( $arOptions );
            foreach ($arOptions as $option) {
                $js .= "if (f.inCodDespesa.value!='') {                                                            \n";
                $js .= "  f.stCodClassificacao.options[".$inContador++."] = new Option(". $option['option'] ."); \n";
                $js .= "}                                                                                        \n";
            }
        }

    } else {
        $js .= "limpaSelect(f.stCodClassificacao,0); \n";
        $js .= "f.stCodClassificacao.options[0] = new Option('Selecione','', 'selected');\n";
    }
    $js .= "LiberaFrames(true,false);";

    return $js;
}

?>
