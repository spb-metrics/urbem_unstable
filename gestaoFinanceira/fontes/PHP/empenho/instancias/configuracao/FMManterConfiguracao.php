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
    * Página de Formulário de Configuração do módulo empenho
    * Data de Criação : 05/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    $Revision: 31968 $
    $Name$
    $Author: vitor $
    $Date: 2007-07-23 11:35:09 -0300 (Seg, 23 Jul 2007) $

    * Casos de uso: uc-02.03.01, uc-02.03.04, uc-02.03.05
*/

/*
$Log$
Revision 1.14  2007/07/23 14:34:39  vitor
Bug#9669#

Revision 1.13  2007/07/03 19:45:34  luciano
Bug#9451#

Revision 1.12  2007/07/03 19:36:59  luciano
Bug#9451#

Revision 1.11  2007/07/03 15:29:16  luciano
Bug#9451#

Revision 1.10  2006/07/17 14:37:38  andre.almeida
Bug #6087#

Revision 1.9  2006/07/14 20:59:57  leandro.zis
Bug #6181#

Revision 1.8  2006/07/05 20:47:34  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once( CAM_GF_INCLUDE."validaGF.inc.php");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GF_EMP_NEGOCIO."REmpenhoConfiguracao.class.php");
include_once(CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeGeral.class.php"                        );
include_once(CAM_GF_CONT_COMPONENTES.'IPopUpContaAnalitica.class.php'                              );
include_once(CAM_GA_ADM_MAPEAMENTO.'TAdministracaoConfiguracaoEntidade.class.php');
include_once(CAM_GF_CONT_MAPEAMENTO.'TContabilidadePlanoAnalitica.class.php'                       );

$stPrograma = "ManterConfiguracao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once ( $pgOcul );

$obRegra = new REmpenhoConfiguracao;
$obRegra->consultar();
$stTipoNumeracao = $obRegra->getNumeracao();
$boAnularAutorizacaoAutomatica = $obRegra->getAnularAutorizacaoAutomatica();
if ($boAnularAutorizacaoAutomatica=="true") {
    $stAnularAutorizacaoAutomatica = "S";
} else {
    $stAnularAutorizacaoAutomatica = "N";
}
$boDataVencimento = $obRegra->getDataVencimento();
if ($boDataVencimento=="true") {
    $stDataVencimento = "S";
} else {
    $stDataVencimento = "N";
}
$boLiquidacaoAutomatica = $obRegra->getLiquidacaoAutomatica();
if ($boLiquidacaoAutomatica=="true") {
    $stLiquidacaoAutomatica = "S";
} else {
    $stLiquidacaoAutomatica = "N";
}
$boOPAutomatica = $obRegra->getOPAutomatica();
if ($boOPAutomatica=="true") {
    $stOPAutomatica = "S";
} else {
    $stOPAutomatica = "N";
}
$boOPCarne = $obRegra->getEmitirCarneOp();
if ($boOPCarne=="true") {
    $stOPCarne = "S";
} else {
    $stOPCarne = "N";
}

// Busca as contas caixa das entidades
$obTAdministracaoConfiguracaoEntidade = new TAdministracaoConfiguracaoEntidade;
$stFiltro = " WHERE parametro = 'conta_caixa' AND exercicio = '".Sessao::getExercicio()."' ";
$obTAdministracaoConfiguracaoEntidade->recuperaTodos($rsContaCaixaEntidades, $stFiltro);
Sessao::remove('arItens');
$inCount = 0;
$arItens = array();
if ( $rsContaCaixaEntidades->getNumLinhas() > 0 ) {
    $arItens = array();
    while (!$rsContaCaixaEntidades->eof()) {
        $obTContabilidadeContaAnalitica = new TContabilidadePlanoAnalitica;
        $stFiltro = " AND pa.cod_plano = ".$rsContaCaixaEntidades->getCampo('valor')." AND pa.exercicio = '".Sessao::getExercicio()."' ";
        $obTContabilidadeContaAnalitica->recuperaContaAnalitica( $rsContaAnalitica, $stFiltro );

        $arItens[$inCount]['inId'            ] = $inCount;
        $arItens[$inCount]['inCodEntidade'   ] = $rsContaCaixaEntidades->getCampo('cod_entidade');
        $arItens[$inCount]['inCodConta'      ] = $rsContaCaixaEntidades->getCampo('valor');
        $arItens[$inCount]['stNomConta'      ] = $rsContaAnalitica->getCampo('nom_conta');
        $inCount++;

        $rsContaCaixaEntidades->proximo();
    }
}
Sessao::write('arItens', $arItens);
$stAcao = $request->get('stAcao');

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obCmbTipoNumeracao = new Select;
$obCmbTipoNumeracao->setRotulo        ( "Tipo de Numeração" );
$obCmbTipoNumeracao->setName          ( "stTipoNumeracao" );
$obCmbTipoNumeracao->setTitle         ( "Informe o tipo de numeração." );
$obCmbTipoNumeracao->setStyle         ( "width: 200px");
$obCmbTipoNumeracao->addOption        ( "", "Selecione" );
$obCmbTipoNumeracao->addOption        ( "P","Por Entidade" );
$obCmbTipoNumeracao->addOption        ( "G","Global" );
$obCmbTipoNumeracao->setValue         ( $stTipoNumeracao );
$obCmbTipoNumeracao->setNull          ( false );

$obRdnAnularAutorizacaoAutomatica = new SimNao;
$obRdnAnularAutorizacaoAutomatica->setRotulo ( "Anular Autorização ao anular Empenho"   );
$obRdnAnularAutorizacaoAutomatica->setName   ( "boAnularAutorizacaoAutomatica" );
$obRdnAnularAutorizacaoAutomatica->setTitle  ( "Informe se este a anulação de um empenho também anulará a autorização do mesmo." );
$obRdnAnularAutorizacaoAutomatica->setChecked( $stAnularAutorizacaoAutomatica );
$obRdnAnularAutorizacaoAutomatica->obRadioSim->setValue  ("Sim");
$obRdnAnularAutorizacaoAutomatica->obRadioNao->setValue  ("Não");

$obRdnDataVencimento = new SimNao;
$obRdnDataVencimento->setRotulo ( "Setar Data de Vencimento da Liquidação"   );
$obRdnDataVencimento->setName   ( "boDataVencimento" );
$obRdnDataVencimento->setTitle  ( "Informe se a data de vencimento da liquidação deve vir preenchida com o último dia do ano." );
$obRdnDataVencimento->setChecked( $stDataVencimento );
$obRdnDataVencimento->obRadioSim->setValue  ("Sim");
$obRdnDataVencimento->obRadioNao->setValue  ("Não");

$obRdnLiquidacaoAutomatica = new SimNao;
$obRdnLiquidacaoAutomatica->setRotulo ( "Setar Liquidação Automática"   );
$obRdnLiquidacaoAutomatica->setName   ( "boLiquidacaoAutomatica" );
$obRdnLiquidacaoAutomatica->setTitle  ( "Informe se a Liquidação será automática." );
$obRdnLiquidacaoAutomatica->setChecked( $stLiquidacaoAutomatica );
$obRdnLiquidacaoAutomatica->obRadioSim->setValue  ("Sim");
$obRdnLiquidacaoAutomatica->obRadioNao->setValue  ("Não");

$obRdnOPAutomatica = new SimNao;
$obRdnOPAutomatica->setRotulo ( "Setar OP Automática"   );
$obRdnOPAutomatica->setName   ( "boOPAutomatica" );
$obRdnOPAutomatica->setTitle  ( "Informe se a OP será automática." );
$obRdnOPAutomatica->setChecked( $stOPAutomatica );
$obRdnOPAutomatica->obRadioSim->setValue  ("Sim");
$obRdnOPAutomatica->obRadioNao->setValue  ("Não");

$obRdnOPCarne = new SimNao;
$obRdnOPCarne->setRotulo("Emitir carnê na OP");
$obRdnOPCarne->setName("boOPCarne");
$obRdnOPCarne->setTitle("Informe se a OP emitirá carnê.");
$obRdnOPCarne->setChecked($stOPCarne);
$obRdnOPCarne->obRadioSim->setValue("Sim");
$obRdnOPCarne->obRadioNao->setValue("Não");

// Segmento Conta Caixa
$obCmbEntidade   = new ITextBoxSelectEntidadeGeral();
$obCmbEntidade->setObrigatorioBarra( true );
$obCmbEntidade->setNull( true );
$obCmbEntidade->obTextBox->obEvento->setOnChange( "montaParametrosGET('limpaPopUpContaAnalitica');" );
$obCmbEntidade->obSelect->obEvento->setOnChange( "montaParametrosGET('limpaPopUpContaAnalitica');" );

$obIPopUPContaCaixa = new IPopUpContaAnalitica( $obCmbEntidade->obSelect );
$obIPopUPContaCaixa->setRotulo( "Conta Caixa"            );
$obIPopUPContaCaixa->setTitle ( "Informe a conta caixa." );
$obIPopUPContaCaixa->setTipoBusca( 'emp_conta_caixa'     );
$obIPopUPContaCaixa->setObrigatorioBarra( true );
$obIPopUPContaCaixa->setNull( true );
$arContaCaixa = array ( &$obCmbEntidade, $obIPopUPContaCaixa );

$obSpnContaCaixa = new Span;
$obSpnContaCaixa->setId ( 'spnContaCaixa' );

$obOk = new Ok;

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obForm->setTarget                  ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm );
$obFormulario->addHidden            ( $obHdnAcao );
$obFormulario->addHidden            ( $obHdnCtrl );

$obFormulario->addTitulo            ( "Dados para Configuração" );

$obFormulario->addComponente        ( $obCmbTipoNumeracao );
//$obFormulario->addComponente        ( $obRdnAnularAutorizacaoAutomatica );
$obFormulario->addComponente        ( $obRdnDataVencimento );
$obFormulario->addComponente        ( $obRdnLiquidacaoAutomatica );
$obFormulario->addComponente        ( $obRdnOPAutomatica );
$obFormulario->addComponente        ( $obRdnOPCarne );

$obFormulario->addTitulo            ( "Conta Caixa" );
$obFormulario->addComponente        ( $obCmbEntidade );
$obFormulario->addComponente        ( $obIPopUPContaCaixa );
$obFormulario->incluir ( 'contaCaixa', $arContaCaixa, true, true );
$obFormulario->addSpan ( $obSpnContaCaixa );

$obFormulario->defineBarra( array( $obOk ) );

$obFormulario->show                 ();

SistemaLegado::executaFrameOculto ( montaSpanContaCaixa() );

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
