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
    * Página de Processamento de Arquivos de Exportação
    * Data de Criação   : 04/02/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    $Revision: 59612 $
    $Name$
    $Author: gelson $
    $Date: 2014-09-02 09:00:51 -0300 (Ter, 02 Set 2014) $

    * Casos de uso: uc-02.00.00
                    uc-02.08.01
*/

/*
$Log$
Revision 1.1  2007/09/24 20:03:12  hboaventura
Ticket#10234#

Revision 1.7  2006/07/05 20:46:14  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
SistemaLegado::executaFramePrincipal("BloqueiaFrames(true,false);");

$stPrograma = "Exportador";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";
$pgAnterior = $HTTP_REFERER; //.'?'.Sessao::getId();

//************************************************/
// Limpa a variavel de sessão para o filtro
//***********************************************/
Sessao::remove('filtro');
Sessao::remove('prefeitura');
//unset( //sessao->filtro      );
//unset( //sessao->prefeitura  );

$arFiltro = array();
foreach ($_POST as $key=>$value) {
    $arFiltro[$key] = $value;
    if ( !is_array($value) ) {
        $pgAnterior .= "&$key=$value";
    }
}
Sessao::write('filtro',$arFiltro);

// Seta os dados para o cabeçalho
$arPrefeitura = array();
$arPrefeitura["cnpj"]         = substr($_REQUEST["stCnpjSetor"],0,14);
$arPrefeitura["prefeitura"]   = substr($_REQUEST["stCnpjSetor"],15,strlen($_REQUEST["stCnpjSetor"])-1);

Sessao::write('prefeitura',$arPrefeitura);

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction                  ( $_POST['hdnPaginaExportacao'] );
$obForm->setTarget                  ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm );
$obFormulario->addHidden            ( $obHdnAcao );
$obFormulario->addHidden            ( $obHdnCtrl );

$obFormulario->show                 ();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
if ($_POST['hdnPaginaExportacao']) {
    include_once($pgJs);
}
?>
