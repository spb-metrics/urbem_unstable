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
    * Página de Formulario de relatório Demostrativo de Saldos
    * Data de Criação   : 21/08/2006

    * @author Analista:
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    * $Id: OCDemonstrativoSaldos.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.04.24
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_TES_NEGOCIO."RTesourariaRelatorioDemonstrativoSaldos.class.php"  );
include_once( CAM_FW_PDF."RRelatorio.class.php" );

$obRRelatorio = new RRelatorio;
$obRTesourariaRelatorioDemonstrativoSaldos = new RTesourariaRelatorioDemonstrativoSaldos;

$stAcao = $request->get('stAcao');

if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$arFiltro = Sessao::read('filtroRelatorio');

$obRTesourariaRelatorioDemonstrativoSaldos->setCodEntidade     ($arFiltro['inCodEntidade']);
$obRTesourariaRelatorioDemonstrativoSaldos->setInicioPeriodo   ($arFiltro['stDataInicial']);
$obRTesourariaRelatorioDemonstrativoSaldos->setFimPeriodo      ($arFiltro['stDataFinal']);
$obRTesourariaRelatorioDemonstrativoSaldos->setInicioReduzido  ($arFiltro['inCodPlanoInicial']);
$obRTesourariaRelatorioDemonstrativoSaldos->setFimReduzido     ($arFiltro['inCodPlanoFinal']);
$obRTesourariaRelatorioDemonstrativoSaldos->setInicioEstrutural($arFiltro['stCodEstruturalInicial']);
$obRTesourariaRelatorioDemonstrativoSaldos->setFimEstrutural   ($arFiltro['stCodEstruturalFinal']);
$obRTesourariaRelatorioDemonstrativoSaldos->setCodRecurso      ($arFiltro['inCodRecurso']);
if ($arFiltro['inCodUso'] != "" && $arFiltro['inCodDestinacao'] != "" && $arFiltro['inCodEspecificacao'] != "") {
    $obRTesourariaRelatorioDemonstrativoSaldos->setDestinacaoRecurso( $arFiltro['inCodUso'].".".$arFiltro['inCodDestinacao'].".".$arFiltro['inCodEspecificacao'] );
}
$obRTesourariaRelatorioDemonstrativoSaldos->setCodDetalhamento ($arFiltro['inCodDetalhamento']);
$obRTesourariaRelatorioDemonstrativoSaldos->setOrdenacao       ($arFiltro['stOrdenacao']);
$obRTesourariaRelatorioDemonstrativoSaldos->setSemMovimentacao ($arFiltro['boMovimentacaoConta']);
$obRTesourariaRelatorioDemonstrativoSaldos->setExercicio       (Sessao::getExercicio());
$obRTesourariaRelatorioDemonstrativoSaldos->geraRecordSetBanco ($arRecordSet);

Sessao::write('arDados', $arRecordSet);

$obRRelatorio->executaFrameOculto( "OCGeraRelatorioDemonstrativoSaldos.php" );
