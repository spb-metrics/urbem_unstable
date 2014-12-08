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
    * Página de Oculto do Relatório de Pagamento de Estagiários
    * Data de Criação: 27/10/2006

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.07.03

    $Id: PRRelatorioPagamentoEstagiarios.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioPagamentoEstagiarios";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

$inCodAtributo = 0;
$boArray = 0;
switch ($_POST["stTipoFiltro"]) {
    case "cgm_codigo_estagio":
        $stCodigos = "";
        foreach (Sessao::read("arEstagios") as $arEstagio) {
            $stCodigos .= $arEstagio["inCodigoEstagio"].",";
        }
        $stCodigos = substr($stCodigos,0,strlen($stCodigos)-1);
    break;
    case "instituicao_ensino":
    case "entidade_intermediadora":
        $stCodigos = $_POST["inCGM"];
        break;
    case "atributo_estagiario":
        $inCodAtributo = $_POST["inCodAtributo"];
        $inCodCadastro = $_POST["inCodCadastro"];
        $stNome = "Atributo_".$inCodAtributo."_".$inCodCadastro;
        if (is_array($_POST[$stNome."_Selecionados"])) {
            $stCodigos = implode(",",$_POST[$stNome."_Selecionados"]);
            $boArray = 1;
        } else {
            $stCodigos = $_POST[$stNome];
        }
        break;
    case "lotacao_grupo":
        $stCodigos = implode(",",$_POST["inCodLotacaoSelecionados"]);
        break;
    case "local_grupo":
        $stCodigos = implode(",",$_POST["inCodLocalSelecionados"]);
        break;
}

if ($_POST["inCodBanco"] != "") {
    include_once(CAM_GT_MON_MAPEAMENTO."TMONBanco.class.php");
    $TMONBanco = new TMONBanco();
    $stFiltro = " WHERE num_banco = '".$_POST['inCodBanco']."'";
    $TMONBanco->recuperaTodos($rsBanco,$stFiltro);
    $inCodBanco = $rsBanco->getCampo("cod_banco");
}
if ($_POST['stNumAgencia'] != "") {
    include_once(CAM_GT_MON_MAPEAMENTO."TMONAgencia.class.php");
    $TMONAgencia = new TMONAgencia();
    $stFiltro = " WHERE num_agencia = '".$_POST['stNumAgencia']."'";
    $TMONAgencia->recuperaTodos($rsAgencia,$stFiltro);
    $inCodAgencia = $rsAgencia->getCampo("cod_agencia");
}

include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
$inCodMes = $_POST["inCodMes"];
$inAno    = $_POST["inAno"];
$inMes = str_pad($inCodMes, 2, "0", STR_PAD_LEFT);
$stCompetencia = $inMes."/".$inAno;
$stFiltroCompetencia = " AND to_char(FPM.dt_final,'mm/yyyy') = '".$stCompetencia."'";
$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
$obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacao($rsPeriodoMovimentacao, $stFiltroCompetencia);

$preview = new PreviewBirt(4,39,2);
$preview->setVersaoBirt("2.5.0");
$preview->setReturnURL( CAM_GRH_EST_INSTANCIAS."relatorios/FLRelatorioPagamentoEstagiarios.php");
$preview->setTitulo('Pagamento de Estagiários');
$preview->setNomeArquivo('pagamentoEstagiarios');
$preview->addParametro('stEntidade', Sessao::getEntidade());
$preview->addParametro('entidade', Sessao::getCodEntidade());
$preview->addParametro('stTipoFiltro', $_POST["stTipoFiltro"]);
$preview->addParametro('stCodigos', $stCodigos);
$preview->addParametro('inCodBanco', $inCodBanco);
$preview->addParametro('inCodAgencia', $inCodAgencia);
$preview->addParametro('inCodAtributo', $inCodAtributo);
$preview->addParametro('boArray', $boArray);
$preview->addParametro('boAgrupar', ($_POST["boAgrupar"]!="") ? "true" : "false");
$preview->addParametro('boQuebrar', ($_POST["boQuebrar"]!="") ? "true" : "false");
$preview->addParametro('stCompetencia', $stCompetencia);
$preview->addParametro('dtInicioCompetencia', $rsPeriodoMovimentacao->getCampo("dt_inicial"));
$preview->addParametro('inCodPeriodoMovimentacao', $rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
$preview->preview();

?>
