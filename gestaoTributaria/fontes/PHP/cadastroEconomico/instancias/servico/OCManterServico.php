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
    * Página de Frame Oculto  Inclusao/Alteracao de Serviços
    * Data de Criação   : 22/11/2004

    * @author Tonismar Régis Bernardo

    * @ignore

    * $Id: OCManterServico.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.02.03
*/

/*
$Log$
Revision 1.5  2006/09/15 14:33:40  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMServico.class.php" );
include_once ( CAM_GT_CEM_COMPONENTES."MontaServico.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterServico";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgFormNivel = "FM".$stPrograma."Nivel.php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obMontaServico = new MontaServico;
$obMontaServico->setCodigoVigenciaServico ( $_REQUEST["inCodigoVigencia"] );

switch ($_REQUEST["stCtrl"]) {
    case "preencheProxComboServico":
        $stNomeComboServico = "inCodServico_".( $_REQUEST["inPosicao"] - 1);
        $stChaveLocal = $_REQUEST[$stNomeComboServico];
        $inPosicao = $_REQUEST["inPosicao"];
        if ( empty( $stChaveLocal ) and $_REQUEST["inPosicao"] > 2 ) {
            $stNomeComboServico = "inCodServico_".( $_REQUEST["inPosicao"] - 2);
            $stChaveLocal = $_REQUEST[$stNomeComboServico];
            $inPosicao = $_REQUEST["inPosicao"] - 1;
        }
        $arChaveLocal = explode("-" , $stChaveLocal );
        $obMontaServico->setCodigoVigenciaServico    ( $_REQUEST["inCodigoVigencia"] );
        $obMontaServico->setCodigoNivelServico       ( $arChaveLocal[0] );
        $obMontaServico->setCodigoServico            ( $arChaveLocal[1] );
        $obMontaServico->setValorReduzidoServico     ( $arChaveLocal[3] );
        $obMontaServico->preencheProxCombo           ( $inPosicao , $_REQUEST["inNumNiveisServico"] );
    break;
    case "preencheCombosServico":
        $obMontaServico->setCodigoVigenciaServico( $_REQUEST["inCodigoVigencia"]   );
        $obMontaServico->setCodigoNivelServico   ( $_REQUEST["inCodigoNivel"]      );
        $obMontaServico->setValorReduzidoServico ( $_REQUEST["stChaveServico"] );
        $obMontaServico->preencheCombos();
    break;
}
