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
* Arquivo de instância para manutenção de locais
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3347 $
$Name$
$Author: pablo $
$Date: 2005-12-05 11:05:04 -0200 (Seg, 05 Dez 2005) $

Casos de uso: uc-01.05.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_ORGAN_NEGOCIO."ROrganogramaLocal.class.php"     );

//Define o nome dos arquivos PHP
$stPrograma          = "ManterLocal";
$pgFilt              = "FL".$stPrograma.".php";
$pgList              = "LS".$stPrograma.".php";
$pgForm              = "FM".$stPrograma.".php";
$pgProc              = "PR".$stPrograma.".php";
$pgOcul              = "OC".$stPrograma.".php";
$pgJs                = "JS".$stPrograma.".js";

include_once( $pgJs );

switch ($_REQUEST ["stCtrl"]) {
    case "buscaLogradouro":
        $obROrganogramaLocal     = new ROrganogramaLocal;
        $rsLogradouro  = new RecordSet;

        if ( empty( $_REQUEST["inNumLogradouro"] ) ) {
            $js .= 'd.getElementById("campoInner").innerHTML = "&nbsp;";';
            $js .= "f.inCodSequencia.value = '';";
        } else {
            $obROrganogramaLocal->setCodLogradouro( $_REQUEST["inNumLogradouro"] ) ;
            $obROrganogramaLocal->listarLogradouros( $rsLogradouro );
            if ( $rsLogradouro->eof() ) {
                $js .= 'f.inNumLogradouro.value = "";';
                $js .= 'f.inNumLogradouro.focus();';
                $js .= 'd.getElementById("campoInner").innerHTML = "&nbsp;";';
                $js .= "alertaAviso('@Valor inválido. (".$_REQUEST["inNumLogradouro"].")','form','erro','".Sessao::getId()."');";
            } else {
                $stNomeLogradouro = $rsLogradouro->getCampo ("tipo_nome");
                $js .= "f.inCodSequencia.value = '".$rsLogradouro->getCampo("prox_sequencia")."';";
                $js .= "f.stNomeLogradouro.value = '$stNomeLogradouro';";
                $js .= 'd.getElementById("campoInner").innerHTML = "'.$stNomeLogradouro.'";';
            }
        }
        SistemaLegado::executaFrameOculto($js);
    break;
    case "buscaLogradouroFiltro":
        $obROrganogramaLocal     = new ROrganogramaLocal;
        $rsLogradouro  = new RecordSet;
        if ($_REQUEST["inNumLogradouro"]) {
            $obROrganogramaLocal->setCodigoLogradouro( $_REQUEST["inNumLogradouro"] ) ;
            $obROrganogramaLocal->listarLogradouros( $rsLogradouro );
        }

        if ( $rsLogradouro->eof() ) {
            $js .= 'f.inNumLogradouro.value = "";';
            $js .= 'f.inNumLogradouro.focus();';
            $js .= 'd.getElementById("campoInner").innerHTML = "&nbsp;";';
            $js .= "alertaAviso('@Valor inválido. (".$_REQUEST["inNumLogradouro"].")','form','erro','".Sessao::getId()."');";
        } else {
            $stNomeLogradouro = $rsLogradouro->getCampo ("tipo_nome");
            $js .= "f.stNomeLogradouro.value = '$stNomeLogradouro';";
            $js .= 'd.getElementById("campoInner").innerHTML = "'.$stNomeLogradouro.'";';
        }
        SistemaLegado::executaFrameOculto($js);
    break;
}

?>
