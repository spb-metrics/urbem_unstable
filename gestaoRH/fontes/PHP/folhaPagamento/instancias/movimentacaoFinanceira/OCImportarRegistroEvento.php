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
    * Página Oculta do Registrar/Importar Evento
    * Data de CriAção   : 19/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Andre Almeida

    * @ignore
    $Revision: 32866 $
    $Name$
    $Author: souzadl $
    $Date: 2007-12-04 09:20:23 -0200 (Ter, 04 Dez 2007) $

    * Caso de uso: uc-04.05.49

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

function gerarSpanOpcoes()
{
    Sessao::remove("arLoteMatriculas");
    Sessao::remove("arLoteEventos");
    switch ($_GET["stOpcao"]) {
        case "lote_evento":
            $stJs = gerarSpanLoteEvento();
            break;
        case "lote_matricula":
            $stJs = gerarSpanLoteMatricula();
            break;
        case "importar":
            $stJs = gerarSpanImportar();
            break;
    }

    return $stJs;
}

function preencherDadosEvento()
{
    $inContrato =  $_GET["inContrato"];
    $inCodigoEvento = $_GET["inCodigoEvento"];
    if ($inCodigoEvento != "") {
        $inCodigoEvento = str_pad($inCodigoEvento,strlen(Sessao::read("stMascaraEvento")),"0",STR_PAD_LEFT);

        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php");
        $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
        $stFiltro = " AND codigo = '".$inCodigoEvento."'";
        $obTFolhaPagamentoEvento->recuperaEventos($rsEvento,$stFiltro);

        $obErro = new erro();
        if ( $rsEvento->getNumLinhas() == -1 ) {
            $obErro->setDescricao("@Código de evento inválido!(".$inCodigoEvento.")");
        }
        if ( !$obErro->ocorreu() and $rsEvento->getCampo("evento_sistema") == "t") {
            $obErro->setDescricao("@Código de evento inválido!(Evento ".$inCodigoEvento." é um evento de sistema)");
        }
        if ( !$obErro->ocorreu() ) {
            $inCodEvento        = $rsEvento->getCampo('cod_evento');
            $stDescricao        = $rsEvento->getCampo('descricao');
            $stObservacao       = $rsEvento->getCampo('observacao');
            $stNatureza         = $rsEvento->getCampo('proventos_descontos');
            $stTipo             = ($rsEvento->getCampo('tipo') == "V") ? "Variável" : "Fixo";
            $stFixado           = $rsEvento->getCampo("fixado");
            $stLimiteCalculo    = $rsEvento->getCampo("limite_calculo");
            $boApresentaParcela = $rsEvento->getCampo("apresenta_parcela");
            Sessao::write("fixado",$stFixado);
            Sessao::write("limite_calculo",$stLimiteCalculo);
            Sessao::write("stTextoComplementar",$stObservacao);
            Sessao::write("stNatureza",$stNatureza);
            Sessao::write("stTipo",$stTipo);
            $boIncluir = "false";
            $boLimpar  = "false";
        } else {
            $inCodContrato      = 0;
            $inCodEvento        = 0;
            $inCodigoEvento     = '';
            $stDescricao        = '&nbsp;';
            $stObservacao       = '&nbsp;';
            $stNatureza         = '&nbsp;';
            $stTipo             = '&nbsp;';
            $stFixado           = '';
            $stLimiteCalculo    = '';
            $boApresentaParcela = '';
            Sessao::remove("fixado");
            Sessao::remove("limite_calculo");
            Sessao::remove("stTextoComplementar");
            Sessao::remove("stNatureza");
            Sessao::remove("stTipo");
            $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
            $boIncluir = "true";
            $boLimpar  = "true";
        }
        $stJs .= "f.inCodigoEvento.value = '".$inCodigoEvento."';\n";
        $stJs .= "d.getElementById('stEvento').innerHTML = '".$stDescricao."';\n";
        $stJs .= "f.hdnDescEvento.value = '".$stDescricao."';\n";
        $stJs .= "f.HdninCodigoEvento.value = '".$inCodEvento."';\n";
        $stJs .= "d.getElementById('stTextoComplementar').innerHTML = '".$stObservacao."';\n";
        $stJs .= "d.getElementById('stNatureza').innerHTML = '".$stNatureza."';\n";
        $stJs .= "d.getElementById('stTipo').innerHTML = '".$stTipo."';\n";

        if ($inContrato != "" and $inCodigoEvento != "" and !$obErro->ocorreu()) {
            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
            $obTPessoalContrato = new TPessoalContrato();
            $stFiltro = " WHERE registro = ".$inContrato;
            $obTPessoalContrato->recuperaTodos($rsContrato,$stFiltro);
            $inCodContrato      = $rsContrato->getCampo("cod_contrato");

            $stJs .= "f.btIncluir.disabled = ".$boIncluir.";";
            $stJs .= "f.btLimpar.disabled = ".$boLimpar.";";
            $stJs .= gerarSpanRegistroEvento($inCodEvento,$inCodContrato,$stFixado,$stTipo,$stLimiteCalculo,$boApresentaParcela);
            $stJs .= peencherDadosRegistroEvento($inCodEvento,$inCodContrato);
        }
    }

    return $stJs;
}

function peencherDadosRegistroEvento($inCodEvento,$inCodContrato)
{
    if ($inCodEvento != 0 and $inCodContrato != 0) {
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);

        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEvento.class.php");
        $obTFolhaPagamentoRegistroEvento = new TFolhaPagamentoRegistroEvento();
        $stProporcional  = ( $_GET["stProporcional"] == "Sim" ) ? "TRUE" : "FALSE";
        $stFiltro  = " AND evento.cod_evento = ".$inCodEvento;
        $stFiltro .= " AND contrato.cod_contrato = ".$inCodContrato;
        $stFiltro .= " AND registro_evento_periodo.cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
        $stFiltro .= " AND proporcional IS ".$stProporcional;
        $obTFolhaPagamentoRegistroEvento->recuperaRegistrosDeEventos($rsRegistroEvento,$stFiltro);

        if ($rsRegistroEvento->getNumLinhas() >= 1) {
            $rsRegistroEvento->addFormatacao("valor","NUMERIC_BR");
            $rsRegistroEvento->addFormatacao("quantidade","NUMERIC_BR");
            $nuValorEvento = $rsRegistroEvento->getCampo("valor");
            $nuQuantidadeEvento = $rsRegistroEvento->getCampo("quantidade");
            $nuQuantidadeParcelasEvento = $rsRegistroEvento->getCampo("parcela");
            $inMesCarencia = $rsRegistroEvento->getCampo("mes_carencia");
            $stExcluirLancamento = "true";
            $stJs .= "d.getElementById('boExcluirLancamento').disabled = false;\n";
            $stJs .= "f.boExcluirLancamentoDisabled.value = 'false';";
        } else {
            $nuValorEvento = "";
            $nuQuantidadeEvento = "";
            $nuQuantidadeParcelasEvento = "";
            $inMesCarencia = 0;
            $stExcluirLancamento = "false";
            $stJs .= "d.getElementById('boExcluirLancamento').disabled = true;\n";
            $stJs .= "f.boExcluirLancamentoDisabled.value = 'true';";
        }
        if (Sessao::read("fixado") == "V") {
            $stJs .= "f.nuValorEvento.value = '".$nuValorEvento."';\n";
            $stJs .= "f.nuQuantidadeEvento.value = '".$nuQuantidadeEvento."';\n";
        } else {
            $stJs .= "f.nuQuantidadeEvento.value = '".$nuQuantidadeEvento."';\n";
        }
        if (Sessao::read("limite_calculo") == "t") {
            $stJs .= "f.nuQuantidadeParcelasEvento.value = '".$nuQuantidadeParcelasEvento."';\n";
            $stJs .= "f.inMesCarenciaEvento.value = ".$inMesCarencia.";\n";
        }
    }

    return $stJs;
}

