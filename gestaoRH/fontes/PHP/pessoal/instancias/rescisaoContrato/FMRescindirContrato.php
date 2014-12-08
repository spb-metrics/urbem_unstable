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
* Página de Formulário de Pessoal - Rescindir Contrato
* Data de Criação   : 18/10/2005

* @author Analista: Vandr? Miguel Ramos
* @author Desenvolvedor: Eduardo Antunez

* @ignore

$Revision: 30930 $
$Name$
$Author: souzadl $
$Date: 2008-03-06 10:39:20 -0300 (Qui, 06 Mar 2008) $

* Casos de uso: uc-04.04.44
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GRH_PES_NEGOCIO."RPessoalRescisaoContrato.class.php"                                );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPadrao.class.php"                                        );

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
$stServidor = $_GET['inRegistro']." - ".$_GET['stNomCGM'];

$arLink = Sessao::read('link');
$stPrograma = "RescindirContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php"."?".Sessao::getId()."&stAcao=".$stAcao."&pg=".$arLink["pg"]."&pos=".$arLink["pos"];
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once($pgJs);

include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );

$rsNorma = $rsPadraoNorma = new RecordSet;

$obRPessoalRescisaoContrato  = new RPessoalRescisaoContrato;
$obRPessoalRescisaoContrato->obRPessoalCausaRescisao->listarCausa($rsCausaRescisao);

$obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao();
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao($obRFolhaPagamentoPeriodoMovimentacao);

$obRFolhaPagamentoPadrao = new RFolhaPagamentoPadrao;
$obRFolhaPagamentoPadrao->obRNorma->obRTipoNorma->listarTodos ( $rsTipoNorma );

$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( ""       );

$obHdnEval = new hiddenEval();
$obHdnEval->setName  ( "stEval" );
$obHdnEval->setValue ( ""       );

$obHdnEvalAviso = new hiddenEval();
$obHdnEvalAviso->setName  ( "stEvalAviso" );
$obHdnEvalAviso->setValue ( ""       );

$obLblServidor = new Label;
$obLblServidor->setRotulo( "Matrícula" );
$obLblServidor->setValue ( $stServidor );

$obTxtDtRescisao = new Data;
$obTxtDtRescisao->setName  ( "dtRescisao" );
if (sessao::read('incluirRescisaoContratoPensionista') != null) {
    $dtRescisao = $_GET['dtRescisao'];
}
$obTxtDtRescisao->setValue ( $dtRescisao );
$obTxtDtRescisao->setRotulo( "Data da Rescisão" );
$obTxtDtRescisao->setNull  ( false );
$obTxtDtRescisao->setTitle ( 'Informe a data de rescisão' );
$obTxtDtRescisao->obEvento->setOnChange ( "montaParametrosGET('validarDataRescisao','dtRescisao,inCodContrato,inCausaRescisao,inCodSubDivisao,dtPosse,dtNomeacao,dtAdmissao');" );

$obTxtCausaRescisao = new TextBox;
$obTxtCausaRescisao->setName     ( "inCausaRescisao"   );
$obTxtCausaRescisao->setValue    ( $inCausaRescisao    );
$obTxtCausaRescisao->setRotulo   ( "Causa da Rescisão" );
$obTxtCausaRescisao->setSize     ( 5                   );
$obTxtCausaRescisao->setMaxLength( 3                   );
$obTxtCausaRescisao->setNull     ( false               );
$obTxtCausaRescisao->setInteiro  ( true                );
$obTxtCausaRescisao->setTitle    ( 'Informe a causa da rescisão' );
$obTxtCausaRescisao->obEvento->setOnChange("montaParametrosGET('gerarSpanObito', 'dtRescisao,inCodContrato,inCausaRescisao,inCodSubDivisao,dtPosse,dtNomeacao,dtAdmissao');");

