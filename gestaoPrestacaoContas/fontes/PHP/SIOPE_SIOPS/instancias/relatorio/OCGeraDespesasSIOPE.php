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
    * Página de Relatório Despesas SIOPE
    * Data de Criação  : 20/06/2008

    * @author Rodrigo Soares Rodrigues

    * Casos de uso : uc-02.01.40

    * $Id: OCGeraDespesasSIOPE.php 61605 2015-02-12 16:04:02Z diogo.zarpelon $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php";

$preview = new PreviewBirt(6,61,1);
$preview->setReturnURL(CAM_GPC_SIOPE_SIOPS_INSTANCIAS.'relatorio/FLDespesasSIOPE.php');
$preview->setTitulo('Despesas Municipais com Educação - SIOPE');
$preview->setVersaoBirt('2.5.0');

$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado('exercicio', Sessao::getExercicio());
$obTOrcamentoEntidade->recuperaEntidades($rsEntidade, "and e.cod_entidade in (".implode(',',$_REQUEST['inCodEntidade']).")");

$preview->addParametro('cod_entidade', implode(',', $_REQUEST['inCodEntidade']));

$preview->addParametro('orgao'  , $_REQUEST['inCodOrgao']);
$preview->addParametro('recurso', $_REQUEST['inCodRecurso']);
$preview->addParametro('descricao_recurso', $_REQUEST['stDescricaoRecurso']);

switch ($_REQUEST['inPeriodicidade']) {
case '1':
    $preview->addParametro('data_inicial', $_REQUEST['stDataInicial']);
    $preview->addParametro('data_final', $_REQUEST['stDataInicial']);
    break;
case '2':
    $preview->addParametro('data_inicial', $_REQUEST['stDataInicial']);
    $preview->addParametro('data_final', $_REQUEST['stDataFinal']);
    break;
case '3':
    $preview->addParametro('data_inicial', '01/01/'.Sessao::getExercicio());
    $preview->addParametro('data_final', '31/12/'.Sessao::getExercicio());
    break;
case '4':
    $preview->addParametro('data_inicial', $_REQUEST['stDataInicial']);
    $preview->addParametro('data_final', $_REQUEST['stDataFinal']);
    break;
}

$preview->addAssinaturas(Sessao::read('assinaturas'));
$preview->preview();