function gerarSpanRegistroEvento($inCodEvento,$inCodContrato,$stFixado,$stTipo,$stLimiteCalculo,$boApresentaParcela)
{
    if ($inCodEvento != 0 and $inCodContrato != 0) {
        $obTxtValor = new Numerico;
        $obTxtValor->setName      ( "nuValorEvento"                  );
        $obTxtValor->setId        ( "nuValorEvento"                  );
        $obTxtValor->setTitle     ( "Informe o valor a ser lançado." );
        $obTxtValor->setAlign     ( "RIGHT"                          );
        $obTxtValor->setRotulo    ( "**Valor"                          );
        $obTxtValor->setMaxLength ( 14                               );
        $obTxtValor->setMaxValue  ( 999999999.99                     );
        $obTxtValor->setSize      ( 12                               );
        $obTxtValor->setDecimais  ( 2                                );
        $obTxtValor->setNegativo  ( false                            );

        $obTxtQuantidade = new Numerico;
        $obTxtQuantidade->setName      ( "nuQuantidadeEvento"            );
        $obTxtQuantidade->setId        ( "nuQuantidadeEvento"            );
        $obTxtQuantidade->setTitle     ( "Informe a quantidade a ser lançada." );
        $obTxtQuantidade->setAlign     ( "RIGHT"                         );
        if ($stFixado == 'Q') {
            $obTxtQuantidade->setRotulo    ( "**Quantidade"                    );
        } else {
            $obTxtQuantidade->setRotulo    ( "Quantidade"                    );
        }
        $obTxtQuantidade->setMaxLength ( 14                              );
        $obTxtQuantidade->setMaxValue  ( 999999999.99                    );
        $obTxtQuantidade->setSize      ( 12                              );
        $obTxtQuantidade->setDecimais  ( 2                               );

        $obTxtQuantidadeParcelas = new TextBox;
        $obTxtQuantidadeParcelas->setName      ( "nuQuantidadeParcelasEvento"    );
        $obTxtQuantidadeParcelas->setId        ( "nuQuantidadeParcelasEvento"    );
        $obTxtQuantidadeParcelas->setRotulo    ( "**Quantidade de Parcelas"        );
        $obTxtQuantidadeParcelas->setInteiro   ( true                            );
        $obTxtQuantidadeParcelas->setMaxLength ( 10                              );
        $obTxtQuantidadeParcelas->setSize      ( 10                              );
        $obTxtQuantidadeParcelas->setTitle     ( "Informe a quantidade de parcelas a ser lançada." );
        
        $obInCarencia= new Inteiro;
        $obInCarencia->setRotulo             ( "Meses de Carência"                                        );
        $obInCarencia->setTitle              ( "Informe a quantidade de meses de carência para o evento." );
        $obInCarencia->setName               ( "inMesCarenciaEvento"                                      );
        $obInCarencia->setValue              ( 0                                                          );
        $obInCarencia->setNull               ( false                                                      );
        //$obInCarencia->obEvento->setOnChange ( "buscaValor('preenchePrevisaoMesAno');"                    );
        
        $obCkbExcluirLancamento = new CheckBox();
        $obCkbExcluirLancamento->setRotulo("Excluir Lançamento");
        $obCkbExcluirLancamento->setName("boExcluirLancamento");
        $obCkbExcluirLancamento->setId("boExcluirLancamento");
        $obCkbExcluirLancamento->setValue("sim");
        $obCkbExcluirLancamento->setTitle("Marque a opïção para excluir o evento informado do contrato.");
        $obCkbExcluirLancamento->setDisabled(true);

        $obHdnExcluirLancamento = new hidden();
        $obHdnExcluirLancamento->setName("boExcluirLancamentoDisabled");

        $obFormulario = new Formulario;
        if ($stFixado == 'V') {
            $obFormulario->addComponente        ( $obTxtValor                                               );
        }
        $obFormulario->addComponente            ( $obTxtQuantidade                                          );
        if ($stTipo == 'Variável' and  $stLimiteCalculo == "t" and $boApresentaParcela == 't') {
            $obFormulario->addComponente( $obTxtQuantidadeParcelas );
            $obFormulario->addComponente( $obInCarencia );
        }

        $obFormulario->addComponente                ( $obCkbExcluirLancamento );
        $obFormulario->addHidden($obHdnExcluirLancamento);
        $obFormulario->montaInnerHTML();
        $stHTML = $obFormulario->getHTML();
    }
    $stJs .= "d.getElementById('spnDadosEvento').innerHTML = '".$stHTML."';";

    return $stJs;
}

#####################################################################################################################
#LOTE DE Matrículas
######################################
function gerarSpanLoteMatricula()
{
    include_once( CAM_GRH_PES_COMPONENTES."IFiltroContrato.class.php" );

    $obIFiltroContrato = new IFiltroContrato;
    $obIFiltroContrato->setTituloFormulario("Dados da Matrícula");
    $obIFiltroContrato->obIContratoDigitoVerificador->setRotulo("Matrícula");
    $obIFiltroContrato->obIContratoDigitoVerificador->obTxtRegistroContrato->obEvento->setOnChange("montaParametrosGET('preencherDadosEvento','inCodigoEvento,inContrato,stProporcional',true);");
    $obIFiltroContrato->obIContratoDigitoVerificador->obTxtRegistroContrato->obEvento->setOnBlur("montaParametrosGET('preencherDadosEvento','inCodigoEvento,inContrato,stProporcional',true);");

    include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoConfiguracao.class.php" );

    //Define a mascara do campo Evento
    $obRFolhaPagamentoConfiguracao = new RFolhaPagamentoConfiguracao;
    $obRFolhaPagamentoConfiguracao->consultar();
    $stMascaraEvento = $obRFolhaPagamentoConfiguracao->getMascaraEvento();
    Sessao::write("stMascaraEvento",$stMascaraEvento);

    include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoConfiguracao.class.php" );
    //Define a mascara do campo Evento
    $obRFolhaPagamentoConfiguracao = new RFolhaPagamentoConfiguracao;
    $obRFolhaPagamentoConfiguracao->consultar();
    $stMascaraEvento = $obRFolhaPagamentoConfiguracao->getMascaraEvento();

    $obBscInnerEvento = new BuscaInner;
    $obBscInnerEvento->setRotulo              ( "**Evento"         );
    $obBscInnerEvento->setId                  ( "stEvento"       );
    $obBscInnerEvento->setTitle               ( "Informe o evento a ser lançado." );
    $obBscInnerEvento->obCampoCod->setName    ( "inCodigoEvento"    );
    $obBscInnerEvento->obCampoCod->setId      ( "inCodigoEvento"    );
    $obBscInnerEvento->obCampoCod->setValue   ( $inCodigoEvento     );
    $obBscInnerEvento->obCampoCod->setPreencheComZeros ( "E"     );
    $obBscInnerEvento->obCampoCod->setMascara ( $stMascaraEvento );
    $obBscInnerEvento->obCampoDescrHidden->setName( "hdnDescEvento" );
    $obBscInnerEvento->setFuncaoBusca( "abrePopUp('".CAM_GRH_FOL_POPUPS."evento/FLManterEvento.php','frm','inCodigoEvento','stEvento','','".Sessao::getId()."&stNaturezasAceitas=P-I-D&stNaturezaChecked=P&boInformarValorQuantidade=t&boInformarQuantidadeParcelas=t&boSugerirValorQuantidade=f&boEventoSistema=f','800','550')" );
    $obBscInnerEvento->obCampoCod->obEvento->setOnBlur("montaParametrosGET('preencherDadosEvento','inCodigoEvento,inContrato,stProporcional',true);");

    $obLblTextoComplementar = new Label;
    $obLblTextoComplementar->setRotulo              ( "Texto Complementar"                                      );
    $obLblTextoComplementar->setId                  ( "stTextoComplementar"                                     );
    $obLblTextoComplementar->setValue("&nbsp;");

    $obLblNatureza = new Label;
    $obLblNatureza->setRotulo          ( "Natureza"                             );
    $obLblNatureza->setId              ( "stNatureza"                           );
    $obLblNatureza->setValue("&nbsp;");

    $obLblTipo = new Label;
    $obLblTipo->setRotulo              ( "Tipo"                                      );
    $obLblTipo->setId                  ( "stTipo"                                    );
    $obLblTipo->setValue("&nbsp;");

    $obCmbLancarProporcional = new Select();
    $obCmbLancarProporcional->setRotulo("Lançar Somente na Aba Proporcional");
    $obCmbLancarProporcional->setTitle("Marque como SIM, para lançar apenas eventos na aba proporcional (válidos somente para esta competência). Para lançar no registro de eventos fixo/variável, utilizar a opïção NãO.");
    $obCmbLancarProporcional->setName("stProporcional");
    $obCmbLancarProporcional->setValue("Não");
    $obCmbLancarProporcional->addOption("Sim","Sim");
    $obCmbLancarProporcional->addOption("Não","Não");

    $obSpnDadosEvento = new Span();
    $obSpnDadosEvento->setId("spnDadosEvento");

    $obSpanLoteMatricula = new Span();
    $obSpanLoteMatricula->setId("spnLoteMatricula");

    $obBtnIncluir = new Button;
    $obBtnIncluir->setName              ( "btIncluir"    );
    $obBtnIncluir->setValue             ( "Incluir"             );
    $obBtnIncluir->obEvento->setOnClick ( "montaParametrosGET( 'incluirLoteMatricula','',true);" );
    $arBarra[] = $obBtnIncluir;

    $obBtnAlterar = new Button;
    $obBtnAlterar->setName              ( "btAlterar"    );
    $obBtnAlterar->setValue             ( "Alterar"             );
    $obBtnAlterar->setDisabled          ( true                  );
    $obBtnAlterar->obEvento->setOnClick ( "montaParametrosGET( 'alterarLoteMatricula', '', true  );" );
    $arBarra[] = $obBtnAlterar;

    $obBtnLimpar = new Button;
    $obBtnLimpar->setName              ( "btLimpar"          );
    $obBtnLimpar->setValue             ( "Limpar"                   );
    $obBtnLimpar->obEvento->setOnClick ( "montaParametrosGET('limparLoteMatricula','',true);");
    $arBarra[] = $obBtnLimpar;

    $obFormulario = new Formulario;
    $obIFiltroContrato->geraFormulario( $obFormulario );
    $obFormulario->addTitulo    ( "Dados do Evento" );
    $obFormulario->addComponente                ( $obCmbLancarProporcional    );
    $obFormulario->addComponente                ( $obBscInnerEvento          );
    $obFormulario->addComponente                ( $obLblTextoComplementar    );
    $obFormulario->addComponente                ( $obLblNatureza    );
    $obFormulario->addComponente                ( $obLblTipo    );
    $obFormulario->addSpan                      ( $obSpnDadosEvento );
    $obFormulario->defineBarra( $arBarra , "left", "<b>**Campo obrigatório.</b>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;");
    $obFormulario->addSpan($obSpanLoteMatricula);
    $obFormulario->montaInnerHTML();

    $stJs  = "d.getElementById('spnOpcao').innerHTML = '".$obFormulario->getHTML()."';"   ;
    $stJs .= $obFormulario->getInnerJavascriptBarra();
    $stJs .= "document.getElementById('inContrato').focus();";
    $stJs .= "f.btIncluir.disabled = true;";
    $stJs .= "f.btLimpar.disabled = true;";

    return $stJs;
}

function gerarListaLoteMatricula()
{
    $rsLoteMatricula = new recordset;
    $arLoteMatricula = ( is_array(Sessao::read('arLoteMatriculas')) ) ? Sessao::read('arLoteMatriculas') : array();
    $rsLoteMatricula->preenche($arLoteMatricula);
    $rsLoteMatricula->addFormatacao("valor","NUMERIC_BR");
    $rsLoteMatricula->addFormatacao("quantidade","NUMERIC_BR");

    $obLista = new Lista;
    $obLista->setRecordSet  ( $rsLoteMatricula );
    $obLista->setTitulo     ("Lista de Eventos Registrados para a Matrícula");
    $obLista->setMostraPaginacao( false );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Código");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Evento");
    $obLista->ultimoCabecalho->setWidth( 25 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Tipo");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addDado();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Prop");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();
    $obLista->addDado();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Valor");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addDado();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Quantidade");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addDado();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Quantidade de Parcelas");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addDado();
    
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Meses de Carência");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addDado();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Ação");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "codigo" );
    $obLista->ultimoDado->setContar();
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "desc_evento" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "tipo" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "proporcional" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "valor" );
    $obLista->ultimoDado->setSomar();
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "quantidade" );
    $obLista->ultimoDado->setSomar();
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "parcelas" );
    $obLista->ultimoDado->setSomar();
    $obLista->commitDado();
    
    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "mes_carencia" );
    $obLista->ultimoDado->setSomar();
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "alterar" );
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('montaAlterarLoteMatricula');" );
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('excluirLoteMatricula');" );
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->setRotuloSomatorio("Somatório dos Lançamentos");

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $stJs = "document.getElementById('spnLoteMatricula').innerHTML = '".$stHtml."';\n";

    return $stJs;
}

