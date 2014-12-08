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
    * Lista de Contratos.
    * Data de Criação: 05/03/2008

    * @author Alexandre Melo

    * Casos de uso: uc-02.03.37

    $Id: LSManterVinculoEmpenhoContrato.php 59612 2014-09-02 12:00:51Z gelson $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_LIC_MAPEAMENTO."TLicitacaoContrato.class.php"									   );

//Define o nome dos arquivos PHP
$stPrograma = "ManterVinculoEmpenhoContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stAcao = $request->get('stAcao');

//Prepara o filtro para pesquisa
$stFiltro .= " AND contrato.exercicio = '".$_REQUEST['inExercicio']."'\n";
$stFiltro .= " AND contrato.cod_entidade IN (";
if(is_array($_REQUEST['inCodEntidade'])){
    foreach ($_REQUEST['inCodEntidade'] as $value) {
        $stEntidades .= $value.", ";
    }
}else{
    $inCodEntidade = explode(",", $_REQUEST['inCodEntidade']);
    for($i=0;$i<count($inCodEntidade);$i++){
        $stEntidades .= $inCodEntidade[$i].", ";    
    }
}
$stFiltro .= substr($stEntidades,0,strlen($stEntidades)-2).") \n";

if ($_REQUEST['inNumContrato']) {
    $stFiltro .= " AND contrato.num_contrato = '".$_REQUEST['inNumContrato']."'"."\n";
}
$stOrdem = "ORDER BY nom_credor";

//Efetua pesquisa para a lista
$obTLicitacaoContrato = new TLicitacaoContrato;
$obTLicitacaoContrato->recuperaDadosContrato($rsRecordset, $stFiltro, $stOrdem);
//********************************************************************//

//Define a paginacao
if ( !Sessao::read('paginando') ) {
    $arFiltro = array();
    foreach ($_REQUEST as $stCampo => $stValor) {
        $arFiltro[$stCampo] = $stValor;
    }
    Sessao::write('filtro', $arFiltro);
    Sessao::write('pg', $_GET['pg'] ? $_GET['pg'] : 0);
    Sessao::write('pos', $_GET['pos']? $_GET['pos'] : 0);
    Sessao::write('paginando', true);
} else {
    Sessao::write('pg', $_GET['pg']);
    Sessao::write('pos', $_GET['pos']);
}
//********************************************************************//

$stLink .= "&stAcao=".$stAcao;
if ($_GET["pg"] and  $_GET["pos"]) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
}

//GERA LISTA
$obLista = new Lista;

//Cabecalho
$obLista->setRecordSet( $rsRecordset );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Entidade");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Contrato");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Credor");
$obLista->ultimoCabecalho->setWidth( 25 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

//Itens
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_entidade" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[num_contrato]/[exercicio]" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_credor" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo( "&inNumContrato" , "num_contrato"  );
$obLista->ultimaAcao->addCampo( "&inCodEntidade" , "cod_entidade"  );
$obLista->ultimaAcao->addCampo( "&inExercicio"   , "exercicio"     );
$obLista->ultimaAcao->addCampo( "&stNomEntidade" , "nom_entidade"  );
$obLista->ultimaAcao->addCampo( "&stNomCredor"   , "nom_credor"    );
$obLista->ultimaAcao->addCampo( "&dtAssinatura"	 , "dt_assinatura" );
$obLista->ultimaAcao->addCampo( "&cgm_contratado", "cgm_contratado");

if ($stAcao == "selecionar") {
    $pgProx = CAM_GF_EMP_INSTANCIAS."empenho/FMManterVinculoEmpenhoContrato.php";
}
$obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );

$obLista->commitAcao();
$obLista->show();

?>
