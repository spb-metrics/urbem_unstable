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
 * Arquivo oculto - Exportação arquivos Planejamento TCE/MG
 *
 * @category    Urbem
 * @package     TCE/MG
 * @author      Eduardo Schitz   <eduardo.schitz@cnm.org.br>
 * $Id: OCExportarAcompanhamentoMensal.php 62335 2015-04-24 19:37:37Z franver $
 */

set_time_limit(0);
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_EXPORTADOR;
include_once CAM_GPC_TCEMG_NEGOCIO.'RTCEMGExportarAcompanhamentoMensal.class.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGConfigurarIDE.class.php';


SistemaLegado::BloqueiaFrames();

$stAcao = $request->get('stAcao');
$arFiltro = Sessao::read('filtroRelatorio');

Sessao::write('exp_arFiltro',$arFiltro);

//Recebe as entidades selecionadas no filtro e concatena elas separando por ','
$stEntidades    = implode(",",$arFiltro['arEntidadesSelecionadas']);
$stDataFinal= SistemaLegado::retornaUltimoDiaMes($arFiltro['stMes'],Sessao::getExercicio() );
if ($arFiltro['stMes'] < 10) {
   $arFiltro['stMes']=  str_pad( $arFiltro['stMes'], 2, '0', STR_PAD_LEFT );
}
$stDataInicial = '01/'.$arFiltro['stMes'].'/'.Sessao::getExercicio();

$stMes = $arFiltro['stMes'];

$obRTCEMGExportarAcompanhamentoMensal = new RTCEMGExportarAcompanhamentoMensal;
$obRTCEMGExportarAcompanhamentoMensal->setArquivos    ($arFiltro["arArquivosSelecionados"]);
$obRTCEMGExportarAcompanhamentoMensal->setExercicio   (Sessao::getExercicio());
$obRTCEMGExportarAcompanhamentoMensal->setMes         ($arFiltro['stMes']);
$obRTCEMGExportarAcompanhamentoMensal->setCodEntidades($stEntidades);
$obRTCEMGExportarAcompanhamentoMensal->setDataInicial ($stDataInicial);
$obRTCEMGExportarAcompanhamentoMensal->setDataFinal   ($stDataFinal);
$obRTCEMGExportarAcompanhamentoMensal->geraRecordset  ($arRecordSetArquivos);

$obExportador = new Exportador();

/**
* OBELAC.csv | Autor : Carlos Adriano
*/
if (in_array("OBELAC.csv",$arFiltro["arArquivosSelecionados"])) {
   $obExportador->addArquivo("OBELAC.csv");
   $stNomeArquivo = "OBELAC";
   include_once(CAM_GPC_TCEMG_INSTANCIAS."layout_arquivos/acompanhamentoMesal/".Sessao::getExercicio()."/OBELAC.csv.inc.php");
}

/**
* OBELAC.csv | Autor : Carlos Adriano
*/
if (in_array("AOB.csv",$arFiltro["arArquivosSelecionados"])) {
   $obExportador->addArquivo("AOB.csv");
   $stNomeArquivo = "AOB";
   include_once(CAM_GPC_TCEMG_INSTANCIAS."layout_arquivos/acompanhamentoMesal/".Sessao::getExercicio()."/AOB.csv.inc.php");
}

/**
* PAREC.csv | Autor : Lisiane Morais
*/
if (in_array("PAREC.csv",$arFiltro["arArquivosSelecionados"])) {
    $obExportador->addArquivo("PAREC.csv");
    $stNomeArquivo = "PAREC";
    include_once(CAM_GPC_TCEMG_INSTANCIAS."layout_arquivos/acompanhamentoMesal/".Sessao::getExercicio()."/PAREC.csv.inc.php");
}

/**
* PARPPS.csv | Autor : Lisiane Morais
*/
if (in_array("PARPPS.csv",$arFiltro["arArquivosSelecionados"])) {
    $obExportador->addArquivo("PARPPS.csv");
    $stNomeArquivo = "PARPPS";
    include_once(CAM_GPC_TCEMG_INSTANCIAS."layout_arquivos/acompanhamentoMesal/".Sessao::getExercicio()."/PARPPS.csv.inc.php");
}

/**
* CONSID.csv | Autor : Lisiane Morais
*/
if (in_array("CONSID.csv",$arFiltro["arArquivosSelecionados"])) {
    $obExportador->addArquivo("CONSID.csv");
    $stNomeArquivo = "CONSID";
    include_once(CAM_GPC_TCEMG_INSTANCIAS."layout_arquivos/acompanhamentoMesal/".Sessao::getExercicio()."/CONSID.csv.inc.php");
}