$obCmbCausaRescisao = new Select;
$obCmbCausaRescisao->setName       ( "stCausaRescisao"       );
$obCmbCausaRescisao->setRotulo     ( "Causa da Rescisão"     );
$obCmbCausaRescisao->setStyle      ( "width: 600px"          );
$obCmbCausaRescisao->setCampoId    ( "num_causa"             );
$obCmbCausaRescisao->setCampoDesc  ( "descricao"             );
$obCmbCausaRescisao->addOption     ( " ","Selecione"         );
$obCmbCausaRescisao->preencheCombo ( $rsCausaRescisao        );
$obCmbCausaRescisao->setNull       ( false                   );
$obCmbCausaRescisao->setTitle      ( "Informe a causa da rescisão." );
$obCmbCausaRescisao->obEvento->setOnChange("montaParametrosGET('gerarSpanObito', 'dtRescisao,inCodContrato,inCausaRescisao,inCodSubDivisao,dtPosse,dtNomeacao,dtAdmissao');");

$obTxtTipoNorma = new TextBox;
$obTxtTipoNorma->setRotulo              ( "Tipo de Norma"                                 );
$obTxtTipoNorma->setTitle               ( "Informe o tipo de norma para seleção da norma" );
$obTxtTipoNorma->setName                ( "inCodTipoNormaTxt"                             );
$obTxtTipoNorma->setValue               ( $inCodTipoNormaTxt                              );
$obTxtTipoNorma->setSize                ( 5                                               );
$obTxtTipoNorma->setMaxLength           ( 5                                               );
$obTxtTipoNorma->setInteiro             ( true                                            );
$obTxtTipoNorma->setNull                ( true                                           );
$obTxtTipoNorma->obEvento->setOnChange  ( "montaParametrosGET('MontaNorma','inCodTipoNorma');"                     );

$obCmbTipoNorma = new Select;
$obCmbTipoNorma->setRotulo              ( "Tipo de Norma"             );
$obCmbTipoNorma->setName                ( "inCodTipoNorma"            );
$obCmbTipoNorma->setValue               ( $inCodTipoNorma             );
$obCmbTipoNorma->setStyle               ( "width: 200px"              );
$obCmbTipoNorma->setCampoID             ( "cod_tipo_norma"            );
$obCmbTipoNorma->setCampoDesc           ( "nom_tipo_norma"            );
$obCmbTipoNorma->addOption              ( "", "Selecione"             );
$obCmbTipoNorma->setNull                ( true                        );
$obCmbTipoNorma->preencheCombo          ( $rsTipoNorma                );
$obCmbTipoNorma->obEvento->setOnChange  ( "montaParametrosGET('MontaNorma','inCodTipoNorma');" );

$obTxtNorma = new TextBox;
$obTxtNorma->setRotulo        ( "Norma"                               );
$obTxtNorma->setTitle         ( "Informe a norma vinculada ao padrão" );
$obTxtNorma->setName          ( "inCodNormaTxt"                       );
$obTxtNorma->setValue         ( $inCodNormaTxt                        );
$obTxtNorma->setSize          ( 5                                     );
$obTxtNorma->setMaxLength     ( 5                                     );
$obTxtNorma->setInteiro       ( true                                  );
$obTxtNorma->setNull          ( true                                 );

$obCmbNorma = new Select;
$obCmbNorma->setRotulo        ( "Norma"         );
$obCmbNorma->setName          ( "inCodNorma"    );
$obCmbNorma->setValue         ( $inCodNorma     );
$obCmbNorma->setStyle         ( "width: 200px"  );
$obCmbNorma->setCampoID       ( "cod_norma"     );
$obCmbNorma->setCampoDesc     ( "nom_norma"     );
$obCmbNorma->addOption        ( "", "Selecione" );
$obCmbNorma->setNull          ( true            );
//$obCmbNorma->preencheCombo    ( $rsNorma        );

//Armazena o CasoCausa retornado para a inclusão
$obHdnCasoCausa = new Hidden;
$obHdnCasoCausa->setName  ( "inCasoCausa" );
$obHdnCasoCausa->setValue ( $stCasoCausa );

$obLblCasoCausa = new Label();
$obLblCasoCausa->setId     ( "stCasoCausa"   );
$obLblCasoCausa->setValue    ( $stCasoCausa    );
$obLblCasoCausa->setRotulo   ( "*Caso da Causa" );

