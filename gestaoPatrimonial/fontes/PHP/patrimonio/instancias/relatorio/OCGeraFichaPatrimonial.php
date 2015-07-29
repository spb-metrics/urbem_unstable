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
    * Página de geração de relatório
    * Data de criação : 04/11/2005

    * @author Analista:
    * @author Programador: Fernando Zank Correa Evangelista

    Caso de uso: uc-03.01.09

    $Id: OCGeraFichaPatrimonial.php 62897 2015-07-06 21:55:07Z jean $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$obRelatorio = new PreviewBirt(3, 6, 14);
$obRelatorio->setVersaoBirt('2.5.0');

$obRelatorio->addParametro('inCodBemInicial' , $_REQUEST['inCodBemInicial']);
$obRelatorio->addParametro('inCodBemFinal'   , $_REQUEST['inCodBemFinal']);
$obRelatorio->addParametro('inCodOrgao'      , $_REQUEST['hdnUltimoOrgaoSelecionado']);
$obRelatorio->addParametro('inCodLocal'      , $_REQUEST['inCodLocal']);
$obRelatorio->addParametro('inCodNatureza'   , $_REQUEST['inCodNatureza']);
$obRelatorio->addParametro('inCodGrupo'      , $_REQUEST['inCodGrupo']);
$obRelatorio->addParametro('inCodEspecie'    , $_REQUEST['inCodEspecie']);

$obRelatorio->addParametro('boQuebraPagina'  , $_REQUEST['boQuebraPagina']);
$obRelatorio->addParametro('stTipoRelatorio' , $_REQUEST['stTipoRelatorio']);
$obRelatorio->addParametro('stHistorico'     , $_REQUEST['stHistorico']);

$obRelatorio->addParametro('stDataInicial'                , $_REQUEST['stDataInicial']);
$obRelatorio->addParametro('stDataFinal'                  , $_REQUEST['stDataFinal']);
$obRelatorio->addParametro('stPeriodoInicialIncorporacao' , $_REQUEST['stPeriodoInicialIncorporacao']);
$obRelatorio->addParametro('stPeriodoFinalIncorporacao'   , $_REQUEST['stPeriodoFinalIncorporacao']);
$obRelatorio->addParametro('stDepreciacoes'               , $_REQUEST['stDepreciacoes']);

$obRelatorio->preview();

?>