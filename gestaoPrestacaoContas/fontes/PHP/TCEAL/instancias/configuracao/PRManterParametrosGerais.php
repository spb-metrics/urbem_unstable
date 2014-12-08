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

    * Página de Formulario de Ajustes Gerais Exportacao - TCE-AL
    * Data de Criação   : 11/07/2006

    * @author Analista: 
    * @author Desenvolvedor: 

    * @ignore

    * $Revision: 57368 $
    * $Name$
    * $Author: diogo.zarpelon $
    * $Date: 2014-02-28 14:23:28 -0300 (Fri, 28 Feb 2014) $
    
    * $id:  

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GPC_TCEAL_MAPEAMENTO."TTCEALExportacaoConfiguracao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterParametrosGerais";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obTExportacao = new TTCEALExportacaoConfiguracao();

$obErro = new Erro;

$obTransacao = new Transacao;
$obTransacao->begin();
$boTransacao = $obTransacao->getTransacao();

$obTExportacao->setDado("cod_modulo",62);
$obTExportacao->setDado("exercicio",  Sessao::getExercicio());
$obTExportacao->setDado("parametro", "tceal_orgao_prefeitura" );
$obTExportacao->setDado( "valor", $_POST['inCodExecutivo'] );
$obErro = $obTExportacao->recuperaPorChave( $rsRecordSet, $boTransacao );
$obErro = $obTExportacao->alteracao( $boTransacao );

$obTExportacao->setDado("cod_modulo",62);
$obTExportacao->setDado("exercicio",  Sessao::getExercicio());
$obTExportacao->setDado("parametro", "tceal_unidade_prefeitura" );
$obTExportacao->setDado( "valor", $_POST['inCodUnidadeExecutivo'] );
$obErro = $obTExportacao->recuperaPorChave( $rsRecordSet, $boTransacao );
$obErro = $obTExportacao->alteracao( $boTransacao );
        
$obTExportacao->setDado("cod_modulo",62);
$obTExportacao->setDado("exercicio",  Sessao::getExercicio());
$obTExportacao->setDado("parametro", "tceal_orgao_camara" );
$obTExportacao->setDado( "valor", $_POST['inCodLegislativo'] );
$obErro = $obTExportacao->recuperaPorChave( $rsRecordSet, $boTransacao );
$obErro = $obTExportacao->alteracao( $boTransacao );
        
$obTExportacao->setDado("cod_modulo",62);
$obTExportacao->setDado("exercicio",  Sessao::getExercicio());
$obTExportacao->setDado("parametro", "tceal_unidade_camara" );
$obTExportacao->setDado( "valor", $_POST['inCodUnidadeLegislativo'] );
$obErro = $obTExportacao->recuperaPorChave( $rsRecordSet, $boTransacao );
$obErro = $obTExportacao->alteracao( $boTransacao );
        
$obTExportacao->setDado("cod_modulo",62);
$obTExportacao->setDado("exercicio",  Sessao::getExercicio());
$obTExportacao->setDado("parametro", "tceal_orgao_rpps" );
$obTExportacao->setDado( "valor", ($_POST['inCodRPPS']?$_POST['inCodRPPS']:$_POST['inCodExecutivo']) );
$obErro = $obTExportacao->recuperaPorChave( $rsRecordSet, $boTransacao );
$obErro = $obTExportacao->alteracao( $boTransacao );
        
$obTExportacao->setDado("cod_modulo",62);
$obTExportacao->setDado("exercicio",  Sessao::getExercicio());
$obTExportacao->setDado("parametro", "tceal_unidade_rpps" );
$obTExportacao->setDado( "valor", ($_POST['inCodUnidadeRPPS']?$_POST['inCodUnidadeRPPS']:$_POST['inCodUnidadeExecutivo']) );
$obErro = $obTExportacao->recuperaPorChave( $rsRecordSet, $boTransacao );
$obErro = $obTExportacao->alteracao( $boTransacao );
        
$obTExportacao->setDado("cod_modulo",62);
$obTExportacao->setDado("exercicio",  Sessao::getExercicio());
$obTExportacao->setDado("parametro", "tceal_orgao_outros" );
$obTExportacao->setDado( "valor", ($_POST['inCodOutros']?$_POST['inCodOutros']:$_POST['inCodExecutivo']) );
$obErro = $obTExportacao->recuperaPorChave( $rsRecordSet, $boTransacao );
$obErro = $obTExportacao->alteracao( $boTransacao );
        
$obTExportacao->setDado("cod_modulo",62);
$obTExportacao->setDado("exercicio",  Sessao::getExercicio());
$obTExportacao->setDado("parametro", "tceal_unidade_outros" );
$obTExportacao->setDado( "valor", ($_POST['inCodUnidadeOutros']?$_POST['inCodUnidadeOutros']:$_POST['inCodUnidadeExecutivo']) );
$obErro = $obTExportacao->recuperaPorChave( $rsRecordSet, $boTransacao );
$obErro = $obTExportacao->alteracao( $boTransacao );

if ( !$obErro->ocorreu() ) {
    $obErro = $obTransacao->commitAndClose();
} else {
    $obTransacao->rollbackAndClose();
}

if ( !$obErro->ocorreu() ) {
    SistemaLegado::alertaAviso($pgForm,"parâmetros atualizados", "incluir", "aviso", Sessao::getId(), "../");
} else {
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
}

?>
