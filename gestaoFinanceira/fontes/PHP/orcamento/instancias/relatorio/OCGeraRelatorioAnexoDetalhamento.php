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
    * Página oculta para gerar relatório
    * Data de Criação   : 27/09/2004

    * @author Desenvolvedor: Eduardo Martins
    * @author Desenvolvedor: Gustavo Tourinho

    * @ignore

    $Revision: 31801 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.17
*/

include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                      );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRelatorioAnexoDetalhamento.class.php" );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF("L");

$stSituacao = "";

$rsAnexoDetalhamento = Sessao::read('rsAnexoDetalhamento');
$arFiltro = Sessao::read('filtroRelatorio');
if (Sessao::read('stSituacao') == "empenhados") {
    $stSituacao = " Valores - Empenhados";
} elseif (Sessao::read('stSituacao') == "pagos") {
    $stSituacao = " Valores - Pagos";
} elseif (Sessao::read('stSituacao') == "liquidados") {
    $stSituacao = " Valores - Liquidados";
}
// Adicionar logo nos relatorios
if ( count( $arFiltro['inCodEntidade'] ) == 1 ) {
    $obRRelatorio->setCodigoEntidade( $arFiltro['inCodEntidade'][0] );
    $obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
}

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$arConfiguracao['nom_acao'] = "Detalhamento do Programa de Trabalho";
$obPDF->setModulo            ( "Orçamento Geral" );
$obPDF->setTitulo            ( "Detalhamento do Programa de Trabalho" );
$obPDF->setSubTitulo         ( "Anexo Detalhamento - Exercício: ".Sessao::getExercicio() . $stSituacao);
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

// RecordSet para Relatorio
$rsAnexoDetalhamento->addFormatacao("vl_corrente", "NUMERIC_BR_NULL");
$rsAnexoDetalhamento->addFormatacao("vl_capital", "NUMERIC_BR_NULL");
$rsAnexoDetalhamento->addFormatacao("vl_total", "NUMERIC_BR_NULL");
$obPDF->addRecordSet( $rsAnexoDetalhamento );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho("CÓDIGO", 25, 10);
$obPDF->addCabecalho("DESCRIÇÃO", 30, 10);
$obPDF->setAlinhamento ( "R" );
$obPDF->addCabecalho("DESPESAS CORRENTES",15, 10);
$obPDF->addCabecalho("DESPESAS DE CAPITAL",15, 10);
$obPDF->addCabecalho("TOTAL", 15, 10);

$obPDF->addIndentacao("alinhamento","descricao","   ");
$obPDF->addQuebraLinha("descricao","TOTAL UNIDADE ....");
$obPDF->addQuebraProximaLinha("descricao","TOTAL UNIDADE ....");
$obPDF->addQuebraLinha("descricao","TOTAL ORGAO ....");
$obPDF->addQuebraProximaLinha("descricao","TOTAL ORGAO ....");

$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("dotacao", 8 );
$obPDF->addCampo("descricao", 8 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo("vl_corrente", 8 );
$obPDF->addCampo("vl_capital", 8 );
$obPDF->addCampo("vl_total", 8 );

$stDataInicial = implode('-',array_reverse(explode('/',$arFiltro['stDataInicial'])));
$stDataFinal = implode('-',array_reverse(explode('/',$arFiltro['stDataFinal'])));

include_once CAM_GF_CONT_MAPEAMENTO.'TContabilidadeNotasExplicativas.class.php';
$obTContabilidadeNotaExplicativa = new TContabilidadeNotasExplicativas;
$obTContabilidadeNotaExplicativa->setDado('cod_acao', Sessao::read('acao'));
$obTContabilidadeNotaExplicativa->setDado('dt_inicial', $stDataInicial);
$obTContabilidadeNotaExplicativa->setDado('dt_final', $stDataFinal);
$obTContabilidadeNotaExplicativa->recuperaNotaExplicativaRelatorio($rsAnexo);

$arNota = explode("\n", $rsAnexo->getCampo('nota_explicativa'));
$inCount = 0;
foreach ($arNota as $arNotaTMP) {
    $arRecordSetNota[$inCount]['nota'] = $arNotaTMP;
    $inCount++;
}

if ($rsAnexo->getCampo('nota_explicativa')) {
    $rsNota = new RecordSet;
    $rsNota->preenche($arRecordSetNota);
    $obPDF->addRecordSet($rsNota);
    $obPDF->setQuebraPaginaLista(false);

    $obPDF->addCabecalho("", 1,  10);
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("NOTAS EXPLICATIVAS", 90, 10);
    $obPDF->addCabecalho("", 1,  10);
    $obPDF->addCabecalho("", 1, 10);
    $obPDF->addCabecalho("", 1, 10);

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo("", 8 );
    $obPDF->addCampo("nota", 8 );
    $obPDF->addCampo("", 8 );
    $obPDF->addCampo("", 8 );
    $obPDF->addCampo("", 8 );
}

$arAssinaturas = Sessao::read('assinaturas');
if ( count($arAssinaturas['selecionadas']) > 0 ) {
    include_once( CAM_FW_PDF."RAssinaturas.class.php" );
    $obRAssinaturas = new RAssinaturas;
    $obRAssinaturas->setArAssinaturas( $arAssinaturas['selecionadas'] );
    $obPDF->setAssinaturasDefinidas( $obRAssinaturas->getArAssinaturas() );
}

$obPDF->show();
?>
