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
    * Interface de processamento da Subfunção Orçamentátia
    * Funções orçamentárias que fazem parte da classificação funcional-programática da despesa
    * Data de Criação   : 15/07/2004

    * @author Analista: Jorge B.
    * @author Desenvolvedor: Roberto Pawelski Rodrigues

    * $Id: PRSubFuncao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.01.03
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include CAM_FW_INCLUDE . 'cabecalho.inc.php';
include CAM_GF_ORC_NEGOCIO . 'ROrcamentoSubfuncao.class.php';
include CAM_GF_PPA_NEGOCIO . 'RPPAManterPPA.class.php';

/**
* Define o nome dos arquivos PHP
*/
$stPrograma = "SubFuncao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

$obROrcamentoSubfuncao = new ROrcamentoSubfuncao;

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obErro = new Erro();

//recupera os exercicios do ppa para propagar o recurso pelos exercicios do ppa
$obRPPAManterPPA = new RPPAManterPPA();
$obRPPAManterPPA->stExercicio = Sessao::getExercicio();
$obRPPAManterPPA->listByExercicio($rsRecordSet);

$stExercicio      = (int) Sessao::getExercicio();
$stExercicioFinal = (int) $rsRecordSet->getCampo('ano_final');

switch ($stAcao) {

    case "incluir":
    /**
    * Seta valores da Subfunção a ser incluída
    */
    $obROrcamentoSubfuncao->setCodigoSubFuncao($_POST['inNumeroSubFuncao']);
    $obROrcamentoSubfuncao->setMascara        ($_POST['stMascara']);
    $obROrcamentoSubfuncao->setDescricao      ($_POST['stDescricao']);

    //faz o insert para cada ano até o ano_final do ppa
    for ($stExercicio; $stExercicio <= $stExercicioFinal; $stExercicio++) {
        if (!$obErro->ocorreu()) {
            $obROrcamentoSubfuncao->setExercicio     ($stExercicio);
            $obErro = $obROrcamentoSubfuncao->incluir($boTransacao);
        }
    }

    if ( !$obErro->ocorreu() ) {
        SistemaLegado::alertaAviso($pgForm,"Subfunção - ".$_POST['stDescricao'],"incluir","aviso", Sessao::getId(), "../");
    } else {
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
    }
    break;

    case "alterar":
    /**
    * Seta valores da Subfunção a ser alterada
    */
    $obROrcamentoSubfuncao->setCodigoSubFuncao  ($_POST['inCodigoSubFuncao']);
    $obROrcamentoSubfuncao->setMascara          ($_POST['stMascara']);
    $obROrcamentoSubfuncao->setDescricao        ($_POST['stDescricao']);

    //faz o insert para cada ano até o ano_final do ppa
    for ($stExercicio; $stExercicio <= $stExercicioFinal; $stExercicio++) {
        if (!$obErro->ocorreu()) {
            $obROrcamentoSubfuncao->setExercicio     ($stExercicio);
            $obErro = $obROrcamentoSubfuncao->alterar($boTransacao);
        }
    }

    $stFiltro = "";
    $arFiltro = Sessao::read('filtro');
    foreach ($arFiltro as $stCampo => $stValor) {
        $stFiltro .= $stCampo."=".urlencode( $stValor )."&";
    }
    $stFiltro .= "pg=".Sessao::read('pg')."&";
    $stFiltro .= "pos=".Sessao::read('pos')."&";
    $stFiltro .= "stAcao=".$_REQUEST['stAcao'];

    if ( !$obErro->ocorreu() ) {
        SistemaLegado::alertaAviso($pgList."?".$stFiltro, "Subfunção ".$_POST['inCodigoSubFuncao']." - ".$_POST['stDescricao'],"alterar","aviso", Sessao::getId(), "../");
    } else {
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
    }
    break;

    case "excluir";
    /**
    * Seta valores da Subfunção a ser excluída
    */
    $obROrcamentoSubfuncao->setCodigoSubFuncao($_GET['inCodigoSubFuncao']);
    $obROrcamentoSubfuncao->setDescricao      ($_GET['stDescricao']);

    //faz o insert para cada ano até o ano_final do ppa
    for ($stExercicio; $stExercicio <= $stExercicioFinal; $stExercicio++) {
        if (!$obErro->ocorreu()) {
            $obROrcamentoSubfuncao->setExercicio     ($stExercicio);
            $obErro = $obROrcamentoSubfuncao->excluir($boTransacao);
        }
    }

    $stFiltro = "";
    $arFiltro = Sessao::read('filtro');
    foreach ($arFiltro as $stCampo => $stValor) {
        $stFiltro .= $stCampo."=".urlencode( $stValor )."&";
    }
    $stFiltro .= "pg=".Sessao::read('pg')."&";
    $stFiltro .= "pos=".Sessao::read('pos')."&";
    $stFiltro .= "stAcao=".$_REQUEST['stAcao'];

    if (!$obErro->ocorreu()) {
        SistemaLegado::alertaAviso($pgList."?stAcao=excluir&".$stFiltro, "Subfunção ".$_GET['inCodigoSubFuncao']." - ".$_GET['stDescQuestao'],"excluir","aviso", Sessao::getId(), "../");
    } else {
        SistemaLegado::alertaAviso($pgList."?stAcao=excluir&pg=".$_GET["pg"]."&pos=".$_GET["pos"], urlencode($obErro->getDescricao()),"n_excluir","erro", Sessao::getId(), "../");
    }
    break;

}
?>
