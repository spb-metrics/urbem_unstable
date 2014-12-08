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
    * Página de Formulário para configuração do módulo compras
    * Data de Criação   : 30/06/2006

    * @author Fernando Zank Correa Evangelista

    * $Id: FMManterConfiguracao.php 60668 2014-11-06 19:32:49Z evandro $

    * Casos de uso : uc-03.04.08
    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once TCOM."TComprasConfiguracao.class.php";
include_once TCOM."TComprasSolicitacao.class.php";
include_once TLIC."TLicitacaoLicitacao.class.php";
include_once CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeGeral.class.php";
include_once CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php";
include_once CAM_GP_COM_MAPEAMENTO."TComprasConfiguracaoEntidade.class.php";

$stPrograma = "ManterConfiguracao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once ( $pgOcul );

$obTComprasConfiguracaoEntidade = new TComprasConfiguracaoEntidade ;
/// buscando os responsáveis
$rsResponsaveis = new RecordSet;
$stFiltro = " and configuracao_entidade.exercicio = '".Sessao::getExercicio()."' ";
$obTComprasConfiguracaoEntidade->recuperaResponsaveis ( $rsResponsaveis, $stFiltro );

Sessao::write('arResponsaveisEntidades' , array());
Sessao::write('arResponsaveisEntidadesExcluidos' , array());
Sessao::write('inUltimoCodigoResp' , 0);

if ($rsResponsaveis->getNumLinhas() > 0) {
    $inID = 0;
    while (!$rsResponsaveis->eof()) {
        $rsResponsaveis->setCampo ( 'inId', $inID++ );
        $rsResponsaveis->proximo();
    }
    Sessao::write('arResponsaveisEntidades', $rsResponsaveis->getElementos());
    Sessao::write('inUltimoCodigoResp', $inID);
}

$obTConfiguracao = new TComprasConfiguracao();
$obTConfiguracao->setDado("parametro","homologacao_automatica");
$obTConfiguracao->recuperaPorChave($rsConfiguracao);
$boEfetuarHomologacao = $rsConfiguracao->getCampo('valor')=='true'?true:false;

$obTConfiguracao = new TComprasConfiguracao();
$obTConfiguracao->setDado("parametro","dotacao_obrigatoria_solicitacao");
$obTConfiguracao->recuperaPorChave($rsConfiguracao);
$boExigeDotacao = $rsConfiguracao->getCampo('valor')=='true'?true:false;

$obTConfiguracao = new TComprasConfiguracao();
$obTConfiguracao->setDado("parametro","reserva_rigida");
$obTConfiguracao->recuperaPorChave($rsConfiguracao);
$boReservaRigida = $rsConfiguracao->getCampo('valor') == 'true' ? true : false;

$obTConfiguracao = new TComprasConfiguracao();
$obTConfiguracao->setDado("parametro","numeracao_licitacao");
$obTConfiguracao->recuperaPorChave($rsConfiguracao);
$stNumeracaoSolicitacao = $rsConfiguracao->getCampo('valor');

$obTSolicitacao = new TComprasSolicitacao();
$obTSolicitacao->recuperaTodos($rsSolicitacao," where exercicio = '".Sessao::getExercicio()."'");

$obTLicitacao = new TLicitacaoLicitacao();
$obTLicitacao->recuperaTodos($rsLicitacao," where exercicio = '".Sessao::getExercicio()."'");

/**
  * Recupera o valor do parâmetro "numeracao_automatica" usado para
  * definir se a numeração dos Ids da Licitação/Compra Direta será automático ou manual.
  **/

$obTConfiguracao = new TComprasConfiguracao;
$obTConfiguracao->setDado( "parametro" , "numeracao_automatica" );
$obTConfiguracao->recuperaPorChave($rsConfiguracao);
$boIdLicitacaoAutomatica = $rsConfiguracao->getCampo('valor');

$obTConfiguracaoVlReferencia = new TComprasConfiguracao;
$obTConfiguracaoVlReferencia->setDado( "parametro" , "tipo_valor_referencia" );
$obTConfiguracaoVlReferencia->recuperaPorChave($rsConfiguracao);
$stValorReferencia = $rsConfiguracao->getCampo('valor');

