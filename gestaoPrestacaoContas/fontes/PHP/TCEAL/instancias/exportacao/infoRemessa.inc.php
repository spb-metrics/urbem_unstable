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

    * Página de Include Oculta - Exportação Arquivos Relacionais - InfoRemessa.xml
    *
    * Data de Criação: 30/05/2014
    *
    * @author: Carolina Schwaab Marçal
    *
    * $Id: infoRemessa.inc.php 60665 2014-11-06 18:59:33Z lisiane $
    *
    * @ignore
    *
*/

include_once CAM_GRH_ENT_MAPEAMENTO.'TEntidade.class.php';
include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
include_once CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALInfoRemessa.class.php';
include_once CAM_GPC_TCEAL_MAPEAMENTO.'TExportacaoRelacionais.class.php';

$undGestora = explode(',', $stEntidades);
$obTTCEALInfoRemessa = new TTCEALInfoRemessa();
$obTExportacaoRelacionais = new TExportacaoRelacionais();

foreach ($undGestora as $inCodEntidade) {

    $obTTCEALInfoRemessa->setDado('stExercicio', Sessao::getExercicio());
    $obTTCEALInfoRemessa->setDado('inCodEntidade', $inCodEntidade );
    $obTTCEALInfoRemessa->setDado('inBimestre', $inBimestre );
    $obTTCEALInfoRemessa->listarExportacaoInfoRemessa($rsRecordSet);
 
    $stNomeArquivo ="InfoRemessa";
    $arResult = array();
    $idCount = 0;

    while (!$rsRecordSet->eof()) {
        $arResult[$idCount]['CodUndGestora'] = $rsRecordSet->getCampo('cod_und_gestora');
        $arResult[$idCount]['CodigoUA']      = $rsRecordSet->getCampo('codigo_ua');
        $arResult[$idCount]['Bimestre']      = $inBimestre;
        $arResult[$idCount]['Exercicio']     = Sessao::getExercicio();
        $arResult[$idCount]['DataInicio']    = $dtInicial;
        $arResult[$idCount]['DataFim']       = $dtFinal;
        $arResult[$idCount]['DataGeracao']   = date('d/m/Y');
        $idCount++;

        $rsRecordSet->proximo();
    }
    
    return $arResult;
}
?>