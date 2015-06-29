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

    * Data de Criação   : 18/01/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    $Revision: 62823 $
    $Name$
    $Author: hboaventura $
    $Date: 2008-08-21 11:36:17 -0300 (Qui, 21 Ago 2008) $

    * Casos de uso: uc-06.03.00
*/

/*
$Log$
Revision 1.3  2007/10/01 04:46:08  diego
Comentado rodapé e adicionada variavel de unidade gestora utilizada nos arquivos

Revision 1.2  2007/09/27 12:53:57  hboaventura
adicionando arquivos

Revision 1.1  2007/06/22 22:50:37  diego
Primeira versão.

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GPC_TCMBA_MAPEAMENTO.Sessao::getExercicio()."/TTBAConfiguracao.class.php" );
include_once( CLA_EXPORTADOR );

$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

$arFiltro = Sessao::read('filtroRelatorio');

$inMes          = $arFiltro['inMes'];
$stEntidades    = $arFiltro['inCodEntidade'];

$stTipoDocumento = "TCM_BA";
$obExportador    = new Exportador();

$obTMapeamento = new TTBAConfiguracao();
$obTMapeamento->setDado( 'exercicio', Sessao::getExercicio() );
$obTMapeamento->setDado( 'cod_entidade', $stEntidades );
$obTMapeamento->recuperaUnidadeGestoraEntidade( $rsEntidade );
$inCodUnidadeGestora = $rsEntidade->getCampo('cod_unidade_gestora');

$stDataInicial = $arFiltro['stDataInicial'];
$stDataFinal   = $arFiltro['stDataFinal'];

Sessao::write('cod_unidade_gestora',$rsEntidade->getCampo('cod_unidade_gestora'));
Sessao::write('nom_unidade'        ,$rsEntidade->getCampo('nom_entidade'));

foreach ($arFiltro["arArquivosSelecionados"] as $stArquivo) {

    include( CAM_GPC_TCMBA_INSTANCIAS."layout_arquivos/".Sessao::getExercicio()."/".substr($stArquivo,0,strpos($stArquivo,'.txt')) . ".inc.php");

    $arRecordSet = null;

}

if ($arFiltro['stTipoExport'] == 'compactados') {
    $obExportador->setNomeArquivoZip('ExportacaoArquivosPrincipais.zip');
}

$obExportador->show();
SistemaLegado::LiberaFrames();
ob_end_flush();
?>