//$jsOnLoad = "executaFuncaoAjax('recuperaFormularioAlteracao')";
$stAcao = $request->get('stAcao');

if (empty( $stAcao )) {
    $stAcao = "alterar";
}

$stLocation = $pgList . "?". Sessao::getId() . "&stAcao=" . $stAcao;

if ($inCodigo) {
    $stLocation .= "&inCodigo=$inCodigo";
}

$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obForm->setTarget                  ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnChecked = new Hidden;
$obHdnChecked->setName( "stCheked" );
$obHdnChecked->setValue( $stCheked );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

if ($rsSolicitacao->getCampo('exercicio') == '') {
    //Radios de Tipo de Relatório
    $obRdbHomologado = new SimNao;
    $obRdbHomologado->setRotulo ( "Efetuar Homologação de Solicitação Automática");
    $obRdbHomologado->setTitle  ( "Informe se após a elaboração de uma solicitação de compra, será efetuada automaticamente a homologação." );
    $obRdbHomologado->setName   ( "stHomologacao" );
    //$obRdbHomologado->setNull(false);
    $obRdbHomologado->obRadioSim->setValue('true');
    $obRdbHomologado->obRadioNao->setValue('false');
    if ($boEfetuarHomologacao)
        $obRdbHomologado->setChecked  ( "S" );
    else
        $obRdbHomologado->setChecked  ( "N" );

    $obRdbExigeDotacao= new SimNao;
    $obRdbExigeDotacao->setRotulo ( "Exige Dotação na Solicitação/Mapa");
    $obRdbExigeDotacao->setTitle  ( "Informe se no momento da solicitação, será obrigatório informar a dotação, e será efetuada a reserva de saldos." );
    $obRdbExigeDotacao->setName   ( "stExigeDotacao" );
    //$obRdbExigeDotacao->setNull(false);
    $obRdbExigeDotacao->obRadioSim->setValue('true');
    $obRdbExigeDotacao->obRadioNao->setValue('false');
    if ($boExigeDotacao)
        $obRdbExigeDotacao->setChecked  ( "S" );
    else
        $obRdbExigeDotacao->setChecked  ( "N" );

} else {
    $obRdbHomologado = new Label;
    $obRdbHomologado->setRotulo ( "Efetuar Homologação de Solicitação Automática");
    if ($boEfetuarHomologacao) {
        $obRdbHomologado->setValue ("Sim");
    } else {
        $obRdbHomologado->setValue ("Não");
    }
    $obHdnHomologado= new Hidden;
    $obHdnHomologado->setName('stHomologacao');
    $obHdnHomologado->setValue($boEfetuarHomologacao?'true':'false');
    $obRdbExigeDotacao= new Label;
    $obRdbExigeDotacao->setRotulo ( "Exige Dotação na Solicitação/Mapa" );
    if ($boExigeDotacao) {
        $obRdbExigeDotacao->setValue ("Sim");
    } else {
        $obRdbExigeDotacao->setValue ("Não");
    }
    $obHdnExigeDotacao = new Hidden;
    $obHdnExigeDotacao->setName('stExigeDotacao');
    $obHdnExigeDotacao->setValue($boExigeDotacao?'true':'false');
}

$obChkNumeracaoPorEntidade = new CheckBox;
$obChkNumeracaoPorEntidade->setName('stNumeracaoPorEntidade');
$obChkNumeracaoPorEntidade->setRotulo('Numeração da Licitação');
$obChkNumeracaoPorEntidade->setTitle('Define como serão numeradas as licitações.');
$obChkNumeracaoPorEntidade->setLabel('Por Entidade');
$obChkNumeracaoPorModalidade= new CheckBox;
$obChkNumeracaoPorModalidade->setLabel('Por Modalidade');
$obChkNumeracaoPorModalidade->setName('stNumeracaoPorModalidade');
switch ($stNumeracaoSolicitacao) {
   case 'geral':
      $obChkNumeracaoPorEntidade->setChecked(false);
      $obChkNumeracaoPorModalidade->setChecked(false);
   break;
   case 'entidade':
      $obChkNumeracaoPorEntidade->setChecked(true);
      $obChkNumeracaoPorModalidade->setChecked(false);
   break;
   case 'modalidade':
      $obChkNumeracaoPorEntidade->setChecked(false);
      $obChkNumeracaoPorModalidade->setChecked(true);
   break;
   case 'entidademodalidade':
      $obChkNumeracaoPorEntidade->setChecked(true);
      $obChkNumeracaoPorModalidade->setChecked(true);
   break;
}

