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
    * Página de Filtro para Consulta de Nota Avulsa
    * Data de Criação   : 01/09/2008

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: FLConsultarNotaAvulsa.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.19
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php" );
include_once ( CAM_GT_CEM_COMPONENTES."IPopUpEmpresa.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ConsultarNotaAvulsa";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "consultar";
}

Sessao::write( 'link', "" );

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget( "telaPrincipal" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl );

$obPopUpEmpresa = new IPopUpEmpresa;
$obPopUpEmpresa->obInnerEmpresa->setNull (true);

$obTxtExercicio = new TextBox ;
$obTxtExercicio->setName       ( "stExercicio"     );
$obTxtExercicio->setId             ( "stExercicio"     );
$obTxtExercicio->setInteiro       ( true          );
$obTxtExercicio->setMaxLength ( 4             );
$obTxtExercicio->setSize           ( 4             );
$obTxtExercicio->setRotulo      ( "Exercício"       );
$obTxtExercicio->setTitle         ( "Exercício"       );
$obTxtExercicio->setNull          ( true              );
$obTxtExercicio->setValue       ( Sessao::getExercicio());

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden( $obHdnAcao            );
$obFormulario->addHidden( $obHdnCtrl            );
$obFormulario->addTitulo( "Dados para Filtro"   );

$obPopUpCGM = new IPopUpCGM( $obForm );
$obPopUpCGM->setNull ( true );
$obPopUpCGM->setRotulo ( "Contribuinte" );
$obPopUpCGM->setTitle ( "Código do Contribuinte" );

$obFormulario->addComponente( $obPopUpCGM );
$obPopUpEmpresa->geraFormulario ( $obFormulario );
$obFormulario->addComponente( $obTxtExercicio );

$obFormulario->ok();
$obFormulario->show();

?>