$obSpnObto = new Span();
$obSpnObto->setId("spnObto");

//Data de Posse
$obHdnDtPosse = new Hidden;
$obHdnDtPosse->setName  ( "dtPosse" );
$obHdnDtPosse->setValue ( substr($_GET['dtPosse'],6,4)."/".substr($_GET['dtPosse'],3,2)."/".substr($_GET['dtPosse'],0,2) );

//Data de Nomeacao
$obHdnDtNomeacao = new Hidden;
$obHdnDtNomeacao->setName  ( "dtNomeacao" );
$obHdnDtNomeacao->setValue ( substr($_GET['dtNomeacao'],6,4)."/".substr($_GET['dtNomeacao'],3,2)."/".substr($_GET['dtNomeacao'],0,2) );

//Data de Admissao
$obHdnDtAdmissao = new Hidden;
$obHdnDtAdmissao->setName  ( "dtAdmissao" );
$obHdnDtAdmissao->setValue ( substr($_GET['dtAdmissao'],6,4)."/".substr($_GET['dtAdmissao'],3,2)."/".substr($_GET['dtAdmissao'],0,2) );

//SubDivisao
$obHdnCodSubDivisao = new Hidden;
$obHdnCodSubDivisao->setName  ( "inCodSubDivisao" );
$obHdnCodSubDivisao->setValue ( $_GET['inCodSubDivisao'] );

//Contrato
$obHdnCodContrato = new Hidden;
$obHdnCodContrato->setName  ( "inCodContrato" );
$obHdnCodContrato->setValue ( $_GET['inCodContrato'] );

//Registro
$obHdnRegistro = new Hidden;
$obHdnRegistro->setName  ( "inRegistro" );
$obHdnRegistro->setValue ( $_GET['inRegistro'] );

//flag para gerar termo de recisão
$obHdnGeraTermoRecisao = new Hidden;
$obHdnGeraTermoRecisao->setName  ( "boGeraTermoRecisao" );
$obHdnGeraTermoRecisao->setValue ( 'false' );

$obSpnAviso = new Span();
$obSpnAviso->setId("spnAviso");

include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoFolhaSituacao.class.php");
$obTFolhaPagamentoFolhaSituacao = new TFolhaPagamentoFolhaSituacao();
$obTFolhaPagamentoFolhaSituacao->recuperaUltimaFolhaSituacao($rsFolhaSituacao);

$obChkFolhaSalario =new CheckBox();
$obChkFolhaSalario->setRotulo("Incorporar Cálculos Rescisão");
$obChkFolhaSalario->setLabel("Folha Salário");
$obChkFolhaSalario->setValue(true);
$obChkFolhaSalario->setName("boFolhaSalario");
$obChkFolhaSalario->setTitle("Informe se os cálculos serão incorporados a rescisão.");
$obChkFolhaSalario->obEvento->setOnchange("montaParametrosGET('validarIncorporarFolhaSalario', 'boFolhaSalario,inCodContrato');");

if (sessao::read('incluirRescisaoContratoPensionista') != null) {
    $obChkFolhaSalario->setChecked(true);
}

if ( $rsFolhaSituacao->getCampo("situacao") == "f" ) {
    $obChkFolhaSalario->setDisabled(true);
}

include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoDecimoCalculado.class.php");
include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
$obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsCompetencia);
$obTFolhaPagamentoEventoDecimoCalculado = new TFolhaPagamentoEventoDecimoCalculado();
$stFiltro  = " AND cod_contrato = ".$_GET['inCodContrato'];
$stFiltro .= " AND cod_periodo_movimentacao = ".$rsCompetencia->getCampo("cod_periodo_movimentacao");
$obTFolhaPagamentoEventoDecimoCalculado->recuperaEventosDecimoCalculado($rsEventosCalculados,$stFiltro);
$obChkFolhaDecimo =new CheckBox();
$obChkFolhaDecimo->setRotulo("Incorporar Cálculos ? Rescisão");
$obChkFolhaDecimo->setLabel("Folha 13° Salário");
$obChkFolhaDecimo->setValue(true);
$obChkFolhaDecimo->setName("boFolhaDecimo");
$obChkFolhaDecimo->setTitle("Informe se os cálculos serão incorporados a rescisão.");
if ( $rsEventosCalculados->getNumLinhas() < 0 ) {
    $obChkFolhaDecimo->setDisabled(true);
}

