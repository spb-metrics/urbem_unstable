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
    * Página do Oculto que gera o relatório do Relatório Restos a Pagar Anulado, Pagamentos ou Estorno
    * Data de Criação   : 08/09/2008

    * @author Analista: Tonismar R. Bernardo
    * @author Desenvolvedor: Henrique Girardi dos Santos

    * @package URBEM
    * @subpackage

    * @ignore

    * $Id: OCGeraRelatorioRestosPagarAnuladoPagamentoEstorno.php 64153 2015-12-09 19:16:02Z evandro $

    * Casos de uso : uc-02.03.08
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';


// Faz a verificação, caso a situacao seja 1,2,3, chama o relatorio = 3, senão chama o 4.
$preview = new PreviewBirt(2, 10, ($_REQUEST['inSituacao'] < 4 ? 3 : 4));
$preview->setTitulo('Relatório do Birt');
$preview->setVersaoBirt( '2.5.0' );
$preview->setExportaExcel( true );

$stCodEntidades = implode(', ', $_REQUEST['inCodEntidade']);
$preview->addParametro('entidade_resto', $stCodEntidades);

$stIncluirAssinaturas = $_REQUEST['stIncluirAssinaturas'];
if ($stIncluirAssinaturas == 'nao') {
    $stIncluirAssinaturas = 'não';
} else {
    $stIncluirAssinaturas = 'sim';
}
$preview->addParametro('incluir_assinaturas', $stIncluirAssinaturas);

if (count($_REQUEST['inCodEntidade']) > 1) {
    $stWhere = "where exercicio='".Sessao::getExercicio()."' and parametro='cod_entidade_prefeitura'";
    $inCodEntidade = SistemaLegado::pegaDado('valor', 'administracao.configuracao', $stWhere);
    $preview->addParametro('entidade', $inCodEntidade);
} else {
    $preview->addParametro('entidade', $_REQUEST['inCodEntidade'][0]);
}

$arEntidades = Sessao::read('arEntidades');
foreach ($_REQUEST['inCodEntidade'] as $inCodEntidade) {
    $arEntidadesAux[] = $arEntidades[$inCodEntidade];
}
$stNomeEntidade = implode("<br/>", $arEntidadesAux);

$preview->addParametro('entidade_descricao', $stNomeEntidade);
$preview->addParametro('exercicio', Sessao::getExercicio());
if ($_REQUEST['inExercicio'] != '') {
    $preview->addParametro('exercicio_resto', $_REQUEST['inExercicio']);
} else {
    $preview->addParametro('exercicio_resto', '');
}
$preview->addParametro('data_inicial', $_REQUEST['stDataInicial']);
$preview->addParametro('data_final', $_REQUEST['stDataFinal']);

// Esses 2 relatórios forma unificados, e como já existiam as PLs prontas para os 2 relatórios, não havia a necessidade de unificar as 2 PLs.
// Com isso foi criado 2 datasets no birt para chamar os relatórios, com isso o tipo_relatório faz a diferença entre eles, e na PL do
// pago_estornado, os códigos da situação são 1 e 2, por isso foi escreve-se esse valor quando o relatório é pago_estornado.
switch ($_REQUEST['inSituacao']) {
case 1:
    $preview->addParametro('situacao_descricao' , 'Anulados');
    $preview->addParametro('situacao'           , $_REQUEST['inSituacao']);
    $preview->addParametro('stCodFuncao'        , $request->get('stCodFuncao'));
    $preview->addParametro('stCodSubFuncao'     , $request->get('stCodSubFuncao'));
    break;
case 2:
    $preview->addParametro('situacao_descricao' , 'Liquidados');
    $preview->addParametro('situacao'           , $_REQUEST['inSituacao']);
    $preview->addParametro('stCodFuncao'        ,$request->get('stCodFuncao'));
    $preview->addParametro('stCodSubFuncao'     ,$request->get('stCodSubFuncao'));
    break;
case 3:
    $preview->addParametro('situacao_descricao' , 'Anulados (Liquidados)');
    $preview->addParametro('situacao'           , $_REQUEST['inSituacao']);
    $preview->addParametro('stCodFuncao'        , $request->get('stCodFuncao'));
    $preview->addParametro('stCodSubFuncao'     , $request->get('stCodSubFuncao'));
    break;
case 4:
    $preview->addParametro('situacao_descricao' , 'Pagamentos');
    $preview->addParametro('situacao'           , '1');
    $preview->addParametro('stCodFuncao'        , $request->get('stCodFuncao'));
    $preview->addParametro('stCodSubFuncao'     , $request->get('stCodSubFuncao'));
    break;
case 5:
    $preview->addParametro('situacao_descricao' , 'Estornos');
    $preview->addParametro('situacao'           , '2');
    $preview->addParametro('stCodFuncao'        , $request->get('stCodFuncao'));
    $preview->addParametro('stCodSubFuncao'     , $request->get('stCodSubFuncao'));
    break;
}

include_once CAM_GF_EMP_NEGOCIO."REmpenhoRelatorioRPAnuLiqEstLiq.class.php";
$obREmpenhoRPAnuLiqEstLiq = new REmpenhoRelatorioRPAnuLiqEstLiq;

