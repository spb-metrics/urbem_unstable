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
 * Página de Relatório RGF Anexo1
 * Data de Criação   : 08/10/2007

 * @author Tonismar Régis Bernardo

 * @ignore

 * $Id: OCGeraRelatorioRazaoDespesa.php 62788 2015-06-17 18:14:39Z evandro $

 * Casos de uso : uc-06.01.20
 */
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGRelatorioRazaoDespesa.class.php";
include_once CAM_FW_LEGADO."funcoesLegado.lib.php";
include_once CLA_MPDF;

$arData = $arEstrutural = $arDataReceita = $registros = array();
$rsData = new Recordset;

$obTTCEMGRelatorioRazaoDespesa = new TTCEMGRelatorioRazaoDespesa;
$obTTCEMGRelatorioRazaoDespesa->setDado('dt_inicial'    , $_REQUEST['stDataInicial']);
$obTTCEMGRelatorioRazaoDespesa->setDado('dt_final'      , $_REQUEST['stDataFinal']);
$obTTCEMGRelatorioRazaoDespesa->setDado('tipo_relatorio', $_REQUEST['stTipoRelatorio']);
$obTTCEMGRelatorioRazaoDespesa->setDado('num_orgao'     , $_REQUEST['inNumOrgao']);
$obTTCEMGRelatorioRazaoDespesa->setDado('num_unidade'   , $_REQUEST['inNumUnidade']);
$obTTCEMGRelatorioRazaoDespesa->setDado('num_pao'       , $_REQUEST['inCodPao']);
$obTTCEMGRelatorioRazaoDespesa->setDado('exercicio'     , Sessao::getExercicio());
$obTTCEMGRelatorioRazaoDespesa->setDado('entidade'      , implode(',', $_REQUEST['inCodEntidade']));
$obTTCEMGRelatorioRazaoDespesa->setDado('cod_recurso'   , isset($_REQUEST['inCodRecurso']) ? implode(',', $_REQUEST['inCodRecurso']) : null);
$obTTCEMGRelatorioRazaoDespesa->setDado('situacao'    , $_REQUEST['inSituacao']);

//Seleciona consulta dependendo do tipo do relatório
switch($_REQUEST['stTipoRelatorio']) {
    case 'educacao_receita_extra_orcamentaria':
        $obTTCEMGRelatorioRazaoDespesa->recuperaDadosReceitaExtraOrcamentaria($rsData);
    break;
    
    case 'educacao_despesa_extra_orcamentaria':
        $obTTCEMGRelatorioRazaoDespesa->recuperaDadosDespesaExtraOrcamentaria($rsData);
    break;
    
    case 'restos_pagar':
        $obTTCEMGRelatorioRazaoDespesa->recuperaDadosRestosPagar($rsData,"","ORDER BY dt_pagamento, empenho",$boTransacao);
    break;

    case 'empenhado':
    case 'liquidado':
    case 'pago':
        $obTTCEMGRelatorioRazaoDespesa->recuperaDadosConsultaEmpenhoLiquidadoPago($rsData);
        foreach($rsData->getElementos() as $registro) {    
            $arOrgaoUnidade[]       = $registro['num_orgao'].",".$registro['num_unidade']; 
        }
    break;

    default:
        $obTTCEMGRelatorioRazaoDespesa->recuperaDadosConsultaPrincipal($rsData);
    break;
}

switch($_REQUEST['inSituacao']) {
    case '1':
        $stNomeRelatorio = "Empenhados";
    break;

    case '2':
        $stNomeRelatorio = "Pagos";
    break;

    case '3':
        $stNomeRelatorio = "Liquidados";
    break;
}

//Preenche com campos de agrupamento
foreach($rsData->getElementos() as $registro) {        
    $arEstrutural[]         = array_key_exists('despesa'         , $registro) ? $registro['despesa'] : null;
    $arData[]               = array_key_exists('dt_pagamento'    , $registro) ? $registro['dt_pagamento'] : null;
    $arDataReceita[]        = array_key_exists('dt_transferencia', $registro) ? $registro['dt_transferencia'] : null;
}