include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php");
$obTFolhaPagamentoEventoCalculado = new TFolhaPagamentoEventoCalculado();
$stFiltro  = " WHERE cod_periodo_movimentacao = ".$rsCompetencia->getCampo("cod_periodo_movimentacao");
$stFiltro .= "   AND cod_contrato = ".$_GET["inCodContrato"];
$obTFolhaPagamentoEventoCalculado->recuperaContratosCalculados($rsContratosCalculados,$stFiltro);
$obLblObservacao = new Label();
$obLblObservacao->setId("lblObservacao");
$obLblObservacao->setRotulo("Observação");
$obLblObservacao->setValue("A folha salário encontra-se fechada e calculada para o contrato que está sendo rescindido.");

$stComplemento  = "stCampo = document.frm.inCasoCausa; \n";
$stComplemento .= "if (stCampo) {\n";
$stComplemento .= "    if ( trim( stCampo.value ) == \"\" ) {\n";
$stComplemento .= "        erro = true;\n";
$stComplemento .= "        mensagem += \"@Campo Caso da Causa inválido!()\";\n";
$stComplemento .= "    }\n";
$stComplemento .= "}\n";

$obBtnOk = new Ok();
$obBtnOk->obEvento->setOnClick("confirmPopUp('Imprimir', 'Imprimir rescisão?', 'document.frm.boGeraTermoRecisao.value = \'true\'; montaParametrosGET(\'submeter\',\'\',true)', 'montaParametrosGET(\'submeter\',\'\',true)');");

$obBtnCancelar = new Button();
$obBtnCancelar->setValue("Cancelar");
$obBtnCancelar->obEvento->setOnClick("Cancelar('".$pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.'&stCodigo='.$_GET['stCodigo'].'&stDescricao='.$_GET['stDescricao']."');");

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden             ( $obHdnCtrl                );
$obFormulario->addHidden             ( $obHdnEval,true           );
$obFormulario->addHidden             ( $obHdnEvalAviso,true           );
$obFormulario->addHidden             ( $obHdnAcao                );
$obFormulario->addHidden             ( $obHdnDtPosse             );
$obFormulario->addHidden             ( $obHdnDtNomeacao          );
$obFormulario->addHidden             ( $obHdnDtAdmissao          );
$obFormulario->addHidden($obHdnCodSubDivisao);
$obFormulario->addHidden             ( $obHdnCasoCausa           );
$obFormulario->addHidden             ( $obHdnCodContrato         );
$obFormulario->addHidden             ( $obHdnRegistro            );
$obFormulario->addHidden             ( $obHdnGeraTermoRecisao    );
$obFormulario->addTitulo             ( "Dados de rescisão" );
$obFormulario->addComponente         ( $obLblServidor );
$obFormulario->addComponenteComposto ( $obTxtCausaRescisao, $obCmbCausaRescisao );
$obFormulario->addSpan($obSpnAviso);
$obFormulario->addComponente         ( $obTxtDtRescisao );
$obFormulario->agrupaComponentes(array($obChkFolhaSalario,$obChkFolhaDecimo));
if ($rsContratosCalculados->getNumLinhas() > 0 and $rsFolhaSituacao->getCampo("situacao") == "f") {
    $obFormulario->addComponente($obLblObservacao);
}
$obFormulario->addSpan               ( $obSpnObto );
$obFormulario->addComponente         ( $obLblCasoCausa );
$obFormulario->addComponenteComposto ( $obTxtTipoNorma, $obCmbTipoNorma       );
$obFormulario->addComponenteComposto ( $obTxtNorma, $obCmbNorma               );
$obFormulario->defineBarra(array($obBtnOk,$obBtnCancelar));
$obFormulario->obJavaScript->setComplementoValida($stComplemento);
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
