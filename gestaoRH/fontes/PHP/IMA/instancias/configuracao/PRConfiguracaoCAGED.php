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
    * Arquivo de Processamento para configuração da exportação do CAGED
    * Data de Criação: 18/04/2008

    * @author Diego Lemos de Souza

    * Casos de uso: uc-04.08.20

    $Id: PRConfiguracaoCAGED.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoCaged.class.php");
include_once(CAM_GRH_IMA_MAPEAMENTO."TIMACagedAutorizadoCei.class.php");
include_once(CAM_GRH_IMA_MAPEAMENTO."TIMACagedAutorizadoCgm.class.php");
include_once(CAM_GRH_IMA_MAPEAMENTO."TIMACagedEstabelecimento.class.php");
include_once(CAM_GRH_IMA_MAPEAMENTO."TIMACagedEvento.class.php");
include_once(CAM_GRH_IMA_MAPEAMENTO."TIMACagedSubDivisao.class.php");

$obTIMAConfiguracaoCaged = new TIMAConfiguracaoCaged();
$obTIMACagedAutorizadoCei = new TIMACagedAutorizadoCei();
$obTIMACagedAutorizadoCgm = new TIMACagedAutorizadoCgm();
$obTIMACagedEstabelecimento = new TIMACagedEstabelecimento();
$obTIMACagedEvento = new TIMACagedEvento();
$obTIMACagedSubDivisao = new TIMACagedSubDivisao();

$obTIMACagedAutorizadoCei->obTIMAConfiguracaoCaged = &$obTIMAConfiguracaoCaged;
$obTIMACagedAutorizadoCgm->obTIMAConfiguracaoCaged = &$obTIMAConfiguracaoCaged;
$obTIMACagedEstabelecimento->obTIMAConfiguracaoCaged = &$obTIMAConfiguracaoCaged;
$obTIMACagedEvento->obTIMAConfiguracaoCaged = &$obTIMAConfiguracaoCaged;
$obTIMACagedSubDivisao->obTIMAConfiguracaoCaged = &$obTIMAConfiguracaoCaged;

//Define o nome dos arquivos PHP
$stPrograma = "ConfiguracaoCAGED";
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgOcul = "OC".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgJS   = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
switch ($stAcao) {
    case "configurar":
        Sessao::setTrataExcecao(true);
        $obTIMACagedAutorizadoCgm->excluirTodos();
        $obTIMACagedAutorizadoCei->excluirTodos();
        $obTIMACagedEstabelecimento->excluirTodos();
        $obTIMACagedSubDivisao->excluirTodos();
        $obTIMACagedEvento->excluirTodos();
        $obTIMAConfiguracaoCaged->excluirTodos();

        $obTIMAConfiguracaoCaged->setDado("cod_cnae",$_POST["HdninCodCnae"]);
        $obTIMAConfiguracaoCaged->setDado("tipo_declaracao",$_POST["stPrimeiraDeclaracao"]);
        $obTIMAConfiguracaoCaged->inclusao();

        if ($_POST["boInformarResponsavel"]) {
            $obTIMACagedAutorizadoCgm->setDado("numcgm",$_POST["inCGM"]);
            $obTIMACagedAutorizadoCgm->setDado("num_autorizacao",$_POST["inNumeroAutorizacao"]);
            $obTIMACagedAutorizadoCgm->inclusao();
            if ($_POST["boInformarCEIAutorizado"]) {
                $obTIMACagedAutorizadoCei->setDado("num_cei",$_POST["inNumeroCEIAutorizacao"]);
                $obTIMACagedAutorizadoCei->inclusao();
            }
        }
        if ($_POST["boInformarCEI"]) {
            $obTIMACagedEstabelecimento->setDado("num_cei",$_POST["inNumeroCEI"]);
            $obTIMACagedEstabelecimento->inclusao();
        }
        if (is_array($_POST["inCodSubDivisaoSelecionados"])) {
            foreach ($_POST["inCodSubDivisaoSelecionados"] as $inCodSubDivisao) {
                $obTIMACagedSubDivisao->setDado("cod_sub_divisao",$inCodSubDivisao);
                $obTIMACagedSubDivisao->inclusao();
            }
        }
        foreach ($_POST["inCodEventoSelecionados"] as $inCodEvento) {
            $obTIMACagedEvento->setDado("cod_evento",$inCodEvento);
            $obTIMACagedEvento->inclusao();
        }

           Sessao::encerraExcecao();
    sistemaLegado::alertaAviso($pgForm,"Configuração do CAGED concluída com sucesso!","incluir","aviso", Sessao::getId(), "../");
    break;
}
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
