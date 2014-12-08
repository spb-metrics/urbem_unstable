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
    *
    * Data de Criação: 27/10/2005

    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
    * @author Documentor: Cassiano de Vasconcellos Ferreira

    * @package framework
    * @subpackage componentes

    Casos de uso: uc-01.01.00

    $Id: OCRelatorio.php 59612 2014-09-02 12:00:51Z gelson $
*/

set_time_limit(0);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"  );

$recebeRequest = $_REQUEST;
if (isset($recebeRequest['stTitulo'])) {
    $recebeRequest['stTitulo']=mb_strtoupper($recebeRequest['stTitulo'], 'UTF-8');
}
$acao="";
if (isset($recebeRequest['acao'])) {
    $acao = "&acao=".$recebeRequest['acao'];
}
Sessao::write('filtroRelatorio',$recebeRequest);

$js = "window.open('frame.php?".Sessao::getId().$acao."','relatorio','width=500,height=300');";
SistemaLegado::executaFrameOculto($js);
?>
