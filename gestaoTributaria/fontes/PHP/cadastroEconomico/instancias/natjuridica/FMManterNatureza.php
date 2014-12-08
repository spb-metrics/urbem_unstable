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
    * Página de Formulario de Inclusao/Alteracao de Serviços

    * Data de Criação   : 13/04/2005

    * @author Fernando Zank Correa Evangelista
    * @author Desenvolvedor: Lizandro Kirst da Silva
    * @ignore

    * $Id: FMManterNatureza.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.02.08

*/

/*
$Log$
Revision 1.9  2007/02/07 18:34:29  rodrigo
#8345#

Revision 1.8  2006/09/15 14:33:22  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMNaturezaJuridica.class.php" );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//Define o nome dos arquivos PHP
$stPrograma    = "ManterNatureza";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";
include_once( $pgJs );
$obRCEMNatureza = new RCEMNaturezaJuridica;

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $_REQUEST["stCtrl"]  );

$obHdnCodigoNatureza =  new Hidden;
$obHdnCodigoNatureza->setName   ( "inCodigoNatureza" );
$obHdnCodigoNatureza->setValue  ( $_REQUEST["inCodigoNatureza"]  );

$obTxtCodigoNatureza = new TextBox ;
$obTxtCodigoNatureza->setRotulo    ( "Código" );
$obTxtCodigoNatureza->setName      ( "inCodigoNatureza");
$obTxtCodigoNatureza->setValue     ( $_REQUEST["inCodigoNatureza"] );
$obTxtCodigoNatureza->setId        ( "codigoNatureza" );
$obTxtCodigoNatureza->setTitle     ( "Código da Natureza" );
$obTxtCodigoNatureza->setSize      ( 5 );
$obTxtCodigoNatureza->setMaxLength ( 5 );
$obTxtCodigoNatureza->setNull      ( false );
//$obTxtCodigoNatureza->obEvento->setOnKeyDown("FormataCampo(this,event,'###-#')");
$obTxtCodigoNatureza->obEvento->setOnKeyUp("mascaraDinamico('999-9', this, event);");
$obTxtCodigoNatureza->obEvento->setOnChange("buscaValor('validaDigitoVerificador');");
$obTxtCodigoNatureza->obEvento->setOnBlur("verificaValor(this);");

$obLblCodigoNatureza = new Label ;
$obLblCodigoNatureza->setRotulo    ( "Código" );
$obLblCodigoNatureza->setName      ( "labelCodigoNatureza");
$obLblCodigoNatureza->setValue     ( $_REQUEST["inCodigoNatureza"] );
$obLblCodigoNatureza->setTitle     ( "Código da Natureza" );

$obTxtNomeNatureza = new TextBox ;
$obTxtNomeNatureza->setRotulo    ( "Nome" );
$obTxtNomeNatureza->setName      ( "stNomeNatureza");
$obTxtNomeNatureza->setValue     ( $_REQUEST["stNomeNatureza"] );
$obTxtNomeNatureza->setId        ( "nomeNatureza" );
$obTxtNomeNatureza->setTitle     ( "Nome da Natureza" );
$obTxtNomeNatureza->setSize      ( 80 );
$obTxtNomeNatureza->setMaxLength ( 80 );
$obTxtNomeNatureza->setNull      ( false );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc );
//$obForm->setTarget( $pgOcul );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->setAjuda      ( "UC-05.02.08");
$obFormulario->addForm       ( $obForm );
$obFormulario->addTitulo     ( "Dados para Natureza Jurídica" );

if ($stAcao == "alterar") {
    $obFormulario->addHidden ($obHdnCodigoNatureza);
}

$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCtrl );
if ($stAcao == "incluir") {
    $obFormulario->addComponente ( $obTxtCodigoNatureza );
}
if ($stAcao == "alterar") {
    $obFormulario->addComponente ( $obLblCodigoNatureza );
    $obFormulario->setFormFocus( $obTxtNomeNatureza->getid() );
}
$obFormulario->addComponente ( $obTxtNomeNatureza );

if ($stAcao == "incluir") {
    $obFormulario->setFormFocus( $obTxtCodigoNatureza->getid() );
    $obFormulario->Ok       ();
} else {
    $obFormulario->Cancelar ();
}

$obFormulario->show();

sistemaLegado::executaFrameOculto( $stJs );
