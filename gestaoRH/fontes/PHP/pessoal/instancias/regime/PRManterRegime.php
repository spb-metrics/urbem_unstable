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
    * Página de Processamento de Pessoal Regime
    * Data de Criação   : 22/04/2005

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Vandré Miguel Ramos

    * @ignore

    $Revision: 30978 $
    $Name$
    $Author: vandre $
    $Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

    Caso de uso: uc-04.04.05

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GRH_PES_NEGOCIO."RPessoalRegime.class.php");

$arLink = Sessao::read('link');
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
$stLink = "&pg=".$arLink["pg"]."&pos=".$arLink["pos"]."&stAcao=".$stAcao;

//Define o nome dos arquivos PHP
$stPrograma = "ManterRegime";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao".$stLink;
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php";

$obRPessoalRegime = new RPessoalRegime;

switch ($stAcao) {
    case "incluir":
        $arSubDivisao = Sessao::read('subDivisao');
        if ($arSubDivisao) {
            $obRPessoalRegime->setCodRegime( $_POST['inCodRegime'] );
            foreach ($arSubDivisao as $arSubDivisao) {
                $obRPessoalRegime->addPessoalSubDivisao();
                $obRPessoalRegime->roUltimoPessoalSubDivisao->setDescricao($arSubDivisao['descricao']);
            }
            $obErro = $obRPessoalRegime->incluirRegime($boTransacao);
            if ( !$obErro->ocorreu() ) {
                $stDescricao = ( $_POST['inCodRegime'] == 1 )? "CLT" : "RJU";
                sistemaLegado::alertaAviso($pgForm,"Regime: ".$stDescricao,"incluir","aviso", Sessao::getId(), "../");
            } else {
                sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }
        } else {
            sistemaLegado::exibeAviso("O Regime deve possuir no mínimo uma subdivisão cadastrada!"," "," ");
        }
    break;

    case "alterar":
        $arSubDivisao = Sessao::read('subDivisao');
        if ($arSubDivisao) {
            $obRPessoalRegime->setCodRegime              ($_REQUEST['inCodRegime']);
            foreach ($arSubDivisao as $arSubDivisao) {
                $obRPessoalRegime->addPessoalSubDivisao();
                $obRPessoalRegime->roUltimoPessoalSubDivisao->setDescricao($arSubDivisao['descricao']);
                $obRPessoalRegime->roUltimoPessoalSubDivisao->setCodSubDivisao($arSubDivisao['inCodSubDivisao']);
            }
            $obErro = $obRPessoalRegime->alterarRegime($boTransacao);

            if ( !$obErro->ocorreu() ) {
                $stDescricao = ( $_POST['inCodRegime'] == 1 )? "CLT" : "RJU";
                sistemaLegado::alertaAviso($pgList,"Regime: ".$stDescricao,"alterar","aviso", Sessao::getId(), "../");
            } else {
                sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
            }
        } else {
            sistemaLegado::exibeAviso("O Regime deve possuir no mínimo uma subdivisão cadastrada!"," "," ");
        }
    break;

    case "excluir";
        if ($_REQUEST['inCodRegime'] == 1 || $_REQUEST['inCodRegime']== 2) {
           sistemaLegado::exibeAviso("O Regime (".$_REQUEST['stDescricaoRegime'].") não pode ser excluído por ser um registro interno do sistema!"," "," ");
        } else {
            $obRPessoalRegime->setCodRegime( $_REQUEST['inCodRegime'] );
            $obRPessoalRegime->addPessoalSubDivisao();
            $obRPessoalRegime->roUltimoPessoalSubDivisao->listarSubDivisao($rsSubDivisao,$stFiltro='',$boTransacao);

            while (!$rsSubDivisao->eof()) {
               $obRPessoalRegime->roUltimoPessoalSubDivisao->setCodSubDivisao($rsSubDivisao->getCampo('cod_sub_divisao'));
               if ( $rsSubDivisao->proximo() ) {
                    $obRPessoalRegime->addPessoalSubDivisao();
               }
            }

            $obErro = $obRPessoalRegime->excluirRegime($boTransacao);

            if ( !$obErro->ocorreu() )
                sistemaLegado::alertaAviso($pgList,"Regime: ".$_REQUEST['stDescricaoRegime'],"excluir","aviso", Sessao::getId(), "../");
            else
                sistemaLegado::alertaAviso($pgList,"Regime: ".urlencode( $obErro->getDescricao() ),"n_excluir","erro", Sessao::getId(), "../");
        }
    break;

}

?>