$incount = 0;
foreach($rsData->getElementos() as $registro) {
    if($orgao != $registro['cod_orgao'] or $unidade != $registro['cod_unidade']) {
       $arOrgaoUnidade[$incount]['cod_orgao'] = $registro['cod_orgao'];
       $arOrgaoUnidade[$incount]['cod_unidade'] = $registro['cod_unidade'];
       
       $incount ++;
       $orgao = $registro['cod_orgao'];
       $unidade = $registro['cod_unidade'];
    }
}

//Seta variável título do relatório
switch($_REQUEST['stTipoRelatorio']) {
    case 'educacao_despesa_extra_orcamentaria':
    $stTipoRelatorio = 'Educação - Despesa Extra Orçamentária';
    break;

    case 'educacao_receita_extra_orcamentaria':
    $stTipoRelatorio = 'Educação - Receita Extra Orçamentária';
    break;
    
    case 'fundeb_60':
    $stTipoRelatorio = 'FUNDEB 60%';
    break;
    
    case 'fundeb_40':
    $stTipoRelatorio = 'FUNDEB 40%';
    break;
    
    case 'ensino_fundamental':
    $stTipoRelatorio = 'Ensino Fundamental';
    break;
    
    case 'gasto_25':
    $stTipoRelatorio = 'Gasto com 25%';
    break;
    
    case 'saude':
    $stTipoRelatorio = 'Saúde';
    break;
    
    case 'diversos':
    $stTipoRelatorio = 'Diversos';
    break;

    case 'restos_pagar':
    $stTipoRelatorio = 'Restos a Pagar';
    break;
    
    case 'empenhado':
    $stTipoRelatorio = 'Empenhado';
    break;
    
    case 'liquidado':
    $stTipoRelatorio = 'Liquidado';
    break;
    
    case 'pago':
    $stTipoRelatorio = 'Pago';
    break;
}

if(is_array($arOrgaoUnidade))         { $arOrgaoUnidade = array_unique($arOrgaoUnidade); }
if(is_array($arEstrutural))           { $arEstrutural   = array_unique($arEstrutural); }
if(is_array($arData))                 { $arData         = array_unique($arData); }
if(is_array($arDataReceita))          { $arDataReceita  = array_unique($arDataReceita); }
if(is_array($rsData->getElementos())) { $registros      = $rsData->getElementos(); }

$arDados = array(
    'registros'       => $rsData->getElementos(),
    'stTipoRelatorio' => $stTipoRelatorio,
    'arEstrutural'    => $arEstrutural,
    'arData'          => $arData,
    'arDataReceita'   => $arDataReceita,
    'arOrgaoUnidade'  => $arOrgaoUnidade,
);
   
// Switch necessário para selecionar template do relatório. Embora parecidos, há campos que constam num que não constam no outro.
switch($_REQUEST['stTipoRelatorio']) {
    case 'restos_pagar':
    $obMPDF = new FrameWorkMPDF(6,55,11);
    break;

    case 'educacao_despesa_extra_orcamentaria':
    $obMPDF = new FrameWorkMPDF(6,55,12);
    break;

    case 'educacao_receita_extra_orcamentaria':
    $obMPDF = new FrameWorkMPDF(6,55,13);
    break;
    
    case 'empenhado':
    case 'liquidado':
    case 'pago':
    $obMPDF = new FrameWorkMPDF(6,55,16);
    break;

    default:
    $obMPDF = new FrameWorkMPDF(6,55,10);
    break;
}

$obMPDF->setDataInicio($request->get("stDataInicial"));
$obMPDF->setDataFinal($request->get("stDataFinal"). " - ".  $stNomeRelatorio);
$obMPDF->setNomeRelatorio("Razão da Despesa");
$obMPDF->setFormatoFolha("A4-L");
$obMPDF->setConteudo($arDados);
$obMPDF->gerarRelatorio();