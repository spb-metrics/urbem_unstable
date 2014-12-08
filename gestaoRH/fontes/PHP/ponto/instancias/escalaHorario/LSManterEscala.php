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
    * Página de Lista para Manter Escalas
    * Data de Criação: 02/10/2008

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: <Alex Cardoso>

    * @ignore

    $Id: $

    * Casos de uso: uc-04.10.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GRH_PON_MAPEAMENTO."TPontoEscala.class.php"                                   );

//Define o nome dos arquivos PHP
$stPrograma = "ManterEscala";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$stCaminho  = CAM_GRH_PON_INSTANCIAS."escalaHorario/";

include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                         );
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//Mantem filtro e paginacao
$link = Sessao::read("link");
if ($_GET["pg"] and  $_GET["pos"]) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
    $link["pg"]  = $_GET["pg"];
    $link["pos"] = $_GET["pos"];
    Sessao::write("link",$link);
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

$stAcao = $request->get('stAcao');
$stLink .= "&stAcao=".$stAcao;

//Monta o filtro
$stFiltro = "";
if ($_REQUEST['inCodEscala']) {
    $stFiltro .= " and cod_escala = ".$_REQUEST['inCodEscala'];
}
if ($_REQUEST['stDescricaoEscala']) {
    $stFiltro .= " and descricao like '".$_REQUEST['stDescricaoEscala']."%'";
}

$rsEscalas       = new RecordSet();
$obTPontoEscalas = new TPontoEscala();
$obTPontoEscalas->recuperaEscalasAtivas($rsEscalas, $stFiltro, " ORDER BY cod_escala ASC ");

$obLista = new Lista;
$obLista->setRecordSet( $rsEscalas );

$obLista->setTitulo('<div align="right">'.$obRFolhaPagamentoFolhaSituacao->consultarCompetencia());

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição" );
$obLista->ultimoCabecalho->setWidth( 70 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTER");
$obLista->ultimoDado->setCampo( "cod_escala" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->commitDado();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 8 );
$obLista->commitCabecalho();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo( "&inCodEscala", "cod_escala" );
$obLista->ultimaAcao->addCampo( "&stDescQuestao", "[cod_escala] - [descricao]" );

if ($stAcao == "excluir") {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProc."?".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgForm."?".Sessao::getId().$stLink );
}

$obLista->commitAcao();

$obLista->show();
?>
