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
    * Página Oculta de busca do Evento
    * Data de Criação   : 01/06/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Andre Almeida

    * @ignore
    $Revision: 30727 $
    $Name$
    $Author: souzadl $
    $Date: 2007-02-01 08:13:47 -0200 (Qui, 01 Fev 2007) $

    * Caso de uso: uc-04.04.14

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

function preencheValorQuantidade($stFixado, $nuValorQuantidade, $boLimiteCalculo)
{
    include_once( CAM_GRH_FOL_COMPONENTES."IBscEvento.class.php"      );
    $obIBscEvento = Sessao::read('IBscEvento');
    $boInformaValorQuantidade    = $obIBscEvento->getInformarValorQuantidade();
    if ( $obIBscEvento->getSugerirValorQuantidade() ) {
        if ($stFixado == 'V') {
            $obIBscEvento->obTxtValor->setValue( $nuValorQuantidade );
        } elseif ($stFixado == 'Q') {
            $obIBscEvento->obTxtQuantidade->setValue( $nuValorQuantidade );
        }
    }
    $obFormulario = new Formulario;
    if ($boInformaValorQuantidade) {
        if ($stFixado != 'Q') {
            $obFormulario->addComponente( $obIBscEvento->obTxtValor );
            $jsFocus = "d.getElementById('nuValorEvento').focus();\n";
        }
        $obFormulario->addComponente( $obIBscEvento->obTxtQuantidade );
        if (!$jsFocus) {
            $jsFocus = "d.getElementById('nuQuantidadeEvento').focus();\n";
        }
    }
    if ($boLimiteCalculo == "t") {
        $obFormulario->addComponente( $obIBscEvento->obTxtQuantidadeParcelas );
        $obFormulario->addComponente($obIBscEvento->obLblMesAno);
    }
    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();

    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);

    $stHtml = $obFormulario->getHTML();

    $stJs .= "d.frm.stHdnEvalIBscEvento.value = '".$stEval."'; \n";
    $stJs .= "d.getElementById('spnDadosEvento').innerHTML = '".$stHtml."';";
    $stJs .= $jsFocus;

    return $stJs;
}

function preencheDescEvento()
{
    include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEvento.class.php" );
    include_once( CAM_GRH_FOL_COMPONENTES."IBscEvento.class.php"        );
    $obIBscEvento                = Sessao::read('IBscEvento');
    $inCodigoEvento              = $_GET['inCodigoEvento'];
    $boInformaValorQuantidade    = $obIBscEvento->getInformarValorQuantidade();
    $boInformaQuantidadeParcelas = $obIBscEvento->getInformarQuantidadeParcelas();
    $stNaturezasAceitas          = $obIBscEvento->getNaturezasAceitas();
    $stEventoSistema             = "";
    if ( $obIBscEvento->getEventoSistema() ) {
        $stEventoSistema = "true";
    }
    if ( $obIBscEvento->getEventoSistema() === false ) {
        $stEventoSistema = "false";
    }
    if ($_GET['boPopUp']) {
        $stJs = "d = window.opener.parent.frames['telaPrincipal'].document; \n";
    }

    $stJs .= "d.getElementById('stEvento').innerHTML = '&nbsp;';\n";
    if ($inCodigoEvento) {
        $obRFolhaPagamentoEvento = new RFolhaPagamentoEvento;
        $obRFolhaPagamentoEvento->setCodigo( $inCodigoEvento );
        $arNaturezasAceitas = explode( "-", $stNaturezasAceitas );
        for ( $i=0; $i<count($arNaturezasAceitas); $i++ ) {
            $obRFolhaPagamentoEvento->setNaturezas( $arNaturezasAceitas[$i] );
        }
        $obRFolhaPagamentoEvento->setEventoSistema( $stEventoSistema );
        $obRFolhaPagamentoEvento->listarEvento( $rsEvento );
        $rsEvento->addFormatacao('valor_quantidade','NUMERIC_BR');
        if ( $rsEvento->getNumLinhas() > 0 ) {
            $stJs .= "d.getElementById('stEvento').innerHTML = '".$rsEvento->getCampo('descricao')."';\n";
            $stJs .= "d.frm.hdnDescEvento.value = '".$rsEvento->getCampo('descricao')."';\n";
            $stJs .= "d.frm.HdninCodigoEvento.value = '".$rsEvento->getCampo('cod_evento')."';\n";
            $stJs .= "d.frm.stHdnFixado.value = '".$rsEvento->getCampo('fixado')."';\n";
            $stJs .= "d.frm.stHdnApresentaParcela.value = '".$rsEvento->getCampo('apresenta_parcela')."';\n";
            $stJs .= "d.getElementById('stTextoComplementar').innerHTML = '".$rsEvento->getCampo('observacao')."';\n";
            if ($boInformaValorQuantidade || $boInformaQuantidadeParcelas) {
                $stJs .= preencheValorQuantidade( $rsEvento->getCampo("fixado"), $rsEvento->getCampo("valor_quantidade"), $rsEvento->getCampo("limite_calculo") );
            }
        } else {
            $stJs .= "d.getElementById('inCodigoEvento').value = '';\n";
            $stJs .= "d.getElementById('inCodigoEvento').focus();\n";
            $stJs .= "d.getElementById('spnDadosEvento').innerHTML = '';\n";
            $stJs .= "d.frm.hdnDescEvento.value = '';\n";
            $stJs .= "d.getElementById('stTextoComplementar').innerHTML = '&nbsp;';\n";
            $stJs .= "alertaAviso('Código de evento inválido. (".$inCodigoEvento.") ','form','erro','".Sessao::getId()."');\n";
        }
    } else {
        $stJs .= "d.getElementById('spnDadosEvento').innerHTML = '';\n";
    }

    return $stJs;
}