function incluirLoteMatricula()
{
    $obErro = validarLoteEvento();
    if ( !$obErro->ocorreu() ) {
        $arLoteMatriculas = Sessao::read("arLoteMatriculas");
        $arTemp["inId"]         = count($arLoteMatriculas);
        $arTemp["registro"]     = $_GET["inContrato"];
        $arTemp["nom_cgm"]      = getNomeCGM($_GET["inContrato"]);
        $arTemp["codigo"]       = $_GET["inCodigoEvento"];
        $arTemp["cod_evento"]   = $_GET["HdninCodigoEvento"];
        $arTemp["desc_evento"]  = $_GET["hdnDescEvento"];
        $arTemp["texto_comp"]   = Sessao::read("stTextoComplementar");
        $arTemp["natureza"]     = Sessao::read("stNatureza");
        $arTemp["tipo"]         = Sessao::read("stTipo");
        $arTemp["proporcional"] = $_GET["stProporcional"];
        $arTemp["valor"]        = str_replace(',','.',str_replace('.','',$_GET["nuValorEvento"]));
        $arTemp["quantidade"]   = str_replace(',','.',str_replace('.','',$_GET["nuQuantidadeEvento"]));
        $arTemp["parcelas"]     = $_GET["nuQuantidadeParcelasEvento"];
        $arTemp["boExcluir"]    = $_GET["boExcluirLancamento"];
        $arTemp["boExcluirDisabled"]    = $_GET["boExcluirLancamentoDisabled"];
        $arTemp["mes_carencia"]     = $_GET["inMesCarenciaEvento"];
        
        $arLoteMatriculas[] = $arTemp;
        Sessao::write("arLoteMatriculas",$arLoteMatriculas);

        $stJs .= gerarListaLoteMatricula();
        $stJs .= limparLoteMatricula();
        $stJs .= "d.getElementById('inCodigoEvento').focus();\n";
    } else {
        $stJs = "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function alterarLoteMatricula()
{
    $obErro = validarLoteEvento("alterar");
    if ( !$obErro->ocorreu() ) {
        $arLoteMatriculas = Sessao::read("arLoteMatriculas");
        $arTemp["inId"]              = Sessao::read("inId");
        $arTemp["registro"]          = $_GET["inContrato"];
        $arTemp["nom_cgm"]           = getNomeCGM($_GET["inContrato"]);
        $arTemp["codigo"]            = $_GET["inCodigoEvento"];
        $arTemp["cod_evento"]        = $_GET["HdninCodigoEvento"];
        $arTemp["desc_evento"]       = $_GET["hdnDescEvento"];
        $arTemp["texto_comp"]        = Sessao::read("stTextoComplementar");
        $arTemp["natureza"]          = Sessao::read("stNatureza");
        $arTemp["tipo"]              = Sessao::read("stTipo");
        $arTemp["proporcional"]      = $_GET["stProporcional"];
        $arTemp["valor"]             = str_replace(',','.',str_replace('.','',$_GET["nuValorEvento"]));
        $arTemp["quantidade"]        = str_replace(',','.',str_replace('.','',$_GET["nuQuantidadeEvento"]));
        $arTemp["parcelas"]          = $_GET["nuQuantidadeParcelasEvento"];
        $arTemp["boExcluir"]         = $_GET["boExcluirLancamento"];
        $arTemp["boExcluirDisabled"] = $_GET["boExcluirLancamentoDisabled"];
        $arTemp["mes_carencia"]      = $_GET["inMesCarenciaEvento"];
        
        $arLoteMatriculas[Sessao::read("inId")] = $arTemp;

        Sessao::write("arLoteMatriculas",$arLoteMatriculas);
        $stJs .= gerarListaLoteMatricula();
        $stJs .= limparLoteMatricula();

        $stJs .= "document.frm.btAlterar.disabled = true;\n";
        $stJs .= "document.frm.btIncluir.disabled = false;\n";
        Sessao::remove("inId");
    } else {
        $stJs = "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function excluirLoteMatricula()
{
    $inId = $_GET["inId"];
    $arTemp = array();
    $arLoteMatriculas = Sessao::read("arLoteMatriculas");
    for ( $i=0; $i<count($arLoteMatriculas); $i++ ) {
        if ($arLoteMatriculas[$i]['inId'] != $inId) {
            $arTemp[] = $arLoteMatriculas[$i];
        }
    }
    Sessao::write("arLoteMatriculas",$arTemp);
    $stJs .= gerarListaLoteMatricula();
    $stJs .= limparLoteMatricula();

    return $stJs;
}

function limparLoteMatricula()
{
    $stJs .= "d.getElementById('spnDadosEvento').innerHTML = '';             \n";
    $stJs .= "d.getElementById('stEvento').innerHTML = '&nbsp;';             \n";
    $stJs .= "f.hdnDescEvento.value = '';                                    \n";
    $stJs .= "f.HdninCodigoEvento.value = '';                                \n";
    $stJs .= "f.inCodigoEvento.value = '';                                   \n";
    $stJs .= "d.getElementById('stTextoComplementar').innerHTML = '&nbsp;';  \n";
    $stJs .= "d.getElementById('stNatureza').innerHTML = '&nbsp;';           \n";
    $stJs .= "d.getElementById('stTipo').innerHTML = '&nbsp;';               \n";
    $stJs .= "f.stProporcional.value = 'Não';                                \n";
    $stJs .= "document.frm.btIncluir.disabled = false;                       \n";
    $stJs .= "document.frm.btAlterar.disabled = true;                        \n";

    return $stJs;
}

function montaAlterarLoteMatricula()
{
    $inId = $_GET["inId"];
    Sessao::write("inId",$inId);
    $arLoteMatriculas = Sessao::read("arLoteMatriculas");
    $arLoteEvento = $arLoteMatriculas[$inId];
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php");
    $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
    $stFiltro = " WHERE codigo = '".$arLoteEvento["codigo"]."'";
    $obTFolhaPagamentoEvento->recuperaTodos($rsEvento,$stFiltro);

    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
    $obTPessoalContrato = new TPessoalContrato();
    $stFiltro = " WHERE registro = ".$arLoteEvento["registro"];
    $obTPessoalContrato->recuperaTodos($rsContrato,$stFiltro);

    $stJs .= gerarSpanRegistroEvento($rsEvento->getCampo("cod_evento"),$rsContrato->getCampo("cod_contrato"),$rsEvento->getCampo("fixado"),$arLoteEvento["tipo"],$rsEvento->getCampo("limite_calculo"),$rsEvento->getCampo("apresenta_parcela"));
    $stJs .= "d.getElementById('stEvento').innerHTML = '".$arLoteEvento["desc_evento"]."';\n";
    $stJs .= "f.inCodigoEvento.value = '".$arLoteEvento["codigo"]."';\n";
    $stJs .= "d.frm.hdnDescEvento.value = '".$arLoteEvento["desc_evento"]."';\n";
    $stJs .= "f.HdninCodigoEvento.value = '".$arLoteEvento["cod_evento"]."';\n";
    $stJs .= "d.getElementById('stTextoComplementar').innerHTML = '".$arLoteEvento["texto_comp"]."';\n";
    $stJs .= "d.getElementById('stNatureza').innerHTML = '".$arLoteEvento["natureza"]."';\n";
    $stJs .= "d.getElementById('stTipo').innerHTML = '".$arLoteEvento["tipo"]."';\n";
    $stJs .= "f.stProporcional.value = '".$arLoteEvento["proporcional"]."';\n";
    $stJs .= "f.inContrato.value = '".$arLoteEvento["registro"]."';";
    $stJs .= "d.getElementById('inNomCGM').innerHTML = '".$arLoteEvento["nom_cgm"]."';\n";
    if ($arLoteEvento["quantidade"] != "") {
        $stJs .= "f.nuQuantidadeEvento.value = '".number_format($arLoteEvento["quantidade"],2,',','.')."';\n";
    }
    if ($arLoteEvento["valor"] != "") {
        $stJs .= "f.nuValorEvento.value = '".number_format($arLoteEvento["valor"],2,',','.')."';\n";
    }
    if ($arLoteEvento["parcelas"] != "") {
        $stJs .= "f.nuQuantidadeParcelasEvento.value = '".$arLoteEvento["parcelas"]."';\n";
    }
    if ($arLoteEvento["mes_carencia"] != "") {
        $stJs .= "f.inMesCarenciaEvento.value = '".$arLoteEvento["mes_carencia"]."';\n";
    } else {
        $stJs .= "f.inMesCarenciaEvento.value = 0;\n";
    }
    if ($arLoteEvento["boExcluirDisabled"] != "") {
        $stJs .= "d.getElementById('boExcluirLancamento').disabled = ".$arLoteEvento["boExcluirDisabled"].";\n";
        $stJs .= "f.boExcluirLancamentoDisabled.value = '".$arLoteEvento["boExcluirDisabled"]."';";
    }
    if ($arLoteEvento["boExcluir"]) {
        $stJs .= "d.getElementById('boExcluirLancamento').checked = true;\n";
    } else {
        $stJs .= "d.getElementById('boExcluirLancamento').checked = false;\n";
    }
    $stJs .= "document.frm.btAlterar.disabled = false;\n";
    $stJs .= "document.frm.btIncluir.disabled = true;\n";

    return $stJs;
}

#####################################################################################################################
#LOTE DE EVENTO
######################################
function gerarSpanLoteEvento()
{
    include_once( CAM_GRH_PES_COMPONENTES."IFiltroContrato.class.php" );

    $obIFiltroContrato = new IFiltroContrato;
    $obIFiltroContrato->setTituloFormulario("Dados da Matrícula");
    $obIFiltroContrato->obIContratoDigitoVerificador->setRotulo("**Matrícula");
    $obIFiltroContrato->obIContratoDigitoVerificador->obTxtRegistroContrato->setNull(false);
    $obIFiltroContrato->obIContratoDigitoVerificador->obTxtRegistroContrato->setNullBarra( false );
    $obIFiltroContrato->obIContratoDigitoVerificador->obTxtRegistroContrato->obEvento->setOnChange("montaParametrosGET('preencherDadosEvento','inCodigoEvento,inContrato,stProporcional',true);");
    $obIFiltroContrato->obIContratoDigitoVerificador->obTxtRegistroContrato->obEvento->setOnBlur("montaParametrosGET('preencherDadosEvento','inCodigoEvento,inContrato,stProporcional',true);");

    include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoConfiguracao.class.php" );
    //Define a mascara do campo Evento
    $obRFolhaPagamentoConfiguracao = new RFolhaPagamentoConfiguracao;
    $obRFolhaPagamentoConfiguracao->consultar();
    $stMascaraEvento = $obRFolhaPagamentoConfiguracao->getMascaraEvento();
    Sessao::write("stMascaraEvento",$stMascaraEvento);

    $obBscInnerEvento = new BuscaInner;
    $obBscInnerEvento->setRotulo              ( "Evento"         );
    $obBscInnerEvento->setId                  ( "stEvento"       );
    $obBscInnerEvento->setTitle               ( "Informe o evento a ser lançado." );
    $obBscInnerEvento->obCampoCod->setName    ( "inCodigoEvento"    );
    $obBscInnerEvento->obCampoCod->setId      ( "inCodigoEvento"    );
    $obBscInnerEvento->obCampoCod->setValue   ( $inCodigoEvento     );
    $obBscInnerEvento->obCampoCod->setPreencheComZeros ( "E"     );
    $obBscInnerEvento->obCampoCod->setMascara ( $stMascaraEvento );
    $obBscInnerEvento->obCampoDescrHidden->setName( "hdnDescEvento" );
    $obBscInnerEvento->setFuncaoBusca( "abrePopUp('".CAM_GRH_FOL_POPUPS."evento/FLManterEvento.php','frm','inCodigoEvento','stEvento','','".Sessao::getId()."&stNaturezasAceitas=P-I-D&stNaturezaChecked=P&boInformarValorQuantidade=t&boInformarQuantidadeParcelas=t&boSugerirValorQuantidade=f&boEventoSistema=f','800','550')" );
    $obBscInnerEvento->obCampoCod->obEvento->setOnBlur("montaParametrosGET('preencherDadosEvento','inCodigoEvento,inContrato,stProporcional',true);");

    $obLblTextoComplementar = new Label;
    $obLblTextoComplementar->setRotulo( "Texto Complementar" );
    $obLblTextoComplementar->setId( "stTextoComplementar" );
    $obLblTextoComplementar->setValue("&nbsp;");

    $obLblNatureza = new Label;
    $obLblNatureza->setRotulo( "Natureza" );
    $obLblNatureza->setId( "stNatureza" );
    $obLblNatureza->setValue("&nbsp;");

    $obLblTipo = new Label;
    $obLblTipo->setRotulo( "Tipo" );
    $obLblTipo->setId( "stTipo" );
    $obLblTipo->setValue("&nbsp;");

    $obCmbLancarProporcional = new Select();
    $obCmbLancarProporcional->setRotulo("Lançar Somente na Aba Proporcional");
    $obCmbLancarProporcional->setTitle("Marque como SIM, para lançar apenas eventos na aba proporcional (válidos somente para esta competência). Para lançar no registro de eventos fixo/variável, utilizar a opição Não.");
    $obCmbLancarProporcional->setName("stProporcional");
    $obCmbLancarProporcional->setValue("Não");
    $obCmbLancarProporcional->addOption("Sim","Sim");
    $obCmbLancarProporcional->addOption("Não","Não");

    $obSpnDadosEvento = new Span();
    $obSpnDadosEvento->setId("spnDadosEvento");

    $obSpanLoteEvento = new Span();
    $obSpanLoteEvento->setId("spnLoteEvento");

    $obBtnIncluir = new Button;
    $obBtnIncluir->setName              ( "btIncluir"    );
    $obBtnIncluir->setValue             ( "Incluir"             );
    $obBtnIncluir->obEvento->setOnClick ( "montaParametrosGET( 'incluirLoteEvento','',true);" );
    $arBarra[] = $obBtnIncluir;

    $obBtnAlterar = new Button;
    $obBtnAlterar->setName              ( "btAlterar"    );
    $obBtnAlterar->setValue             ( "Alterar"             );
    $obBtnAlterar->setDisabled          ( true                  );
    $obBtnAlterar->obEvento->setOnClick ( "montaParametrosGET( 'alterarLoteEvento', '', true  );" );
    $arBarra[] = $obBtnAlterar;

    $obBtnLimpar = new Button;
    $obBtnLimpar->setName              ( "btLimpar"          );
    $obBtnLimpar->setValue             ( "Limpar"                   );
    $obBtnLimpar->obEvento->setOnClick ( "montaParametrosGET('limparLoteEvento','',true);");
    $arBarra[] = $obBtnLimpar;

    $obFormulario = new Formulario;
    $obFormulario->addTitulo    ( "Dados do Evento" );
    $obFormulario->addComponente                ( $obBscInnerEvento          );
    $obFormulario->addComponente                ( $obLblTextoComplementar    );
    $obFormulario->addComponente                ( $obLblNatureza    );
    $obFormulario->addComponente                ( $obLblTipo    );

    $obFormulario->addComponente                ( $obCmbLancarProporcional    );
    $obIFiltroContrato->geraFormulario( $obFormulario );
    $obFormulario->addSpan                      ( $obSpnDadosEvento );
    $obFormulario->defineBarra( $arBarra , "left", "<b>**Campo obrigatório.</b>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;");
    $obFormulario->addSpan($obSpanLoteEvento);
    $obFormulario->montaInnerHTML();

    $stJs  = "d.getElementById('spnOpcao').innerHTML = '".$obFormulario->getHTML()."';"   ;
    $stJs .= $obFormulario->getInnerJavascriptBarra();
    $stJs .= "document.getElementById('inCodigoEvento').focus();";
    $stJs .= "f.btIncluir.disabled = true;";
    $stJs .= "f.btLimpar.disabled = true;";

    return $stJs;
}

function validarLoteEvento($stAcao="incluir")
{
    $obErro = new Erro();
    if ($_GET["inContrato"] == "") {
        $stErro .= "@Campo Matrícula inválido!()";
    }
    if ($_GET["inCodigoEvento"] == "") {
        $stErro .= "@Campo Evento inválido!()";
    }
    if ($stErro == "") {
        if (Sessao::read("fixado") == "V" and $_GET["nuValorEvento"] == "") {
            $stErro .= "@Campo Valor inválido!()";
        }
        if (Sessao::read("fixado") == "Q" and $_GET["nuQuantidadeEvento"] == "") {
            $stErro .= "@Campo Quantidade inválido!()";
        }
        if (isset($_GET["nuQuantidadeParcelasEvento"]) and $_GET["nuQuantidadeParcelasEvento"] == "") {
            $stErro .= "@Campo Quantidade de Parcelas inválido!()";
        }

        $inCodigoEvento  = ($_GET["inCodigoEvento"] != "") ? str_pad($_GET["inCodigoEvento"],strlen(Sessao::read("stMascaraEvento")),"0",STR_PAD_LEFT) : "";
        $arLoteEventos = Sessao::read("arLoteEventos");
        if (is_array($arLoteEventos) and $stAcao == "incluir") {
            foreach ($arLoteEventos as $arLoteEvento) {
                if ($arLoteEvento["proporcional"] == $_GET["stProporcional"] and $arLoteEvento["registro"] == $_GET["inContrato"] and $arLoteEvento["codigo"] == $inCodigoEvento) {
                    $stErro .= "@O evento ".$inCodigoEvento." já foi incluído para o contrato ".$_GET["inContrato"]."!()";
                    break;
                }
            }
        }
        $arLoteMatriculas = Sessao::read("arLoteMatriculas");
        if (is_array($arLoteMatriculas) and $stAcao == "incluir") {
            foreach ($arLoteMatriculas as $arLoteMatricula) {
                if ($arLoteMatricula["proporcional"] == $_GET["stProporcional"] and $arLoteMatricula["registro"] == $_GET["inContrato"] and $arLoteMatricula["codigo"] == $inCodigoEvento) {
                    $stErro .= "@O evento ".$inCodigoEvento." já foi incluído para o contrato ".$_GET["inContrato"]."!()";
                    break;
                }
            }
        }
        if ($inCodigoEvento != "") {
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEvento.class.php");
            $obTFolhaPagamentoRegistroEvento = new TFolhaPagamentoRegistroEvento();
            $stFiltro  = " AND codigo = '".$inCodigoEvento."'";
            $stFiltro .= " AND configuracao_evento_caso.cod_configuracao = 1";
            $obErro = $obTFolhaPagamentoRegistroEvento->recuperaRelacionamentoConfiguracao( $rsEvento, $stFiltro);
            if (!$obErro->ocorreu()) {
                if ($rsEvento->getNumLinhas() < 0) {
                    $stErro .= "@O evento informado não possui configuração para a subdivisïção/cargo e/ou especialidade do contrato em manutenção.";
                }
            }
        }
    }
    $obErro->setDescricao($stErro);

    return $obErro;
}

function getNomeCGM($inRegistro)
{
    $stNomCgm = "";

    // Busca o nome do servidor
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
    $obTPessoalContrato = new TPessoalContrato();
    $stFiltro = " AND registro = ".$inRegistro;
    $obTPessoalContrato->recuperaCgmDoRegistroServidor($rsCgm,$stFiltro);

    if ($rsCgm->getNumLinhas() == -1) {
        // Busca o nome do pensionista
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalPensionista.class.php");
        $obTPessoalPensionista = new TPessoalPensionista();
        $obTPessoalContrato->recuperaCgmDoRegistro($rsCgm,$stFiltro);
    }

    $stNomCgm = $rsCgm->getCampo("numcgm")."-".$rsCgm->getCampo("nom_cgm");

    return $stNomCgm;
}

function gerarListaLoteEvento()
{
    $rsLoteEvento = new recordset;
    $arLoteEvento = ( is_array(Sessao::read('arLoteEventos')) ) ? Sessao::read('arLoteEventos') : array();
    $rsLoteEvento->preenche($arLoteEvento);
    $rsLoteEvento->addFormatacao("valor","NUMERIC_BR");
    $rsLoteEvento->addFormatacao("quantidade","NUMERIC_BR");

    $obLista = new Lista;
    $obLista->setRecordSet  ( $rsLoteEvento );
    $obLista->setTitulo     ("Lista de Matrículas");
    $obLista->setMostraPaginacao( false );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Matrícula");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Nome");
    $obLista->ultimoCabecalho->setWidth( 25 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Prop");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();
    $obLista->addDado();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Valor");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addDado();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Quantidade");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addDado();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Quantidade de Parcelas");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addDado();
    
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Meses de Carência");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addDado();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Ação");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "registro" );
    $obLista->ultimoDado->setContar();
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "nom_cgm" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "proporcional" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "valor" );
    $obLista->ultimoDado->setSomar();
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "quantidade" );
    $obLista->ultimoDado->setSomar();
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "parcelas" );
    $obLista->ultimoDado->setSomar();
    $obLista->commitDado();
    
    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "mes_carencia" );
    $obLista->ultimoDado->setSomar();
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "alterar" );
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('montaAlterarLoteEvento');" );
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('excluirLoteEvento');" );
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->setRotuloSomatorio("Somatório dos Lançamentos");

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $stJs = "document.getElementById('spnLoteEvento').innerHTML = '".$stHtml."';\n";

    return $stJs;
}

