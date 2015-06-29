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
    * Arquivo de Filtro
    * Data de Criação: 03/09/2007

    * @author Desenvolvedor: Tiago Finger

    * Casos de uso: uc-04.05.51

    $Id: PRRelatorioCustomizavelEvento.php 62661 2015-06-01 20:57:46Z evandro $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once (CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php"                 );

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioCustomizavelEvento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
$obTFolhaPagamentoPeriodoMovimentacao->setDado("mes", $_REQUEST['inCodMes']);
$obTFolhaPagamentoPeriodoMovimentacao->setDado("ano", $_REQUEST['inAno']);
$obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacaoDaCompetencia($rsPeriodoMovimentacao);

//competência
if ($_REQUEST['inCodMes'] && $_REQUEST['inAno']) {
    $stCompetencia = str_pad($_REQUEST['inCodMes'], 2, "0", STR_PAD_LEFT)."/".$_REQUEST['inAno'];
}
$stValoresFiltro = "";
switch ($_REQUEST['stTipoFiltro']) {
    case "contrato_todos":
    case "cgm_contrato_todos":
        $stValoresFiltro = "";
        $arContratos = Sessao::read("arContratos");
        foreach ($arContratos as $arContrato) {
            $stValoresFiltro .= $arContrato["cod_contrato"].",";
        }
        $stValoresFiltro = substr($stValoresFiltro,0,strlen($stValoresFiltro)-1);
        break;
    case "lotacao_grupo":        
        $stValoresFiltro = implode(",",$_REQUEST["inCodLotacaoSelecionados"]);
        break;
    case "local_grupo":
        $stValoresFiltro = implode(",",$_REQUEST["inCodLocalSelecionados"]);
        break;
    case "padrao_grupo":
        $stValoresFiltro = implode(",",$_REQUEST["inCodPadraoSelecionados"]);
        break;
    case "reg_sub_fun_esp_grupo":
        $stValoresFiltro  = implode(",",$_REQUEST["inCodRegimeSelecionadosFunc"])."#";
        $stValoresFiltro .= implode(",",$_REQUEST["inCodSubDivisaoSelecionadosFunc"])."#";
        $stValoresFiltro .= implode(",",$_REQUEST["inCodFuncaoSelecionados"])."#";
        if (is_array($_REQUEST["inCodEspecialidadeSelecionadosFunc"])) {
            $stValoresFiltro .= implode(",",$_REQUEST["inCodEspecialidadeSelecionadosFunc"]);
        }
        break;
    case "reg_sub_car_esp_grupo":
        $stValoresFiltro  = implode(",",$_REQUEST["inCodRegimeSelecionados"])."#";
        $stValoresFiltro .= implode(",",$_REQUEST["inCodSubDivisaoSelecionados"])."#";
        $stValoresFiltro .= implode(",",$_REQUEST["inCodCargoSelecionados"])."#";
        if (is_array($_REQUEST["inCodEspecialidadeSelecionados"])) {
            $stValoresFiltro .= implode(",",$_REQUEST["inCodEspecialidadeSelecionados"]);
        }
        break;
}

$preview = new PreviewBirt(4,27,8);
$preview->setFormato("pdf");
$preview->setVersaoBirt("2.5.0");
$preview->setReturnURL( CAM_GRH_FOL_INSTANCIAS."relatorio/FLRelatorioCustomizavelEvento.php");
$preview->addParametro('stCompetencia', $stCompetencia);
$preview->addParametro('cod_complementar', ($_REQUEST['inCodComplementar']) ? $_REQUEST['inCodComplementar'] : 0);
$preview->addParametro('dt_inicial', $rsPeriodoMovimentacao->getCampo('dt_inicial'));
$preview->addParametro('dt_final', $rsPeriodoMovimentacao->getCampo('dt_final'));
$preview->addParametro('cod_periodo_movimentacao', $rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao'));
$preview->addParametro("stApresentarPorMatricula", $_REQUEST['boApresentarPorMatricula']);
$preview->addParametro("inApresentaValor", ($_REQUEST['boValor']) ? 1 : 0);
$preview->addParametro("inApresentaQuantidade", ($_REQUEST['boQuantidade']) ? 1 : 0);
$preview->addParametro("count_eventos", count($_REQUEST['inCodEventoSelecionados']));
for ($inIndex = 1; $inIndex <= 7; $inIndex++) {
    $inCodEvento = $_REQUEST["inCodEventoSelecionados"][$inIndex-1];
    $inCodEvento = ($inCodEvento != "") ? $inCodEvento : 0;
    if ($inCodEvento != "0") {
        $preview->addParametro("cod_evento$inIndex", $inCodEvento );
    }
}
$preview->addParametro("stTipoFiltro", $_POST["stTipoFiltro"]);
$preview->addParametro("stValoresFiltro",$stValoresFiltro);
$preview->addParametro("cod_configuracao", $_REQUEST["stConfiguracao"]);
$preview->addParametro("stSituacao", $_REQUEST["stSituacao"]);
$preview->addParametro("entidade", Sessao::getCodEntidade());
$preview->addParametro("stEntidade", Sessao::getEntidade());
$preview->addParametro("stOrdem", $_REQUEST["stOrdenacao"]);
$preview->addParametro("dtPeriodoInicial",$rsPeriodoMovimentacao->getCampo("dt_inicial"));
$preview->addParametro("dtPeriodoFinal",$rsPeriodoMovimentacao->getCampo("dt_final"));
$preview->addParametro("boAgrupar",$_POST["boAgrupar"]);
$preview->addParametro("boQuebrar",$_POST["boQuebrar"]);
$preview->preview();
?>