if ($_REQUEST['inCodOrgao'] != "") {
    $obREmpenhoRPAnuLiqEstLiq->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setExercicio($_REQUEST["inExercicio"]);
    $obREmpenhoRPAnuLiqEstLiq->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($_REQUEST["inCodOrgao"]);
    $obREmpenhoRPAnuLiqEstLiq->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->listar($rsOrgao);

    $preview->addParametro('cod_orgao', $_REQUEST['inCodOrgao']);
    $preview->addParametro('orgao_descricao',
                            $_REQUEST['inCodOrgao'].' - '.$rsOrgao->getCampo('nom_orgao'));
} else {
    $preview->addParametro('cod_orgao', '');
    $preview->addParametro('orgao_descricao', '');
}

if ($_REQUEST['inCodUnidade'] != "") {
    $obREmpenhoRPAnuLiqEstLiq->obROrcamentoUnidadeOrcamentaria->setNumeroUnidade($_REQUEST["inCodUnidade"]);
    $obREmpenhoRPAnuLiqEstLiq->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($_REQUEST["inCodOrgao"]);
    $obREmpenhoRPAnuLiqEstLiq->obROrcamentoUnidadeOrcamentaria->setExercicio($_REQUEST["inExercicio"]);
    $obREmpenhoRPAnuLiqEstLiq->obROrcamentoUnidadeOrcamentaria->consultar($rsUnidade,
                                                                          $stFiltro,
                                                                          "",
                                                                          $boTransacao);

    $preview->addParametro('cod_unidade'      , $_REQUEST['inCodUnidade']);
    $preview->addParametro('unidade_descricao',
                            $_REQUEST['inCodUnidade'].' - '.$rsUnidade->getCampo('nom_unidade'));
} else {
    $preview->addParametro('cod_unidade'      , '');
    $preview->addParametro('unidade_descricao', '');
}

if ($_REQUEST['inCodFornecedor'] != "") {
    include_once(CAM_GA_CGM_NEGOCIO."RCGM.class.php");
    $RCGM = new RCGM;
    $RCGM->setNumCGM($_REQUEST["inCodFornecedor"]);
    $RCGM->listar($rsDadosCGM);
    $stDescFornecedor = $rsDadosCGM->getCampo("nom_cgm");
    $preview->addParametro('cod_credor', $_REQUEST['inCodFornecedor']);
    $preview->addParametro('nome_credor',
                           $_REQUEST['inCodFornecedor'].' - '.$stDescFornecedor);
} else {
    $preview->addParametro('cod_credor', '');
    $preview->addParametro('nome_credor', '');
}

if (trim($_REQUEST['inCodDespesa']) != "") {
    $stWhere  = " where exercicio='".Sessao::getExercicio()."'";
    $stWhere .= " and cod_estrutural = '".$_REQUEST['inCodDespesa']."'";
    $stDescricao = SistemaLegado::pegaDado('descricao',
                                             'orcamento.conta_despesa',
                                             $stWhere);
    $preview->addParametro('elemento_despesa',
                           str_replace(".","",$_REQUEST['inCodDespesa']));
    $preview->addParametro('elemento_despesa_masc', $_REQUEST['inCodDespesa']);

    $stDespesaDescricao = $_REQUEST['inCodDespesa'].' - '. $stDescricao;
    $preview->addParametro('despesa_descricao', $stDespesaDescricao);
} else {
    $preview->addParametro('elemento_despesa', '');
    $preview->addParametro('despesa_descricao', '');
}

if ($_REQUEST['inCodRecurso'] != "") {
    include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecurso.class.php");
    $obRegra = new TOrcamentoRecurso();
    $obRegra->setDado("cod_recurso", "'".$_REQUEST['inCodRecurso']."'" );
    $obRegra->setDado("exercicio"  , Sessao::getExercicio()            );
    $obRegra->recuperaRelacionamento( $rsLista );
    $stDescricaoRecurso  = $_REQUEST['inCodRecurso'];
    $stDescricaoRecurso .= ' - '.$rsLista->getCampo("nom_recurso");

    $preview->addParametro('cod_recurso'      , $_REQUEST['inCodRecurso']);
    $preview->addParametro('recurso_descricao', $stDescricaoRecurso);
} else {
    $preview->addParametro('cod_recurso'      , '');
    $preview->addParametro('recurso_descricao', '');
}

if ($_REQUEST['inCodUso'] != "" && $_REQUEST['inCodDestinacao'] != "" && $_REQUEST['inCodEspecificacao'] != "") {
    $preview->addParametro('destinacao_recurso', $_REQUEST['inCodUso'].$_REQUEST['inCodDestinacao'].$_REQUEST['inCodEspecificacao']);
} else {
    $preview->addParametro('destinacao_recurso', '');
}

if ($_REQUEST['inCodDetalhamento'] != "") {
    $preview->addParametro('cod_detalhamento', $_REQUEST['inCodDetalhamento']);
} else {
    $preview->addParametro('cod_detalhamento', '');
}

if (Sessao::getExercicio() > '2012') {
    $preview->addParametro('boTCEMS', 'true');
} else {
    $preview->addParametro('boTCEMS', 'false');
}

$preview->addAssinaturas( Sessao::read('assinaturas') );
$preview->preview();

?>