function limparLoteEvento()
{
    $stJs .= "f.inContrato.value = '';                           \n";
    $stJs .= "d.getElementById('inNomCGM').innerHTML = '&nbsp;'; \n";
    if (Sessao::read("fixado") == "V") {
        $stJs .= "f.nuValorEvento.value = '';                    \n";
        $stJs .= "f.nuQuantidadeEvento.value = '';               \n";
    } else {
        $stJs .= "f.nuQuantidadeEvento.value = '';               \n";
    }
    if (Sessao::read("limite_calculo") == "t") {
        $stJs .= "f.nuQuantidadeParcelasEvento.value = '';       \n";
    }
    $stJs .= "f.stProporcional.value = 'não';\n";
    $stJs .= "d.getElementById('boExcluirLancamento').disabled = true; \n";
    $stJs .= "d.getElementById('boExcluirLancamento').checked = false; \n";
    $stJs .= "document.frm.btAlterar.disabled = true;                  \n";
    $stJs .= "document.frm.btIncluir.disabled = false;                 \n";

    return $stJs;
}

function incluirLoteEvento()
{
    $obErro = validarLoteEvento();
    if ( !$obErro->ocorreu() ) {
        $arLoteEventos = Sessao::read("arLoteEventos");
        $arTemp["inId"]              = count($arLoteEventos);
        $arTemp["registro"]          = $_GET["inContrato"];
        $arTemp["nom_cgm"]           = getNomeCGM($_GET["inContrato"]);
        $arTemp["codigo"]            = $_GET["inCodigoEvento"];
        $arTemp["cod_evento"]        = $_GET["HdninCodigoEvento"];
        $arTemp["desc_evento"]       = $_GET["hdnDescEvento"];
        $arTemp["texto_comp"]        = Sessao::read("stTextoComplementar");
        $arTemp["natureza"]          = Sessao::read("stNatureza");
        $arTemp["tipo"]              = Sessao::read("stTipo");
        $arTemp["proporcional"]      = $_GET["stProporcional"];
        $arTemp["valor"]             = str_replace(',','.',str_replace('.','',$_GET["nuValorEvento"]));
        $arTemp["quantidade"]        = str_replace(',','.',str_replace('.','',$_GET["nuQuantidadeEvento"]));
        $arTemp["parcelas"]          = $_GET["nuQuantidadeParcelasEvento"];
        $arTemp["boExcluir"]         = $_GET["boExcluirLancamento"];
        $arTemp["boExcluirDisabled"] = $_GET["boExcluirLancamentoDisabled"];
        $arTemp["mes_carencia"]      = $_GET["inMesCarenciaEvento"];
        
        $arLoteEventos[] = $arTemp;
        Sessao::write("arLoteEventos",$arLoteEventos);
        
        $stJs .= gerarListaLoteEvento();
        $stJs .= limparLoteEvento();
        $stJs .= "d.getElementById('inContrato').focus();\n";
    } else {
        $stJs = "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function alterarLoteEvento()
{
    $obErro = validarLoteEvento("alterar");
    if ( !$obErro->ocorreu() ) {
        $arLoteEventos = Sessao::read("arLoteEventos");
        $arTemp["inId"]              = Sessao::read("inId");
        $arTemp["registro"]          = $_GET["inContrato"];
        $arTemp["nom_cgm"]           = getNomeCGM($_GET["inContrato"]);
        $arTemp["codigo"]            = $_GET["inCodigoEvento"];
        $arTemp["cod_evento"]        = $_GET["HdninCodigoEvento"];
        $arTemp["desc_evento"]       = $_GET["hdnDescEvento"];
        $arTemp["texto_comp"]        = Sessao::read("stTextoComplementar");
        $arTemp["natureza"]          = Sessao::read("stNatureza");
        $arTemp["tipo"]              = Sessao::read("stTipo");
        $arTemp["proporcional"]      = $_GET["stProporcional"];
        $arTemp["valor"]             = str_replace(',','.',str_replace('.','',$_GET["nuValorEvento"]));
        $arTemp["quantidade"]        = str_replace(',','.',str_replace('.','',$_GET["nuQuantidadeEvento"]));
        $arTemp["parcelas"]          = $_GET["nuQuantidadeParcelasEvento"];
        $arTemp["boExcluir"]         = $_GET["boExcluirLancamento"];
        $arTemp["boExcluirDisabled"] = $_GET["boExcluirLancamentoDisabled"];
        $arTemp["mes_carencia"]      = $_GET["inMesCarenciaEvento"];
        
        $arLoteEventos[Sessao::read("inId")] = $arTemp;
        Sessao::write("arLoteEventos",$arLoteEventos);
        $stJs .= gerarListaLoteEvento();
        $stJs .= limparLoteEvento();

        $stJs .= "document.frm.btAlterar.disabled = true;\n";
        $stJs .= "document.frm.btIncluir.disabled = false;\n";
        Sessao::remove("inId");
    } else {
        $stJs = "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function excluirLoteEvento()
{
    $inId = $_GET["inId"];
    $arTemp = array();
    $arLoteEventos = Sessao::read("arLoteEventos");
    for ( $i=0; $i<count( $arLoteEventos ); $i++ ) {
        if ($arLoteEventos[$i]['inId'] != $inId) {
            $arTemp[] = $arLoteEventos[$i];
        }
    }
    Sessao::write("arLoteEventos",$arTemp);
    $stJs .= gerarListaLoteEvento();

    return $stJs;
}

function montaAlterarLoteEvento()
{
    $inId = $_GET["inId"];
    Sessao::write("inId",$inId);
    $arLoteEventos = Sessao::read("arLoteEventos");
    $arLoteEvento = $arLoteEventos[$inId];
    $stJs .= "d.getElementById('stEvento').innerHTML = '".$arLoteEvento["desc_evento"]."';\n";
    $stJs .= "f.inCodigoEvento.value = '".$arLoteEvento["codigo"]."';\n";
    $stJs .= "d.frm.hdnDescEvento.value = '".$arLoteEvento["desc_evento"]."';\n";
    $stJs .= "f.HdninCodigoEvento.value = '".$arLoteEvento["cod_evento"]."';\n";
    $stJs .= "d.getElementById('stTextoComplementar').innerHTML = '".$arLoteEvento["texto_comp"]."';\n";
    $stJs .= "d.getElementById('stNatureza').innerHTML = '".$arLoteEvento["natureza"]."';\n";
    $stJs .= "d.getElementById('stTipo').innerHTML = '".$arLoteEvento["tipo"]."';\n";
    $stJs .= "f.stProporcional.value = '".$arLoteEvento["proporcional"]."';";
    $stJs .= "f.inContrato.value = '".$arLoteEvento["registro"]."';";
    $stJs .= "d.getElementById('inNomCGM').innerHTML = '".$arLoteEvento["nom_cgm"]."';\n";
    if ($arLoteEvento["quantidade"] != "") {
        $stJs .= "f.nuQuantidadeEvento.value = '".number_format($arLoteEvento["quantidade"],2,',','.')."';";
    }
    if ($arLoteEvento["valor"] != "") {
        $stJs .= "f.nuValorEvento.value = '".number_format($arLoteEvento["valor"],2,',','.')."';";
    }
    if ($arLoteEvento["parcelas"] != "") {
        $stJs .= "f.nuQuantidadeParcelasEvento.value = '".$arLoteEvento["parcelas"]."';";
    }
    if ($arLoteEvento["mes_carencia"] != "") {
        $stJs .= "f.inMesCarenciaEvento.value = '".$arLoteEvento["mes_carencia"]."';";
    } else {
        $stJs .= "f.inMesCarenciaEvento.value = 0;";
    }
    if ($arLoteEvento["boExcluirDisabled"] != '') {
        $stJs .= "d.getElementById('boExcluirLancamento').disabled = ".$arLoteEvento["boExcluirDisabled"].";\n";
        $stJs .= "f.boExcluirLancamentoDisabled.value = '".$arLoteEvento["boExcluirDisabled"]."';";
    }
    if ($arLoteEvento["boExcluir"]) {
        $stJs .= "d.getElementById('boExcluirLancamento').checked = true;\n";
    } else {
        $stJs .= "d.getElementById('boExcluirLancamento').checked = false;\n";
    }
    $stJs .= "document.frm.btAlterar.disabled = false;\n";
    $stJs .= "document.frm.btIncluir.disabled = true;\n";

    return $stJs;
}

#####################################################################################################################
#IMPORTAR
######################################
function gerarSpanImportar()
{
    $arColunas[0]['coluna'] = "Matrícula";
    $arColunas[0]['valor']  = "contrato";

    $arColunas[1]['coluna'] = "Evento";
    $arColunas[1]['valor']  = "evento";

    $arColunas[2]['coluna'] = "Valor";
    $arColunas[2]['valor']  = "valor";

    $arColunas[3]['coluna'] = "Quantidade";
    $arColunas[3]['valor']  = "quantidade";

    $arColunas[4]['coluna'] = "Quantidade Parcelas";
    $arColunas[4]['valor']  = "quantidadeParcelas";
    
    $arColunas[5]['coluna'] = "Meses de Carência";
    $arColunas[5]['valor']  = "mesesCarencia";

    $rsColunas = new RecordSet;
    $rsColunas->preenche( $arColunas );

    $obFilArquivo = new FileBox;
    $obFilArquivo->setRotulo        ( "Arquivo de Importação" );
    $obFilArquivo->setName          ( "stCaminho" );
    $obFilArquivo->setId            ( "stCaminho" );
    $obFilArquivo->setSize          ( 40          );
    $obFilArquivo->setMaxLength     ( 100         );

    $obCmbCasaDecimal = new Select;
    $obCmbCasaDecimal->setRotulo     ( "Delimitador de Casa Decimal"   );
    $obCmbCasaDecimal->setName       ( "stCasaDecimal"  );
    $obCmbCasaDecimal->setId         ( "stCasaDecimal"  );
    $obCmbCasaDecimal->setValue      ( ""               );
    $obCmbCasaDecimal->addOption     ( "" , "Últimos dois algarismos"   );
    $obCmbCasaDecimal->addOption     ( ",", "Vírgula"  );
    $obCmbCasaDecimal->addOption     ( ".", "Ponto"  );

    $obTxtDelimitador = new TextBox;
    $obTxtDelimitador->setRotulo            ( "Delimitador de Coluna"                     );
    $obTxtDelimitador->setTitle             ( "Informe o delimitador a ser utilizado na leitura do arquivo." );
    $obTxtDelimitador->setName              ( "stDelimitador"                             );
    $obTxtDelimitador->setId                ( "stDelimitador"                             );
    $obTxtDelimitador->setValue             ( ";"                                         );
    $obTxtDelimitador->setSize              ( 1                                           );
    $obTxtDelimitador->setMaxLength         ( 1                                           );
    $obTxtDelimitador->setCaracteresAceitos("[^#]");

    $obCmbColuna1 = new Select;
    $obCmbColuna1->setRotulo     ( "Coluna 1"  );
    $obCmbColuna1->setName       ( "stColuna1" );
    $obCmbColuna1->setValue      ( "contrato"  );
    $obCmbColuna1->setCampoID    ( "valor"     );
    $obCmbColuna1->setCampoDesc  ( "coluna"    );
    $obCmbColuna1->preencheCombo ( $rsColunas  );

    $rsColunas->setPrimeiroElemento();
    $obCmbColuna2 = new Select;
    $obCmbColuna2->setRotulo     ( "Coluna 2"  );
    $obCmbColuna2->setName       ( "stColuna2" );
    $obCmbColuna2->setValue      ( "evento"    );
    $obCmbColuna2->setCampoID    ( "valor"     );
    $obCmbColuna2->setCampoDesc  ( "coluna"    );
    $obCmbColuna2->preencheCombo ( $rsColunas  );

    $rsColunas->setPrimeiroElemento();
    $obCmbColuna3 = new Select;
    $obCmbColuna3->setRotulo     ( "Coluna 3"  );
    $obCmbColuna3->setName       ( "stColuna3" );
    $obCmbColuna3->setValue      ( "valor"  );
    $obCmbColuna3->setCampoID    ( "valor"     );
    $obCmbColuna3->setCampoDesc  ( "coluna"    );
    $obCmbColuna3->preencheCombo ( $rsColunas  );

    $rsColunas->setPrimeiroElemento();
    $obCmbColuna4 = new Select;
    $obCmbColuna4->setRotulo     ( "Coluna 4"  );
    $obCmbColuna4->setName       ( "stColuna4" );
    $obCmbColuna4->setValue      ( "quantidade"  );
    $obCmbColuna4->setCampoID    ( "valor"     );
    $obCmbColuna4->setCampoDesc  ( "coluna"    );
    $obCmbColuna4->preencheCombo ( $rsColunas  );

    $rsColunas->setPrimeiroElemento();
    $obCmbColuna5 = new Select;
    $obCmbColuna5->setRotulo     ( "Coluna 5"  );
    $obCmbColuna5->setName       ( "stColuna5" );
    $obCmbColuna5->setValue      ( "quantidadeParcelas"  );
    $obCmbColuna5->setCampoID    ( "valor"     );
    $obCmbColuna5->setCampoDesc  ( "coluna"    );
    $obCmbColuna5->preencheCombo ( $rsColunas  );
    
    $rsColunas->setPrimeiroElemento();
    $obCmbColuna6 = new Select;
    $obCmbColuna6->setRotulo     ( "Coluna 6"  );
    $obCmbColuna6->setName       ( "stColuna6" );
    $obCmbColuna6->setValue      ( "mesesCarencia"  );
    $obCmbColuna6->setCampoID    ( "valor"     );
    $obCmbColuna6->setCampoDesc  ( "coluna"    );
    $obCmbColuna6->preencheCombo ( $rsColunas  );

    $obSpnListaEventos = new Span;
    $obSpnListaEventos->setId    ( "spnListaEventos" );
    $obSpnListaEventos->setValue ( ""                );

    $obSpnValoresSomados = new Span;
    $obSpnValoresSomados->setId    ( "spnValoresSomados" );
    $obSpnValoresSomados->setValue ( ""                  );

    $obBtnImportarEvento = new Button;
    $obBtnImportarEvento->setName                    ( "btnImportar"       );
    $obBtnImportarEvento->setValue                   ( "Importar"          );
    $obBtnImportarEvento->setTipo                    ( "button"            );
    $obBtnImportarEvento->obEvento->setOnClick       ( "buscaValor('importarEventos');" );
    $obBtnImportarEvento->setDisabled                ( false               );

    $obbtnLimparLista = new Button;
    $obbtnLimparLista->setName                    ( "btnLimparLista" );
    $obbtnLimparLista->setValue                   ( "Limpar"         );
    $obbtnLimparLista->setTipo                    ( "button"         );
    $obbtnLimparLista->obEvento->setOnClick       ( "executaFuncaoAjax('limparImportar','',true);"  );
    $obbtnLimparLista->setDisabled                ( false            );

    $botoesForm = array ( $obBtnImportarEvento , $obbtnLimparLista );

    $obFormulario = new Formulario;
    $obFormulario->addTitulo     ( "Folha/Importação" );
    $obFormulario->addComponente ( $obFilArquivo      );
    $obFormulario->addComponente ( $obCmbCasaDecimal  );
    $obFormulario->addComponente ( $obTxtDelimitador  );
    $obFormulario->addComponente ( $obCmbColuna1      );
    $obFormulario->addComponente ( $obCmbColuna2      );
    $obFormulario->addComponente ( $obCmbColuna3      );
    $obFormulario->addComponente ( $obCmbColuna4      );
    $obFormulario->addComponente ( $obCmbColuna5      );
    $obFormulario->addComponente ( $obCmbColuna6      );
    $obFormulario->defineBarra($botoesForm);
    $obFormulario->addSpan               ( $obSpnListaEventos                                               );
    $obFormulario->addSpan               ( $obSpnValoresSomados                                             );

    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();

    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);

    $stJs  = "d.getElementById('spnOpcao').innerHTML = '".$obFormulario->getHTML()."';"   ;
    //$stJs .= "document.frm.stEval.value = '".$stEval."'; \n";
    return $stJs;

}

function preencheSpnListaEventos($boMostraAcao = true)
{
    $rsEventosCadastrados = new recordset;
    $arEventosCadastrados = ( is_array(Sessao::read('EventosCadastrados')) ) ? Sessao::read('EventosCadastrados') : array();
    $rsEventosCadastrados->preenche($arEventosCadastrados);
    $obLista = new Lista;
    $obLista->setRecordSet  ( $rsEventosCadastrados );
    $obLista->setTitulo     ("Eventos Cadastrados"  );
    $obLista->setMostraPaginacao( false );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Matrícula");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Evento");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Valor");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addDado();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Quantidade");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addDado();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Quantidade de Parcelas");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addDado();
    
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Meses de Carência");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addDado();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("SituAaão");
    $obLista->ultimoCabecalho->setWidth( 35 );
    $obLista->commitCabecalho();

    if ($boMostraAcao) {
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Ação");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();
    }

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "registro" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "inCodigoEvento" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "valor" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "quantidade" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "parcelas" );
    $obLista->commitDado();
    
    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "mes_carencia" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "stSituacao" );
    $obLista->commitDado();

    if ($boMostraAcao) {
        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "alterar" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:alterarEvento();" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:excluirEvento();" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();
    }

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $stJs = "d.getElementById('spnListaEventos').innerHTML = '".$stHtml."';\n";

    return $stJs;
}

