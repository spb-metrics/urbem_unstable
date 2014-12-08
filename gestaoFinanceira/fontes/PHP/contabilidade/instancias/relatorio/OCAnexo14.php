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
    * Arquivo Oculo para processamento do relatório
    * Data de Criação   : 03/05/2005

    * @author Analista: Gelson
    * @author Desencolvedor: Marcelo B. Paulino

    * @ignore

    $Id: OCAnexo14.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.11

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                           );
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadeRelatorioAnexo14.class.php" );

$obRRelatorio = new RRelatorio;
$obRegra      = new RContabilidadeRelatorioAnexo14;

//seta elementos do filtro
$stFiltro = "";

//seta elementos do filtro para ENTIDADE
$arFiltro = Sessao::read('filtroRelatorio');
if ($arFiltro['inCodEntidade'] != "") {
    $stFiltro .= "cod_entidade IN  (";
    foreach ($arFiltro['inCodEntidade'] as $key => $valor) {
        $stFiltro .= $valor." , ";
    }
    $stFiltro = substr( $stFiltro, 0, strlen($stFiltro) - 2 ) . ")  ";
} else {
    $stFiltro .= $arFiltro['stTodasEntidades'];
}
$obRegra->setFiltro         ( $stFiltro );
$obRegra->setExercicio      ( Sessao::getExercicio() );
$obRegra->setDtInicial      ( $arFiltro['stDataInicial'] );
$obRegra->setDtFinal        ( $arFiltro['stDataFinal'] );
$obRegra->setTipoRelatorio  ( $arFiltro['inTipoRelatorio'] );

$obRegra->geraRecordSet( $rsRecordSet );
Sessao::write('rsRecordSet', $rsRecordSet);
$obRRelatorio->executaFrameOculto( "OCGeraRelatorioAnexo14.php" );
?>
