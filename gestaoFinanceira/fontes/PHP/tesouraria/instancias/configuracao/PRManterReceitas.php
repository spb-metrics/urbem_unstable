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
    * Página de Processamento de Receitas
    * Data de Criação   : 12/09/2005

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 31732 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.04.03
*/

/*
$Log$
Revision 1.6  2006/07/05 20:39:21  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GF_CONT_NEGOCIO."RContabilidadePlanoContaAnalitica.class.php" );

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterReceitas";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

foreach ($_POST as $stConta => $stValorCredito) {
    if ( strpos( $stConta, 'CodCredito' ) and !strpos( $stConta, 'stCodCredito' ) ) {
        list( $stNomVariavel, $stTipoConta, $stExercicio, $inCodPlano, $inCount ) = explode( '_', $stConta );

        $obRContabilidadePlanoContaAnalitica = new RContabilidadePlanoContaAnalitica();
        $obRContabilidadePlanoContaAnalitica->setCodPlano ( $inCodPlano  );
        $obRContabilidadePlanoContaAnalitica->setExercicio( $stExercicio );
        if ($stValorCredito) {
            list( $inCodCredito, $inCodEspecie, $inCodGenero, $inCodNatureza ) = explode( '.', $stValorCredito );
            $obRContabilidadePlanoContaAnalitica->addCredito();
            $obRContabilidadePlanoContaAnalitica->roUltimoCredito->setCodCredito ( $inCodCredito  );
            $obRContabilidadePlanoContaAnalitica->roUltimoCredito->setCodEspecie ( $inCodEspecie  );
            $obRContabilidadePlanoContaAnalitica->roUltimoCredito->setCodGenero  ( $inCodGenero   );
            $obRContabilidadePlanoContaAnalitica->roUltimoCredito->setCodNatureza( $inCodNatureza );
        }
        $arContaAnaliticaCredito[] = $obRContabilidadePlanoContaAnalitica;
    }
}

$obRContabilidadePlanoContaAnalitica = new RContabilidadePlanoContaAnalitica();
$obRContabilidadePlanoContaAnalitica->setContaAnaliticaCredito( $arContaAnaliticaCredito );

$obErro = $obRContabilidadePlanoContaAnalitica->salvarContasAnaliticaCredito();

$pgProx = ( $_POST['stRedireciona'] ) ? $_POST['stRedireciona'] : $pgForm;

echo "<script language='JavaScript'>LiberaFrames(true,false);</script>";
if ( !$obErro->ocorreu() )
    SistemaLegado::exibeAviso("Configuração da Tesouraria","alterar","aviso");
else
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");

?>
