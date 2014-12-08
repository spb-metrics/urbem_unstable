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
    * Página de Listagem de Anulacao de Empenho
    * Data de Criação   : 06/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    * $Id: LSAnularLiquidacao.php 60003 2014-09-25 12:51:26Z michel $

    * Casos de uso: uc-02.03.04
                    uc-02.03.18
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include CAM_GF_EMP_NEGOCIO."REmpenhoEmpenhoAutorizacao.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterLiquidacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgCons = "FMConsultarLiquidacao.php";
$stCaminho = CAM_GF_EMP_INSTANCIAS."liquidacao/";

$obREmpenhoEmpenhoAutorizacao = new REmpenhoEmpenhoAutorizacao;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'alterar'  : $pgProx = $pgForm; break;
    case 'excluir'  : $pgProx = $pgProc; break;
    case 'consultar': $pgProx = $pgCons; break;
    case 'anular'   : $pgProx = "FMAnularLiquidacao.php"; break;
    DEFAULT         : $pgProx = $pgForm;
}

//MANTEM FILTRO E PAGINACAO
$stLink .= "&stAcao=".$stAcao;
if ($_REQUEST['pg'] and  $_REQUEST['pos']) {
    Sessao::write('pg', $_REQUEST['pg']);
    Sessao::write('pos', $_REQUEST['pos']);
}

//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
$arFiltro = Sessao::read('arFiltro');
if ($arFiltro['paginando']) {
    $arFiltro['pg']        = $_REQUEST['pg'];
    $arFiltro['pos']       = $_REQUEST['pos'];
    $_REQUEST = $arFiltro;
} else {
    $arFiltro = $_REQUEST;
    $arFiltro['paginando'] = true;
    $arFiltro['pg']        = $_REQUEST['pg'];
    $arFiltro['pos']       = $_REQUEST['pos'];
}
Sessao::write('arFiltro', $arFiltro);

foreach ($_REQUEST['inCodEntidade'] as $value) {
    $stCodEntidade .= $value . " , ";
}
$stCodEntidade = substr($stCodEntidade,0,strlen($stCodEntidade)-2);

