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
    * Página de Processamento - Parâmetros do Arquivo CREDOR
    * Data de Criação   : 11/02/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Cleisson da Silva Barboza

    * @ignore

    $Revision: 12203 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 20:51:50 +0000 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.08.06
*/

/*
$Log$
Revision 1.7  2006/07/05 20:46:25  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_EXP_NEGOCIO."RExportacaoTCERSArqCredor.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterCredor";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Percorre os resultados do formulaáio adicionando os credores que possuem tipo informado
//$obRegra = new RExportacaoTCERSArqCredor();
$obRegra = new RExportacaoTCERSCredor();

$cont=0;
foreach ($_POST as $key=>$value) {
    if ( strstr( $key , "inTipoConversao" ) ) {
        $arCredor = explode( "_" , $key );
        if ($value<>"") {
            $cont++;
           // $obRegra->addCredor();
           // $obRegra->roUltimoCredor->setExercicio  ( $arCredor[2] );
           // $obRegra->roUltimoCredor->setNumCGM     ( $arCredor[1] );
           // $obRegra->roUltimoCredor->setTipoCredor  ( $value );
           $obRegra->setExercicio  ( $arCredor[2] );
           $obRegra->setNumCGM     ( $arCredor[1] );
           $obRegra->setTipoCredor  ( $value );
           $obErro = $obRegra->salvar();
           if($obErro->ocorreu()) break;
        }
    } elseif ( strstr( $key , "inTipo" ) ) {
        $arCredor = explode( "_" , $key );
        if ($value<>"") {
            $cont++;
            //$obRegra->addCredor();
            //$obRegra->roUltimoCredor->setExercicio   ( Sessao::getExercicio() );
            //$obRegra->roUltimoCredor->setNumCGM      ( $arCredor[1] );
            //$obRegra->roUltimoCredor->setTipoCredor  ( $value );
            $obRegra->setExercicio   ( Sessao::getExercicio() );
            $obRegra->setNumCGM      ( $arCredor[1] );
            $obRegra->setTipoCredor  ( $value );
            $obErro = $obRegra->salvar();
            if($obErro->ocorreu()) break;
        }
    }

}

if ( !$obErro->ocorreu() ) {
    SistemaLegado::alertaAviso($pgFilt."?".$stFiltro, " ".$cont." credores incluídos/alterados ", "incluir", "aviso", Sessao::getId(), "../");
} else {
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
}

?>
