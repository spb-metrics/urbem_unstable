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
 * Página de Filtro de Responsavel Licitacao
 * Data de Criação   : 21/01/2015
 * @author Analista: Ane Caroline Fiegenbaum Pereira
 * @author Desenvolvedor: Evandro Melos
 * $Id: $
 * $Name: $
 * $Revision: $
 * $Author: $
 * $Date: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GP_LIC_MAPEAMENTO."TLicitacaoLicitacao.class.php");

$stPrograma = "ManterResponsavelLicitacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stCaminho =  CAM_GPC_TGO."instancias/configuracao/" ;
$_REQUEST['stAcao']= 'alterar';
$stAcao = $_REQUEST['stAcao'];

$stFiltro="";

$pgProx = $pgForm;

$arFiltro = Sessao::read('filtro');
//seta o filtro na sessao e vice-versa
if (isset($_GET['pg'])) {
    $pg=$_GET['pg'];
} else {
    $pg=0;
}
if (isset($_GET['pos'])) {
    $pos=$_GET['pos'];
} else {
    $pos=0;
}

$arLicitacao = "";
$arCGMResponsaveis = "";
Sessao::write('pg',$pg);
Sessao::write('pos',$pos);
Sessao::write("arLicitacao",$arLicitacao);
Sessao::write("arCGMResponsaveis",$arCGMResponsaveis);

if ( !Sessao::read('paginando') ) {
    foreach ($_POST as $stCampo => $stValor) {
        $arFiltro[$stCampo] = $stValor;
    }
    Sessao::write('paginando',true);
}

if ($arFiltro) {
    foreach ($arFiltro as $key => $value) {
        $_REQUEST[$key] = $value;
    }
}

Sessao::write('paginando',true);
Sessao::write('filtro',$arFiltro);

$obTLicitacaoLicitacao = new TLicitacaoLicitacao();

if ($_REQUEST['inCodEntidade']) {
    $inCodEntidade = implode(',',$_REQUEST['inCodEntidade']);
    $obTLicitacaoLicitacao->setDado('cod_entidade',$inCodEntidade);
}

if ($_REQUEST['inCodLicitacao']) {
    $obTLicitacaoLicitacao->setDado('cod_licitacao',$_REQUEST['inCodLicitacao']);
}
if ($_REQUEST['stExercicioLicitacao'])
    $obTLicitacaoLicitacao->setDado('exercicio',$_REQUEST['stExercicioLicitacao']);

if ($_REQUEST['stChaveProcesso']) {
    $arProcesso = explode ('/',$_REQUEST['stChaveProcesso']);
    $obTLicitacaoLicitacao->setDado('cod_processo',$arProcesso[0]);
    $obTLicitacaoLicitacao->setDado('exercicio_processo',$arProcesso[1]);
}

if ($_REQUEST['inPeriodicidade'] != "") {

    if ($_REQUEST['stDataInicial']) {

        $dtDataInicial = $_REQUEST["stDataInicial"];
        $dtDataFinal   = $_REQUEST["stDataFinal"];

        $stFiltro .= "  AND ll.timestamp::DATE BETWEEN TO_DATE('".$dtDataInicial."','dd/mm/yyyy')   \n";
        $stFiltro .= "  AND TO_DATE('".$dtDataFinal."','dd/mm/yyyy')                                   \n";
    }
}

if ($_REQUEST['stMapaCompras']) {
    $arMapa = explode ('/',$_REQUEST['stMapaCompras']);
    $obTLicitacaoLicitacao->setDado('cod_mapa',$arMapa[0]);
    $obTLicitacaoLicitacao->setDado('exercicio_mapa',$arMapa[1]);
}

if ($_REQUEST['inCodTipoLicitacao'])
    $obTLicitacaoLicitacao->setDado('cod_tipo_licitacao',$_REQUEST['inCodTipoLicitacao']);

if ($_REQUEST['inCodModalidade'])
    $obTLicitacaoLicitacao->setDado('cod_modalidade',$_REQUEST['inCodModalidade']);

if ($_REQUEST['inCodCriterio'])
    $obTLicitacaoLicitacao->setDado('cod_criterio',$_REQUEST['inCodCriterio']);

if ($_REQUEST['inCodTipoObjeto'])
    $obTLicitacaoLicitacao->setDado('cod_tipo_objeto',$_REQUEST['inCodTipoObjeto']);

if ($_REQUEST['HdnstObjeto'])
    $obTLicitacaoLicitacao->setDado('cod_objeto',$_REQUEST['HdnstObjeto']);

$stOrder = "
                ORDER BY
                        ll.exercicio DESC,
                        ll.cod_entidade,
                        ll.cod_licitacao,
                        ll.cod_modalidade
";

if ($stAcao == 'consultar') {
    Sessao::write('consulta', true);
}

$obTLicitacaoLicitacao->recuperaLicitacaoResponsavelTCMGO($rsLicitacao,$stFiltro, $stOrder);

$stFiltro = $stLink = "";

$stLink .= "&stAcao=".$stAcao;

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsLicitacao );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Licitação" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Entidade");
$obLista->ultimoCabecalho->setWidth( 35 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Processo");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Modalidade");
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Ação");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "[cod_licitacao]/[exercicio]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "entidade" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "processo" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[cod_modalidade] - [descricao]" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo( "&stEntidade","entidade");
$obLista->ultimaAcao->addCampo( "&stProcesso","processo");
$obLista->ultimaAcao->addCampo( "&stMapaCompra","mapa_compra");
$obLista->ultimaAcao->addCampo( "&inCodLicitacao","cod_licitacao");
$obLista->ultimaAcao->addCampo( "&stModalidade","modalidade");
$obLista->ultimaAcao->addCampo( "&stCodObjeto","cod_objeto");
$obLista->ultimaAcao->addCampo( "&inCodTipoObjeto","cod_tipo_objeto");
$obLista->ultimaAcao->addCampo( "&inCodComissao","cod_comissao");
$obLista->ultimaAcao->addCampo( "&inCodTipoLicitacao","cod_tipo_licitacao");
$obLista->ultimaAcao->addCampo( "&inCodCriterio","cod_criterio");
$obLista->ultimaAcao->addCampo( "&vlCotado","vl_cotado");
$obLista->ultimaAcao->addCampo( "&stExercicioLicitacao","exercicio");
$obLista->ultimaAcao->addCampo( "&inCodModalidade","cod_modalidade");
$obLista->ultimaAcao->addCampo( "&inCodEntidade","cod_entidade");

$obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );

$obLista->commitAcao();
$obLista->Show();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
