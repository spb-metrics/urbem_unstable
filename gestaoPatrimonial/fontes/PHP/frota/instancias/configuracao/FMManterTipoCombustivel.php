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
    * Data de Criação: 16/11/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    $Id: FMManterTipoCombustivel.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.02.05
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_FRO_MAPEAMENTO.'TFrotaCombustivel.class.php' );

$stPrograma = "ManterTipoCombustivel";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

if ($stAcao == 'alterar') {
    $obTFrotaCombustivel = new TFrotaCombustivel();
    $obTFrotaCombustivel->setDado( 'cod_combustivel', $_REQUEST['inCodCombustivel'] );
    $obTFrotaCombustivel->recuperaPorChave( $rsCombustivel );

    //cria um textbox para o codigo do combustivel
    $obCodCombustivel = new TextBox();
    $obCodCombustivel->setName( 'inCodCombustivel' );
    $obCodCombustivel->setId( 'inCodCombustivel' );
    $obCodCombustivel->setValue( $rsCombustivel->getCampo( 'cod_combustivel' ) );
    $obCodCombustivel->setRotulo( 'Código' );
    $obCodCombustivel->setLabel( true );

} else {
    $rsCombustivel = new RecordSet();
}

//cria um novo formulario
$obForm = new Form;
$obForm->setAction ($pgProc);
$obForm->setTarget ("oculto");

//Cria o hidden da acao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

//cria a acao de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl" );
$obHdnCtrl->setValue("");

//cria textbox para o combustivel
$obTxtCombustivel = new TextBox();
$obTxtCombustivel->setName( 'stCombustivel' );
$obTxtCombustivel->setId( 'stCombustivel' );
$obTxtCombustivel->setRotulo( 'Descrição' );
$obTxtCombustivel->setTitle( 'Informe a descrição do combustível.' );
$obTxtCombustivel->setNull( false );
$obTxtCombustivel->setMaxLength( 15 );
$obTxtCombustivel->setSize( 15 );
$obTxtCombustivel->setValue( $rsCombustivel->getCampo( 'nom_combustivel' ) );

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->setAjuda('uc-03.02.05');
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );

$obFormulario->addTitulo    ( 'Combustível do Veículo' );

if ($stAcao == 'alterar') {
    $obFormulario->addComponente( $obCodCombustivel );
}
$obFormulario->addComponente( $obTxtCombustivel );

if ($stAcao == 'alterar') {
    $obFormulario->Cancelar( $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao );
} else {
    $obFormulario->OK();
}
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
