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
    * Arquivo de processamento para vincular e/ou desvincular o contrato aos empenhos.
    * Data de Criação: 05/03/2008

    * @author Alexandre Melo

    * Casos de uso: uc-02.03.37

    $Id: PRManterVinculoEmpenhoContrato.php 59612 2014-09-02 12:00:51Z gelson $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_EMP_MAPEAMENTO.'TEmpenhoEmpenhoContrato.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterVinculoEmpenhoContrato";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

switch ($stAcao) {
    case "incluir":

        $obErro = new Erro;
        $obTEmpenhoEmpenhoContrato = new TEmpenhoEmpenhoContrato();

        $inCodEntidade = Sessao::read('inCodEntidade');

        //Exclui os itens que foram retirados da lista
        if (Sessao::read('elementos_excluidos') != "") {
            $arElementosExcluidosSessao = Sessao::read('elementos_excluidos');
            foreach ($arElementosExcluidosSessao AS $arElementosExcluidosTMP) {
                $obTEmpenhoEmpenhoContrato->setDado('cod_empenho', $arElementosExcluidosTMP['cod_empenho']);
                $obTEmpenhoEmpenhoContrato->setDado('cod_entidade', $inCodEntidade);
                $obTEmpenhoEmpenhoContrato->setDado('exercicio', $arElementosExcluidosTMP['exercicio']);
                $obTEmpenhoEmpenhoContrato->recuperaTodos($rsEmpenhos);
                if ($rsEmpenhos->getNumLinhas() > 0) {
                    $obTEmpenhoEmpenhoContrato->setDado( "cod_entidade" , $rsEmpenhos->getCampo('cod_entidade'));
                       $obTEmpenhoEmpenhoContrato->setDado( "cod_empenho"  , $rsEmpenhos->getCampo('cod_empenho'));
                       $obTEmpenhoEmpenhoContrato->setDado( "exercicio"    , $rsEmpenhos->getCampo('exercicio'));
                       $obErro = $obTEmpenhoEmpenhoContrato->exclusao();
                }
            }
        }

        //Inclui os itens da lista de empenhos
        if (Sessao::read('elementos') != "") {
            $rsEmpenhos  = new RecordSet;
            $arElementos = Sessao::read('elementos');

            foreach ($arElementos AS $arElementosTMP) {
                $obTEmpenhoEmpenhoContrato->setDado('cod_empenho', $arElementosTMP['cod_empenho']);
                $obTEmpenhoEmpenhoContrato->setDado('cod_entidade', $inCodEntidade);
                $obTEmpenhoEmpenhoContrato->setDado('exercicio', $arElementosTMP['exercicio']);
                $obTEmpenhoEmpenhoContrato->recuperaTodos($rsEmpenhos);

                if ($rsEmpenhos->getNumLinhas() < 0) {
                    $dtEmpenho = $arElementosTMP['dt_empenho'];
                    $dtContrato = Sessao::read('dtContrato');
                    if (sistemaLegado::comparaDatas($dtContrato, $dtEmpenho)) {
                        $obErro->setDescricao("Data do empenho deve ser maior ou igual a data do Contrato!");
                    } else {
                           $obTEmpenhoEmpenhoContrato->setDado( "exercicio"    , $arElementosTMP['exercicio']);
                        $obTEmpenhoEmpenhoContrato->setDado( "cod_entidade" , $inCodEntidade);
                        $obTEmpenhoEmpenhoContrato->setDado( "cod_empenho"  , $arElementosTMP['cod_empenho']);
                        $obTEmpenhoEmpenhoContrato->setDado( "num_contrato" , Sessao::read('inNumContrato'));
                        $obErro = $obTEmpenhoEmpenhoContrato->inclusao();
                    }
                }
            }

        }
        if (Sessao::read('elementos_excluidos') == "" and Sessao::read('elementos') == "") {
            $obErro->setDescricao( "Efetue a inclusão e/ou exclusão de empenho referente ao contrato relacionado!" );
        }
        if (!$obErro->ocorreu()) {
            sistemaLegado::alertaAviso($pgFilt,"Contrato: ".Sessao::read('inNumContrato'),"incluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }

        break;
}

?>