function validarQuantidade()
{
    $nuQuantidadeEvento = str_replace(".","",$_GET['nuQuantidadeEvento']);
    $nuQuantidadeEvento = str_replace(",",".",$nuQuantidadeEvento);
    $nuValidacao = 99999999.99;
    if ($nuQuantidadeEvento > $nuValidacao) {
        $stValidacao = number_format($nuValidacao,2,",",".");
        $stJs  = "f.nuQuantidadeEvento.value = '';\n";
        $stJs .= "d.getElementById('nuQuantidadeEvento').focus();\n";
        $stJs .= "alertaAviso('campo Quantidade inválido!(".$_GET['nuQuantidadeEvento'].")','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function validarValor()
{
    $nuValorEvento = str_replace(".","",$_GET['nuValorEvento']);
    $nuValorEvento = str_replace(",",".",$nuValorEvento);
    $nuValidacao = 99999999.99;
    if ($nuValorEvento > $nuValidacao) {
        $stValidacao = number_format($nuValidacao,2,",",".");
        $stJs  = "f.nuValorEvento.value = '';\n";
        $stJs .= "d.getElementById('nuValorEvento').focus();\n";
        $stJs .= "alertaAviso('campo Valor inválido!(".$_GET['nuValorEvento'].").','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function preencherPrevisaoMesAno()
{
    include_once(CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoContratoServidor.class.php");
    include_once(CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php");
    $obRFolhaPagamentoPeriodoContratoServidor = new RFolhaPagamentoPeriodoContratoServidor( new RFolhaPagamentoPeriodoMovimentacao );
    $obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoPeriodoMovimentacao->listarUltimaMovimentacao($rsUltimaMovimentacao);
    $arDataFinal        = explode("/",$rsUltimaMovimentacao->getCampo('dt_final'));
    $inResto            = (($_GET['nuQuantidadeParcelasEvento'])%12);
    $inInt              = intval((($_GET['nuQuantidadeParcelasEvento'])/12));
    if ($inResto) {
        $inAno = $arDataFinal[2] + $inInt;
    } else {
        $inAno = $arDataFinal[2] + $inInt-1;
    }
    $inMes = ( $inResto == 0 ) ? 12 : $inResto;
    $inMes = ( strlen($inMes) == 1 ) ? '0'.$inMes : $inMes;
    $stMesAno = $inMes ."/". $inAno;
    $stJs .= "d.getElementById('stMesAno').innerHTML = '".$stMesAno."'  \n";

    return $stJs;
}

switch ($_GET['stCtrl']) {
    case "preencheDescEvento":
        $js = preencheDescEvento();
    break;
    case "validarQuantidade":
        $js = validarQuantidade();
    break;
    case "validarValor":
        $js = validarValor();
    break;
    case "preencherPrevisaoMesAno":
        $js = preencherPrevisaoMesAno();
        break;
}

if ($js) {
    echo $js;
}
?>
