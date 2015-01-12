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
    * Página de Filtro do Configuração Dirf
    * Data de Criação: 22/11/2007

    * @author Diego Lemos de Souza

    * Casos de uso: uc-04.08.14

    $Id: FMManterConfiguracaoDirf.php 61330 2015-01-07 15:57:46Z diogo.zarpelon $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                        );
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenhoAutorizacao.class.php"                                 );
include_once ( CAM_GF_CONT_COMPONENTES."IPopUpEstrutural.class.php"                                      );
include_once ( CAM_GRH_FOL_COMPONENTES."IBuscaInnerEvento.class.php"                                     );
include_once ( CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoDirfPrestador.class.php"                          );
include_once ( CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoDirfIrrf.class.php"                               );
include_once ( CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoDirfInss.class.php"                               );
include_once ( CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoDirfInss.class.php"                               );
include_once ( CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoDirfPlano.class.php"                              );

$stPrograma = "ManterConfiguracaoDirf";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$jsOnload = "montaParametrosGET('montaListaPrestadoresServico', '');";

Sessao::write("arPrestadoresServico", array());
Sessao::write("arPlanoSaude", array());

$stIdsComponentes = "";
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);
//**************************************************************************************************************************//
//Define COMPONENTES DO FORMULARIO
//**************************************************************************************************************************//
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                             	( "stAcao"                                                              );
$obHdnAcao->setValue                            	( $_REQUEST["stAcao"]                                                   );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             	( "stCtrl"                                                              );
$obHdnCtrl->setValue                            	( $stCtrl                                                               );

//Instancia o form
$obForm = new Form;
$obForm->setAction      ( $pgProc  );
$obForm->setTarget      ( "oculto" );

$rsConfiguracaoDirf = new RecordSet();
$inTipoInscricao = 1;
if ($_REQUEST["stAcao"] == "alterar") {
    include_once( CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoDirf.class.php"                           );
    $obTIMAConfiguracaoDirf = new TIMAConfiguracaoDirf();
    $stFiltro = " AND configuracao_dirf.exercicio = '".$_REQUEST["inExercicio"]."'";
    $obTIMAConfiguracaoDirf->recuperaRelacionamento($rsConfiguracaoDirf,$stFiltro);

    if ($rsConfiguracaoDirf->getNumLinhas() == 1) {
        $inExercicio        = $rsConfiguracaoDirf->getCampo("exercicio");
        $inCGMCNPJ          = $rsConfiguracaoDirf->getCampo("responsavel_entrega");
        $stCGMCNPJ          = $rsConfiguracaoDirf->getCampo("responsavel_entrega_nome");
        $inCGM              = $rsConfiguracaoDirf->getCampo("responsavel_prefeitura");
        $stCGM              = $rsConfiguracaoDirf->getCampo("responsavel_prefeitura_nome");
        $stTelefone         = substr($rsConfiguracaoDirf->getCampo("telefone"),0,2)."-".substr($rsConfiguracaoDirf->getCampo("telefone"),2,strlen($rsConfiguracaoDirf->getCampo("telefone")));
        $stEmail            = $rsConfiguracaoDirf->getCampo("email");
        $stRamal            = $rsConfiguracaoDirf->getCampo("ramal");
        $stFax              = substr($rsConfiguracaoDirf->getCampo("fax"),0,2)."-".substr($rsConfiguracaoDirf->getCampo("fax"),2,strlen($rsConfiguracaoDirf->getCampo("fax")));
        $stNatureza         = $rsConfiguracaoDirf->getCampo("descricao");
        $inNatureza         = $rsConfiguracaoDirf->getCampo("cod_natureza");

        include_once( CAM_GRH_FOL_MAPEAMENTO.'TFolhaPagamentoEvento.class.php');
        $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
        $obTFolhaPagamentoEvento->setDado('cod_evento', $rsConfiguracaoDirf->getCampo('cod_evento_molestia'));
        $obTFolhaPagamentoEvento->listar($rsEvento);
        $inCodigoEventoMolestia = $rsEvento->getCampo("codigo");
        $stJs = "jQuery('#stEventoMolestia').html('".$rsEvento->getCampo('descricao')."');\n";
    } else {
        $inExercicio = "";
    }

    $stFiltro = " WHERE configuracao_dirf_prestador.exercicio = '".$_REQUEST["inExercicio"]."'";
    $obTIMAConfiguracaoDirfPrestador = new TIMAConfiguracaoDirfPrestador();
    $obTIMAConfiguracaoDirfPrestador->recuperaRelacionamento($rsConfiguracaoDirfPrestador, $stFiltro);

    Sessao::remove("arPrestadoresServico");
    $arElementos = array();
    while (!$rsConfiguracaoDirfPrestador->eof()) {
        $arTMP = array();
        $arTMP["inId" ]                      = count($arElementos) + 1;
        $arTMP["tipo" ]                      = $rsConfiguracaoDirfPrestador->getCampo("tipo");
        $arTMP["tipo_formatado"]             = $rsConfiguracaoDirfPrestador->getCampo("tipo_formatado");
        $arTMP["codigo_retencao"]            = $rsConfiguracaoDirfPrestador->getCampo("cod_dirf");
        $arTMP["descricao_retencao"]         = $rsConfiguracaoDirfPrestador->getCampo("descricao_codigo_dirf");
        $arTMP["desdobramento"]              = $rsConfiguracaoDirfPrestador->getCampo("cod_estrutural");
        $arTMP["descricao_elemento_despesa"] = $rsConfiguracaoDirfPrestador->getCampo("descricao_conta_despesa");
        $arTMP["cod_conta_despesa"]          = $rsConfiguracaoDirfPrestador->getCampo("cod_conta");
        $arTMP["cod_prestador"]              = $rsConfiguracaoDirfPrestador->getCampo("cod_prestador");

        $arElementos[] = $arTMP;
        $rsConfiguracaoDirfPrestador->proximo();
    }
    Sessao::write("arPrestadoresServico", $arElementos);

    $stFiltro = " WHERE configuracao_dirf_plano.exercicio = '".$_REQUEST['inExercicio']."'";
    $obTIMAConfiguracaoDirfPlano = new TIMAConfiguracaoDirfPlano();
    $obTIMAConfiguracaoDirfPlano->recuperaRelacionamento($rsConfiguracaoDirfPlano, $stFiltro);
    Sessao::remove("arPlanoSaude");
    $arElementosPlano = array();
    while ( !$rsConfiguracaoDirfPlano->eof() ) {
        $arTMP = array();
        $arTMP['inId'] = count($arElementosPlano)+1;
        $arTMP['inRegistro'] = $rsConfiguracaoDirfPlano->getCampo('registro_ans');
        $arTMP['inCGMPlanoSaude'] = $rsConfiguracaoDirfPlano->getCampo('numcgm');
        $arTMP['stNomCGMPlanoSaude'] = $rsConfiguracaoDirfPlano->getCampo('nom_cgm');
        $arTMP['inCodigoEventoPlanoSaude'] = $rsConfiguracaoDirfPlano->getCampo('codigo');
        $arTMP['stNomEventoPlanoSaude'] = $rsConfiguracaoDirfPlano->getCampo('descricao');
        $arElementosPlano[] = $arTMP;
        $rsConfiguracaoDirfPlano->proximo();
    }
    Sessao::write("arPlanoSaude", $arElementosPlano);

    $stFiltro = " WHERE configuracao_dirf_irrf.exercicio = '".$_REQUEST["inExercicio"]."'";
    $obTIMAConfiguracaoDirfIrrf = new TIMAConfiguracaoDirfIrrf();
    $obTIMAConfiguracaoDirfIrrf->recuperaRelacionamento($rsConfiguracaoDirfIrrf, $stFiltro);

    if ($rsConfiguracaoDirfIrrf->getNumLinhas() > 0) {
        $stFiltro  = " WHERE trim(cod_estrutural) = '".substr($rsConfiguracaoDirfIrrf->getCampo("cod_estrutural"),2)."' ";
        $stFiltro .= "   AND exercicio = ".$_REQUEST["inExercicio"];
        $obTOrcamentoContaReceita = new TOrcamentoContaReceita;
        $obTOrcamentoContaReceita->recuperaTodos($rsContaReceita, $stFiltro);

        $inCodClassificacaoIRRF = $rsContaReceita->getCampo("cod_estrutural");
        $stCodClassificacaoIRRF = $rsContaReceita->getCampo("descricao");
    }

    $stFiltro = " WHERE configuracao_dirf_inss.exercicio = '".$_REQUEST["inExercicio"]."'";
    $obTIMAConfiguracaoDirfInss = new TIMAConfiguracaoDirfInss();
    $obTIMAConfiguracaoDirfInss->recuperaRelacionamento($rsConfiguracaoDirfInss, $stFiltro);
    if ($rsConfiguracaoDirfInss->getNumLinhas() > 0) {
        $inCodClassificacaoINSS = $rsConfiguracaoDirfInss->getCampo("cod_estrutural");
        $stCodClassificacaoINSS = $rsConfiguracaoDirfInss->getCampo("nom_conta");
    }

    if ( $rsConfiguracaoDirf->getCampo('pagamento_mes_competencia') == 't' ) {
        $opPagamentoMes = 'S';
    } else {
        $opPagamentoMes = 'N';
    }
}

$obRadioPagamentoMes = new SimNao;
$obRadioPagamentoMes->setName('boPagamentoMes');
$obRadioPagamentoMes->setRotulo('Pagamento do Salário Ocorre no Mês de Competência?');
$obRadioPagamentoMes->setTitle('Informe se o pagamento do mês ocorre na competência.');
$obRadioPagamentoMes->setChecked( $_REQUEST['stAcao'] == 'alterar' ? $opPagamentoMes : 'S' );
$obRadioPagamentoMes->obRadioSim->setValue('Sim');
$obRadioPagamentoMes->obRadioNao->setValue('Não');

include_once(CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php");
$obIPopUpCGM = new IPopUpCGM($obForm);
$obIPopUpCGM->setRotulo("CGM");
$obIPopUpCGM->setTitle("Selecione o CGM do responsável pela entrega da DIRF.");
$obIPopUpCGM->setNull(false);
$obIPopUpCGM->setValue($stCGM);
$obIPopUpCGM->obCampoCod->setValue($inCGM);

$obIPopUpCGMCNPJ = new IPopUpCGM($obForm);
$obIPopUpCGMCNPJ->setRotulo("CGM do Responsável Perante o CNPJ");
$obIPopUpCGMCNPJ->setTitle("Selecione o CGM do responsável pela entrega da DIRF.");
$obIPopUpCGMCNPJ->setNull(false);
$obIPopUpCGMCNPJ->setTipo('fisica');
$obIPopUpCGMCNPJ->setId("stNomCGMCNPJ");
$obIPopUpCGMCNPJ->obCampoCod->setName("inCGMCNPJ");
$obIPopUpCGMCNPJ->obCampoCod->setId("inCGMCNPJ");
$obIPopUpCGMCNPJ->setValue($stCGMCNPJ);
$obIPopUpCGMCNPJ->obCampoCod->setValue($inCGMCNPJ);

$obTxtTelefone = new TextBox();
$obTxtTelefone->setRotulo("Telefone");
$obTxtTelefone->setName("stTelefone");
$obTxtTelefone->setTitle("Informe o telefone para contato do responsável pelas informações da DIRF (DDD+número).");
$obTxtTelefone->setNull(false);
$obTxtTelefone->setSize(12);
$obTxtTelefone->setMascara("99-999999999");
$obTxtTelefone->setValue($stTelefone);

$obTxtRamal = new TextBox();
$obTxtRamal->setRotulo("Ramal");
$obTxtRamal->setName("stRamal");
$obTxtRamal->setTitle("Informe o ramal do telefone para contato do responsável pelas informações da DIRF.");
$obTxtRamal->setSize(5);
$obTxtRamal->setMascara("99999");
$obTxtRamal->setValue($stRamal);

$obTxtFax = new TextBox();
$obTxtFax->setRotulo("Fax");
$obTxtFax->setName("stFax");
$obTxtFax->setTitle("Informe o número do fax para contato do responsável pelas informações da DIRF.");
$obTxtFax->setSize(12);
$obTxtFax->setMascara("99-999999999");
$obTxtFax->setValue($stFax);

$obTxtEmail = new TextBox();
$obTxtEmail->setRotulo("Email");
$obTxtEmail->setName("stMail");
$obTxtEmail->setTitle("Informe o email do responsável pelas informações da DIRF.");
$obTxtEmail->setSize(40);
$obTxtEmail->setMaxLength(30);
$obTxtEmail->setValue($stEmail);

$obIntExercicio = new Inteiro();
$obIntExercicio->setRotulo("Exercício");
$obIntExercicio->setTitle("Informe o exercício de vigência das configurações.");
$obIntExercicio->setName("inExercicio");
$obIntExercicio->setId("inExercicio");
$obIntExercicio->setSize(5);
$obIntExercicio->setMaxLength(4);
$obIntExercicio->setNull(false);
$obIntExercicio->setValue($inExercicio);
if ($_REQUEST["stAcao"] == "alterar") {
    $obIntExercicio->setReadOnly(true);
}
if ($_REQUEST["stAcao"] == "incluir") {
    $obIntExercicio->obEvento->setOnBlur("montaParametrosGET('limpaCodigosExercicio','stAcao');");
}
$stIdsComponentes .= $obIntExercicio->getId().",";

$obNaturezaEstabelecimento = new BuscaInner();
$obNaturezaEstabelecimento->setRotulo                 ( 'Natureza do Estabelecimento'              );
$obNaturezaEstabelecimento->setTitle                  ( 'Selecione o código da natureza do estabelecimento.'                 );
$obNaturezaEstabelecimento->setId                     ( 'stNatureza'         );
$obNaturezaEstabelecimento->setNull                   ( false              );
$obNaturezaEstabelecimento->setValue($stNatureza);
$obNaturezaEstabelecimento->obCampoCod->setName       ( "inNatureza"            );
$obNaturezaEstabelecimento->obCampoCod->setId       ( "inNatureza"            );
$obNaturezaEstabelecimento->obCampoCod->setValue($inNatureza);
$obNaturezaEstabelecimento->obCampoCod->setSize       ( 6                  );
$obNaturezaEstabelecimento->obCampoCod->setMaxLength  ( 10                 );
$obNaturezaEstabelecimento->obCampoCod->setAlign      ( "left"             );
$pgOculNatureza = "'".CAM_GRH_IMA_PROCESSAMENTO."OCNaturezaEstabelecimento.php?".Sessao::getId()."&".$obNaturezaEstabelecimento->obCampoCod->getName()."='+this.value+'&stNomCampoCod=".$obNaturezaEstabelecimento->obCampoCod->getName()."&stIdCampoDesc=".$obNaturezaEstabelecimento->getId()."'";
$obNaturezaEstabelecimento->obCampoCod->obEvento->setOnChange ( "ajaxJavaScript(".$pgOculNatureza.",'preencheNaturezaEstabelecimento');");
$obNaturezaEstabelecimento->setFuncaoBusca("abrePopUp('".CAM_GRH_IMA_POPUPS."configuracao/FLNaturezaEstabelecimento.php','".$obForm->getName()."', '". $obNaturezaEstabelecimento->obCampoCod->stName ."','". $obNaturezaEstabelecimento->stId . "','','" . Sessao::getId() ."','800','550');");

$obRegra             = new REmpenhoEmpenhoAutorizacao;
$stMascaraRubrica    = $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->recuperaMascara();
$obBscRubricaDespesa = new BuscaInner;
$obBscRubricaDespesa->setRotulo               ( "Elemento de Despesa"                       );
$obBscRubricaDespesa->setTitle                ( "Informe o elemento de despesa."            );
$obBscRubricaDespesa->setId                   ( "stDescricaoDespesa"                        );
$obBscRubricaDespesa->setNullBarra            ( false                                       );
$obBscRubricaDespesa->obCampoCod->setName     ( "inCodDespesa"                              );
$obBscRubricaDespesa->obCampoCod->setId       ( "inCodDespesa"                              );
$obBscRubricaDespesa->obCampoCod->setSize     ( strlen($stMascaraRubrica)                   );
$obBscRubricaDespesa->obCampoCod->setMaxLength( strlen($stMascaraRubrica)                   );
$obBscRubricaDespesa->obCampoCod->setValue    ( ''                                          );
$obBscRubricaDespesa->obCampoCod->setAlign    ("left"                                       );
$obBscRubricaDespesa->obCampoCod->obEvento->setOnFocus("selecionaValorCampo( this );"       );
$obBscRubricaDespesa->obCampoCod->obEvento->setOnKeyUp("mascaraDinamico('".$stMascaraRubrica."', this, event);");
$obBscRubricaDespesa->obCampoCod->obEvento->setOnBlur ("montaParametrosGET('mascaraClassificacaoElementoDespesa', 'inCodDespesa,stMascClassificacao,inExercicio');");
$obBscRubricaDespesa->setFuncaoBusca( "abrePopUp('".CAM_GF_ORC_POPUPS."classificacaodespesa/FLClassificacaoDespesa.php','frm','inCodDespesa','stDescricaoDespesa','&mascClassificacao=$stMascaraRubrica&inExercicio='+document.getElementById('inExercicio').value,'".Sessao::getId()."','800','550');" );
$stIdsComponentes .= $obBscRubricaDespesa->obCampoCod->getId().",";

$obHdnMascaraRubrica =  new Hidden;
$obHdnMascaraRubrica->setName  ( "stMascClassificacao" );
$obHdnMascaraRubrica->setValue ( $stMascaraRubrica  );

$obCmbTipoPrestador = new Select();
$obCmbTipoPrestador->setRotulo    ( "Tipo de Prestador" );
$obCmbTipoPrestador->setTitle     ( "Selecione o Tipo de Prestador" );
$obCmbTipoPrestador->setName      ( "stTipoPrestador" );
$obCmbTipoPrestador->setId        ( "stTipoPrestador" );
$obCmbTipoPrestador->setNullBarra ( false );
$obCmbTipoPrestador->setValue     ( $stTipoPrestador );
$obCmbTipoPrestador->addOption    ( "","Selecione" );
$obCmbTipoPrestador->addOption    ( "F","Pessoa Fisica" );
$obCmbTipoPrestador->addOption    ( "J","Pessoa Juridica" );
$obCmbTipoPrestador->obEvento->setOnChange("montaParametrosGET('limpaCodigoRetencao','');");
$stIdsComponentes .= $obCmbTipoPrestador->getId().",";

$obPopUpRetencaoDIRF = new BuscaInner();
$obPopUpRetencaoDIRF->setRotulo                ( "Código de Retenção da DIRF" );
$obPopUpRetencaoDIRF->setTitle                 ( "Informe o código de retenção da DIRF referente ao tipo de pagamento." );
$obPopUpRetencaoDIRF->setId                    ( "stCodDIRF" );
$obPopUpRetencaoDIRF->setValue                 ( $stCodDIRF );
$obPopUpRetencaoDIRF->setNullBarra             ( false );
$obPopUpRetencaoDIRF->obCampoCod->setName      ( "inCodDIRF" );
$obPopUpRetencaoDIRF->obCampoCod->setId        ( "inCodDIRF" );
$obPopUpRetencaoDIRF->obCampoCod->setValue     ( $inCodDIRF );
$obPopUpRetencaoDIRF->obCampoCod->setSize      ( 6 );
$obPopUpRetencaoDIRF->obCampoCod->setMaxLength ( 10 );
$obPopUpRetencaoDIRF->obCampoCod->setAlign     ( "left" );
$pgOculRetencaoDIRF = "'".CAM_GRH_IMA_PROCESSAMENTO."OCProcurarRetencaoDIRF.php?".Sessao::getId()."&".$obPopUpRetencaoDIRF->obCampoCod->getName()."='+this.value+'&campoNum=".$obPopUpRetencaoDIRF->obCampoCod->getName()."&campoNom=".$obPopUpRetencaoDIRF->getId()."&inExercicio='+document.getElementById('inExercicio').value+'&stTipoPrestador='+document.getElementById('stTipoPrestador').value";
$obPopUpRetencaoDIRF->obCampoCod->obEvento->setOnBlur ( "ajaxJavaScript(".$pgOculRetencaoDIRF.",'preencheRetencaoDIRF');");
$obPopUpRetencaoDIRF->setFuncaoBusca("montaParametrosGET('validarPopUpCodigoRetencao');");
$stIdsComponentes .= $obPopUpRetencaoDIRF->obCampoCod->getId().",";

$obHdnInId = new Hidden;
$obHdnInId->setName( "inId" );
$obHdnInId->setId( "inId" );
$obHdnInId->setValue( $_REQUEST["inId"] );

$obIPopUpEstrutural = new IPopUpEstrutural();
$obIPopUpEstrutural->setRotulo               ( "Código Classificação INSS" );
$obIPopUpEstrutural->setTitle                ( "Informe o Código Classificação(receita extra-orçamentária) no plano de contas referente a retenção da previdência INSS." );
$obIPopUpEstrutural->setId                   ( "stCodClassificacaoINSS" );
$obIPopUpEstrutural->setValue                ( $stCodClassificacaoINSS  );
$obIPopUpEstrutural->obCampoCod->setName     ( "inCodClassificacaoINSS" );
$obIPopUpEstrutural->obCampoCod->setValue    ( $inCodClassificacaoINSS  );

$obPopUpClassificacaoIRRF = new BuscaInner;
$obPopUpClassificacaoIRRF->setRotulo               ( "Código Classificação IRRF"                 );
$obPopUpClassificacaoIRRF->setTitle                ( "Informe o código de classificação (receita orçamentária) referente a retenção do IRRF." );
$obPopUpClassificacaoIRRF->setId                   ( "stCodClassificacaoIRRF"                    );
$obPopUpClassificacaoIRRF->setValue                ( $stCodClassificacaoIRRF                     );
$obPopUpClassificacaoIRRF->obCampoCod->setName     ( "inCodClassificacaoIRRF"                    );
$obPopUpClassificacaoIRRF->obCampoCod->setId       ( "inCodClassificacaoIRRF"                    );
$obPopUpClassificacaoIRRF->obCampoCod->setSize     ( strlen($stMascaraRubrica)                   );
$obPopUpClassificacaoIRRF->obCampoCod->setMaxLength( strlen($stMascaraRubrica)                   );
$obPopUpClassificacaoIRRF->obCampoCod->setValue    ( $inCodClassificacaoIRRF                     );
$obPopUpClassificacaoIRRF->obCampoCod->setAlign    ("left"                                       );
$obPopUpClassificacaoIRRF->obCampoCod->obEvento->setOnFocus("selecionaValorCampo( this );"       );
$obPopUpClassificacaoIRRF->obCampoCod->obEvento->setOnKeyUp("mascaraDinamico('".$stMascaraRubrica."', this, event);");
$obPopUpClassificacaoIRRF->obCampoCod->obEvento->setOnBlur ("montaParametrosGET('mascaraClassificacaoIRRF', 'inCodClassificacaoIRRF,stMascClassificacao,inExercicio');");
$obPopUpClassificacaoIRRF->setFuncaoBusca( "abrePopUp('".CAM_GF_ORC_POPUPS."classificacaoreceita/FLClassificacaoReceita.php','frm','inCodClassificacaoIRRF','stCodClassificacaoIRRF','&mascClassificacao=$stMascaraRubrica&inExercicio='+document.getElementById('inExercicio').value,'".Sessao::getId()."','800','550');" );

$obSpnPrestadoresServico = new Span;
$obSpnPrestadoresServico->setId ( "spnListaPrestadoresServico" );

$obIPopUpCGMPlanoSaude = new IPopUpCGM($obForm);
$obIPopUpCGMPlanoSaude->setRotulo("CGM do Plano Privado de Assistência à Saúde");
$obIPopUpCGMPlanoSaude->setTitle("Selecione o CGM do Plano Privado de Assistência à Saúde.");
$obIPopUpCGMPlanoSaude->setTipo('juridica');
$obIPopUpCGMPlanoSaude->setId("stNomCGMPlanoSaude");
$obIPopUpCGMPlanoSaude->obCampoCod->setName("inCGMPlanoSaude");
$obIPopUpCGMPlanoSaude->obCampoCod->setId("inCGMPlanoSaude");
$obIPopUpCGMPlanoSaude->setNull(true);
$obIPopUpCGMPlanoSaude->setValue($stCGMPlanoSaude);
$obIPopUpCGMPlanoSaude->obCampoCod->setValue($inCGMPlanoSaude);

$obIntRegistro = new Inteiro();
$obIntRegistro->setRotulo("Número Registro ANS");
$obIntRegistro->setTitle("Informe o número de registro da Agência Nacional de Saúde.");
$obIntRegistro->setName("inRegistro");
$obIntRegistro->setId("inRegistro");
$obIntRegistro->setSize(7);
$obIntRegistro->setMaxLength(6);
$obIntRegistro->setValue($inRegistro);

$obIBscEventoPlanoSaude = new IBuscaInnerEvento();
$obIBscEventoPlanoSaude->setRotulo("Evento Plano de Saúde");
$obIBscEventoPlanoSaude->setId("stEventoPlanoSaude");
$obIBscEventoPlanoSaude->setTitle("");
$obIBscEventoPlanoSaude->obCampoCod->setName("inCodigoEventoPlanoSaude");
$obIBscEventoPlanoSaude->obCampoCod->setId("inCodigoEventoPlanoSaude");
$obIBscEventoPlanoSaude->setNaturezasDesconto();
$obIBscEventoPlanoSaude->setNaturezaChecked("D");
$obIBscEventoPlanoSaude->montaOnChange();
$obIBscEventoPlanoSaude->montaPopUp();

$obSpnPlanoSaude = new Span;
$obSpnPlanoSaude->setId ( "spnListaPlanoSaude" );

$obBtnIncluirPlanoSaude = new Button();
$obBtnIncluirPlanoSaude->setName( "btnIncluirPlanoSaude" );
$obBtnIncluirPlanoSaude->setValue( "Incluir" );
$obBtnIncluirPlanoSaude->setTitle( "Clique para incluir plano de saúde." );
$obBtnIncluirPlanoSaude->obEvento->setOnClick("montaParametrosGET('incluirPlanoSaude','stNomCGMPlanoSaude, HdninCodigoEventoPlanoSaude, inCGMPlanoSaude, inRegistro, inCodigoEventoPlanoSaude');");

$obBtnLimparPlanoSaude = new Button();
$obBtnLimparPlanoSaude->setName( "btnLimparPlanoSaude" );
$obBtnLimparPlanoSaude->setValue( "Limpar" );
$obBtnLimparPlanoSaude->obEvento->setOnClick("montaParametrosGET('limparPlanoSaude','stNomCGMPlanoSaude, HdninCodigoEventoPlanoSaude, inCGMPlanoSaude, inRegistro, inCodigoEventoPlanoSaude');");

$obIBscEventoMolestia = new IBuscaInnerEvento();
$obIBscEventoMolestia->setRotulo("Evento Base");
$obIBscEventoMolestia->setId("stEventoMolestia");
$obIBscEventoMolestia->setNaturezasBase();
$obIBscEventoMolestia->setNaturezaChecked('B');
$obIBscEventoMolestia->setTitle("");
$obIBscEventoMolestia->obCampoCod->setName("inCodigoEventoMolestia");
$obIBscEventoMolestia->obCampoCod->setId("inCodigoEventoMolestia");
$obIBscEventoMolestia->obCampoCod->setValue($inCodigoEventoMolestia);
$obIBscEventoMolestia->montaOnChange();
$obIBscEventoMolestia->montaPopUp();

$obBtnOk = new Ok;

$obBtnLimpar = new Button();
$obBtnLimpar->setName								( "btnLimpar" 															);
$obBtnLimpar->setValue("Limpar");
$obBtnLimpar->setTitle								( "Clique para limpar os dados dos campos." 							);
$obBtnLimpar->obEvento->setOnClick				    ( "document.frm.reset();");

$arCampos = array($obBscRubricaDespesa, $obCmbTipoPrestador, $obPopUpRetencaoDIRF);
$stIdsComponentes = substr($stIdsComponentes,0,strlen($stIdsComponentes)-1);

//**************************************************************************************************************************//
//Define FORMULARIO
//**************************************************************************************************************************//
$obFormulario = new Formulario;
$obFormulario->addHidden      ( $obHdnAcao );
$obFormulario->addHidden      ( $obHdnCtrl );
$obFormulario->addHidden      ( $obHdnInId );
$obFormulario->addHidden      ( $obHdnMascaraRubrica );
$obFormulario->addTitulo      ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() , "right");
$obFormulario->addForm        ( $obForm );
$obFormulario->addTitulo      ( "Configuração DIRF" );
$obFormulario->addComponente  ( $obIntExercicio );
$obFormulario->addComponente  ( $obRadioPagamentoMes );
$obFormulario->addTitulo      ( "Informações do Responsável pela Entrega" );
$obFormulario->addComponente  ( $obIPopUpCGM );
$obFormulario->addComponente  ( $obTxtTelefone );
$obFormulario->addComponente  ( $obTxtRamal );
$obFormulario->addComponente  ( $obTxtFax );
$obFormulario->addComponente  ( $obTxtEmail );
$obFormulario->addTitulo      ( "Informações do Estabelecimento" );
$obFormulario->addComponente  ( $obIPopUpCGMCNPJ );
$obFormulario->addComponente  ( $obNaturezaEstabelecimento );
$obFormulario->addTitulo      ("Informações para Lançamento - Prestadores de Serviço");
$obFormulario->addComponente  ( $obBscRubricaDespesa );
$obFormulario->addComponente  ( $obCmbTipoPrestador );
$obFormulario->addComponente  ( $obPopUpRetencaoDIRF );
$obFormulario->incluirAlterar ( "PrestadoresDeServico", $arCampos, true, false, $stIdsComponentes);
$obFormulario->addSpan        ( $obSpnPrestadoresServico );
$obFormulario->addTitulo      ("Informações sobre Retenções - Prestadores de Serviço");
$obFormulario->addComponente  ( $obIPopUpEstrutural );
$obFormulario->addComponente  ( $obPopUpClassificacaoIRRF );
$obFormulario->addTitulo      ("Informações de Plano Privado de Assistência à Saúde - Coletivo Empresarial");
$obFormulario->addComponente  ( $obIPopUpCGMPlanoSaude );
$obFormulario->addComponente  ( $obIntRegistro );
$obFormulario->addComponente  ( $obIBscEventoPlanoSaude );
$obFormulario->defineBarra    ( array( $obBtnIncluirPlanoSaude, $obBtnLimparPlanoSaude ) );
$obFormulario->addSpan        ( $obSpnPlanoSaude );
$obFormulario->addTitulo      ("Informações de Rendimentos Isentos Por Moléstia Grave");
$obFormulario->addComponente  ( $obIBscEventoMolestia);
$obFormulario->defineBarra    ( array( $obBtnOk, $obBtnLimpar ) 										);

$obFormulario->show();

if ($_REQUEST['stAcao'] == 'alterar') {
    $stJs .= "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."','montaListaPlanoSaude');";
    $stJs .= "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."','montaListaPrestadoresServico');";
}

$jsOnload = $stJs;

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
