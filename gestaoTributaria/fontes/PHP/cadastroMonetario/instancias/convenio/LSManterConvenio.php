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
    * Página de Formulario de Inclusao/Alteracao de  Convenio

    * Data de Criação   : 10/10/2005

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho
    * @ignore

    * $Id: LSManterConvenio.php 63839 2015-10-22 18:08:07Z franver $

    *Casos de uso: uc-05.05.04

*/

/*
$Log$
Revision 1.9  2006/09/15 14:57:44  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONCarteira.class.php" );
include_once ( CAM_GT_MON_NEGOCIO."RMONBanco.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConvenio";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$stCaminho   = CAM_GT_MON_INSTANCIAS."convenio/";
$obRMONConvenio = new RMONConvenio;
$obRMONBanco = new RMONBanco;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "alterar";
}
//Define arquivos PHP para cada acao
switch ($_REQUEST['stAcao']) {
    case 'alterar'  : $pgProx = $pgForm; break;
    case 'excluir'  : $pgProx = $pgProc; break;
    case 'baixar'   : $pgProx = $pgFormBaixar; break;
    DEFAULT         : $pgProx = $pgForm;
}

//MANTEM FILTRO E PAGINACAO
$link = Sessao::read('link');
$stLink .= "&stAcao=".$_REQUEST['stAcao'];
if ($_GET["pg"] and  $_GET["pos"]) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
    $link["pg"]  = $_GET["pg"];
    $link["pos"] = $_GET["pos"];
}

//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array($link) ) {
    $_REQUEST = $link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
}

Sessao::write('stLink', $stLink);
Sessao::write('link', $link);

//MONTA O FILTRO
if ($_REQUEST["inNumConvenio"]) {
    $obRMONConvenio->setNumeroConvenio( $_REQUEST['inNumConvenio'] );
}
if ($_REQUEST["cmbTipoConvenio"]) {
    $obRMONConvenio->setTipoConvenio( $_REQUEST['cmbTipoConvenio'] );
}
if ($_REQUEST["inCodBancoTxt"]) {
    $obRMONBanco = new RMONBanco;
    $obRMONBanco->setNumBanco( $_REQUEST["inCodBancoTxt"] );
    $obRMONBanco->consultarBanco();
    $obRMONConvenio->setCodigoBanco( $obRMONBanco->getCodBanco() );
}

$stLink .= "&stAcao=".$_REQUEST['stAcao'];
$obRMONConvenio->listarConvenio($rsLista);

$obLista = new Lista;
$obLista->setRecordSet( $rsLista );
$obLista->setTitulo("Registros de Convenio");
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Banco ");
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Agência ");
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Tipo de convênio ");
$obLista->ultimoCabecalho->setWidth( 50 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Convênio ");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_banco" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_agencia" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_tipo" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "num_convenio" );
$obLista->commitDado();
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $_REQUEST['stAcao'] );
$obLista->ultimaAcao->addCampo("&inCodBanco",       "cod_banco"     );
$obLista->ultimaAcao->addCampo("&inNumBanco",       "num_banco"     );
$obLista->ultimaAcao->addCampo("&stNomBanco",       "nom_banco"     );
$obLista->ultimaAcao->addCampo("&inCodAgencia",     "cod_agencia" );
$obLista->ultimaAcao->addCampo("&inNumAgencia",     "num_agencia"     );
$obLista->ultimaAcao->addCampo("&stNomAgencia",     "nom_agencia"     );
$obLista->ultimaAcao->addCampo("&inCodConvenio",    "cod_convenio"     );
$obLista->ultimaAcao->addCampo("&inNumConvenio",    "num_convenio"     );
$obLista->ultimaAcao->addCampo("&inCodTipoConvenio","cod_tipo"  );
$obLista->ultimaAcao->addCampo("&stNomTipoConvenio","nom_tipo"  );
$obLista->ultimaAcao->addCampo("&inNumConvenio",    "num_convenio"  );
$obLista->ultimaAcao->addCampo("&flVariacao",       "variacao"     );
$obLista->ultimaAcao->addCampo("&flTaxaBancaria",   "taxa_bancaria"     );
$obLista->ultimaAcao->addCampo("&flCedente",        "cedente"     );
$obLista->ultimaAcao->addCampo("&stDescQuestao","[cod_convenio]");

if ($_REQUEST['stAcao'] == "excluir") {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}

$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->setAjuda  ( "UC-05.05.04" );
$obFormulario->show();
