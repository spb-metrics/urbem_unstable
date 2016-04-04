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
 * Arquivo instância para popup de Penalidade
 * Data de Criação: 11/08/2008

 * @author Analista      : Heleno Menezes da Silva
 * @author Desenvolvedor : Pedro Vaz de Mello de Medeiros

 * @ignore

 $Id: FLPenalidade.php 64421 2016-02-19 12:14:17Z fabio $

 * Casos de uso:
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

# Define o nome dos arquivos PHP
$stPrograma = "Penalidade";

$pgList = "LS" . $stPrograma . ".php";

# Destroi arrays de sessao que armazenam os dados do FILTRO
unset( $sessao->filtro );
unset( $sessao->link );

$campoNum = $_REQUEST[ 'campoNum' ];
$campoNom = $_REQUEST[ 'campoNom' ];

# Instancia o formulário
$obForm = new Form();
$obForm->setAction( $pgList );

$obHdnCtrl = new Hidden();
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnTipoBusca = new Hidden();
$obHdnTipoBusca->setName( "tipoBusca" );
$obHdnTipoBusca->setValue( $_REQUEST['tipoBusca'] );

$obHdnForm = new Hidden();
$obHdnForm->setName( "nomForm" );
$obHdnForm->setValue( $nomForm );

$obHdnAcao = new Hidden();
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $_GET['stAcao'] );

# Define HIDDEN com o o nome do campo texto
$obHdnCampoNum = new Hidden();
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $campoNum  );

$obHdnCampoNom = new Hidden();
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $campoNom  );

# Definição das Caixas de Texto
$obTxtNomeCgm = new TextBox();
$obTxtNomeCgm->setName( "campoNom" );
$obTxtNomeCgm->setRotulo( "Nome" );
$obTxtNomeCgm->setSize( 60 );
$obTxtNomeCgm->setMaxLength( 60 );

# Criação do formulário
$obFormulario = new Formulario();
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnTipoBusca );
$obFormulario->addHidden( $obHdnForm );
$obFormulario->addHidden( $obHdnCampoNom );
$obFormulario->addHidden( $obHdnCampoNum );
$obFormulario->addTitulo( "Dados do Filtro para penalidade" );
$obFormulario->addComponente( $obTxtNomeCgm );
$obFormulario->ok();

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
