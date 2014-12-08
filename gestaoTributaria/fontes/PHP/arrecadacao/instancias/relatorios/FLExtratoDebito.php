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
    * Página de Filtro para Consulta de Imóveis
    * Data de Criação   : 13/07/2007

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Diego Bueno Coelho

    * @ignore

    * $Id: FLExtratoDebito.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.13
*/

/*
$Log$
Revision 1.2  2007/08/01 13:56:54  dibueno
Bug#9793#

Revision 1.1  2007/07/16 16:03:57  dibueno
Bug #9659#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMConfiguracao.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php" );

include_once ( CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php" );

include_once ( CAM_GT_CIM_COMPONENTES."IPopUpImovel.class.php");
include_once ( CAM_GT_CEM_COMPONENTES."IPopUpEmpresa.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ExtratoDebito";
$pgFilt = "FL".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma."s.js";

//include_once( $pgJS );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "consultar";
}

Sessao::write( 'sessao_transf4', array( 'filtro' => array(), 'pg' => '' , 'pos' => '', 'paginando' => false ) );
Sessao::write( 'link', array() );

// CONSULTA CONFIGURACAO DO MODULO IMOBILIARIO
$obRCIMConfiguracao = new RCIMConfiguracao;
$obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMConfiguracao->consultarConfiguracao();
$stMascaraInscricao = $obRCIMConfiguracao->getMascaraIM();

// CONSULTA CONFIGURACAO DO MODULO ECONOMICO
$obRCEMConfiguracao = new RCEMConfiguracao;
$obRCEMConfiguracao->setCodigoModulo( 14 );
$obRCEMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCEMConfiguracao->consultarConfiguracao();
$stMascaraInscricaoEconomico = $obRCEMConfiguracao->getMascaraInscricao();

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction  ( $pgForm );
$obForm->setTarget  ( "telaPrincipal" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName     ( "stCtrl" );
$obHdnCtrl->setValue    ( $stCtrl );

$obPopUpImovel = new IPopUpImovel;
$obPopUpImovel->obInnerImovel->setNull      (true);

$obPopUpEmpresa = new IPopUpEmpresa;
$obPopUpEmpresa->obInnerEmpresa->setNull    (true);

$obTxtExercicio = new TextBox;
$obTxtExercicio->setName        ( "stExercicio" );
$obTxtExercicio->setInteiro     ( true          );
$obTxtExercicio->setMaxLength   ( 4             );
$obTxtExercicio->setSize        ( 4             );
$obTxtExercicio->setRotulo      ( "Exercício"       );
$obTxtExercicio->setTitle       ( "Exercício"       );
$obTxtExercicio->setNull        ( true              );
#$obTxtExercicio->setValue       ( 0 );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden        ( $obHdnAcao        );
$obFormulario->addHidden        ( $obHdnCtrl        );
$obFormulario->addTitulo        ( "Dados para Filtro"       );

$obPopUpCGM = new IPopUpCGM     ( $obForm           );
$obPopUpCGM->setNull            ( true              );
$obPopUpCGM->setRotulo          ( "Contribuinte"            );
$obPopUpCGM->setTitle           ( "Código do Contribuinte"  );
$obFormulario->addComponente    ( $obPopUpCGM       );

$obPopUpEmpresa->geraFormulario ( $obFormulario     );
$obPopUpImovel->geraFormulario  ( $obFormulario     );

$obFormulario->addComponente    ( $obTxtExercicio   );

$obFormulario->OK();
$obFormulario->show();

?>
