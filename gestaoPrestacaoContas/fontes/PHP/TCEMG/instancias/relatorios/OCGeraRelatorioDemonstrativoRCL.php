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
  * Página Oculta para gerar o arquivo Demostrativo RCL
  * Data de Criação: 24/07/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  * @ignore
  * $Id: OCGeraRelatorioDemonstrativoRCL.php 59612 2014-09-02 12:00:51Z gelson $
  * $Date: 2014-08-12 10:06:21 -0300 (Tue, 12 Aug 2014) $
  * $Author: franver $
  * $Rev: 59281 $
  *
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GPC_TCEMG_NEGOCIO."RTCEMGRelatorioDemostrativoRCL.class.php";
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php';
include_once CLA_MPDF;

$inEntidades = $_REQUEST['inCodEntidade'];

$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado( 'exercicio', Sessao::getExercicio() );
$obTOrcamentoEntidade->recuperaRelacionamentoNomes( $rsEntidade, $stCondicao );

$inPrefeituraInstituto = null;
$inCamara = null;

foreach($inEntidades as $stCodEntidade) {
    $tipoEntidade = SistemaLegado::pegaDado("parametro"
                                           ,"administracao.configuracao"
                                           ,"WHERE cod_modulo = 8 
                                               AND parametro ilike 'cod_entidade%'
                                               AND exercicio = '".Sessao::getExercicio()."'
                                               AND valor = '".$stCodEntidade."';");
    
    $tipoEntidade = trim(substr($tipoEntidade,13,15));
    
    switch($tipoEntidade){
        case "prefeitura":
        case "rpps":
            $inPrefeituraInstituto = SistemaLegado::pegaDado("valor"
                                                            ,"administracao.configuracao"
                                                            ,"WHERE cod_modulo = 8 
                                                                AND parametro ilike 'cod_entidade_prefeitura'
                                                                AND exercicio = '".Sessao::getExercicio()."';").','.SistemaLegado::pegaDado("valor"
                                                            ,"administracao.configuracao"
                                                            ,"WHERE cod_modulo = 8 
                                                                AND parametro ilike 'cod_entidade_rpps'
                                                                AND exercicio = '".Sessao::getExercicio()."';");
             break;
        
        case "camara":
            $inCamara = SistemaLegado::pegaDado("valor"
                                               ,"administracao.configuracao"
                                               ,"WHERE cod_modulo = 8 
                                                   AND parametro ilike 'cod_entidade_camara'
                                                   AND exercicio = '".Sessao::getExercicio()."';");
             break;
    }
}

$obRTCEMGRelatorioDemostrativoRCL = new RTCEMGRelatorioDemostrativoRCL();

if(!is_null($inPrefeituraInstituto) && !is_null($inCamara)){
    $inCodEntidades = $inPrefeituraInstituto.",".$inCamara;
    $obRTCEMGRelatorioDemostrativoRCL->setTipoConsulta(null);
}else if(!is_null($inPrefeituraInstituto)){
    $inCodEntidades = $inPrefeituraInstituto;
    $obRTCEMGRelatorioDemostrativoRCL->setTipoConsulta('PrefeituraInstituto');
}else{
    $inCodEntidades = $inCamara;
    $obRTCEMGRelatorioDemostrativoRCL->setTipoConsulta('Camara');
}

$stExercicio = Sessao::getExercicio();
$inPeriodo = $request->get('cmbPeriodo');

if ($request->get('stPeriodicidade') == 'Bimestre'){
    $stDataInicial = "01/".str_pad((string)(($inPeriodo*2)-1),2,'0',STR_PAD_LEFT)."/".$stExercicio;
    $stDataFinal = SistemaLegado::retornaUltimoDiaMes(str_pad((string)($inPeriodo*2),2,'0',STR_PAD_LEFT),$stExercicio);
} elseif ($request->get('stPeriodicidade') == 'Trimestre') {
    $stDataInicial = "01/".str_pad((string)(($inPeriodo*3)-2),2,'0',STR_PAD_LEFT)."/".$stExercicio;
    $stDataFinal = SistemaLegado::retornaUltimoDiaMes(str_pad((string)($inPeriodo*3),2,'0',STR_PAD_LEFT),$stExercicio);
} else {
    $stDataInicial = "01/".$inPeriodo."/".$stExercicio;
    $stDataFinal = SistemaLegado::retornaUltimoDiaMes($inPeriodo,$stExercicio);
}

$obRTCEMGRelatorioDemostrativoRCL->setExercicio    ($stExercicio);
$obRTCEMGRelatorioDemostrativoRCL->setCodEntidades ($inCodEntidades);
$obRTCEMGRelatorioDemostrativoRCL->setDataInicial  ($stDataInicial);
$obRTCEMGRelatorioDemostrativoRCL->setDataFinal    ($stDataFinal);
$obRTCEMGRelatorioDemostrativoRCL->setDataFinal    ($stDataFinal);
$obRTCEMGRelatorioDemostrativoRCL->setTipoSituacao ($request->get("stSituacao"));

$obRTCEMGRelatorioDemostrativoRCL->geraRecordSet($rsRecordSet);

$inMes = (int)substr($stDataFinal,3,2);
$inAno = substr($stDataFinal,8,2);
$arCabecalhoMeses = array();
$arMes = array(1 => "Janeiro", 2 => "Fevereiro", 3 => "Março", 4 => "Abril", 5 => "Maio", 6 => "Junho", 7 => "Julho", 8 => "Agosto", 9 => "Setembro", 10 => "Outubro", 11 => "Novembro", 12 => "Dezembro");

for ( $inCount = 1; $inCount <= 12; $inCount++ ) {
    $arCabecalhoMeses["mes_".$inCount] = $arMes[$inMes];
    $arCabecalhoMeses["ano_".$inCount] = $inAno;
    $inMes = $inMes - 1;
    if ( $inMes == 0 ) {
        $inMes = 12;
        $inAno = $inAno - 1;
    }
}

$arDados = array( "arReceitas"                    => $rsRecordSet["arReceitas"],
                  "arReceitasTotal"               => $rsRecordSet["arReceitasTotal"],
                  "arDemostrativoReceitaExclusao" => $rsRecordSet["arDemostrativoReceitaExclusao"],
                  "arDespesas"                    => $rsRecordSet["arDespesas"],
                  "arDespesasTotal"               => $rsRecordSet["arDespesasTotal"],
                  "arDespesasDeducoes"            => $rsRecordSet["arDespesasDeducoes"],
                  "arDespesasDeducoesTotal"       => $rsRecordSet["arDespesasDeducoesTotal"],
                  "arValorTotalDespesaPessoal"    => $rsRecordSet["arValorTotalDespesaPessoal"],
                  "arValoresDemostrativoRCL"      => $rsRecordSet["arValoresDemostrativoRCL"],
                  "arCabecalhoMes"                => $arCabecalhoMeses
                  );

$obMPDF = new FrameWorkMPDF(6,55,7);
$obMPDF->setCodEntidades($inCodEntidades);
$obMPDF->setDataInicio($stDataInicial);
$obMPDF->setDataFinal($stDataFinal);
$obMPDF->setFormatoFolha("A4-L");

$obMPDF->setNomeRelatorio("Demostrativo RCL");

$obMPDF->setConteudo($arDados);

$obMPDF->gerarRelatorio();

?>