/**
* IDE.csv | Autor : Lisiane Morais
*/
if (in_array("IDE.csv",$arFiltro["arArquivosSelecionados"])) {
    $obExportador->addArquivo("IDE.csv");
    $stNomeArquivo = "IDE";
    include_once(CAM_GPC_TCEMG_INSTANCIAS."layout_arquivos/acompanhamentoMesal/".Sessao::getExercicio()."/IDE.csv.inc.php");
}

/**
 *
 *   CVC.csv | Autor : Lisiane Morais
*/
if (in_array("CVC.csv",$arFiltro["arArquivosSelecionados"])) {
   $obExportador->addArquivo("CVC.csv");
   $stNomeArquivo = "CVC";
   include_once(CAM_GPC_TCEMG_INSTANCIAS."layout_arquivos/acompanhamentoMesal/".Sessao::getExercicio()."/CVC.csv.inc.php");  
}

/**
* ALQ.csv | Autor : Jean da Silva
*/
if (in_array("ALQ.csv",$arFiltro["arArquivosSelecionados"])){
   $obExportador->addArquivo("ALQ.csv");
   $stNomeArquivo = "ALQ";
   include_once(CAM_GPC_TCEMG_INSTANCIAS."layout_arquivos/acompanhamentoMesal/".Sessao::getExercicio()."/ALQ.csv.inc.php");  
}

/**
* EXT.csv | Autor : Jean da Silva
*/
if (in_array("EXT.csv",$arFiltro["arArquivosSelecionados"])){
    $obExportador->addArquivo("EXT.csv");
    $stNomeArquivo = "EXT";
    include_once(CAM_GPC_TCEMG_INSTANCIAS."layout_arquivos/acompanhamentoMesal/".Sessao::getExercicio()."/EXT.csv.inc.php");  
}

/**
* LQD.csv | Autor : Jean da Silva
*/
if (in_array("LQD.csv",$arFiltro["arArquivosSelecionados"])) {
    $obExportador->addArquivo("LQD.csv");
    $stNomeArquivo = "LQD";
    include_once(CAM_GPC_TCEMG_INSTANCIAS."layout_arquivos/acompanhamentoMesal/".Sessao::getExercicio()."/LQD.csv.inc.php");  
}

/**
* CTB.csv | Autor : Carolina Schwaab Marcal
*/
if (in_array("CTB.csv",$arFiltro["arArquivosSelecionados"])) {
    $obExportador->addArquivo("CTB.csv");
    $stNomeArquivo = "CTB";
    include_once(CAM_GPC_TCEMG_INSTANCIAS."layout_arquivos/acompanhamentoMesal/".Sessao::getExercicio()."/CTB.csv.inc.php");  
}

/**
* CAIXA.csv | Autor : Carolina Schwaab Marcal
*/
if (in_array("CAIXA.csv",$arFiltro["arArquivosSelecionados"])) {
    $obExportador->addArquivo("CAIXA.csv");
    $stNomeArquivo = "CAIXA";
    include_once(CAM_GPC_TCEMG_INSTANCIAS."layout_arquivos/acompanhamentoMesal/".Sessao::getExercicio()."/CAIXA.csv.inc.php");  
}

/**
* PESSOA.csv | Autor : Arthur Cruz
*/
if (in_array("PESSOA.csv",$arFiltro["arArquivosSelecionados"])) {
    $obExportador->addArquivo("PESSOA.csv");
    $stNomeArquivo = "PESSOA";
    include_once(CAM_GPC_TCEMG_INSTANCIAS."layout_arquivos/acompanhamentoMesal/".Sessao::getExercicio()."/PESSOA.csv.inc.php");  
}

/**
* NTF.csv | Autor : Michel Teixeira
*/
if (in_array("NTF.csv",$arFiltro["arArquivosSelecionados"])) {
    $obExportador->addArquivo("NTF.csv");
    $stNomeArquivo = "NTF";
    include_once(CAM_GPC_TCEMG_INSTANCIAS."layout_arquivos/acompanhamentoMesal/".Sessao::getExercicio()."/NTF.csv.inc.php");  
}

/**
* ORGAO.csv | Autor : Michel Teixeira
*/
if (in_array("ORGAO.csv",$arFiltro["arArquivosSelecionados"])) {
    $obExportador->addArquivo("ORGAO.csv");
    $stNomeArquivo = "ORGAO";
    include_once(CAM_GPC_TCEMG_INSTANCIAS."layout_arquivos/acompanhamentoMesal/".Sessao::getExercicio()."/ORGAO.csv.inc.php");  
}