function preencheSpnValoresSomados($boMostraAcao = true)
{
    $arEventosCadastrados = Sessao::read('EventosCadastrados');
    $arValoresSomados = array();
    if (is_array($arEventosCadastrados)) {
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php");
        $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
        foreach ($arEventosCadastrados as $inIndex=>$arEventoCadastrado) {
            if ($arEventoCadastrado["stSituacao"] == 'Ok') {
                $inCodigoEvento             = $arEventoCadastrado["inCodigoEvento"];
                $nuValorEvento              = str_replace( '.', '',  $arEventoCadastrado["valor"] );
                $nuValorEvento              = str_replace( ',', '.', $nuValorEvento );
                $nuQuantidadeEvento         = str_replace( '.', '',  $arEventoCadastrado["quantidade"] );
                $nuQuantidadeEvento         = str_replace( ',', '.', $nuQuantidadeEvento );
                $inQuantidadeParcelasEvento = $arEventoCadastrado["parcelas"];
                $inMesCarenciaEvento        = $arEventoCadastrado["mes_carencia"];

                $stFiltro = " WHERE codigo = '".$inCodigoEvento."'";
                $obTFolhaPagamentoEvento->recuperaTodos($rsEvento,$stFiltro);

                $arValoresSomados[$inCodigoEvento]["registro"]++;
                $arValoresSomados[$inCodigoEvento]["inCodigoEvento"]             = $inCodigoEvento;
                $arValoresSomados[$inCodigoEvento]["stDescEvento"]               = trim($rsEvento->getCampo("descricao"));
                $arValoresSomados[$inCodigoEvento]["nuValorEvento"]             += $nuValorEvento;
                $arValoresSomados[$inCodigoEvento]["nuQuantidadeEvento"]        += $nuQuantidadeEvento;
                $arValoresSomados[$inCodigoEvento]["nuQuantidadeParcelasEvento"]+= $inQuantidadeParcelasEvento;
                $arValoresSomados[$inCodigoEvento]["inMesCarenciaEvento"]       += $inMesCarenciaEvento;
            }
        }
    }
    $arTemp = array();
    foreach ($arValoresSomados as $arValorSomado) {
        $arTemp[] = $arValorSomado;
    }

    $rsValoresSomados = new RecordSet;
    $rsValoresSomados->preenche($arTemp);
    $rsValoresSomados->addFormatacao("nuValorEvento"             , "NUMERIC_BR");
    $rsValoresSomados->addFormatacao("nuQuantidadeEvento"        , "NUMERIC_BR");

    $obLista = new Lista;
    $obLista->setRecordSet  ( $rsValoresSomados );
    $obLista->setTitulo     ("Resumo Valores Somados"  );
    $obLista->setMostraPaginacao( false );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 4 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Quantidade de Matrícula");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Evento");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Descrição");
    $obLista->ultimoCabecalho->setWidth( 35 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Valor");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Quantidade");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Quantidade de Parcelas");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Meses de Carência");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "registro" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "inCodigoEvento" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "stDescEvento" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "nuValorEvento" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "nuQuantidadeEvento" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "nuQuantidadeParcelasEvento" );
    $obLista->commitDado();
    
    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "inMesCarenciaEvento" );
    $obLista->commitDado();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $stJs = "d.getElementById('spnValoresSomados').innerHTML = '".$stHtml."';\n";

    return $stJs;

}