if ($rsLicitacao->getCampo('exercicio') != '') {
   $obChkNumeracaoPorEntidade->setDisabled(true);
   $obHdnNumeracaoPorEntidade = new Hidden;
   $obHdnNumeracaoPorEntidade->setName('stNumeracaoPorEntidade');
   $obHdnNumeracaoPorEntidade->setValue($obChkNumeracaoPorEntidade->getChecked()?'on':'');
   $obChkNumeracaoPorModalidade->setDisabled(true);
   $obHdnNumeracaoPorModalidade= new Hidden;
   $obHdnNumeracaoPorModalidade->setName('stNumeracaoPorModalidade');
   $obHdnNumeracaoPorModalidade->setValue($obChkNumeracaoPorModalidade->getChecked()?'on':'');
}

// Configuração para setar se o número da Licitação será automática ou manual.
$obRdbIdLicitacaoAutomaticoSim = new Radio;
$obRdbIdLicitacaoAutomaticoSim->setRotulo  ( "Numeração Automática de Licitação/Compra Direta" );
$obRdbIdLicitacaoAutomaticoSim->setTitle   ( "Informe se a numeração da licitação/compra direta deve ser gerada automática pelo sistema." );
$obRdbIdLicitacaoAutomaticoSim->setName    ( "boIdLicitacaoAutomatica");
$obRdbIdLicitacaoAutomaticoSim->setLabel   ( "Sim"  );
$obRdbIdLicitacaoAutomaticoSim->setValue   ( "t" );
$obRdbIdLicitacaoAutomaticoSim->setChecked ( ($boIdLicitacaoAutomatica == "t" ? true : false) );

$obRdbIdLicitacaoAutomaticoNao = new Radio;
$obRdbIdLicitacaoAutomaticoNao->setName    ( "boIdLicitacaoAutomatica");
$obRdbIdLicitacaoAutomaticoNao->setLabel   ( "Não" );
$obRdbIdLicitacaoAutomaticoNao->setValue   ( "f" );
$obRdbIdLicitacaoAutomaticoNao->setChecked ( ($boIdLicitacaoAutomatica == "f" ? true : false) );

# Configuração para setar a modificação do Valor de Referência do Item.
$obRdbVlrRefExato = new Radio;
$obRdbVlrRefExato->setName    ( 'stValorReferencia');
$obRdbVlrRefExato->setRotulo  ( 'Valor de Referência do Item' );
$obRdbVlrRefExato->setTitle   ( 'Informe a modificação que poderá ser realizada no valor de referência do item.' );
$obRdbVlrRefExato->setLabel   ( 'Até o valor solicitado'  );
$obRdbVlrRefExato->setValue   ( 'solicitado' );
$obRdbVlrRefExato->setChecked ( ($stValorReferencia == 'solicitado' ? true : false) );

$obRdbVlrRefLivre = new Radio;
$obRdbVlrRefLivre->setName    ( 'stValorReferencia'  );
$obRdbVlrRefLivre->setLabel   ( 'Aceita Modificação' );
$obRdbVlrRefLivre->setValue   ( 'livre' );
$obRdbVlrRefLivre->setChecked ( ($stValorReferencia == 'livre' ? true : false) );

$obRdbVlrRef10PorCento = new Radio;
$obRdbVlrRef10PorCento->setName    ( 'stValorReferencia' );
$obRdbVlrRef10PorCento->setLabel   ( 'Até 10% do valor'  );
$obRdbVlrRef10PorCento->setValue   ( '10%'               );
$obRdbVlrRef10PorCento->setChecked ( ($stValorReferencia == '10%' ? true : false) );