$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodDespesa( $_REQUEST['inCodDespesa'] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodFornecedor( $_REQUEST['inCodFornecedor'] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setExercicio( $_REQUEST['dtExercicioEmpenho'] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodEmpenhoInicial( $_REQUEST['inCodEmpenhoInicial'] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodEmpenhoFinal( $_REQUEST['inCodEmpenhoFinal'] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setDtVencimento( $_REQUEST['stDtVencimento'] );

$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->stDtLiquidacaoInicial = $_REQUEST['stDtInicial'];
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->stDtLiquidacaoFinal = $_REQUEST['stDtFinal'];

$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodLiquidacaoInicial( $_REQUEST['inCodLiquidacaoInicial'] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodLiquidacaoFinal( $_REQUEST['inCodLiquidacaoFinal'] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $stCodEntidade );

if ($_REQUEST['inCodTipoDocumento']) {
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodTipoDocumento( $_REQUEST['inCodTipoDocumento'] );
}

if ($stAcao == 'anular') {
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setSomarLiquidacao( true  );
}
if ( $_REQUEST['dtExercicioEmpenho'] == Sessao::getExercicio() ) {
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->listarPorNota( $rsLista );
} elseif ( $_REQUEST['dtExercicioEmpenho'] < Sessao::getExercicio() ) {
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->listarRestosPorNota( $rsLista );
} else {
    $rsLista = new RecordSet;
}

if ($_REQUEST['pg'] and  $_REQUEST['pos']) {
    $stLink.= '&pg='.$_REQUEST['pg'].'&pos='.$_REQUEST['pos'];
}

Sessao::write('rsListaImpressao', $rsLista);

$obLista = new Lista;
$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Entidade");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Empenho");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Data do Empenho");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Liquidação");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Data da Liquidação");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Credor");
$obLista->ultimoCabecalho->setWidth( 65 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_entidade" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_empenho]/[exercicio]" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "dt_empenho" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_nota]/[exercicio_nota]" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "dt_liquidacao" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_fornecedor" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo( "&inCodEmpenho"     , "cod_empenho"     );
$obLista->ultimaAcao->addCampo( "&inCodPreEmpenho"  , "cod_pre_empenho" );
$obLista->ultimaAcao->addCampo( "&inCodEntidade"    , "cod_entidade"    );
$obLista->ultimaAcao->addCampo( "&inCodReserva"     , "cod_reserva"     );
$obLista->ultimaAcao->addCampo( "&inCodAutorizacao" , "cod_autorizacao" );
$obLista->ultimaAcao->addCampo( "&inCodNota"        , "cod_nota" );
$obLista->ultimaAcao->addCampo( "&stDtLiquidacao"   , "dt_liquidacao" );
$obLista->ultimaAcao->addCampo( "&stExercicioNota"  , "exercicio_nota" );
$obLista->ultimaAcao->addCampo( "&dtExercicioEmpenho"     , "exercicio"     );
$obLista->ultimaAcao->addCampo( "&boImplantado"           , "implantado"    );

if ($stAcao == "imprimir") {
    $obLista->ultimaAcao->addCampo( "&inCodNota"        , "cod_nota"        );
    $obLista->ultimaAcao->addCampo( "&stExercicioNota"  , "exercicio_nota"  );
    $pgProx = CAM_FW_POPUPS."relatorio/OCRelatorio.php";
    $stLink .= "&stCaminho=".CAM_GF_EMP_INSTANCIAS."liquidacao/OCRelatorioNotaLiquidacaoEmpenho.php";
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );

    $stLinkBotao = $pgProx."?".Sessao::getId()."&stCtrl=imprimirTodos".$stLink;
    $obBotaoImprimirTodos = new Button;
    $obBotaoImprimirTodos->setId   ("imprimirTodos");
    $obBotaoImprimirTodos->setName ("imprimirTodos");
    $obBotaoImprimirTodos->setValue("Imprimir Todos");
    $obBotaoImprimirTodos->setStyle("color: red;");
    $obBotaoImprimirTodos->setTipo ("button");
    $obBotaoImprimirTodos->setDefinicao("imprimirTodos");
    $obBotaoImprimirTodos->obEvento->setOnClick("javascript:window.open('".$stLinkBotao."', 'oculto');");
    $obBotaoImprimirTodos->montaHTML();

    $obLinkImpTodos = new Link;
    $obLinkImpTodos->setHref($pgProx."?".Sessao::getId().$stLink);
    $obLinkImpTodos->setValue('Imprimir Todos');
    $obLinkImpTodos->montaHtml();

    $obTabelaBtnImprimirTodos = new Tabela;
    $obTabelaBtnImprimirTodos->addLinha();
    $obTabelaBtnImprimirTodos->ultimaLinha->addCelula();
    $obTabelaBtnImprimirTodos->ultimaLinha->ultimaCelula->setColSpan (1);
    $obTabelaBtnImprimirTodos->ultimaLinha->ultimaCelula->setClass   ( $obLista->getClassPaginacao() );
    $obTabelaBtnImprimirTodos->ultimaLinha->ultimaCelula->addConteudo( "<div align=\"center\">".$obBotaoImprimirTodos->getHTML()."&nbsp;</div>");
    $obTabelaBtnImprimirTodos->ultimaLinha->commitCelula();
    $obTabelaBtnImprimirTodos->commitLinha();
    $obTabelaBtnImprimirTodos->montaHTML();

    $obLista->commitAcao();
    $obLista->montaHTML();
    echo $obLista->getHTML().$obTabelaBtnImprimirTodos->getHTML();
} else {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
    $obLista->commitAcao();
    $obLista->montaHTML();
    echo $obLista->getHTML();
}

SistemaLegado::liberaFrames();
?>
