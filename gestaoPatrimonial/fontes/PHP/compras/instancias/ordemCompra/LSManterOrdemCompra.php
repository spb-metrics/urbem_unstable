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
    * Página de lista de Ordem de compra
    * Data de Criação   : 22/09/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @ignore

    * $Id: LSManterOrdemCompra.php 64086 2015-12-01 11:48:08Z diogo.zarpelon $

    * Casos de uso: uc-03.04.24
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GP_COM_MAPEAMENTO."TComprasOrdem.class.php");

$stPrograma = "ManterOrdemCompra";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stCaminho = CAM_GP_COM_INSTANCIAS."ordemCompra/";

$stAcao = $request->get("stAcao");

$stTipoOrdem = ( strpos($stAcao,'OS')===false ) ? 'C' : 'S';

if ($stAcao == 'reemitir' || $stAcao == 'reemitirOS') {
    $pgProx = $pgProc;
} else {
    $pgProx = $pgForm;
}

$paginando = Sessao::read('paginando');

//filtros
if (!$paginando) {
    foreach ($_POST as $stCampo => $stValor) {
        $filtro[$stCampo] = $stValor;
    }
    Sessao::write('filtro',$filtro);
    Sessao::write('pg' , ($_GET['pg'] ? $_GET['pg'] : 0));
    Sessao::write('pos', ($_GET['pos']? $_GET['pos'] : 0));
    Sessao::write('paginando', true);
} else {
    Sessao::write('pg',  $_GET['pg']);
    Sessao::write('pos', $_GET['pos']);
}

$filtro = Sessao::read('filtro');
if ($filtro) {
    foreach ($filtro as $key => $value) {
        $_REQUEST[$key] = $value;
    }
}
Sessao::write('paginando', true);

// utilizado pelo FM
$arFiltros['filtro'] = Sessao::read('filtro');
$arFiltros['pg'] = Sessao::read('pg');
$arFiltros['pos'] = Sessao::read('pos');
$arFiltros['paginando'] = true;

Sessao::write('arFiltros', $arFiltros);

if (is_array($_REQUEST["inCodEntidade"])) {
    $filtro['inCodEntidade'] = implode(",", $_REQUEST["inCodEntidade"]);
}

Sessao::write('filtro',$filtro);
Sessao::write ('stIncluirAssinaturaUsuario', $_REQUEST['stIncluirAssinaturaUsuario']);

/***************************
   FILTRAGEM DOS DADOS
***************************/

if ( strpos($stAcao,'incluir') === false ) {
    if ($_REQUEST["inCodOrdemCompra"] != "")
        $stFiltro .= " ordem.cod_ordem = ".$_REQUEST["inCodOrdemCompra"]." \nAND ";

    if ($_REQUEST["stExercicioOrdemCompra"] != "")
        $stFiltro .= " ordem.exercicio = '".$_REQUEST["stExercicioOrdemCompra"]."' \n AND ";
}

if ($_REQUEST["inCodEntidade"] != "")
    $stFiltro .= " empenho.cod_entidade in (".$filtro['inCodEntidade'].") \nAND ";

if ($_REQUEST["stExercicio"] != "")
    $stFiltro .= " empenho.exercicio = '".$_REQUEST["stExercicio"]."' \nAND ";

if ($_REQUEST["inCodDespesa"] != "")
    $stFiltro .= " pre_empenho_despesa.cod_despesa = ".$_REQUEST["inCodDespesa"]." \nAND ";

// verifica os campos de código inicial e final para o filtro.
// caso um dos campos não seja preenchido, é feita a parquisa a partir dele
// para no máximo (se for código final) ou mínimo (se for código inicial)
if ( ($_REQUEST["inCodEmpenhoInicial"] != "") && ($_REQUEST["inCodEmpenhoFinal"] != "") ) {
    $stFiltro .= " empenho.cod_empenho between ".$_REQUEST["inCodEmpenhoInicial"]." and ".$_REQUEST["inCodEmpenhoFinal"]." \nAND ";
} elseif ($_REQUEST["inCodEmpenhoInicial"] != "") {
    $stFiltro .= " empenho.cod_empenho >= ".$_REQUEST["inCodEmpenhoInicial"]." \nAND ";
} elseif ($_REQUEST["inCodEmpenhoFinal"] != "") {
    $stFiltro .= " empenho.cod_empenho <= ".$_REQUEST["inCodEmpenhoFinal"]." \nAND ";
}