/**
* RSP.csv | Autor : Jean da Silva
*/
if (in_array("RSP.csv",$arFiltro["arArquivosSelecionados"])){
    $obExportador->addArquivo("RSP.csv");
    $stNomeArquivo = "RSP";
    include_once(CAM_GPC_TCEMG_INSTANCIAS."layout_arquivos/acompanhamentoMesal/".Sessao::getExercicio()."/RSP.csv.inc.php");  
}

if (in_array("OPS.csv",$arFiltro["arArquivosSelecionados"])) {
    $obExportador->addArquivo("OPS.csv");
    $stNomeArquivo = "OPS";
    include_once(CAM_GPC_TCEMG_INSTANCIAS."layout_arquivos/acompanhamentoMesal/".Sessao::getExercicio()."/OPS.csv.inc.php");  
}

/**
* AOC.csv | Autor : Carlos Adriano Vernieri da Silva
*/
if (in_array("AOC.csv",$arFiltro["arArquivosSelecionados"])) {
    $obExportador->addArquivo("AOC.csv");
    $stNomeArquivo = "AOC";
    include_once(CAM_GPC_TCEMG_INSTANCIAS."layout_arquivos/acompanhamentoMesal/".Sessao::getExercicio()."/AOC.csv.inc.php");  
}//if AOC.csv

/**
* AOP.csv | Autor : Carlos Adriano Vernieri da Silva
*/
if (in_array("AOP.csv",$arFiltro["arArquivosSelecionados"])){   
    $obExportador->addArquivo("AOP.csv");
    $stNomeArquivo = "AOP";
    include_once(CAM_GPC_TCEMG_INSTANCIAS."layout_arquivos/acompanhamentoMesal/".Sessao::getExercicio()."/AOP.csv.inc.php");  
}

/**
* LAO.csv | Autor : Jean da Silva
*/
if (in_array("LAO.csv",$arFiltro["arArquivosSelecionados"])) {
    $obExportador->addArquivo("LAO.csv");
    $stNomeArquivo = "LAO";
    include_once(CAM_GPC_TCEMG_INSTANCIAS."layout_arquivos/acompanhamentoMesal/".Sessao::getExercicio()."/LAO.csv.inc.php");     
}
/**
* DCLRF.csv | Autor : Carlos Adriano
*/
if (in_array("DCLRF.csv",$arFiltro["arArquivosSelecionados"])){
    $obExportador->addArquivo("DCLRF.csv");
    $stNomeArquivo = "DCLRF";
    include_once(CAM_GPC_TCEMG_INSTANCIAS."layout_arquivos/acompanhamentoMesal/".Sessao::getExercicio()."/DCLRF.csv.inc.php");
}

/**
* CONSOR.csv | Autor : Franver Sarmento de Moraes
*/
if (in_array("CONSOR.csv",$arFiltro["arArquivosSelecionados"])) {
    $obExportador->addArquivo("CONSOR.csv");
    $stNomeArquivo = "CONSOR";
    include_once(CAM_GPC_TCEMG_INSTANCIAS."layout_arquivos/acompanhamentoMesal/".Sessao::getExercicio()."/CONSOR.csv.inc.php");
}
/**
* PARELIC.csv | Autor : Lisiane Morais
*/
if (in_array("PARELIC.csv",$arFiltro["arArquivosSelecionados"])) {
    $obExportador->addArquivo("PARELIC.csv");
    $stNomeArquivo = "PARELIC";
    include_once(CAM_GPC_TCEMG_INSTANCIAS."layout_arquivos/acompanhamentoMesal/".Sessao::getExercicio()."/PARELIC.csv.inc.php");
}
    
/**
* HOMOLIC.csv | Autor : Evandro Melos
*/
if (in_array("HOMOLIC.csv",$arFiltro["arArquivosSelecionados"])) {
    $obExportador->addArquivo("HOMOLIC.csv");
    $stNomeArquivo = "HOMOLIC";
    include_once(CAM_GPC_TCEMG_INSTANCIAS."layout_arquivos/acompanhamentoMesal/".Sessao::getExercicio()."/HOMOLIC.csv.inc.php");
}

/**
* ABERLIC.csv | Autor : Jean da Silva
*/
if (in_array("ABERLIC.csv",$arFiltro["arArquivosSelecionados"])){
    $obExportador->addArquivo("ABERLIC.csv");
    $stNomeArquivo = "ABERLIC";
    include_once(CAM_GPC_TCEMG_INSTANCIAS."layout_arquivos/acompanhamentoMesal/".Sessao::getExercicio()."/ABERLIC.csv.inc.php");   
}

