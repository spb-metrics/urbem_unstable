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
    * Oculto de Intervalo do Pop Up de Item
    * Data de Criação: 12/12/2007

    * @author Analista: Gelson W. Golçanves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    * $Id: OCIntervaloPopUpItem.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.03.20
    *               uc-03.03.24
    *               uc-03.03.25
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

$stJs = "";

switch ($_REQUEST["stCtrl"]) {

case "verificaDadosItem":
    if ($_REQUEST['inCodItemInicial'] == "" && $_REQUEST['inCodItemFinal'] != "") {
        $stJs .= "alertaAviso('@O código do item inicial não foi informado.','form','erro','".Sessao::getId()."', '');";
        $stJs .= "$('inCodItemFinal').value = '';";

    } elseif ($_REQUEST['inCodItemInicial'] != "" && $_REQUEST['inCodItemFinal'] != "") {
        if ($_REQUEST['inCodItemInicial'] > $_REQUEST['inCodItemFinal']) {
            $stJs .= "alertaAviso('@O código do item inicial não pode ser inferior ao código do item final.','form','erro','".Sessao::getId()."', '');";
            $stJs .= "$('".$_REQUEST['inObjId']."').value = '';";
        }
    }

    break;
}

if ($stJs != "") echo $stJs;

?>
