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
    * Data de Criação: 07/10/2008

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Luiz Felipe Prestes Teixeira

    * @package URBEM
    * @subpackage

    * Casos de uso :
    $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(TLIC."TLicitacaoRescisaoContrato.class.php");
include_once(CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php");

$stAcao = $request->get("stAcao");

// padrão do programa
$stPrograma = "ManterRescindirContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );
//fim do padrão

// cria o objeto com os dados da licitacaoRescisaoContrato
$obTLicitacaoRescisaoContrato = new TLicitacaoRescisaoContrato;
$obTLicitacaoRescisaoContrato->setDado('num_contrato', $request->get("inNumContrato"));
$obTLicitacaoRescisaoContrato->setDado('licitacao', false);
$obTLicitacaoRescisaoContrato->recuperaContratoRescisao($rsRescisaoContrato);

$stNumeroContato = $rsRescisaoContrato->getCampo('num_contrato');
$stNumeroContato .= "/".$rsRescisaoContrato->getCampo('exercicio');

$obLblExercicioContrato = new Label;
$obLblExercicioContrato->setRotulo ( "Exercício do Contrato");
$obLblExercicioContrato->setValue ( $rsRescisaoContrato->getCampo('exercicio') );

$obLblExercicioCompra = new Label;
$obLblExercicioCompra->setRotulo ( "Exercício da Compra");
$obLblExercicioCompra->setValue ( $rsRescisaoContrato->getCampo('exercicio_compra_direta') );

$obLblNumeroContrato = new Label;
$obLblNumeroContrato->setRotulo('Número Contrato');
$obLblNumeroContrato->setValue($stNumeroContato);

$obLblEntidade = new Label;
$obLblEntidade->setRotulo('Entidade');
$obLblEntidade->setValue($rsRescisaoContrato->getCampo('cod_entidade')." - ".$rsRescisaoContrato->getCampo('entidade'));

$obLblContratado = new Label;
$obLblContratado->setRotulo('Contratado');
$obLblContratado->setValue($rsRescisaoContrato->getCampo('cgm_contratado')." - ".$rsRescisaoContrato->getCampo('contratado'));

//monta o popUp de pessoa juridica
$obResponsavelJuridico = new IPopUpCGMVinculado( $obForm );
$obResponsavelJuridico->setTabelaVinculo       ( 'sw_cgm_pessoa_fisica' );
$obResponsavelJuridico->setCampoVinculo        ( 'numcgm' );
$obResponsavelJuridico->setNomeVinculo         ( 'Responsavel' );
$obResponsavelJuridico->setRotulo              ( 'Responsável Jurídico da Rescisão' );
$obResponsavelJuridico->setName                ( 'stResponsavelJuridico');
$obResponsavelJuridico->setId                  ( 'stResponsavelJuridico');
$obResponsavelJuridico->obCampoCod->setName    ( "inCodResponsavelJuridico" );
$obResponsavelJuridico->obCampoCod->setId      ( "inCodResponsavelJuridico" );
$obResponsavelJuridico->obCampoCod->setNull    ( true );
$obResponsavelJuridico->setNull                ( true );

//monta o campo Data Data de Rescisão'
$obTxtDataRescisao = new Data;
$obTxtDataRescisao->setRotulo('Data da Rescisão');
$obTxtDataRescisao->setTitle('Informe a data da rescisão.');
$obTxtDataRescisao->setName('dtRescisao');
$obTxtDataRescisao->setNull(false);

//monta o campo Moeda Multa
$obVlMulta = new Moeda;
$obVlMulta->setRotulo('Valor da Multa');
$obVlMulta->setTitle('Informe o valor da multa.');
$obVlMulta->setName('vlMulta');
$obVlMulta->setNull(false);

//monta o campo Moeda Indenizaçãos
$obVlIndenizacao = new Moeda;
$obVlIndenizacao->setRotulo('Valor da Indenização');
$obVlIndenizacao->setTitle('Informe o valor da indenização.');
$obVlIndenizacao->setName('vlIndenizacao');
$obVlIndenizacao->setNull(false);

//monta a textArea Motivo
$obVlMotivo = new TextArea;
$obVlMotivo->setRotulo('Motivo');
$obVlMotivo->setTitle('Informe o motivo.');
$obVlMotivo->setName('stMotivo');
$obVlMotivo->setNull(false);

// objetos hidden das labels
$obHdnInNumContrato = new Hidden;
$obHdnInNumContrato->setName( "inNumContrato" );
$obHdnInNumContrato->setValue( $rsRescisaoContrato->getCampo('num_contrato') );

$obHdnStExercicio = new Hidden;
$obHdnStExercicio->setName( "stExercicio" );
$obHdnStExercicio->setValue( $rsRescisaoContrato->getCampo('exercicio') );

$obHdnInCodEntidade = new Hidden;
$obHdnInCodEntidade->setName( "inCodEntidade" );
$obHdnInCodEntidade->setValue( $rsRescisaoContrato->getCampo('cod_entidade') );

$obHdnInCgmContratado = new Hidden;
$obHdnInCgmContratado->setName( "inCgmContratado" );
$obHdnInCgmContratado->setValue( $rsRescisaoContrato->getCampo('cgm_contratado') );

//define o formulário
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnInCgmContratado );
$obFormulario->addHidden( $obHdnInNumContrato );
$obFormulario->addHidden( $obHdnStExercicio );
$obFormulario->addHidden( $obHdnInCodEntidade );
$obFormulario->addTitulo        ( "Dados do Contrato"   );
$obFormulario->addComponente( $obLblExercicioContrato );
$obFormulario->addComponente( $obLblExercicioCompra );
$obFormulario->addComponente( $obLblNumeroContrato );
$obFormulario->addComponente( $obLblEntidade );
$obFormulario->addComponente( $obLblContratado );
$obFormulario->addComponente( $obResponsavelJuridico );
$obFormulario->addComponente( $obTxtDataRescisao );
$obFormulario->addComponente( $obVlMulta );
$obFormulario->addComponente( $obVlIndenizacao );
$obFormulario->addComponente( $obVlMotivo );

$obFormulario->Ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>