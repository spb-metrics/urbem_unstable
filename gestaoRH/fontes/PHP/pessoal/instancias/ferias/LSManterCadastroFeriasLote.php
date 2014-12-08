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
    * Pï¿½gina de Lista do Fï¿½rias
    * Data de Criaï¿½ï¿½o: 08/06/2006

    * @author Analista: Vandrï¿½ Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 32200 $
    $Name$
    $Author: souzadl $
    $Date: 2008-03-10 13:40:16 -0300 (Seg, 10 Mar 2008) $

    * Casos de uso: uc-04.04.22
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

//Define o nome dos arquivos PHP
$stPrograma = "ManterCadastroFerias";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=".$_REQUEST["stAcao"];
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=".$_REQUEST["stAcao"];
$pgJS   = "JS".$stPrograma.".js";
include_once($pgJS);
include_once($pgOcul);

sistemalegado::BloqueiaFrames();
flush();

$jsOnload = "executaFuncaoAjax('processarListaLote');";
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

$stCaminho = CAM_GRH_PES_INSTANCIAS."ferias/";

$arTemp = array();
foreach ($_REQUEST as $key => $valor) {
    if (trim($key) != "hdnTipoFiltro" and trim($key) != "hdnCompetencia") {
        $arTemp[$key] = $valor;
    }
}
$_REQUEST = $arTemp;

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'alterar': $pgProx = $pgForm; break;
    case 'excluir': $pgProx = $pgProc; break;
    DEFAULT       : $pgProx = $pgForm;
}
//MANTEM FILTRO E PAGINACAO
$link = Sessao::read("link");
if ($_GET["pg"] and  $_GET["pos"]) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
    $link["pg"]  = $_GET["pg"];
    $link["pos"] = $_GET["pos"];
    Sessao::write("link",$link);
}
//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array($link) ) {
    $_REQUEST = $link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
    Sessao::write("link",$link);
}

include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
if ($_REQUEST["inAno"] != "" and $_REQUEST["inCodMes"] != "") {
    $obTFolhaPagamentoPeriodoMovimentacao->setDado("ano",$_REQUEST["inAno"]);
    $obTFolhaPagamentoPeriodoMovimentacao->setDado("mes",$_REQUEST["inCodMes"]);
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacaoDaCompetencia($rsPeriodoMovimentacao);
} else {
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
}

