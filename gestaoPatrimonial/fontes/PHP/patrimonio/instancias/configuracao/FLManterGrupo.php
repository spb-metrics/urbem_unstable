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
    * Data de Criação: 05/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Revision: 26727 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-11-12 16:31:31 -0200 (Seg, 12 Nov 2007) $

    * Casos de uso: uc-03.01.04
*/

/*
$Log$
Revision 1.2  2007/10/17 13:27:12  hboaventura
correção dos arquivos

Revision 1.1  2007/09/18 15:11:11  hboaventura
Adicionando ao repositório

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_PAT_COMPONENTES."ISelectNatureza.class.php");

$stPrograma = "ManterGrupo";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//cria um novo formulario
$obForm = new Form;
$obForm->setAction ($pgList);

//Cria o hidden da acao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

//cria a acao de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl" );
$obHdnCtrl->setValue("");

//instancia o componente ISelectNatureza
$obISelectNatureza = new ISelectNatureza( $obForm );
$obISelectNatureza->setNull( true );

//cria o textbox da descrição do grupo
$obTxtDescricaoGrupo = new TextBox();
$obTxtDescricaoGrupo->setId    ( 'stDescricaoGrupo' );
$obTxtDescricaoGrupo->setName  ( 'stDescricaoGrupo' );
$obTxtDescricaoGrupo->setRotulo( 'Descrição do Grupo' );
$obTxtDescricaoGrupo->setTitle ( 'Informe a descrição do grupo do bem ' );
$obTxtDescricaoGrupo->setSize  ( 50 );
$obTxtDescricaoGrupo->setNull  ( true );
$obTxtDescricaoGrupo->setValue ( $stNomGrupo );

// //cria um busca inner para retornar uma Conta Contábil de Depreciação Acumulada
// $obBscContaContabilDepreciacao = new BuscaInner;
// $obBscContaContabilDepreciacao->setRotulo               ( "Conta Contábil de Depreciação Acumulada"     );
// $obBscContaContabilDepreciacao->setTitle                ( "Informe a conta do plano de contas."         );
// $obBscContaContabilDepreciacao->setId                   ( "stDescricaoContaDepreciacao"                          );
// $obBscContaContabilDepreciacao->obCampoCod->setName     ( "inCodContaDepreciacao"                                );
// $obBscContaContabilDepreciacao->obCampoCod->setSize     ( 10  );
// $obBscContaContabilDepreciacao->obCampoCod->setAlign    ("left" );
// $obBscContaContabilDepreciacao->setValoresBusca      ( CAM_GF_CONT_POPUPS."planoConta/OCPlanoConta.php?".Sessao::getId(),$obForm->getName(),"contaContabilDepreciacaoAcumulada");
// $obBscContaContabilDepreciacao->setFuncaoBusca       ( "abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLPlanoConta.php','frm','inCodContaDepreciacao','stDescricaoContaDepreciacao','contaContabilDepreciacaoAcumulada','".Sessao::getId()."','800','550');" );
// $obBscContaContabilDepreciacao->setNull              ( false );
// $obBscContaContabilDepreciacao->setValue( $stNomContaDepreciacao );
// $obBscContaContabilDepreciacao->obCampoCod->setValue( $inCodPlanoDepreciacao );

$obTipoBusca = new TipoBusca( $obTxtDescricaoGrupo );

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->setAjuda     ('UC-03.01.04');
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );
$obFormulario->addTitulo    ( "Dados para o Filtro" );
$obFormulario->addComponente( $obISelectNatureza );
$obFormulario->addComponente( $obTipoBusca );
//$obFormulario->addComponente( $obBscContaContabilDepreciacao);
$obFormulario->OK();
$obFormulario->show();
