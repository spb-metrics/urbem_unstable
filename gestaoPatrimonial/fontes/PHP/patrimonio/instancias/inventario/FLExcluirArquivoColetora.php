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
 * Página de Formulario
 * Data de Criação: 19/12/2012

 * @author Analista:      Gelson Wolowski
 * @author Desenvolvedor: Carolina Marçal

 * @ignore

 $Id:$

 * Casos de uso:

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once CAM_GP_PAT_COMPONENTES."ISelectEspecie.class.php";

# Define o nome dos arquivos PHP
$stPrograma = "ExcluirArquivoColetora";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";

$obForm = new Form;

$obForm->setAction ( $pgList );

$stAcao='excluir';
$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao"            );
$obHdnAcao->setId    ( "stAcao"            );
$obHdnAcao->setValue ( $stAcao );

$obPeriodicidade = new Periodicidade();
$obPeriodicidade->setRotulo         ( "Periodicidade Emissão" );
$obPeriodicidade->setTitle          ( "Informe a Periodicidade de Importação dos arquivos que deseja pesquisar" );
$obPeriodicidade->setExibeDia		( false );
$obPeriodicidade->setNull 			( false );
$obPeriodicidade->setValue          ( 4 );

$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->addTitulo     ( "Dados para Filtro" );
$obFormulario->addHidden     ( $obHdnAcao      );
$obFormulario->addComponente ( $obPeriodicidade );

$obFormulario->Ok();
$obFormulario->Show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