function retornaArrayGet($inId)
{
    $arEvento['inId'                      ] = $inId;
    $arEvento['inContrato'                ] = $_GET['inContrato'];
    $arEvento['inCodigoEvento'            ] = $_GET['inCodigoEvento'];
    $arEvento['nuValorEvento'             ] = $_GET['nuValorEvento'];
    $arEvento['nuQuantidadeEvento'        ] = $_GET['nuQuantidadeEvento'];
    $arEvento['nuQuantidadeParcelasEvento'] = $_GET['nuQuantidadeParcelasEvento'];
    $arEvento['stHdnFixado'               ] = $_GET['stHdnFixado'];
    $arEvento['stHdnApresentaParcela'     ] = $_GET['stHdnApresentaParcela'];
    $arEvento['stSituacao'                ] = 'Ok';
    $arEvento['inMesCarenciaEvento'       ] = $_GET['inMesCarenciaEvento'];

    return $arEvento;
}

function validaRegistroContrato($inRegistro)
{
    include_once ( CAM_GRH_PES_NEGOCIO."RPessoalContrato.class.php" );
    $boErro = false;
    $stErro = "";
    $stValidaInteiro = "";
    $stValidaInteiro = validaInteiro( $inRegistro );
    if ($stValidaInteiro == "") {
        $obRPessoalContrato = new RPessoalContrato;
        $obRPessoalContrato->listarCgmDoRegistro( $rsContrato, $inRegistro );
        if ( $rsContrato->getNumLinhas() <= 0 ) {
            $boErro = true;
        }
    } else {
        $boErro = true;
    }

    if ($boErro) {
        $stErro = " - contrato informado já inválido";
    }

    return $stErro;
}

