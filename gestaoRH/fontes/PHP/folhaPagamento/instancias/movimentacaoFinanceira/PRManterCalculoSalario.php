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
* Página de Processamento
* Data de Criação   : 05/12/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @ignore

$Revision: 31676 $
$Name$
$Author: souzadl $
$Date: 2008-01-24 06:33:36 -0200 (Qui, 24 Jan 2008) $

* Casos de uso: uc-04.05.09
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$stPrograma = "ManterCalculoSalario";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao;
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao;
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao;
$pgOcul = "OC".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao;
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao;

include_once(CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoCalcularFolhas.class.php");
$obRFolhaPagamentoCalcularFolhas = new RFolhaPagamentoCalcularFolhas();
$obRFolhaPagamentoCalcularFolhas->setTipoFiltro($_REQUEST['stTipoFiltro']);
switch ($_REQUEST['stTipoFiltro']) {
    case 'contrato':
    case 'cgm_contrato':
        $obRFolhaPagamentoCalcularFolhas->setCodigos(Sessao::read('arContratos'));
        break;
    case 'local':
        $obRFolhaPagamentoCalcularFolhas->setCodigos($_POST['inCodLocalSelecionados']);
        break;
    case 'lotacao':
        $obRFolhaPagamentoCalcularFolhas->setCodigos($_POST['inCodLotacaoSelecionados']);
        break;
}
//Verificação de configuração de tabelas.
//Caso exista uma que não esteja configurada estoura erro.

//BUSCA COMPETENCIA
include_once(CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php");
$obPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
$obPeriodoMovimentacao->listarUltimaMovimentacao($rsUltimaMovimentacao);

$stCompetencia = $rsUltimaMovimentacao->getCampo('dt_final');

//VERIFICA SE EXISTE CÁLCULO DE PENSÃO ALIMENTÍCIA CONFIGURADA

include_once ( CAM_GRH_FOL_MAPEAMENTO.'TFolhaPagamentoPensaoEvento.class.php' );

$obTFolhaPagamentoPensaoEvento = new TFolhaPagamentoPensaoEvento;
$obTFolhaPagamentoPensaoEvento->recuperaTodos($rsPensaoEvento);

if ($rsPensaoEvento->getNumLinhas() < 0) {
    SistemaLegado::exibeAviso(urlencode("Configuração do Cálculo de Pensão Alimentícia inexistente!"),"n_incluir","erro");
    SistemaLegado::LiberaFrames();
    exit();
}

//VERIFICA SE EXISTE CÁLCULO DE FÉRIAS

include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoFeriasEvento.class.php" );

$obTFolhaPagamentoFeriasEvento = new TFolhaPagamentoFeriasEvento;
$obTFolhaPagamentoFeriasEvento->recuperaTodos($rsFeriasEvento);

if ($rsFeriasEvento->getNumLinhas() < 0) {
    SistemaLegado::exibeAviso(urlencode("Configuração do Cálculo de Férias inexistente!"),"n_incluir","erro");
    SistemaLegado::LiberaFrames();
    exit();
}

//VERIFICA SE EXISTE CÁLCULO DE 13º

include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoDecimoEvento.class.php" );

$obTFolhaPagamentoDecimoEvento = new TFolhaPagamentoDecimoEvento;
$obTFolhaPagamentoDecimoEvento->recuperaTodos($rsDecimoEvento);

if ($rsDecimoEvento->getNumLinhas() < 0) {
    SistemaLegado::exibeAviso(urlencode("Configuração Cálculo de 13º Salário inexistente!"),"n_incluir","erro");
    SistemaLegado::LiberaFrames();
    exit();
}

//VERIFICA SE O CÁLCULO PREVIDÊNCIA ESTÁ EM VIGÊNCIA

include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPrevidenciaPrevidencia.class.php" );

$obTFolhaPagamentoPrevidenciaPrevidencia = new TFolhaPagamentoPrevidenciaPrevidencia;
$obTFolhaPagamentoPrevidenciaPrevidencia->recuperaTodos($rsPrevidenciaPrevidencia);
$rsPrevidenciaPrevidencia->setUltimoElemento();

if ($rsPrevidenciaPrevidencia->getCampo("vigencia") > $stCompetencia || $rsPrevidenciaPrevidencia->getCampo("vigencia") == "") {
    SistemaLegado::exibeAviso(urlencode("Configuração da Previdência inexistente ou não está em vigor para competência!"),"n_incluir","erro");
    SistemaLegado::LiberaFrames();
    exit();
}

//VERIFICA SE O CÁLCULO SALÁRIO FAMÍLIA ESTÁ EM VIGOR

include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoSalarioFamilia.class.php" );

$obTFolhaPagamentoSalarioFamilia = new TFolhaPagamentoSalarioFamilia;
$obTFolhaPagamentoSalarioFamilia->recuperaTodos($rsSalarioFamilia);

$rsSalarioFamilia->setUltimoElemento();
if ($rsSalarioFamilia->getCampo("vigencia") > $stCompetencia || $rsSalarioFamilia->getCampo("vigencia") == "") {
    SistemaLegado::exibeAviso(urlencode("Configuração do Salário Família inexistente ou não está em vigor para competência!"),"n_incluir","erro");
    SistemaLegado::LiberaFrames();
    exit();
}

//VERIFICA SE O CÁLCULO IRRF ESTÁ EM VIGOR

include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoTabelaIrrf.class.php" );

$obTFolhaPagamentoTabelaIRRF = new TFolhaPagamentoTabelaIrrf;
$obTFolhaPagamentoTabelaIRRF->recuperaUltimaVigencia($rsRecordset);

if (SistemaLegado::dataToBr($rsRecordset->getCampo("vigencia")) > $stCompetencia || $rsRecordset->getCampo("vigencia") == "") {
    SistemaLegado::exibeAviso(urlencode("Configuração da Tabela IRRF inexistente ou não está em vigor para competência!"),"n_incluir","erro");
    SistemaLegado::LiberaFrames();
    exit();
}

//VERIFICA SE O CÁLCULO DO FGTS ESTÁ EM VIGOR

include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoFgts.class.php" );

$obTFolhaPagamentoFgts = new TFolhaPagamentoFgts;
$obTFolhaPagamentoFgts->recuperaTodos($rsRecordSet);

$rsRecordSet->setUltimoElemento();

if ($rsRecordSet->getCampo("vigencia") > $stCompetencia || $rsRecordSet->getCampo("vigencia") == "") {
    SistemaLegado::exibeAviso(urlencode("Configuração do FGTS inexistente ou não está em vigor para competência!"),"n_incluir","erro");
    SistemaLegado::LiberaFrames();
    exit();
}

$obRFolhaPagamentoCalcularFolhas->setRecalcular(Sessao::read("rsRecalcular"));
$obRFolhaPagamentoCalcularFolhas->setCalcularSalario();
$obRFolhaPagamentoCalcularFolhas->calcularFolha();
?>
