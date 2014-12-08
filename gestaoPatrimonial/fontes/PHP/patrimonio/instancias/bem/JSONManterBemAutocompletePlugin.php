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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioBem.class.php";

$stFiltro = " WHERE descricao ILIKE '".$_GET['q']."%' GROUP BY descricao ORDER BY descricao LIMIT 15";

$obTPatrimonioBem = new TPatrimonioBem();
$obTPatrimonioBem->recuperaDescricaoBem( $rsBem, $stFiltro );

$rsBem->setPrimeiroElemento();

$json = '[';

    while (!$rsBem->eof()) {
        $json.= '{"descricao":"'.ucfirst(addslashes($rsBem->getCampo('descricao'))).'"},';
        $rsBem->proximo();
    }

$json = substr($json, 0, -1).']';

echo $json;

?>
