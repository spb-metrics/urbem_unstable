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

    $Id: OCGeraFichaPatrimonial.php 62830 2015-06-25 14:49:46Z jean $

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







            
this.queryText += "            FROM  patrimonio.bem                                                                      
this.queryText += "
this.queryText += "      INNER JOIN  sw_cgm
this.queryText += "              ON  sw_cgm.numcgm = bem.numcgm                                                                      
this.queryText += "
this.queryText += "      INNER JOIN  patrimonio.especie
this.queryText += "              ON  especie.cod_natureza = bem.cod_natureza
this.queryText += "             AND  especie.cod_grupo    = bem.cod_grupo
this.queryText += "             AND  especie.cod_especie  = bem.cod_especie                                                           
this.queryText += "      
this.queryText += "      INNER JOIN  patrimonio.grupo
this.queryText += "              ON  grupo.cod_natureza = especie.cod_natureza
this.queryText += "             AND  grupo.cod_grupo    = especie.cod_grupo                                                           
this.queryText += "        
this.queryText += "      INNER JOIN  patrimonio.natureza
this.queryText += "              ON  natureza.cod_natureza = grupo.cod_natureza                                                      
this.queryText += "
this.queryText += "       LEFT JOIN  patrimonio.historico_bem
this.queryText += "              ON  historico_bem.cod_bem = bem.cod_bem                                                                   
this.queryText += "
this.queryText += "      INNER JOIN  organograma.local
this.queryText += "              ON  local.cod_local = historico_bem.cod_local                                                   
this.queryText += "     
this.queryText += "      INNER JOIN  organograma.orgao
this.queryText += "              ON  orgao.cod_orgao = historico_bem.cod_orgao               
this.queryText += "     
this.queryText += "      INNER JOIN  organograma.orgao_descricao
this.queryText += "              ON  orgao_descricao.cod_orgao = orgao.cod_orgao               
this.queryText += "     
this.queryText += "      INNER JOIN  patrimonio.situacao_bem
this.queryText += "              ON  situacao_bem.cod_situacao = historico_bem.cod_situacao
this.queryText += "
this.queryText += "       LEFT JOIN  patrimonio.bem_comprado
this.queryText += "              ON  bem_comprado.cod_bem = bem.cod_bem                                                                    
this.queryText += "
this.queryText += "       LEFT JOIN  patrimonio.bem_baixado
this.queryText += "              ON  bem_baixado.cod_bem = bem.cod_bem
this.queryText += "                                                                                                                                                                                                               ";
this.queryText += "       LEFT JOIN ( SELECT *                                                                                                                                                                                    ";
this.queryText += "                     FROM patrimonio.reavaliacao                                                                                                                                                               ";
this.queryText += "                    WHERE reavaliacao.dt_reavaliacao = (SELECT MAX(dt_reavaliacao) FROM patrimonio.reavaliacao AS r WHERE reavaliacao.cod_bem = r.cod_bem AND reavaliacao.cod_reavaliacao = r.cod_reavaliacao) ";
this.queryText += "                 ) AS reavaliacao
this.queryText += "              ON reavaliacao.cod_bem = bem.cod_bem