if ( $rsSolicitacao->getNumLinhas() > 0 ) {
    $obHdnReservaRigida = new Label();
    $obHdnReservaRigida->setRotulo('Reserva Rígida');
    if ($boReservaRigida) {
        $obHdnReservaRigida->setValue('Sim');
    } else {
        $obHdnReservaRigida->setValue('Não');
    }
} else {
    $obRdbReservaRigidaSim = new Radio();
    $obRdbReservaRigidaSim->setRotulo( "Reserva Rígida" );
    $obRdbReservaRigidaSim->setTitle( "Informar se a dotação deve ter saldo para efetuar a solicitação." );
    $obRdbReservaRigidaSim->setName( "boReservaRigida" );
    $obRdbReservaRigidaSim->setLabel( "Sim" );
    $obRdbReservaRigidaSim->setValue( "true" );
    if ($boReservaRigida) {
        $obRdbReservaRigidaSim->setChecked( true );
    }

    $obRdbReservaRigidaNao = new Radio();
    $obRdbReservaRigidaNao->setName( "boReservaRigida" );
    $obRdbReservaRigidaNao->setLabel( "Não" );
    $obRdbReservaRigidaNao->setValue( "false" );
    if (!$boReservaRigida) {
        $obRdbReservaRigidaNao->setChecked( true );
    }
}

////////// seção para controle de responsáveis pelas entidades

$obITextBoxSelectEntidadeGeral = new  ITextBoxSelectEntidadeGeral( );
$obITextBoxSelectEntidadeGeral->setNull( true );
$obITextBoxSelectEntidadeGeral->setObrigatorioBarra ( true );

$obIPopUpCGM = new IPopUpCGM( $obForm );
$obIPopUpCGM->setObrigatorioBarra( true );
$obIPopUpCGM->setNull ( true );

$arResponsaveis = array ( &$obIPopUpCGM, $obITextBoxSelectEntidadeGeral );

$obSpnResponsaveis = new Span;
$obSpnResponsaveis->setId ( 'spnResponsaveis' );

//define o formulário
$obFormulario = new Formulario;
$obFormulario->addForm          ( $obForm                   );
$obFormulario->setAjuda         ("UC-03.04.08"              );
$obFormulario->addHidden        ( $obHdnAcao                );
$obFormulario->addHidden        ( $obHdnCtrl                );
$obFormulario->addHidden        ( $obHdnChecked             );
$obFormulario->addTitulo        ( "Dados de Configuração"   );
if ($rsSolicitacao->getCampo('exercicio') != '') {
   $obFormulario->addHidden ($obHdnHomologado);
   $obFormulario->addHidden ($obHdnExigeDotacao);
}
$obFormulario->addComponente($obRdbHomologado);
$obFormulario->addComponente($obRdbExigeDotacao);
if ($rsLicitacao->getCampo('exercicio') != '') {
   $obFormulario->addHidden ($obHdnNumeracaoPorEntidade);
   $obFormulario->addHidden ($obHdnNumeracaoPorModalidade);
}
$obFormulario->agrupaComponentes(array($obChkNumeracaoPorEntidade, $obChkNumeracaoPorModalidade));
$obFormulario->agrupaComponentes(array($obRdbIdLicitacaoAutomaticoSim, $obRdbIdLicitacaoAutomaticoNao));

# Configuração para valor de referência.
$obFormulario->agrupaComponentes(array($obRdbVlrRefExato, $obRdbVlrRefLivre, $obRdbVlrRef10PorCento));

if ($rsSolicitacao->getNumLinhas() > 0)
    $obFormulario->addComponente($obHdnReservaRigida);
else
    $obFormulario->agrupaComponentes(array($obRdbReservaRigidaSim,$obRdbReservaRigidaNao));

$obFormulario->addTitulo ( 'Responsáveis' );
$obFormulario->addComponente ( $obITextBoxSelectEntidadeGeral );
$obFormulario->addComponente ( $obIPopUpCGM                   );
$obFormulario->incluir ( 'Responsavel', $arResponsaveis, true, true );
$obFormulario->addSpan ( $obSpnResponsaveis );

$obFormulario->OK();
$obFormulario->show();

sistemaLegado::executaFrameOculto ( montSpanResponsaveis () );

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
