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
  * Página que gera o Relatório Anexo II - TCEMG
  * Data de Criação: 15/07/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  * @ignore
  * $Id: OCGeraRelatorioAnexoII.php 61189 2014-12-12 19:46:17Z arthur $
  * $Date: 2014-12-12 17:46:17 -0200 (Sex, 12 Dez 2014) $
  * $Author: arthur $
  * $Rev: 61189 $
  *
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$preview = new PreviewBirt(6,55,2);
$preview->setTitulo('Relatorio de Demostrativo dos Gastos com a Manutenção e Desenvolvimento do Ensino');
$preview->setVersaoBirt( '2.5.0' );

$boRestos = $request->get("stRestos") == "true" ? "true" : "false";

$preview->addParametro("stExercicio"  , Sessao::getExercicio());
$preview->addParametro("stFiltro"     , "AND od.cod_entidade IN (2) AND od.cod_recurso = 101 AND od.cod_funcao = 12 AND od.cod_subfuncao IN (122,272,361,365,367)");
$preview->addParametro("stDataInicial", $request->get("stDataInicial"));
$preview->addParametro("stDataFinal"  , $request->get("stDataFinal"));
$preview->addParametro("stSituacao"   , $request->get("stSituacao"));
$preview->addParametro("boRestos"     , $boRestos);
$preview->addParametro("stPeriodo"    , $request->get("stDataInicial")." at&eacute; ".$request->get("stDataFinal"));
$preview->preview();
?>