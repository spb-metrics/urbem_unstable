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
    * Página de lista para o cadastro de localização
    * Data de Criação   : 10/10/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @ignore

    * $Id: LSManterLocalizacao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.03
*/

/*
$Log$
Revision 1.11  2006/09/18 10:30:48  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_CIM_NEGOCIO."RCIMLocalizacao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterLocalizacao";
$pgFilt      = "FL".$stPrograma.".php";
$pgList      = "LS".$stPrograma.".php";
$pgForm      = "FM".$stPrograma.".php";
$pgFormNivel = "FM".$stPrograma."Nivel.php";
$pgFormBaixa = "FM".$stPrograma."Baixa.php";
$pgFormiCaracteristica = "FM".$stPrograma."Caracteristica.php";
$pgProc      = "PR".$stPrograma.".php";
$pgOcul      = "OC".$stPrograma.".php";
$pgJS        = "JS".$stPrograma.".js";

$stCaminho = CAM_GT_CIM_INSTANCIAS."localizacao/";
//$stCaminho = CAM_GT_CIM_INSTANCIAS."lote/";

$obRCIMLocalizacao = new RCIMLocalizacao;
$link = Sessao::read('link');

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "alterar";
}
//Define arquivos PHP para cada acao
switch ($_REQUEST['stAcao']) {
    case 'alterar'  : $pgProx = $pgFormNivel; break;
    case 'reativar' :
    case 'baixar'   : $pgProx = $pgFormBaixa; break;
    case 'excluir'  : $pgProx = $pgProc; break;
    case 'historico': $pgProx = $pgFormiCaracteristica; break;
    DEFAULT         : $pgProx = $pgForm;
}
//MANTEM FILTRO E PAGINACAO
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

Sessao::write('link', $link);
Sessao::write('stLink', $stLink);

$obRCIMLocalizacao->setCodigoVigencia( $_REQUEST["inCodigoVigencia"] );
//MONTA O FILTRO
if ($_REQUEST["stValorComposto"]) {
    //RETIRA O PONTO FINAL DO VALOR COMPOSTO CASO EXISTA
    /*
    if ( strrpos( $_REQUEST["stValorComposto"], "." ) == strlen( $_REQUEST["stValorComposto"] ) - 1 ) {
       $stValorComposto = $_REQUEST["stValorComposto"];
       $_REQUEST["stValorComposto"] = substr( $stValorComposto, 0, strlen( $stValorComposto ) -1 );
    }*/

    $obRCIMLocalizacao->setValorComposto( $_REQUEST["stValorComposto"] );
}
if ($_REQUEST["stNomeLocalizacao"]) {
    $obRCIMLocalizacao->setNomeLocalizacao( $_REQUEST["stNomeLocalizacao"] );
}

if ($_REQUEST['stAcao'] == 'reativar') {
    $obRCIMLocalizacao->verificaBaixaLocalizacao( $rsListaLocalizacao );
} else {
    $obRCIMLocalizacao->listarLocalizacao( $rsListaLocalizacao );
    if ( $rsListaLocalizacao->eof() && $_REQUEST["stValorComposto"] ) { //nao encontrou nada, verificar se esta baixado
        $obRCIMLocalizacao->verificaBaixaLocalizacao( $rsListaLocalizacaoBaixa );
        if ( !$rsListaLocalizacaoBaixa->eof()) {
            $stJs = "alertaAviso('@Localização baixada. (".$_REQUEST["stValorComposto"].")','form','erro','".Sessao::getId()."');";

            SistemaLegado::executaFrameOculto($stJs);
        }
    }
}

$obLista = new Lista;
$obLista->setRecordSet( $rsListaLocalizacao );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código ");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Nível" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Nome" );
$obLista->ultimoCabecalho->setWidth( 45 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "valor_composto" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_nivel" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_localizacao" );
$obLista->commitDado();
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $_REQUEST['stAcao'] );
$obLista->ultimaAcao->addCampo("&inCodigoVigencia",    "cod_vigencia"     );
$obLista->ultimaAcao->addCampo("&inCodigoNivel",       "cod_nivel"        );
$obLista->ultimaAcao->addCampo("&stNomeNivel",         "nom_nivel"        );
$obLista->ultimaAcao->addCampo("&inCodigoLocalizacao", "cod_localizacao"  );
$obLista->ultimaAcao->addCampo("&stValorComposto",     "valor_composto"   );
$obLista->ultimaAcao->addCampo("&stValorReduzido",     "valor_reduzido"   );
$obLista->ultimaAcao->addCampo("&stNomeLocalizacao",   "nom_localizacao"  );
$obLista->ultimaAcao->addCampo("&stDescQuestao",       "[valor_composto]-[nom_localizacao]"  );
if ($_REQUEST['stAcao'] == "reativar") {
    $obLista->ultimaAcao->addCampo("&stJustificativa", "justificativa" );
    $obLista->ultimaAcao->addCampo("&stTimeStamp", "timestamp" );
    $obLista->ultimaAcao->addCampo("&stDTInicio", "dt_inicio" );
}

if ($_REQUEST['stAcao'] == "excluir") {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}
$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->setAjuda  ( "UC-05.01.03" );
$obFormulario->show();

?>
