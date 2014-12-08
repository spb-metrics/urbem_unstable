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
    * Página Oculta - Exportação Arquivos GF

    * Data de Criação   :

    * @author Analista: Gelson
    * @author Desenvolvedor: Vitor Hugo

    * @ignore

    $Id: OCExportacaoBalanco.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GPC_TGO_MAPEAMENTO."TTGOConfiguracaoEntidade.class.php" );
include_once( CLA_EXPORTADOR );

SistemaLegado::BloqueiaFrames();

$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

$arFiltroRelatorio = Sessao::read('filtroRelatorio');

$stEntidades    = implode(",",$arFiltroRelatorio['inCodEntidade']);
$arUnidadesGestoras = array();

$obTConfiguracao = new TTGOConfiguracaoEntidade();
$obTConfiguracao->setDado('parametro','tc_codigo_unidade_gestora');

foreach ($arFiltroRelatorio['inCodEntidade'] as $inCodEntidade) {
    $obTConfiguracao->setDado('cod_entidade', $inCodEntidade );
    $obTConfiguracao->consultar();
    if ( trim($arUnidadesGestoras[ $obTConfiguracao->getDado('valor') ]) ) {
        $arUnidadesGestoras[ $obTConfiguracao->getDado('valor') ] .= ',';
    }
    $arUnidadesGestoras[ $obTConfiguracao->getDado('valor') ] .= $inCodEntidade;
}

$stTipoDocumento = "TCM_GO";

$obExportador    = new Exportador();

foreach ($arFiltroRelatorio["arArquivosSelecionados"] as $stArquivo) {
    //foreach ($arUnidadesGestoras as $inUnidadeGestora => $stEntidades) {
        $arArquivo = explode( '.',$stArquivo );
        if ($stArquivo == 'Ide.txt' OR $stArquivo == 'Orgao.txt') {
            $obExportador->addArquivo($stArquivo);
        } else {
            $obExportador->addArquivo($arArquivo[0].Sessao::getExercicio().'.'.$arArquivo[1]);
        }
        //$//obExportador->addArquivo($inUnidadeGestora.Sessao::getExercicio().$stArquivo);
        $obExportador->roUltimoArquivo->setTipoDocumento($stTipoDocumento);
        include( substr($stArquivo,0,strpos($stArquivo,'.txt')) . ".inc.php");
        $arRecordSet = null;
    //}
}

if ($arFiltroRelatorio['stTipoExport'] == 'compactados') {
    $obExportador->setNomeArquivoZip('Balanco'.Sessao::getExercicio().'.zip');
}

$obExportador->show();
SistemaLegado::LiberaFrames();
?>
