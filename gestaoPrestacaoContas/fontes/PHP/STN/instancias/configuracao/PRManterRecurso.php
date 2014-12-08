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
    * Página de Processamento para Configuração
    * Data de Criação  : 07/05/2008

    * @author Analista Tonismar Regis Bernardo
    * @author Desenvolvedor Leopoldo Braga Barreiro

    * @package URBEM
    * @subpackage

    * $Id: PRManterRecurso.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso : uc-06.01.09
    *
* */

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GPC_STN_MAPEAMENTO."TSTNVinculoRecurso.class.php";
require_once CAM_GF_ORC_MAPEAMENTO . "TOrcamentoRecurso.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterRecurso";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

// a acao passada pelo menu é igual ao cod_vinculo referente ao que se pede no meu
$inCodVinculo = $stAcao;
$inCodUnidades = array();

if ($_REQUEST['inCodUnidade'] == 0) {
    $inCodUnidades = explode(",", $_REQUEST['inCodigosUnidade']);
} else {
    array_push($inCodUnidades, $_REQUEST['inCodUnidade']);
}

Sessao::setTrataExcecao ( true );
foreach ($inCodUnidades as $inTmpCodUnidade) {
    $obTSTNVinculoRecurso = new TSTNVinculoRecurso();
    $obTSTNVinculoRecurso->setDado('exercicio'   , Sessao::getExercicio());
    $obTSTNVinculoRecurso->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
    $obTSTNVinculoRecurso->setDado('num_orgao'   , $_REQUEST['inCodOrgao']);
    $obTSTNVinculoRecurso->setDado('num_unidade' , $inTmpCodUnidade);
    $obTSTNVinculoRecurso->setDado('cod_vinculo' , $inCodVinculo);
    $obTSTNVinculoRecurso->excluirVinculoRecurso();

    if (is_array($_REQUEST['inCodRecursoSelecionado'])) {
        foreach ($_REQUEST['inCodRecursoSelecionado'] as $inCodRecurso) {
            $obTSTNVinculoRecurso->setDado('cod_recurso' , $inCodRecurso);
            $obTSTNVinculoRecurso->setDado('cod_vinculo' , $inCodVinculo);
        $obTSTNVinculoRecurso->setDado('cod_tipo' , 2);
            $obTSTNVinculoRecurso->inclusao();
        }
    }

    if (is_array($_REQUEST['inCodRecursoSelecionado2'])) {
        foreach ($_REQUEST['inCodRecursoSelecionado2'] as $inCodRecurso) {
            $obTSTNVinculoRecurso->setDado('cod_recurso' , $inCodRecurso);
            $obTSTNVinculoRecurso->setDado('cod_vinculo' , $inCodVinculo);
        $obTSTNVinculoRecurso->setDado('cod_tipo' , 1);
            $obTSTNVinculoRecurso->inclusao();
        }
    }
}

SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=$stAcao","Configuração","incluir","incluir_n", Sessao::getId(), "../");

Sessao::encerraExcecao();
?>