function validaCodigoEvento($inCodigoEvento, $stMascaraEvento, &$rsEvento)
{
    include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEvento.class.php" );
    include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoConfiguracao.class.php" );
    $stErro = "";

    $ate = strlen( $stMascaraEvento ) - strlen( $inCodigoEvento );
    for ($i=0; $i<$ate; $i++) {
        $inCodigoEvento = "0".$inCodigoEvento;
    }

    $obRFolhaPagamentoEvento = new RFolhaPagamentoEvento;
    $obRFolhaPagamentoEvento->setCodigo( $inCodigoEvento );
    $obRFolhaPagamentoEvento->setNaturezas( 'P' );
    $obRFolhaPagamentoEvento->setNaturezas( 'I' );
    $obRFolhaPagamentoEvento->setNaturezas( 'D' );
    $obRFolhaPagamentoEvento->setEventoSistema( 'false' );
    $obRFolhaPagamentoEvento->listarEvento( $rsEvento );

    if ( $rsEvento->getNumLinhas() <= 0 ) {
        $stErro = " - evento informado é inválido";
    }

    return $stErro;
}

function validaValor($nuValor, $stCasaDecimal = "", $mensagem   ="valor informado é inválido")
{
    $stErro = "";
    if ($stCasaDecimal != "") {
        if ($stCasaDecimal != '.') {
            $nuValor = str_replace( '.', '', $nuValor );
        }
        if ($stCasaDecimal != ',') {
            $nuValor = str_replace( ',', '', $nuValor );
        }
        $stCasaDecimal = "\\".$stCasaDecimal;
    }

    //Verifica se o valor possui de 0 a 9 numeros, o separador decimal e 0 a 2 digitos de casa decimal OU se o valor já formado por e somente até 11 números
    if ( !((ereg( "^[0-9]{0,9}".$stCasaDecimal."[0-9]{0,2}$", $nuValor, $matriz )) || (ereg( "^[0-9]{0,11}$", $nuValor, $matriz )) ) ) {
        $stErro = " - $mensagem";
    }

    return $stErro;
}

function validaInteiro($nuValor)
{
    $stErro = "";
    if ( !ereg( "^[0-9]{0,10}$", $nuValor, $matriz ) ) {
        $stErro = " - quantidade de parcelas informada é inválida";
    }

    return $stErro;
}

function validaEventoImportacao($inContrato
                               , $inCodigoEvento
                               , $nuValorEvento
                               , $nuQuantidadeEvento
                               , $nuQuantidadeParcelasEvento
                               , $inMesesCarencia
                               , $stFixado
                               , $stApresentaParcelas
                               , $inCodPeriodoMovimentacao
                               , $stMascaraEvento) {
    include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEvento.class.php" );
    $stErro = "";
    
    $obTFolhaPagamentoRegistroEvento = new TFolhaPagamentoRegistroEvento;
    $obTFolhaPagamentoRegistroEvento->setDado( "cod_periodo_movimentacao", $inCodPeriodoMovimentacao );
    $obTFolhaPagamentoRegistroEvento->setDado( "registro"                , $inContrato               );
    $obTFolhaPagamentoRegistroEvento->setDado( "codigo"                  , formataEvento( $inCodigoEvento, $stMascaraEvento ) );
    $obTFolhaPagamentoRegistroEvento->recuperaEventosPorContratoEPeriodo( $rsRegistroEventos );
    
    if ( $rsRegistroEventos->getNumLinhas() > 0 ) {
        $stErro = " - já existe um evento cadastrado para este contrato";
    } else {
        // Verifica se este evento já foi cadastrado para o contrato
        $ate = count( Sessao::read('EventosCadastrados') );
        $arEventosCadastrados = Sessao::read('EventosCadastrados');
        for ($i=0; $i<$ate; $i++) {
            if ( ( $inCodigoEvento == $arEventosCadastrados[$i]['inCodigoEvento'] ) && ( $inContrato == $arEventosCadastrados[$i]['inContrato'] ) ) {
                $stErro = " - já existe um evento cadastrado para este contrato";
            }
        }
    }

    if( !$inContrato )
        $stErro .= " - contrato não informado";
    if( !$inCodigoEvento )
        $stErro .= " - evento não informado";
    if( ( $stFixado != 'Q' ) && (( $nuValorEvento =='' ) || ( $nuValorEvento == '0,00' ) ) )
        $stErro .= " - valor não informado";
    if( ( $stFixado == 'Q' ) && (!( $nuValorEvento =='' ) || ( $nuValorEvento == '0,00' ) ) )
        $stErro .= " - valor não pode ser informado para este evento";
    if( ( $stFixado == 'Q' ) && ( $nuQuantidadeEvento =='' ) || ( $nuQuantidadeEvento == '0,00' ) )
        $stErro .= " - quantidade não informada";
    if( ( $stApresentaParcelas != 'f' ) && (( $nuQuantidadeParcelasEvento =='' ) || ( $nuQuantidadeParcelasEvento == '0,00' )) )
        $stErro .= " - quantidade de parcelas não informada";
    if( ( $stApresentaParcelas == 'f' ) && (!( $nuQuantidadeParcelasEvento =='' ) || ( $nuQuantidadeParcelasEvento == '0,00' )) )
        $stErro .= " - quantidade de parcelas não pode ser informada para este evento";
    if( ( $inMesesCarencia == '' ) )
        $stErro .= " - os meses de carência não podem ser informados para este evento";

    return $stErro;
}

