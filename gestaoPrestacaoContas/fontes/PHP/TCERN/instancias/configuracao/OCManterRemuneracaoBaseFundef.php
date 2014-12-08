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
    * Página de Oculto do TCE-RN Configurar Remuneração Base Fundef
    * Data de Criação: 22/07/2013

    * @author Analista
    * @author Desenvolvedor Tallis

    * @package URBEM
    * @subpackage

    * @ignore

    $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterRemuneracaoBaseFundef";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function atualizarEventos()
{
    include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEvento.class.php"                                    );

    $obRFolhaPagamentoEvento = new RFolhaPagamentoEvento();
    $obRFolhaPagamentoEvento->listarEvento($rsEventos);

    // Busca a os codigos de lotacao definidos na configuracao
    $stCampo   = "valor";
    $stTabela  = "administracao.configuracao";
    $stFiltro  = " WHERE exercicio = '".Sessao::getExercicio()."'";
    $stFiltro .= "   AND parametro = 'remuneracao_base_fundef".Sessao::getEntidade()."' ";
    $stFiltro .= "   AND cod_modulo = 49 ";

    $stFiltroEventos = SistemaLegado::pegaDado($stCampo, $stTabela, $stFiltro);
    if (strpos($stFiltroEventos, ',') !== false) {
        $arFiltroEventos = explode(',', $stFiltroEventos);
    } else {
        $arFiltroEventos = array();
        if (strlen(trim($stFiltroEventos)) > 0) { //string contem somente um numero
            $arFiltroEventos[] = $stFiltroEventos;
        }
    }

    $arSelectMultiploEventos = Sessao::read("arSelectMultiploEventos");

    // Atualizando o componente na tela!
    if (is_array($arSelectMultiploEventos) && !empty($arSelectMultiploEventos)) {
        foreach ($arSelectMultiploEventos as $obSelectMultiploEventos) {
            if ($obSelectMultiploEventos->getName() == 'inCodEvento') {

                //atualiza
                $stJs .= "jQuery('#" . $obSelectMultiploEventos->getNomeLista2() . " option').each(function () {jQuery(this).remove();});";
                while (!$rsEventos->eof()) {
                    if (is_array($arFiltroEventos) and in_array($rsEventos->getCampo("cod_evento"), $arFiltroEventos)) {
                        $stJs .= "	jQuery('#" . $obSelectMultiploEventos->getNomeLista2() . "').addOption('" . $rsEventos->getCampo("cod_evento") . "','" . $rsEventos->getCampo("codigo") . " - " . $rsEventos->getCampo("descricao") . "', false);";
                        $stJs .= "	jQuery('#" . $obSelectMultiploEventos->getNomeLista1() . "').removeOption('" . $rsEventos->getCampo("cod_evento") . "');";
                    }
                    $rsEventos->proximo();
                }
            }
        }
    }

    return $stJs;
}

switch ($_REQUEST['stCtrl']) {
    case "atualizarEventos":
        $stJs = atualizarEventos();
        break;
}
if ($stJs) {
    echo $stJs;
}

?>
