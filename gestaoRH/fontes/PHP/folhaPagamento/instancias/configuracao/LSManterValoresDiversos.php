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
    * Arquivo de Lista
    * Data de Criação: 31/10/2007

    * @author Diego Lemos de Souza

    * Casos de uso: uc-04.05.22

    $Id: LSManterValoresDiversos.php 59612 2014-09-02 12:00:51Z gelson $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      				);

//Define o nome dos arquivos PHP
$stPrograma = "ManterValoresDiversos";
$pgFilt      = "FL".$stPrograma.".php";
$pgList      = "LS".$stPrograma.".php";
$pgForm      = "FM".$stPrograma.".php";
$pgProc      = "PR".$stPrograma.".php";
$pgOcul      = "OC".$stPrograma.".php";
$pgJS        = "JS".$stPrograma.".js";

$stCaminho = CAM_GRH_FOL_INSTANCIAS."configuracao/";

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//Define a ação
$stAcao = $request->get('stAcao');

//Define a página
$pgProx = $pgForm;

//MANTEM FILTRO E PAGINACAO
$stLink .= "&stAcao=".$stAcao."&inAba=".$_REQUEST['inAba']."&inNumCGM=".$_REQUEST["inNumCGM"];
if ($_GET["pg"] and  $_GET["pos"]) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
    #sessao->link["pg"]  = $_GET["pg"];
    #sessao->link["pos"] = $_GET["pos"];
    $link["pg"]  = $_GET["pg"];
    $link["pos"] = $_GET["pos"];
}
Sessao::write('link', $link);
//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array(Sessao::read("link")) ) {
    $_REQUEST = Sessao::read("link");
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
        #sessao->link[$key] = $valor;
    }
    Sessao::write('link', $link);
}

$rsRegistros = new RecordSet();

include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoValorDiversos.class.php");
$obTFolhaPagamentoValorDiversos = new TFolhaPagamentoValorDiversos();
$stFiltro = "";
if ($_POST["inCodigo"] != "") {
    $stFiltro = "AND valor_diversos.cod_valor = ".$_POST["inCodigo"];
}
if (trim($_POST["stDescricao"])) {
    $stFiltro .= "AND valor_diversos.descricao ilike '%".trim($_POST["stDescricao"])."%'";
}
$stFiltro .= " AND ativo IS TRUE ";
$obTFolhaPagamentoValorDiversos->recuperaRelacionamento($rsRegistros,$stFiltro," ORDER BY cod_valor");

$obLista = new Lista;
$obLista->setRecordSet( $rsRegistros );
$stTitulo = ' </div></td></tr><tr><td colspan="8" class="alt_dados">Lista de Valores Diversos';
$obLista->setTitulo             ('<div align="right">'.$obRFolhaPagamentoFolhaSituacao->consultarCompetencia().$stTitulo);

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Código" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição" );
$obLista->ultimoCabecalho->setWidth( 70 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "cod_valor" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao ( $stAcao );
$obLista->ultimaAcao->addCampo( "&inCodigo"              , "cod_valor" );
$obLista->ultimaAcao->addCampo( "&stDescricao"           , "descricao" );
$obLista->ultimaAcao->addCampo( "&nuValor"               , "valor" );
$obLista->ultimaAcao->addCampo( "&dataVigencia"          , "data_vigencia" );
if ($stAcao == "excluir") {
    $obLista->ultimaAcao->addCampo( "&stDescQuestao"      , "Confirme a exclusão do valor diverso [descricao]");
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProc."?".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->setLink( $pgForm."?".Sessao::getId().$stLink );
}
$obLista->commitAcao();
$obLista->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