/**
* REGLIC.csv | Autor : Carolina Schwaab Marcal
*/
if (in_array("REGLIC.csv",$arFiltro["arArquivosSelecionados"])) {
    $obExportador->addArquivo("REGLIC.csv");
    $stNomeArquivo = "REGLIC";
    include_once(CAM_GPC_TCEMG_INSTANCIAS."layout_arquivos/acompanhamentoMesal/".Sessao::getExercicio()."/REGLIC.csv.inc.php");   
}

/**
* JULGLIC.csv | Autor : Jean da Silva
*/
if (in_array("JULGLIC.csv",$arFiltro["arArquivosSelecionados"])){
    $obExportador->addArquivo("JULGLIC.csv");
    $stNomeArquivo = "JULGLIC";
    include_once(CAM_GPC_TCEMG_INSTANCIAS."layout_arquivos/acompanhamentoMesal/".Sessao::getExercicio()."/JULGLIC.csv.inc.php");   
}

/**
* HABLIC.csv | Autor : Jean da Silva
*/

if (in_array("HABLIC.csv",$arFiltro["arArquivosSelecionados"])){
    $obExportador->addArquivo("HABLIC.csv");
    $stNomeArquivo = "HABLIC";
    include_once(CAM_GPC_TCEMG_INSTANCIAS."layout_arquivos/acompanhamentoMesal/".Sessao::getExercicio()."/HABLIC.csv.inc.php");   
}

/**
* EMP.csv | Autor : Michel Teixeira
*/
if (in_array("EMP.csv",$arFiltro["arArquivosSelecionados"])){   
    $obExportador->addArquivo("EMP.csv");
    $stNomeArquivo = "EMP";
    include_once(CAM_GPC_TCEMG_INSTANCIAS."layout_arquivos/acompanhamentoMesal/".Sessao::getExercicio()."/EMP.csv.inc.php");   
}

/**
* DDC.csv | Autor : Arthur Cruz
*/
if (in_array("DDC.csv",$arFiltro["arArquivosSelecionados"])) {
    $obExportador->addArquivo("DDC.csv");
    $stNomeArquivo = "DDC";
    include_once(CAM_GPC_TCEMG_INSTANCIAS."layout_arquivos/acompanhamentoMesal/".Sessao::getExercicio()."/DDC.csv.inc.php");   
}

/**
* DISPENSA.csv | Autor : Jean da Silva
*/
if (in_array("DISPENSA.csv",$arFiltro["arArquivosSelecionados"])) {
    $obExportador->addArquivo("DISPENSA.csv");
    $stNomeArquivo = "DISPENSA";
    include_once(CAM_GPC_TCEMG_INSTANCIAS."layout_arquivos/acompanhamentoMesal/".Sessao::getExercicio()."/DISPENSA.csv.inc.php");   
}

/**
* ANL.csv | Autor : Eduardo Schitz EM ANDAMENTO TEM QUE SER CRIADO O DOCUMENTO DE ANÁLISE AINDA
*/
if (in_array("ANL.csv",$arFiltro["arArquivosSelecionados"])){
    $obExportador->addArquivo("ANL.csv");
    $stNomeArquivo = "ANL";
    include_once(CAM_GPC_TCEMG_INSTANCIAS."layout_arquivos/acompanhamentoMesal/".Sessao::getExercicio()."/ANL.csv.inc.php");   
}

/**

* ITEM.csv | Autor : Michel Teixeira

*/
if (in_array("ITEM.csv",$arFiltro["arArquivosSelecionados"])){   
    $obExportador->addArquivo("ITEM.csv");    
    $stNomeArquivo = "ITEM";
    include_once(CAM_GPC_TCEMG_INSTANCIAS."layout_arquivos/acompanhamentoMesal/".Sessao::getExercicio()."/ITEM.csv.inc.php");   
}

/**
* REC.csv | Autor : Jean da Silva
* Pode haver mudança da classe de Negócio RTCEMGExportacaoArquivosPlanejamento.class.php
* Assim o mapeamento TExportacaoTCEMGItem.class.php deve ir junto na nova classe de Negócio
*/
if (in_array("REC.csv",$arFiltro["arArquivosSelecionados"])){
    $obExportador->addArquivo("REC.csv");
    $stNomeArquivo = "REC";
    include_once(CAM_GPC_TCEMG_INSTANCIAS."layout_arquivos/acompanhamentoMesal/".Sessao::getExercicio()."/REC.csv.inc.php");   
}

