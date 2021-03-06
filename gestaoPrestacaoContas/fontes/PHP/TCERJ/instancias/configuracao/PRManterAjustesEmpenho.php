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
set_time_limit(0);
/**
    * Página de Processamento - Ajuste de Parâmetros do Arquivo EMPENHO.TXT
    * Data de Criação   : 15/05/2006

    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Anderson C. Konze

    * @ignore

    $Revision: 59612 $
    $Name$
    $Autor: $
    $Date: 2014-09-02 09:00:51 -0300 (Ter, 02 Set 2014) $

    * Casos de uso: uc-02.08.14
*/

/*
$Log$
Revision 1.1  2007/09/24 20:03:12  hboaventura
Ticket#10234#

Revision 1.2  2006/07/05 20:46:20  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GPC_TCERJ_NEGOCIO."RExportacaoTCERJAjustesEmpenho.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterAjustesEmpenho";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obRegra = new RExportacaoTCERJAjustesEmpenho();

$obErro = new Erro;
$cont=0;
foreach ($_POST as $key=>$value) {
    if ( strstr( $key , "stNPL" ) ) {
       $arProcLicit = explode( "_" , $key );

       if ($value <> $arProcLicit[5]) {
           $cont++;
           $obRegra->setExercicio       ( Sessao::getExercicio() );
           $obRegra->setCodPreEmpenho   ( $arProcLicit[3] );
           $obRegra->setTimestamp       ( $_POST['timestamp_'.$arProcLicit[6]]);
           $obRegra->setCodAtributo     ( $arProcLicit[4] );
           $obRegra->setValor           ( $value );
           $obErro = $obRegra->salvarAjustes();
           if($obErro->ocorreu()) break;
        }

    }
}
if ( !$obErro->ocorreu() ) {
    SistemaLegado::alertaAviso($pgFilt."?".$stFiltro, " ".$cont." Atributos ajustados ", "incluir", "aviso", Sessao::getId(), "../");
} else {
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
}

?>
