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
* Página de Listagem de Cargos
* Data de Criação   : 08/12/2004

* @author Analista: Leandro Oliveira.
* @author Desenvolvedor: Vandre Miguel Ramos

* @ignore

$Revision: 30857 $
$Name$
$Author: alex $
$Date: 2007-12-13 11:15:36 -0200 (Qui, 13 Dez 2007) $

* Casos de uso: uc-04.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php" );
include_once( CAM_GRH_PES_NEGOCIO."RConfiguracaoPessoal.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterServidor";
$pgFilt      = "FL".$stPrograma.".php";
$pgList      = "LS".$stPrograma.".php";
$pgForm      = "FM".$stPrograma.".php";
$pgProc      = "PR".$stPrograma.".php";
$pgOcul      = "OC".$stPrograma.".php";
$pgJS        = "JS".$stPrograma.".js";

$stCaminho = CAM_GRH_PES_INSTANCIAS."servidor/";

include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

$obRPessoalServidor = new RPessoalServidor;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if (empty($stAcao) or trim($stAcao)=="alterar_servidor") {
    $stAcao = "alterar";
}
//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'alterar': $pgProx = $pgForm; break;
    case 'excluir': $pgProx = $pgProc; break;
    DEFAULT       : $pgProx = $pgForm;
}
//MANTEM FILTRO E PAGINACAO
$stLink .= "&stAcao=".$stAcao."&inAba=".$_REQUEST['inAba']."&inNumCGM=".$_REQUEST["inNumCGM"];
$link = Sessao::read("link");
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
    Sessao::write("link",$link);
}
$rsContrato = new RecordSet;

//MONTA O FILTRO
$obRConfiguracaoPessoal = new RConfiguracaoPessoal();
$obRConfiguracaoPessoal->consultar();
$obRPessoalServidor->addContratoServidor();
$inContrato = $_REQUEST["inContrato"];
$inNumCGM = $_REQUEST["inNumCGM"];
if ( !empty($inNumCGM) ) {
    $obRPessoalServidor->obRCGMPessoaFisica->setNumCGM( $_REQUEST["inNumCGM"] );
}
if ( !empty($inContrato) ) {
    $obRPessoalServidor->roUltimoContratoServidor->setRegistro( $inContrato );
}
$obRPessoalServidor->roUltimoContratoServidor->listarContratos($rsContrato);

$obLista = new Lista;
$obLista->setRecordSet( $rsContrato );

$stTitulo = ' </div></td></tr><tr><td colspan="6" class="alt_dados">Registros';
$obLista->setTitulo             ('<div align="right">'.$obRFolhaPagamentoFolhaSituacao->consultarCompetencia().$stTitulo);

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Matrícula");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Servidor" );
$obLista->ultimoCabecalho->setWidth( 40 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
if ( $obRConfiguracaoPessoal->getContagemInicial() == "dtPosse" ) {
    $obLista->ultimoCabecalho->addConteudo( "Data da Posse" );
} elseif ( $obRConfiguracaoPessoal->getContagemInicial() == "dtNomeacao" ) {
    $obLista->ultimoCabecalho->addConteudo( "Data da Nomeação" );
} else {
    $obLista->ultimoCabecalho->addConteudo( "Data Admissão" );
}
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Situação");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "registro" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_cgm" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
if ( $obRConfiguracaoPessoal->getContagemInicial() == "dtPosse" ) {
    $obLista->ultimoDado->setCampo( "dt_posse" );
} elseif ($obRConfiguracaoPessoal->getContagemInicial() == "dtNomeacao") {
    $obLista->ultimoDado->setCampo( "dt_nomeacao" );
} else {
    $obLista->ultimoDado->setCampo( "dt_admissao" );
}
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "situacao" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo("&inCodContrato"     , "cod_contrato"       );
$obLista->ultimaAcao->addCampo("&inCodServidor"     , "cod_servidor"       );
$obLista->ultimaAcao->addCampo("&inNumCGM"          , "numcgm"             );

if ($stAcao == "excluir") {
    $obLista->ultimaAcao->addCampo("stDescQuestao"  ,"registro");
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink);
} else {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}
$obLista->commitAcao();

$obLista->show();

?>
