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
    * Arquivo de Filtro da DIRF.
    * Data de Criação: 22/11/2007

    * @author Diego Lemos de Souza

    * Casos de uso: uc-04.08.15

    $Id: FLExportarDIRF.php 59612 2014-09-02 12:00:51Z gelson $

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao( new RFolhaPagamentoPeriodoMovimentacao );
$stTitulo = $obRFolhaPagamentoFolhaSituacao->consultarCompetencia();

//Define o nome dos arquivos PHP
$stPrograma = "ExportarDIRF";
$pgFilt      = "FL".$stPrograma.".php";
$pgList      = "LS".$stPrograma.".php";
$pgForm      = "FM".$stPrograma.".php";
$pgProc      = "PR".$stPrograma.".php";
$pgOcul      = "OC".$stPrograma.".php";
$pgJS        = "JS".$stPrograma.".js";

Sessao::write('link', '');

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                                 ( "stAcao"                                          );
$obHdnAcao->setValue                                ( $stAcao                                           );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setTarget("oculto");
$obForm->setAction                                  ( $pgProc                                           );

include_once(CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php");
$obIFiltroComponentes = new IFiltroComponentes();
$obIFiltroComponentes->setMatricula();
$obIFiltroComponentes->setCGMMatricula();
$obIFiltroComponentes->setLotacao();
$obIFiltroComponentes->setLocal();
$obIFiltroComponentes->setRegSubFunEsp();
$obIFiltroComponentes->setAtributoServidor();
$obIFiltroComponentes->setAtributoPensionista();
$obIFiltroComponentes->setTodos();

include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
$obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
$arCompetencia = explode("/",$rsPeriodoMovimentacao->getCampo("dt_final"));

include_once(CAM_GRH_PES_COMPONENTES."ISelectAnoCompetencia.class.php");
$obISelectAnoCompetencia = new ISelectAnoCompetencia();
$obISelectAnoCompetencia->obCmbAnoCompetencia->setValue($arCompetencia[2]-1);
$obISelectAnoCompetencia->obCmbAnoCompetencia->setRotulo("Ano-Calendário");
$obISelectAnoCompetencia->obCmbAnoCompetencia->setTitle("Selecione o ano-calendário para emissão do arquivo da DIRF. Exemplo: entrega em 2007 da DIRF ano base 2006.");
$obISelectAnoCompetencia->obCmbAnoCompetencia->setNull(false);
$obISelectAnoCompetencia->obCmbAnoCompetencia->obEvento->setOnChange("montaParametrosGET('gerarSpanNumeroRecibo','stIndicador,inAnoCompetencia');");

$obRdoNormal = new Radio();
$obRdoNormal->setRotulo("Tipo de Declaração");
$obRdoNormal->setTitle("Informe o tipo de declaração: normal ou retificadora.");
$obRdoNormal->setName("stIndicador");
$obRdoNormal->setValue("O");
$obRdoNormal->setLabel("Normal");
$obRdoNormal->setNull(false);
$obRdoNormal->setChecked(true);
$obRdoNormal->obEvento->setOnChange("montaParametrosGET('gerarSpanNumeroRecibo','stIndicador,inAnoCompetencia');");

$obRdoRetificadora = new Radio();
$obRdoRetificadora->setRotulo("Tipo de Declaração");
$obRdoRetificadora->setTitle("Informe o tipo de declaração: normal ou retificadora.");
$obRdoRetificadora->setName("stIndicador");
$obRdoRetificadora->setValue("R");
$obRdoRetificadora->setLabel("Retificadora");
$obRdoRetificadora->setNull(false);
$obRdoRetificadora->obEvento->setOnChange("montaParametrosGET('gerarSpanNumeroRecibo','stIndicador,inAnoCompetencia');");

$obSpnNumeroRecibo = new Span();
$obSpnNumeroRecibo->setId("spnNumeroRecibo");

$obHdnNumeroRecibo = new hiddenEval();
$obHdnNumeroRecibo->setName("hdnNumeroRecibo");

$obCkbPrestadorServico = new CheckBox();
$obCkbPrestadorServico->setRotulo("Adicionar Prestadores de Serviço");
$obCkbPrestadorServico->setName("boPrestadoresServico");
$obCkbPrestadorServico->setId("boPrestadoresServico");
$obCkbPrestadorServico->setValue(true);
$obCkbPrestadorServico->setTitle("Marque para que seja adicionado no arquivo valores pagos aos prestadores de serviço.");

$obBtnOk = new Ok();
$obBtnOk->obEvento->setOnClick("montaParametrosGET('submeter','',true);");

$obBtnLimpar = new Limpar();

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addTitulo                            ( $stTitulo ,"right"  );
$obFormulario->addForm                              ( $obForm                                           );
$obFormulario->addHidden                            ( $obHdnAcao                                        );
$obIFiltroComponentes->geraFormulario($obFormulario);
$obISelectAnoCompetencia->geraFormulario($obFormulario);
$obFormulario->agrupaComponentes(array($obRdoNormal,$obRdoRetificadora));
$obFormulario->addSpan($obSpnNumeroRecibo);
$obFormulario->addHidden($obHdnNumeroRecibo,true);
$obFormulario->addComponente($obCkbPrestadorServico);
$obFormulario->defineBarra(array($obBtnOk,$obBtnLimpar));
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