function formataValor($stValor, $stCasaDecimal = "")
{
    if ($stValor != "") {
        if ( validaValor( $stValor, $stCasaDecimal ) == "" ) {
            switch ($stCasaDecimal) {
                case "":
                    if ( strlen( $stValor ) == 2 ) {
                        $stValor = "0".$stValor;
                    } elseif ( strlen( $stValor ) == 1 ) {
                        $stValor = "00".$stValor;
                    }
                    $stValor = substr( $stValor, 0, strlen( $stValor ) - 2 ) .".". substr( $stValor, strlen( $stValor ) - 2, 2 );
                break;
                case ".":
                    $stValor = str_replace( ',', '', $stValor );
                break;
                case ",":
                    $stValor = str_replace( '.', '', $stValor );
                    $stValor = str_replace( ',', '.', $stValor );
                break;
            }
            $stValor = number_format ( $stValor, 2, ",", ".");
        }
    } else {
        $stValor = "0,00";
    }

    return $stValor;
}

function formataEvento($inCodigoEvento, $stMascaraEvento)
{
    if ($inCodigoEvento != "") {
        $ate = strlen( $stMascaraEvento ) - strlen( $inCodigoEvento );
        for ($i=0; $i<$ate; $i++) {
            $inCodigoEvento = "0".$inCodigoEvento;
        }
    } else {
        $inCodigoEvento = "&nbsp;";
    }

    return $inCodigoEvento;
}

function importarEventos()
{
    include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoConfiguracao.class.php" );
    include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php" );
    include_once( CLA_ARQUIVO_CSV );

    $inId          = 0;
    $stErroLinha   = "";
    $stErroArquivo = "";

    $stCaminho = $_FILES["stCaminho"]["tmp_name"];
    $stDelimitador = $_REQUEST['stDelimitador'];
    $stCasaDecimal = $_REQUEST['stCasaDecimal'];
    Sessao::write("EventosCadastrados",array());

    $obRFolhaPagamentoConfiguracao = new RFolhaPagamentoConfiguracao;
    $obRFolhaPagamentoConfiguracao->consultar();
    $stMascaraEvento = $obRFolhaPagamentoConfiguracao->getMascaraEvento();

    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao;
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao( $rsUltimaMovimentacao );
    $inCodPeriodoMovimentacao = $rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao");

    //Monta colunas do arquivo
    for ($i=1; $i<=6; $i++) {
        for ($j=$i+1; $j<=6; $j++) {
            if ($_REQUEST[ "stColuna".$i ] == $_REQUEST[ "stColuna".$j ]) {
                $stErroArquivo = "Coluna $i é igual a coluna $j.";
            }
        }
        
        $inColuna = $i-1;
        switch ($_REQUEST[ "stColuna".$i ]) {
            case "contrato":
                $inColContratro          = $inColuna;
            break;
            case "evento":
                $inColEvento             = $inColuna;
            break;
            case "valor":
                $inColValor              = $inColuna;
            break;
            case "quantidade":
                $inColQuantidade         = $inColuna;
            break;
            case "quantidadeParcelas":
                $inColQuantidadeParcelas = $inColuna;
            break;
            case "mesesCarencia":
                $inColMesesCarencia = $inColuna;
            break;
        }
    }
    
    if ($stErroArquivo == "") {
        $arquivoEventos = new ArquivoCSV( $stCaminho );
        $arquivoEventos->setDelimitadorColuna( $stDelimitador );
        $obErro = $arquivoEventos->Abrir('r');
        if ( !$obErro->ocorreu() ) {
            $arEventosCadastrados = Sessao::read("EventosCadastrados");
            while ( !feof( $arquivoEventos->reArquivo ) ) {
                $stErroLinha = "Erro";
                $arLinhas = $arquivoEventos->LerLinha();
                if ($arLinhas != '') {
                    $stErroLinha .= validaRegistroContrato ( $arLinhas[ $inColContratro          ] );
                    $stErroLinha .= validaCodigoEvento     ( $arLinhas[ $inColEvento             ], $stMascaraEvento, $rsEvento );
                    $stErroLinha .= validaValor            ( $arLinhas[ $inColValor              ], $stCasaDecimal );
                    $stErroLinha .= validaValor            ( $arLinhas[ $inColQuantidade         ], $stCasaDecimal, "quantidade informada é inválida" );
                    $stErroLinha .= validaInteiro          ( $arLinhas[ $inColQuantidadeParcelas ] );
                    $stErroLinha .= validaEventoImportacao ( $arLinhas[ $inColContratro          ]
                                                           , $arLinhas[ $inColEvento             ]
                                                           , $arLinhas[ $inColValor              ]
                                                           , $arLinhas[ $inColQuantidade         ]
                                                           , $arLinhas[ $inColQuantidadeParcelas ]
                                                           , $arLinhas[ $inColMesesCarencia      ]
                                                           , $rsEvento->getCampo( "fixado" )
                                                           , $rsEvento->getCampo( "apresenta_parcela" )
                                                           , $inCodPeriodoMovimentacao
                                                           , $stMascaraEvento );

                    if ($stErroLinha == "Erro") {
                        $stErroLinha = "Ok";
                    }
                    $inId = $inId + 1;

                    $arEvento['inId'                      ] = $inId;
                    $arEvento['registro'                  ] = $arLinhas[ $inColContratro          ] ? $arLinhas[ $inColContratro          ] : "&nbsp;";
                    $arEvento['inCodigoEvento'            ] = formataEvento( $arLinhas[ $inColEvento ], $stMascaraEvento );
                    $arEvento['cod_evento'                ] = $rsEvento->getCampo("cod_evento");
                    $arEvento['valor'                     ] = formataValor( $arLinhas[ $inColValor ], $stCasaDecimal );
                    $arEvento['quantidade'                ] = formataValor( $arLinhas[ $inColQuantidade         ], $stCasaDecimal );
                    $arEvento['parcelas'                  ] = $arLinhas[ $inColQuantidadeParcelas ] ? $arLinhas[ $inColQuantidadeParcelas ] : "&nbsp;";
                    $arEvento['stHdnFixado'               ] = $rsEvento->getCampo( "fixado" ) ? $rsEvento->getCampo( "fixado" ) : "&nbsp;";
                    $arEvento['stHdnApresentaParcela'     ] = $rsEvento->getCampo( "apresenta_parcela" ) ? $rsEvento->getCampo( "apresenta_parcela" ) : "&nbsp;";
                    $arEvento['stSituacao'                ] = $stErroLinha;
                    $arEvento['boExcluirDisabled'         ] = "true";
                    $arEvento['mes_carencia'              ] = $arLinhas[ $inColMesesCarencia ] ? $arLinhas[ $inColMesesCarencia ] : "&nbsp;";

                    $arEventosCadastrados[] = $arEvento;
                }
            }
            Sessao::write("EventosCadastrados",$arEventosCadastrados);
            $arquivoEventos->Fechar();

            $stJs .= preencheSpnListaEventos( false );
            $stJs .= preencheSpnValoresSomados( false );
        } else {
            $stJs = "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
        }
    }

    return $stJs;
}

function limpaCamposLista()
{
    $stJs .= "document.getElementById('inNomCGM').innerHTML       = '&nbsp;'; \n";
    $stJs .= "document.getElementById('inContrato').value         = '';       \n";
    $stJs .= "document.getElementById('inCodigoEvento').value        = '';    \n";
    $stJs .= "document.getElementById('stEvento').innerHTML       = '&nbsp;'; \n";
    $stJs .= "document.getElementById('spnDadosEvento').innerHTML = '';       \n";

    return $stJs;
}

######################################
#IMPORTAR
######################################

function submeter()
{
    $obErro = new Erro();
    switch ($_GET["stOpcao"]) {
        case "lote_evento":
            if (count(Sessao::read("arLoteEventos")) == 0) {
                $obErro->setDescricao("@Deve haver pelo menos uma Matrícula para um evento na lista de Matrículas.");
            }
            break;
        case "lote_matricula":
            if (count(Sessao::read("arLoteMatriculas")) == 0) {
                $obErro->setDescricao("@Deve haver pelo menos um evento para uma Matrícula na lista de eventos.");
            }
            break;
        case "importar":
            break;
    }
    if (!$obErro->ocorreu()) {
        $stJs .= "parent.frames[2].Salvar();";
    } else {
        $stJs = "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function limparImportar()
{
    $stJs .= "document.frm.reset();\n";
    $stJs .= "document.getElementById('stOpcaoImportar').checked = true;\n";
    $stJs .= gerarSpanImportar();

    return $stJs;
}

$boAjax = true;
switch ($_REQUEST["stCtrl"]) {
    case "gerarSpanOpcoes":
        $stJs = gerarSpanOpcoes();
        break;
    case "gerarSpanLoteEvento":
        $stJs = gerarSpanLoteEvento();
        break;
    case "preencherDadosEvento":
        $stJs = preencherDadosEvento();
        break;
    case "incluirLoteEvento":
        $stJs = incluirLoteEvento();
        break;
    case "alterarLoteEvento":
        $stJs = alterarLoteEvento();
        break;
    case "excluirLoteEvento":
        $stJs = excluirLoteEvento();
        break;
    case "limparLoteEvento":
        $stJs = limparLoteEvento();
        break;
    case "montaAlterarLoteEvento":
        $stJs = montaAlterarLoteEvento();
        break;

    case "incluirLoteMatricula":
        $stJs = incluirLoteMatricula();
        break;
    case "alterarLoteMatricula":
        $stJs = alterarLoteMatricula();
        break;
    case "excluirLoteMatricula":
        $stJs = excluirLoteMatricula();
        break;
    case "limparLoteMatricula":
        $stJs = limparLoteMatricula();
        break;
    case "montaAlterarLoteMatricula":
        $stJs = montaAlterarLoteMatricula();
        break;

    case "importarEventos":
        $boAjax = false;
        $stJs = importarEventos();
        break;
    case "limparImportar":
        $stJs = limparImportar();
        break;

    case "submeter":
        $stJs = submeter();
        break;

}

if ($stJs) {
    if ($boAjax) {
        echo $stJs;
    } else {
        sistemaLegado::executaFrameOculto($stJs);
    }
}
?>