include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalFerias.class.php");
$obTPessoalFerias = new TPessoalFerias();
$obTPessoalFerias->setDado("stAcao"                     ,$_REQUEST["stAcao"]);
$obTPessoalFerias->setDado("stTipoFiltro"               ,$_REQUEST["stTipoFiltro"]);
$obTPessoalFerias->setDado("stValoresFiltro"            ,$_REQUEST["stCodigos"]);
$obTPessoalFerias->setDado("inCodPeriodoMovimentacao"   ,$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
$obTPessoalFerias->setDado("boFeriasVencidas"           ,(trim($_REQUEST['boApresentarSomenteFerias']) != "") ? 'true' : 'false');
$obTPessoalFerias->setDado("inCodLote"                  ,(trim($_REQUEST["inCodLote"]) != "") ? $_REQUEST["inCodLote"] : 0);
$obTPessoalFerias->setDado("inCodRegime"                ,$_REQUEST["inCodRegime"]);
$obTPessoalFerias->concederFerias($rsLista,$stFiltro," ORDER BY nom_cgm, dt_inicial_aquisitivo, dt_final_aquisitivo");

include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoGeradoContratoServidor.class.php");
$obTPessoalAssentamentoGeradoContratoServidor = new TPessoalAssentamentoGeradoContratoServidor();
while (!$rsLista->eof()) {
    $stFiltro  = " AND assentamento_gerado_contrato_servidor.cod_contrato = ".$rsLista->getCampo("cod_contrato")." \n";
    $stFiltro .= " AND assentamento_assentamento.cod_motivo = 10 \n";
    $stFiltro .= " AND (assentamento_gerado.periodo_inicial BETWEEN to_date('".$rsLista->getCampo("dt_inicial_aquisitivo_formatado")."','dd/mm/yyyy') AND to_date('".$rsLista->getCampo("dt_final_aquisitivo_formatado")."','dd/mm/yyyy') \n";
    $stFiltro .= "  OR  assentamento_gerado.periodo_final BETWEEN to_date('".$rsLista->getCampo("dt_inicial_aquisitivo_formatado")."','dd/mm/yyyy') AND to_date('".$rsLista->getCampo("dt_final_aquisitivo_formatado")."','dd/mm/yyyy')) \n";
    $obTPessoalAssentamentoGeradoContratoServidor->recuperaRelacionamento($rsAssentamentoGerado,$stFiltro);
    $inQuantFaltas = 0;
    while (!$rsAssentamentoGerado->eof()) {
        $inQuantFaltas += $rsAssentamentoGerado->getCampo("dias_do_periodo");
        $rsAssentamentoGerado->proximo();
    }

    $arDiasFeriasAbono = gerarQuantDiasGozoAbono($inQuantFaltas,$rsLista->getCampo("dt_inicial_aquisitivo_formatado"),$rsLista->getCampo("dt_final_aquisitivo_formatado"),$_REQUEST["inCodFormaPagamento"]);
    $rsLista->setCampo("dias_ferias",$arDiasFeriasAbono[0]);
    $rsLista->setCampo("dias_abono",$arDiasFeriasAbono[1]);
    $rsLista->setCampo("dias_faltas",$inQuantFaltas);
    $rsLista->proximo();
}
$rsLista->setPrimeiroElemento();

$obLista = new Lista;
$obLista->setRecordSet( $rsLista );
$obLista->setMostraPaginacao(false);
$obLista->setMostraSelecionaTodos(true);
$stTitulo = '</div></td></tr><tr><td colspan="8" class="alt_dados">Matrículas';
$obLista->setTitulo('<div align="right">'.$obRFolhaPagamentoFolhaSituacao->consultarCompetencia().$stTitulo);

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Matrícula" );
$obLista->ultimoCabecalho->setWidth( 7 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Servidor" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Admissão" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Função" );
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Dias" );
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Abono" );
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "registro" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[numcgm]-[nom_cgm]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "dt_admissao_formatado" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "desc_funcao" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "dias_ferias" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "dias_abono" );
$obLista->commitDado();

$obChkLote = new CheckBox;
$obChkLote->setName( "boLote_[cod_contrato]"  );
switch ($_REQUEST['stTipoFiltro']) {
    case "contrato":
    case "cgm_contrato":
    case "contrato_todos":
    case "cgm_contrato_todos":
    case "geral":
        $obChkLote->setValue("[cod_contrato]_[dias_ferias]_[dias_abono]_[dias_faltas]");
        break;
    case "funcao":
        $obChkLote->setValue("[cod_contrato]_[dias_ferias]_[dias_abono]__[dias_faltas]_[cod_funcao]");
        break;
    case "lotacao":
        $obChkLote->setValue("[cod_contrato]_[dias_ferias]_[dias_abono]__[dias_faltas]_[cod_orgao]");
        break;
    case "local":
        $obChkLote->setValue("[cod_contrato]_[dias_ferias]_[dias_abono]__[dias_faltas]_[cod_local]");
        break;
}
$obChkLote->setChecked(true);

$obLista->addDadoComponente( $obChkLote );
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo('[cod_contrato]');
$obLista->commitDadoComponente();

$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget("oculto");

$obHdnAcao =  new Hidden;
$obHdnAcao->setName("stAcao");
$obHdnAcao->setValue($stAcao);

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName("stCtrl");
$obHdnCtrl->setValue($stCtrl);

$obHdnRegime =  new Hidden;
$obHdnRegime->setName("inCodRegime");
$obHdnRegime->setValue($_REQUEST["inCodRegime"]);

$obHdnTipoFiltro =  new Hidden;
$obHdnTipoFiltro->setName("stTipoFiltro");
$obHdnTipoFiltro->setValue($_REQUEST["stTipoFiltro"]);

$obHdnTipoFiltroLote =  new Hidden;
$obHdnTipoFiltroLote->setName("stTipoFiltroLote");
$obHdnTipoFiltroLote->setValue($_REQUEST["stTipoFiltroLote"]);

$obHdnCodigos =  new Hidden;
$obHdnCodigos->setName("stCodigos");
$obHdnCodigos->setValue($_REQUEST["stCodigos"]);

$obHdnFormaPagamento =  new Hidden;
$obHdnFormaPagamento->setName("inCodFormaPagamento");
$obHdnFormaPagamento->setValue($_REQUEST["inCodFormaPagamento"]);

$obHdnInicialFerias =  new Hidden;
$obHdnInicialFerias->setName("dtInicialFerias");
$obHdnInicialFerias->setValue($_REQUEST["dtInicialFerias"]);

$obHdnFinalFerias =  new Hidden;
$obHdnFinalFerias->setName("dtFinalFerias");
$obHdnFinalFerias->setValue($_REQUEST["dtFinalFerias"]);

$obHdnRetornoFerias =  new Hidden;
$obHdnRetornoFerias->setName("dtRetornoFerias");
$obHdnRetornoFerias->setValue($_REQUEST["dtRetornoFerias"]);

$obHdnMes =  new Hidden;
$obHdnMes->setName("inCodMes");
$obHdnMes->setValue($_REQUEST["inCodMes"]);

$obHdnAno =  new Hidden;
$obHdnAno->setName("inAno");
$obHdnAno->setValue($_REQUEST["inAno"]);

$obHdnPagamento13 =  new Hidden;
$obHdnPagamento13->setName("boPagamento13");
$obHdnPagamento13->setValue($_REQUEST["boPagamento13"]);

$obHdnTipo =  new Hidden;
$obHdnTipo->setName("inCodTipo");
$obHdnTipo->setValue($_REQUEST["inCodTipo"]);

$obHdnLote =  new Hidden;
$obHdnLote->setName("stNomeLote");
$obHdnLote->setValue($_REQUEST["stNomeLote"]);

$obOk = new Ok();

$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addHidden($obHdnRegime);
$obFormulario->addHidden($obHdnTipoFiltro);
$obFormulario->addHidden($obHdnTipoFiltroLote);
$obFormulario->addHidden($obHdnCodigos);
$obFormulario->addHidden($obHdnFormaPagamento);
$obFormulario->addHidden($obHdnInicialFerias);
$obFormulario->addHidden($obHdnFinalFerias);
$obFormulario->addHidden($obHdnRetornoFerias);
$obFormulario->addHidden($obHdnMes);
$obFormulario->addHidden($obHdnAno);
$obFormulario->addHidden($obHdnPagamento13);
$obFormulario->addHidden($obHdnTipo);
$obFormulario->addHidden($obHdnLote);
$obFormulario->addLista($obLista);
$obFormulario->defineBarra(array($obOk));
$obFormulario->show();

sistemalegado::LiberaFrames();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
