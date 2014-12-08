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
    * Lista de Alterar Assentamento Gerado
    * Data de Criação: 09/05/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage

    $Revision: 32866 $
    $Name$
    $Author: tiago $
    $Date: 2007-07-19 12:24:20 -0300 (Qui, 19 Jul 2007) $

    * Casos de uso: uc-04.04.14
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalGeracaoAssentamento.class.php"                              );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php"                                      );

//Define o nome dos arquivos PHP
$stPrograma = "ManterGeracaoAssentamento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stCaminho = CAM_GRH_PES_INSTANCIAS."assentamento/";

include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');

$obRPessoalGeracaoAssentamento = new RPessoalGeracaoAssentamento;
$rsContrato = new recordset;
if ($_REQUEST['inContrato'] != "") {
    $obTPessoalContrato = new TPessoalContrato;
    $stFiltro = " WHERE registro = ".$_REQUEST['inContrato'];
    $obTPessoalContrato->recuperaTodos($rsContrato,$stFiltro);
}

$stLink .= '&inCodLotacao='      .$_REQUEST['inCodLotacao'];
$stLink .= '&inCodAssentamento=' .$_REQUEST['inCodAssentamento'];
$stLink .= '&inContrato='        .$_REQUEST['inContrato'];
$stLink .= '&boCargoExercido='   .$_REQUEST['boCargoExercido'];
$stLink .= '&inCodCargo='        .$_REQUEST['inCodCargo'];
$stLink .= '&inCodEspecialidade='.$_REQUEST['inCodEspecialidade'];
$stLink .= '&boFuncaoExercida='  .$_REQUEST['boFuncaoExercida'];
$stLink .= '&stDataInicial='     .$_REQUEST['stDataInicial'];
$stLink .= '&stDataFinal='       .$_REQUEST['stDataFinal'];
$stLink .= '&stModoGeracao='     .$_REQUEST['stModoGeracao'];
$stLink .= '&HdninCodLotacao='   .$_REQUEST['HdninCodLotacao'];
$stLink .= "&stAcao=".$stAcao;

$stFiltroPaginacao = '';
//MANTEM FILTRO E PAGINACAO
$arLink = Sessao::read('link');
if ($_GET["pg"] and  $_GET["pos"]) {
    $arLink["pg"]  = $_GET["pg"];
    $arLink["pos"] = $_GET["pos"];

    $stFiltroPaginacao = "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
}

$rsLista = new RecordSet;
$arFiltros['inCodAssentamento'] = $_REQUEST['inCodAssentamento'];
$arFiltros['inCodClassificacao'] = $_REQUEST['inCodClassificacao'];
$arFiltros['inCodContrato']     = $rsContrato->getCampo("cod_contrato");
if ($_POST['boCargoExercido']) {
    $arFiltros['inCodCargo']        = $_REQUEST['inCodCargo'];
    $arFiltros['inCodEspecialidade']= $_REQUEST['inCodEspecialidade'];
}
if ($_POST['boFuncaoExercida']) {
    $arFiltros['inCodFuncao']             = $_REQUEST['inCodCargo'];
    $arFiltros['inCodEspecialidadeFuncao']= $_REQUEST['inCodEspecialidade'];
}
$arFiltros['inCodLotacao']       = $_REQUEST['inCodLotacao'];
$arFiltros['dtPeriodoInicial2']  = $_REQUEST['stDataInicial'];
$arFiltros['dtPeriodoFinal2']    = $_REQUEST['stDataFinal'];

//$stOrdem = " to_date(periodo_inicial,'dd/mm/yyyy')";
$stOrdem = " nom_cgm,cod_contrato,assentamento_gerado.periodo_inicial";

$obRPessoalGeracaoAssentamento->listarAssentamentoServidor( $rsLista,$arFiltros,$stOrdem );

$obLista = new Lista;
//$stTitulo = ' </div></td></tr><tr><td colspan="5" class="alt_dados">Período Aberto';
$obLista->setTitulo             ('<div align="right">'.$obRFolhaPagamentoFolhaSituacao->consultarCompetencia());

$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Matrícula");
$obLista->ultimoCabecalho->setWidth( 1 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Servidor");
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Assentamento");
$obLista->ultimoCabecalho->setWidth( 17 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Período");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Situação");
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
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "descricao_assentamento" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "[periodo_inicial] a [periodo_final]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "situacao" );
$obLista->commitDado();

$obLista->addAcao();
$stAcao = ( $stAcao == "excluir" ) ? "Excluir" : $stAcao;
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo("&inCodAssentamentoGerado","cod_assentamento_gerado");
$obLista->ultimaAcao->addCampo("&inRegistro","registro");
$obLista->ultimaAcao->setLinkId("botaoAcao");
$obLista->ultimaAcao->setLink( $pgForm."?".Sessao::getId().$stLink.$stFiltroPaginacao );
$obLista->commitAcao();
$obLista->show();

?>