// idem ao caso do codEmpenhoInicial e codEmpenhoFinal
if ( ($_REQUEST["inCodAutorizacaoInicial"] != "") && ($_REQUEST["inCodAutorizacaoFinal"] != "") ) {
    $stFiltro .= "  autorizacao_empenho.cod_autorizacao between ".$_REQUEST["inCodAutorizacaoInicial"]." AND ".$_REQUEST["inCodAutorizacaoFinal"]." \nAND ";
} elseif ($_REQUEST["inCodAutorizacaoInicial"] != "") {
    $stFiltro .= " autorizacao_empenho.cod_autorizacao >= ".$_REQUEST["inCodAutorizacaoInicial"]." \nAND ";
} elseif ($_REQUEST["inCodAutorizacaoFinal"] != "") {
    $stFiltro .= " autorizacao_empenho.cod_autorizacao <= ".$_REQUEST["inCodAutorizacaoFinal"]." \nAND ";
}

if ($_REQUEST["inCodFornecedor"] != "") {
    $stFiltro .= " item_pre_empenho_julgamento.cgm_fornecedor = ".$_REQUEST["inCodFornecedor"]." \nAND ";
}

// idem ao caso do codEmpenhoInicial e codEmpenhoFinal
if ( strpos($stAcao,'incluir') !== false ) {
    if ( ($_REQUEST["stDtInicial"] != "") && ($_REQUEST["stDtFinal"] != "") ) {
        $stFiltro .= " empenho.dt_empenho BETWEEN TO_DATE('".$_REQUEST["stDtInicial"]."', 'dd/mm/yyyy') AND TO_DATE('".$_REQUEST["stDtFinal"]."', 'dd/mm/yyyy') \nAND ";
    } elseif ($_REQUEST["stDtInicial"] != "") {
        $stFiltro .= " empenho.dt_empenho >= TO_DATE('".$_REQUEST["stDtInicial"]."', 'dd/mm/yyyy') \nAND ";
    } elseif ($_REQUEST["stDtFinal"] != "") {
        $stFiltro .= " empenho.dt_empenho <= TO_DATE('".$_REQUEST["stDtFinal"]."', 'dd/mm/yyyy') \nAND ";
    }
} else {
    if ( ($_REQUEST["stDtInicial"] != "") && ($_REQUEST["stDtFinal"] != "") ) {
        $stFiltro .= " ordem.timestamp::date BETWEEN TO_DATE('".$_REQUEST["stDtInicial"]."', 'dd/mm/yyyy') AND TO_DATE('".$_REQUEST["stDtFinal"]."', 'dd/mm/yyyy') \nAND ";
    } elseif ($_REQUEST["stDtInicial"] != "") {
        $stFiltro .= " ordem.timestamp::date >= TO_DATE('".$_REQUEST["stDtInicial"]."', 'dd/mm/yyyy') \nAND ";
    } elseif ($_REQUEST["stDtFinal"] != "") {
        $stFiltro .= " ordem.timestamp::date <= TO_DATE('".$_REQUEST["stDtFinal"]."', 'dd/mm/yyyy') \nAND ";
    }
}

$stFiltro = ($stFiltro) ? "\nAND ".substr($stFiltro,0,strlen($stFiltro)-4):'';
$obComprasOrdem = new TComprasOrdem();
$obComprasOrdem->setDado('tipo', $stTipoOrdem );
$obComprasOrdem->setDado('acao', $stAcao );

// há uma diferença na pesquisa feita. Na recuperaListagemEmpenho não faz ligação
// com a tabela de ordem de compra, pois na inclusão não há nada na tabela
if ( strpos($stAcao,'incluir') !== false ) {
    $obComprasOrdem->setDado('stFiltro',$stFiltro);
    $obComprasOrdem->recuperaListagemEmpenho($rsLista);

} else {
    $stOrdem = ' ORDER BY empenho.cod_empenho DESC ';

    // Filtro utilizado para não listar Ordem anuladas na alteração ou anulação.
    switch ($stAcao) {
        case 'anularOS'   :
        case 'anularOC'   :
        case 'anular'     :
        case 'alterarOS'  :
        case 'alterarOC'  :
        case 'alterar'    :
            $stFiltro .= " AND NOT EXISTS
                                    (
                                        SELECT  1
                                          FROM  compras.ordem_anulacao
                                         WHERE  ordem_anulacao.cod_ordem    = ordem.cod_ordem
                                           AND  ordem_anulacao.exercicio    = ordem.exercicio
                                           AND  ordem_anulacao.cod_entidade = ordem.cod_entidade
                                           AND  ordem_anulacao.cod_ordem    = ordem.cod_ordem
                                           AND  ordem_anulacao.tipo         = ordem.tipo
                                    ) ";
        break;
    }

    $obComprasOrdem->recuperaListagemOrdemCompra($rsLista, $stFiltro, $stOrdem);
}

