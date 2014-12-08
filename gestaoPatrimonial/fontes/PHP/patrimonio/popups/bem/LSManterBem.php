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
 * Data de Criação: 13/09/2007

 * @author Analista: Gelson W. Gonçalves
 * @author Desenvolvedor: Henrique Boaventura

 * @package URBEM
 * @subpackage

 * Casos de uso: uc-03.01.06

 $Id: LSManterBem.php 59612 2014-09-02 12:00:51Z gelson $

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioBem.class.php";

$stPrograma = "ManterBem";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

//inclui o arquivo de js
include_once( $pgJs );

//seta o caminho para a popup de exclusao
$stCaminho = CAM_GP_PAT_INSTANCIAS."bem/";

$arFiltro = Sessao::read('filtro');

//seta o filtro na sessao e vice-versa
if ( !Sessao::read('paginando') ) {
    foreach ($_POST as $stCampo => $stValor) {
        $arFiltro[$stCampo] = $stValor;
    }
    Sessao::write('pg',($_GET['pg'] ? $_GET['pg'] : 0));
    Sessao::write('pos',($_GET['pos']? $_GET['pos'] : 0));
    Sessao::write('paginando',true);
} else {
    Sessao::write('pg',$_GET['pg']);
    Sessao::write('pos',$_GET['pos']);
}

if ($arFiltro) {
    foreach ($arFiltro as $key => $value) {
        $_REQUEST[$key] = $value;
    }
} else {
    $_GET['pg'] = Sessao::read('pg');
    $_GET['pos'] = Sessao::read('pos');
}

Sessao::write('paginando',true);
Sessao::write('filtro',$arFiltro);

if ($_REQUEST['inCodBem'] != '') {
    $stFiltro .= " AND bem.cod_bem = ".$_REQUEST['inCodBem']." ";
}
if ($_REQUEST['inCodNatureza'] != '') {
    $stFiltro .= " AND bem.cod_natureza = ".$_REQUEST['inCodNatureza']." ";
}
if ($_REQUEST['inCodGrupo'] != '') {
    $stFiltro .= " AND bem.cod_grupo = ".$_REQUEST['inCodGrupo']." ";
}
if ($_REQUEST['inCodEspecie'] != '') {
    $stFiltro .= " AND bem.cod_especie = ".$_REQUEST['inCodEspecie']." ";
}
if ($_REQUEST['inCodEspecie'] != '') {
    $stFiltro .= " AND bem.cod_especie = ".$_REQUEST['inCodEspecie']." ";
}
if ($_REQUEST['stHdnNomBem'] != '') {
    $stFiltro .= " AND bem.descricao LIKE '".$_REQUEST['stHdnNomBem']."' ";
}
if ($_REQUEST['stPlacaIdentificacao'] == 'nao') {
    $stFiltro .= " AND bem.num_placa IS NULL AND ";
}
if ($_REQUEST['stHdnNumeroPlaca'] != '') {
    $stFiltro .= " AND bem.num_placa LIKE '".$_REQUEST['stHdnNumeroPlaca']."' ";
}
if ($_REQUEST['boBemBaixado'] == 'false') {
    $stFiltro .= " AND NOT EXISTS ( SELECT 1
                                      FROM patrimonio.bem_baixado
                                     WHERE bem_baixado.cod_bem = bem.cod_bem
                                  )
    ";
}

if (preg_match('/stNomBemOrgao/', $_REQUEST['stCampoNom'])) {
    $arCampoBem = explode('_', $_REQUEST['stCampoNom']);

    $inCodOrgao = $arCampoBem[1];
    $inCodLocal = $arCampoBem[2];

    $stFiltro .= " AND EXISTS ( SELECT 1 FROM patrimonio.historico_bem AS hist_bem WHERE timestamp = ( SELECT MAX(timestamp) FROM patrimonio.historico_bem WHERE historico_bem.cod_bem = hist_bem.cod_bem) AND hist_bem.cod_local = ".$inCodLocal." AND hist_bem.cod_orgao = ".$inCodOrgao." AND hist_bem.cod_bem = bem.cod_bem ) ";
}

if ($_REQUEST['stOrdenacao'] == 'codigo') {
    $stOrder = ' ORDER BY  bem.cod_bem ';
} else {
    $stOrder = ' ORDER BY bem.descricao ';
}

if ($stFiltro != '') {
    $stFiltro = ' WHERE '.substr($stFiltro,4);
}

$obTPatrimonioBem = new TPatrimonioBem();
$obTPatrimonioBem->recuperaTodos( $rsBem, $stFiltro, $stOrder );

//instancia uma nova lista
$obLista = new Lista;
$stLink .= "&stAcao=".$stAcao;

$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsBem );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Classificação" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Código" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Número da Placa" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição" );
$obLista->ultimoCabecalho->setWidth( 50 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Ação" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[cod_natureza].[cod_grupo].[cod_especie]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_bem" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "num_placa" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->commitDado();

$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:insere( '".$_REQUEST['stCampoNum']."','".$_REQUEST['stCampoNom']."' );" );
$obLista->ultimaAcao->addCampo("1","cod_bem");
$obLista->ultimaAcao->addCampo("2","descricao");

$obLista->commitAcao();
$obLista->show();

?>
