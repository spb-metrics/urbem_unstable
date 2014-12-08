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
    * Página de Processamento do arquivo de relatorio de cargaPatrimonial
    * Data de Criação   : 07/01/2009

    * @author Analista: Gelson W
    * @author Desenvolvedor: Luiz Felipe Prestes Teixeira
    * @ignore

    $Id: $

    */

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$pgGera = "OCGeraRelatorioCargaPatrimonial.php";

$link  = "inCodOrgao=".$_REQUEST['hdnUltimoOrgaoSelecionado'];
$link .= "&inCodLocal=".$_REQUEST['inCodLocal'];
$link .= "&tipoRelatorio=".$_REQUEST['tipoRelatorio'];
$link .= "&inCodNatureza=".$_REQUEST['inCodNatureza'];
$link .= "&inCodGrupo=".$_REQUEST['inCodGrupo'];
$link .= "&inCodEspecie=".$_REQUEST['inCodEspecie'];
$link .= "&stClassificacaoReduzida=".$_REQUEST['hdninCodOrganograma'];

SistemaLegado::alertaAviso($pgGera."?".Sessao::getId()."&".$link,"Relatório de Carga Patrimonial","incluir","aviso", Sessao::getId(), "../" );

?>
