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
    * Página de Formulario de Inclusao/Alteracao de Fornecedores
    * Data de Criação   : 29/07/2004

    * @author Desenvolvedor: Marcelo Boezzio Paulino

    * @ignore

    $Revision: 31000 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.06
*/

/*
$Log$
Revision 1.6  2006/07/05 20:43:43  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoClassificacaoDespesa.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ClassificacaoDespesa";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

//Definições das funções de formulário
$stFncJavaScript .= " function buscaValor() { \n";
$stFncJavaScript .= "     document.frm.target = 'oculto'; \n";
$stFncJavaScript .= "     document.frm.stCtrl.value = 'mascaraClassificacao'; \n";
$stFncJavaScript .= "     document.frm.action = '".$pgOcul."?".Sessao::getId()."'; \n";
$stFncJavaScript .= "     document.frm.submit(); \n";
$stFncJavaScript .= "     document.frm.target = ''; \n";
$stFncJavaScript .= "     document.frm.action = '".$pgList."?".Sessao::getId()."'; \n";
$stFncJavaScript .= " } \n";

$obROrcamentoClassificacaoDespesa = new ROrcamentoClassificacaoDespesa;

//Recupera Mascara da Classificao de Despesa
$mascClassificacao = $obROrcamentoClassificacaoDespesa->recuperaMascara();

//destroi arrays de sessao que armazenam os dados do FILTRO
Sessao::remove('filtroPopUp');
Sessao::remove('pg');
Sessao::remove('pos');
Sessao::remove('paginando');
Sessao::remove('link');

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );
//$obForm->setTarget( "telaPrincipal" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $_REQUEST["stAcao"] );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $_REQUEST["stCtrl"] );

$obHdnForm = new Hidden;
$obHdnForm->setName( "nomForm" );
$obHdnForm->setValue( $_REQUEST["nomForm"] );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $_REQUEST["campoNum"] );

//Define HIDDEN com o o nome do campo texto
$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST["campoNom"] );

$obHdnMascClassificacao = new Hidden;
$obHdnMascClassificacao->setName ( "stMascClassificacao" );
$obHdnMascClassificacao->setValue( $_REQUEST["mascClassificacao"] );

$obTxtExercicio = new TextBox;
$obTxtExercicio->setName      ( "inExercicio" );
$obTxtExercicio->setValue     ( $_REQUEST["inExercicio"] );
$obTxtExercicio->setRotulo    ( "Exercício" );
$obTxtExercicio->setTitle     ( "Informe o exercício para filtro" );
$obTxtExercicio->setSize      ( 4 );
$obTxtExercicio->setMaxLength ( 4 );
$obTxtExercicio->setNull      ( true );
$obTxtExercicio->setReadOnly  ( true );

//Define o objeto TEXT para armazenar o NUMERO DO ORGAO NO ORCAMENTO
$obTxtCodClassificacao = new TextBox;
$obTxtCodClassificacao->setName     ( "inCodClassificacao" );
$obTxtCodClassificacao->setValue    ( $_REQUEST['inCodClassificacao'] );
$obTxtCodClassificacao->setRotulo   ( "Código" );
$obTxtCodClassificacao->setSize     ( strlen($_REQUEST['mascClassificacao']) );
$obTxtCodClassificacao->setMaxLength( strlen($_REQUEST['mascClassificacao']) );
$obTxtCodClassificacao->setNull     ( true );
$obTxtCodClassificacao->setTitle    ( 'Informe um código' );
$obTxtCodClassificacao->obEvento->setOnKeyUp("mascaraDinamico('".$_REQUEST['mascClassificacao']."', this, event);");
$obTxtCodClassificacao->obEvento->setOnChange("buscaValor();");

//Define o objeto TEXT para armazenar a DESCRICAO DO ORGAO
$obTxtDescClassificacao = new TextBox;
$obTxtDescClassificacao->setName     ( "stDescricao" );
$obTxtDescClassificacao->setRotulo   ( "Descrição" );
$obTxtDescClassificacao->setSize     ( 80 );
$obTxtDescClassificacao->setMaxLength( 80 );
$obTxtDescClassificacao->setNull     ( true );
$obTxtDescClassificacao->setTitle    ( 'Informe uma descrição' );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->obJavaScript->addFuncao( $stFncJavaScript );
$obFormulario->addHidden( $obHdnAcao              );
$obFormulario->addHidden( $obHdnCtrl              );
$obFormulario->addHidden( $obHdnMascClassificacao );
$obFormulario->addHidden( $obHdnForm              );
$obFormulario->addHidden( $obHdnCampoNum          );
$obFormulario->addHidden( $obHdnCampoNom          );
$obFormulario->addTitulo( "Dados para filtro de Classificação de Despesa" );
$obFormulario->addComponente( $obTxtCodClassificacao   );
$obFormulario->addComponente( $obTxtDescClassificacao  );
if (trim($_REQUEST["inExercicio"])!="") {
    $obFormulario->addComponente($obTxtExercicio);
}
$obFormulario->addIFrameOculto("oculto");
$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