/**
* ARC.csv | Autor : Jean da Silva
*/
if (in_array("ARC.csv",$arFiltro["arArquivosSelecionados"])){
    $obExportador->addArquivo("ARC.csv");
    $stNomeArquivo = "ARC";
    include_once(CAM_GPC_TCEMG_INSTANCIAS."layout_arquivos/acompanhamentoMesal/".Sessao::getExercicio()."/ARC.csv.inc.php");   
}

/**
* CONTRATOS.csv | Autor : Michel Teixeira
*/
if (in_array("CONTRATOS.csv",$arFiltro["arArquivosSelecionados"])){   
    $obExportador->addArquivo("CONTRATOS.csv");
    $stNomeArquivo = "CONTRATOS";
    include_once(CAM_GPC_TCEMG_INSTANCIAS."layout_arquivos/acompanhamentoMesal/".Sessao::getExercicio()."/CONTRATOS.csv.inc.php");   
}

/**
* CONV.csv | Autor : Michel Teixeira
*/
if (in_array("CONV.csv",$arFiltro["arArquivosSelecionados"])){   
    $obExportador->addArquivo("CONV.csv");
    $stNomeArquivo = "CONV";
    include_once(CAM_GPC_TCEMG_INSTANCIAS."layout_arquivos/acompanhamentoMesal/".Sessao::getExercicio()."/CONV.csv.inc.php");   
}

/**
* RESPLIC.csv | Autor : Lisiane Morais
*/
if ( in_array("RESPLIC.csv",$arFiltro["arArquivosSelecionados"]) ) {
    $obExportador->addArquivo("RESPLIC.csv");
    $stNomeArquivo = "RESPLIC";
    include_once(CAM_GPC_TCEMG_INSTANCIAS."layout_arquivos/acompanhamentoMesal/".Sessao::getExercicio()."/RESPLIC.csv.inc.php");   
}

/**
* AEX.csv | Autor : Jean da Silva
*/
if (in_array("AEX.csv",$arFiltro["arArquivosSelecionados"])){
    $obExportador->addArquivo("AEX.csv");
    $stNomeArquivo = "AEX";
    include_once(CAM_GPC_TCEMG_INSTANCIAS."layout_arquivos/acompanhamentoMesal/".Sessao::getExercicio()."/AEX.csv.inc.php");   
}

# REGADESAO.csv | #21182  Diogo Zarpelon
if (in_array("REGADESAO.csv", $arFiltro["arArquivosSelecionados"])) {
    $obExportador->addArquivo("REGADESAO.csv");
    $stNomeArquivo = "REGADESAO";
    include_once(CAM_GPC_TCEMG_INSTANCIAS."layout_arquivos/acompanhamentoMesal/".Sessao::getExercicio()."/REGADESAO.csv.inc.php");   
}

/**
* OBELAC.csv | Autor : Carlos Adriano
*/

if (Sessao::getExercicio() == '2015' AND in_array("SUPDEF.csv",$arFiltro["arArquivosSelecionados"])) {
   $obExportador->addArquivo("SUPDEF.csv");
   $stNomeArquivo = "SUPDEF";
   include_once(CAM_GPC_TCEMG_INSTANCIAS."layout_arquivos/acompanhamentoMesal/".Sessao::getExercicio()."/SUPDEF.csv.inc.php");
}

	
if ( $arFiltro['stTipoExport'] == 'compactados'){
    $obTTCEMGConfigurarIDE = new TTCEMGConfigurarIDE;
    $obTTCEMGConfigurarIDE->setDado('exercicio', Sessao::getExercicio());
    $obTTCEMGConfigurarIDE->setDado('entidades', $stEntidades);
    $obTTCEMGConfigurarIDE->recuperaDadosExportacao($rsRecordSet);
    
    if ($rsRecordSet->inNumLinhas > 0) {
        $inCodMunicipio = str_pad($rsRecordSet->getCampo('codmunicipio'), 5, '0', STR_PAD_LEFT);
        $inCodOrgao = str_pad($rsRecordSet->getCampo('codorgao'), 2, '0', STR_PAD_LEFT);
        $inMes = str_pad($request->get('stMes'), 2, '0', STR_PAD_LEFT);
    } else {
        SistemaLegado::alertaAviso("FLExportarArquivosPlanejamento.php?".Sessao::getId()."&stAcao=$stAcao", "É necessário configurar a IDE para gerar um arquivo compactado.", "", "aviso", Sessao::getId(), "../");
        die;
    }
    
    $obExportador->setNomeArquivoZip('AM_'.$inCodMunicipio.'_'.$inCodOrgao.'_'.$inMes.'_'.Sessao::getExercicio().'.zip');
}

$obExportador->show();
SistemaLegado::LiberaFrames();

?>