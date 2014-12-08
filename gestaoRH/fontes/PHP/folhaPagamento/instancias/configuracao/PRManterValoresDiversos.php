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
    * Arquivo de Processamento
    * Data de Criação: 31/10/2007

    * @author Diego Lemos de Souza

    * Casos de uso: uc-04.05.22

    $Id: PRManterValoresDiversos.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stAcao = $_REQUEST["stAcao"] ? $_REQUEST["stAcao"] : $_GET["stAcao"];
$link = Sessao::read("link");
$stLink = "&pg=".$link["pg"]."&pos=".$link["pos"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterValoresDiversos";
$pgFilt      = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgList      = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgForm      = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgProc      = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgOcul      = "OC".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgJS        = "JS".$stPrograma.".js";

include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoValorDiversos.class.php");
$obTFolhaPagamentoValorDiversos = new TFolhaPagamentoValorDiversos();

Sessao::setTrataExcecao(true);

$inValor = str_replace(".", "", $_POST["nuValor"]);
$inValor = str_replace(",", ".", $inValor);

switch ($stAcao) {
    case "incluir":
        $pgRetorno = $pgForm;
        if ($inValor <= 0.00) {
            Sessao::getExcecao()->setDescricao("O Valor deve será maior que zero.");
        }
        $stFiltro  = " AND valor_diversos.cod_valor = ".(int) $_POST["inCodigo"];
        $stFiltro .= " AND ativo IS TRUE ";
        $obTFolhaPagamentoValorDiversos->recuperaRelacionamento($rsValorDiverso,$stFiltro);
        if ($rsValorDiverso->getNumLinhas() == 1) {
            Sessao::getExcecao()->setDescricao("O código informado já foi cadastrado.");
        } else {
            $obTFolhaPagamentoValorDiversos->setDado("cod_valor",$_POST["inCodigo"]);
            $obTFolhaPagamentoValorDiversos->setDado("descricao",$_POST["stDescricao"]);
            $obTFolhaPagamentoValorDiversos->setDado("valor",$_POST["nuValor"]);
            $obTFolhaPagamentoValorDiversos->setDado("ativo","true");
            $obTFolhaPagamentoValorDiversos->setDado("data_vigencia",$_POST["dataVigencia"]);
            $obTFolhaPagamentoValorDiversos->inclusao();
        }
        $stMensagem = "Valor Diverso ".$_POST["stDescricao"]." incluído com sucesso.";
        break;
    case "alterar";
        $pgRetorno = $pgList;
        if ($inValor <= 0.00) {
            Sessao::getExcecao()->setDescricao("O Valor deve será maior que zero.");
        }

        $obTFolhaPagamentoValorDiversos->setDado("cod_valor",$_POST["inCodigo"]);
        $obTFolhaPagamentoValorDiversos->setDado("descricao",$_POST["stDescricao"]);
        $obTFolhaPagamentoValorDiversos->setDado("valor",$_POST["nuValor"]);
        $obTFolhaPagamentoValorDiversos->setDado("ativo","true");
        $obTFolhaPagamentoValorDiversos->setDado("data_vigencia",$_POST["dataVigencia"]);
        $obTFolhaPagamentoValorDiversos->inclusao();
        $stMensagem = "Valor Diverso ".$_POST["stDescricao"]." alterado com sucesso.";
        break;
    case "excluir":
        $pgRetorno = $pgList;
        $obTFolhaPagamentoValorDiversos->setDado("cod_valor",$_GET["inCodigo"]);
        $obTFolhaPagamentoValorDiversos->recuperaPorChave($rsValoresDiversos);
        while (!$rsValoresDiversos->eof()) {
            $obTFolhaPagamentoValorDiversos->setDado("descricao",$rsValoresDiversos->getCampo("descricao"));
            $obTFolhaPagamentoValorDiversos->setDado("valor",$rsValoresDiversos->getCampo("valor"));
            $obTFolhaPagamentoValorDiversos->setDado("timestamp",$rsValoresDiversos->getCampo("timestamp"));
            $obTFolhaPagamentoValorDiversos->setDado("ativo","false");
            $obTFolhaPagamentoValorDiversos->setDado("data_vigencia",$rsValoresDiversos->getCampo("data_vigencia"));
            $obTFolhaPagamentoValorDiversos->alteracao();
            $rsValoresDiversos->proximo();
        }
        $stMensagem = "Valor Diverso ".$_GET["stDescricao"]." excluído com sucesso.";
        break;
}
Sessao::encerraExcecao();
sistemaLegado::alertaAviso($pgRetorno,$stMensagem,$stAcao,"aviso",Sessao::getId(),"../");
?>
