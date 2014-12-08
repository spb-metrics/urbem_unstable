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
/*
    * Formulário para Vinculo de unidade orcamentaria da GF e organograma
    * Data de Criação: 06/03/2013

    * @author Analista      Gelson Goncalves <gelson.goncalves@cnm.org.br>
    * @author Desenvolvedor Carolina Schwaab Marçal <henrique.santos@cnm.org.br>

    * @package URBEM
    * @subpackage

    * @ignore

    $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenhoAutorizacao.class.php" );
include_once ( CAM_FW_HTML . "MontaAtributos.class.php"       );

SistemaLegado::BloqueiaFrames();
$stPrograma = 'ConfiguracaoUnidadeOrcamentariaMANAD';
$pgFilt = 'FL'.$stPrograma.'.php';
$pgList = 'LS'.$stPrograma.'.php';
$pgForm = 'FM'.$stPrograma.'.php';
$pgProc = 'PR'.$stPrograma.'.php';
$pgOcul = 'OC'.$stPrograma.'.php';
$pgJs   = 'JS'.$stPrograma.'.js';

include_once ($pgJs);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

SistemaLegado::executaFramePrincipal( "buscaDado('MontaListaUniOrcam');" );

//*****************************************************//
// Define COMPONENTES DO FORMULARIO
//*****************************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

//Define Span para DataGrid
$obSpnUniOrcam = new Span;
$obSpnUniOrcam->setId ( "spnUniOrcam" );
$obSpnUniOrcamConversao = new Span;
$obSpnUniOrcamConversao->setId ( "spnUniOrcamConversao" );

//****************************************//
// Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo( "Dados de Unidade Orçamentária do Exercício Atual" );

$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );

$obFormulario->addSpan( $obSpnUniOrcam );

$obFormulario->addTitulo( "Dados de Unidades Orçamentárias da Conversão de Dados" );
$obFormulario->addSpan( $obSpnUniOrcamConversao );

$obFormulario->defineBarra( array( new Ok(true) ) );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