/**************************
    MONTA A LISTAGEM
***************************/
$stFiltro = "";
$stLink   = "";

$stLink .= "&stAcao=".$stAcao;

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsLista );

// CABECALHOS DA LISTAGEM
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Empenho");
$obLista->ultimoCabecalho->setWidth( 4 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Entidade");
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Data Empenho" );
$obLista->ultimoCabecalho->setWidth( 7 );
$obLista->commitCabecalho();

if ( strpos($stAcao,'incluir') !== false ) {
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Vencimento" );
    $obLista->ultimoCabecalho->setWidth( 6 );
    $obLista->commitCabecalho();
} else {
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( (($stTipoOrdem == "C") ? "OC" : "OS") );
    $obLista->ultimoCabecalho->setWidth( 6 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( (($stTipoOrdem == "C") ? "OC" : "OS")." Data" );
    $obLista->ultimoCabecalho->setWidth( 6 );
    $obLista->commitCabecalho();
}

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Ação");
$obLista->ultimoCabecalho->setWidth( 3 );
$obLista->commitCabecalho();

// DADOS DA LISTAGEM
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "[cod_empenho]/[exercicio_empenho]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[cod_entidade] - [entidade]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "dt_empenho" );
$obLista->commitDado();

if ( strpos($stAcao,'incluir') !== false ) {
    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "dt_vencimento" );
    $obLista->commitDado();
} else {
    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "[cod_ordem]/[exercicio_ordem]" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "timestamp" );
    $obLista->commitDado();
}

$obLista->addAcao();
if ($stAcao == 'reemitir') {
    $obLista->ultimaAcao->setAcao( 'relatorio' );
} else {
    $obLista->ultimaAcao->setAcao( str_replace("OS","",$stAcao) );
}

if (($stAcao == 'incluir')||($stAcao == 'incluirOS')) {
    $stAcaoBotao = 'selecionar';
} else {
    $stAcaoBotao = $stAcao;
}

$obLista->ultimaAcao->setAcao( str_replace("OS","", $stAcaoBotao) );
$obLista->ultimaAcao->addCampo("&inCodEmpenho", "cod_empenho");
$obLista->ultimaAcao->addCampo("&stExercicioEmpenho", "exercicio_empenho");
$obLista->ultimaAcao->addCampo("&inCodEntidade", "cod_entidade");
$obLista->ultimaAcao->addCampo("&stEntidade", "entidade");
$obLista->ultimaAcao->addCampo("&inCodigo", "codigo");
$obLista->ultimaAcao->addCampo("&stExercicio", "exercicio");
$obLista->ultimaAcao->addCampo("&inCodObjeto", "cod_objeto");
$obLista->ultimaAcao->addCampo("&inCodModalidade", "cod_modalidade");
$obLista->ultimaAcao->addCampo("&stModalidade", "descricao_modalidade");
$obLista->ultimaAcao->addCampo("&inCodFornecedor", "cgm_fornecedor");
$obLista->ultimaAcao->addCampo("&stFornecedor", "fornecedor");
$obLista->ultimaAcao->addCampo("&stCondicoesPagamento", "condicoes_pagamento");
$obLista->ultimaAcao->addCampo("&stLocalEntregaMaterial", "local_entrega_material");
$obLista->ultimaAcao->addCampo("&cgm_entrega_material", "cgm_entrega_material");
$obLista->ultimaAcao->addCampo("&stTipo", "tipo");
$obLista->ultimaAcao->addCampo("&stTipoOrdem", "tipo_ordem");
if ( strpos($stAcao,'incluir') === false ) {
    $obLista->ultimaAcao->addCampo("&inCodOrdemCompra", "cod_ordem");
    $obLista->ultimaAcao->addCampo("&stExercicioOrdemCompra", "exercicio_ordem");
    $obLista->ultimaAcao->addCampo("&dtOrdemCompra", "timestamp");
}

$obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
$obLista->setAjuda("UC-03.04.24");

$obLista->commitAcao();
$obLista->Show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
