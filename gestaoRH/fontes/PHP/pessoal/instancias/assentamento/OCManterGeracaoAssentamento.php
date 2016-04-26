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
    * Página Oculta do Gerar Assentamento
    * Data de Criação   : 19/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Andre Almeida

    * @ignore
    $Id: OCManterGeracaoAssentamento.php 65033 2016-04-19 20:31:30Z jean $

    * Caso de uso: uc-04.04.14

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
// include_once ( "../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php"  );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroContrato.class.php"                                      );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCGMContrato.class.php"                                   );
include_once ( CAM_GRH_PES_COMPONENTES."IBuscaInnerLotacao.class.php"                                   );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalCargo.class.php"                                            );
include_once ( CAM_GA_ORGAN_NEGOCIO."ROrganogramaOrgao.class.php"                                       );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalVantagem.class.php"                                         );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalAssentamento.class.php"                                     );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php"                                         );
include_once ( CAM_GA_ORGAN_NEGOCIO."ROrganogramaOrgao.class.php"                                       );
include_once ( CAM_GA_NORMAS_MAPEAMENTO."TNorma.class.php"                                              );
include_once ( CAM_GA_NORMAS_MAPEAMENTO."TTipoNorma.class.php"                                          );
include_once ( CAM_GA_NORMAS_MAPEAMENTO."TNormaDataTermino.class.php"                                   );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalRegime.class.php"                                           );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPadrao.class.php"                                    );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );

// global  $inContrato
//         ,$inCodRegime
//         ,$inCodSubDivisao
//         ,$inCodSubDivisaoFuncao
//         ,$inCodFuncao
//         ,$inCodCargo
//         ,$inCodEspecialidadeCargo
//         ,$inCodRegimeFuncao
//         ,$inCodEspecialidadeFuncao
//         ,$inCodPadrao
//         ,$stDataProgressao
//         ,$dtDataProgressao
//         ,$stHorasMensais; 

function gerarAssentamento($boExecuta=false,$stArquivo="Form")
{
    switch ($_REQUEST['stModoGeracao']) {
        case 'contrato':
            $stJs .= gerarSpan1($boExecuta,$stArquivo);
        break;
        case 'cgm/contrato':
            $stJs .= gerarSpan2($boExecuta,$stArquivo);
        break;
        case 'cargo':
            $stJs .= gerarSpan3($boExecuta,$stArquivo);
        break;
        case 'lotacao':
            $stJs .= gerarSpan4($boExecuta,$stArquivo);
        break;
    }
    $stJs .= "f.hdnModoGeracao.value = '".$_REQUEST['stModoGeracao']."';";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function gerarSpan1($boExecuta=false,$stArquivo="Form")
{
    $obFormulario = new Formulario;

    $obIContrato = new IFiltroContrato(true);
    if ($stArquivo == "Form") {
        $obIContrato->setTituloFormulario ( "Gerar Assentamento por Matrícula" );
    } else {
        $obIContrato->setTituloFormulario ( "Filtro por Matrícula" );
    }
    $stOnBlur = $obIContrato->obIContratoDigitoVerificador->obTxtRegistroContrato->obEvento->getOnBlur();
    $obIContrato->obIContratoDigitoVerificador->obTxtRegistroContrato->obEvento->setOnBlur($stOnBlur." preencheClassificacao(this.value, 'matricula');buscaValor('buscaContrato');");
    //$obIContrato->obIContratoDigitoVerificador->setNull(false);
    $obIContrato->geraFormulario( $obFormulario );

    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();

    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);

    $stHtml = $obFormulario->getHTML();

    $stJs  = "f.stEval.value = '".$stEval."'; \n";
    $stJs .= "d.getElementById('spnSpan1').innerHTML = '".$stHtml."';";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function gerarSpan2($boExecuta=false,$stArquivo="Form")
{
    $obFormulario = new Formulario;

    $obICGMContrato = new IFiltroCGMContrato();

    if ($stArquivo == "Form") {
        $obICGMContrato->setTituloFormulario( "Gerar Assentamento por CGM/Matrícula" );
    } else {
        $obICGMContrato->setTituloFormulario( "Filtro por CGM/Matrícula" );
    }

    $stOnBlur = $obICGMContrato->obCmbContrato->obEvento->getOnBlur();
    $obICGMContrato->obCmbContrato->obEvento->setOnBlur($stOnBlur." preencheClassificacao(this.value, 'cgm');");

    $obICGMContrato->obBscCGM->setNull(true);

    $obICGMContrato->geraFormulario( $obFormulario );

    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();

    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);

    $stHtml = $obFormulario->getHTML();

    $stJs  = "f.stEval.value = '".$stEval."'; \n";
    $stJs .= "d.getElementById('spnSpan1').innerHTML = '".$stHtml."';";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function gerarSpan3($boExecuta=false,$stArquivo="Form")
{
    $obRPessoalCargo = new RPessoalCargo;
    $obRPessoalCargo->listarCargo( $rsCargos );

    $obFormulario = new Formulario;

    //Define objetos RADIO para armazenar o TIPO do assentamento por cargo
    $obChkCargoExercido = new Checkbox;
    $obChkCargoExercido->setRotulo                          ( "Cargo Exercido"                                          );
    $obChkCargoExercido->setName                            ( "boCargoExercido"                                         );
    if ($stArquivo == "Form") {
        $obChkCargoExercido->setTitle                       ( "Informe se o assentamento será gerado por cargo exercido." );
    } else {
        $obChkCargoExercido->setTitle                       ( "Informe se o filtro será por cargo exercido."            );
    }
    $obChkCargoExercido->setValue                           ( true                                                      );

    $obChkFuncaoExercida = new Checkbox;
    $obChkFuncaoExercida->setRotulo                         ( "Função Exercida"                                         );
    $obChkFuncaoExercida->setName                           ( "boFuncaoExercida"                                        );
    if ($stArquivo == "Form") {
        $obChkFuncaoExercida->setTitle                      ( "Informe se o assentamento será gerado por função exercida." );
    } else {
        $obChkFuncaoExercida->setTitle                      ( "Informe se o filtro será por função exercida."           );
    }
    $obChkFuncaoExercida->setValue                          ( true                                                      );

    //Define objeto TEXTBOX para armazenar o Código do cargo
    $obTxtCodCargo = new TextBox;
    if ($stArquivo == "Form") {
        $obTxtCodCargo->setRotulo                           ( "*Cargo"                                                  );
        $obTxtCodCargo->setTitle                            ( "Informe o cargo dos servidores para os quais serão gerados assentamentos." );
    } else {
        $obTxtCodCargo->setRotulo                           ( "Cargo"                                                  );
        $obTxtCodCargo->setTitle                            ( "Informe o cargo."                                        );
    }
    $obTxtCodCargo->setName                                 ( "inCodCargoTxt"                                           );
    $obTxtCodCargo->setId                                   ( "inCodCargoTxt"                                           );
    $obTxtCodCargo->setSize                                 ( 10                                                        );
    $obTxtCodCargo->setMaxLength                            ( 10                                                        );
    $obTxtCodCargo->setInteiro                              ( true                                                      );
    $obTxtCodCargo->obEvento->setOnChange                   ( "buscaValor('preencherEspecialidade'); preencheClassificacao(this.value, 'cargo');" );

    //Define objeto SELECT para listar a DESCRIÇÃOO do cargo
    $obCmbCargo = new Select;
    if ($stArquivo == "Form") {
        $obCmbCargo->setRotulo                              ( "*Cargo"                                                  );
        $obCmbCargo->setTitle                               ( "Informe o cargo dos servidores para os quais serão gerados assentamentos." );
    } else {
        $obCmbCargo->setRotulo                              ( "Cargo"                                                   );
        $obCmbCargo->setTitle                               ( "Informe o cargo."                                        );
    }
    $obCmbCargo->setName                                    ( "inCodCargo"                                              );
    $obCmbCargo->setStyle                                   ( "width: 200px"                                            );
    $obCmbCargo->addOption                                  ( "", "Selecione"                                           );
    $obCmbCargo->setCampoID                                 ( "cod_cargo"                                               );
    $obCmbCargo->setCampoDesc                               ( "descricao"                                               );
    $obCmbCargo->preencheCombo                              ( $rsCargos                                                 );
    $obCmbCargo->obEvento->setOnChange                      ( "buscaValor('preencherEspecialidade'); preencheClassificacao(this.value, 'cargo');" );

    //Define objeto TEXTBOX para armazenar o CÓDIGO da especialidade
    $obTxtCodEspecialidade = new TextBox;
    $obTxtCodEspecialidade->setRotulo                       ( "Especialidade"                                           );
    if ($stArquivo == "Form") {
        $obTxtCodEspecialidade->setTitle                    ( "Informe a especialidade dos servidores para os quais serão gerados assentamentos." );
    } else {
        $obTxtCodEspecialidade->setTitle                    ( "Informe a especialidade."                                );
    }
    $obTxtCodEspecialidade->setName                         ( "inCodExpecialidadeTxt"                                   );
    $obTxtCodEspecialidade->setId                           ( "inCodExpecialidadeTxt"                                   );
    $obTxtCodEspecialidade->setSize                         ( 10                                                        );
    $obTxtCodEspecialidade->setMaxLength                    ( 10                                                        );
    $obTxtCodEspecialidade->setInteiro                      ( true                                                      );

    //Define objeto SELECT para listar a DESCRIÇÃO da especialidade
    $obCmbEspecialidade = new Select;
    $obCmbEspecialidade->setRotulo                          ( "Especialidade"                                           );
    if ($stArquivo == "Form") {
        $obCmbEspecialidade->setTitle                       ( "Informe a especialidade dos servidores para os quais serão gerados assentamentos." );
    } else {
        $obCmbEspecialidade->setTitle                       ( "Informe a especialidade."                                );
    }
    $obCmbEspecialidade->setName                            ( "inCodEspecialidade"                                      );
    $obCmbEspecialidade->setStyle                           ( "width: 200px"                                            );
    $obCmbEspecialidade->addOption                          ( "", "Selecione"                                           );

    if ($stArquivo == "Form") {
        $obFormulario->addTitulo                            ( "Gerar Assentamento por Cargo"                            );
    } else {
        $obFormulario->addTitulo                            ( "Filtro por Cargo"                                        );
    }
    $obFormulario->addComponente                            ( $obChkCargoExercido                                       );
    $obFormulario->addComponente                            ( $obChkFuncaoExercida                                      );
    $obFormulario->addComponenteComposto                    ( $obTxtCodCargo, $obCmbCargo                               );
    $obFormulario->addComponenteComposto                    ( $obTxtCodEspecialidade, $obCmbEspecialidade               );

    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();

    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);

    $stHtml = $obFormulario->getHTML();

    $stJs  = "f.stEval.value = '".$stEval."'; \n";
    $stJs .= "d.getElementById('spnSpan1').innerHTML = '".$stHtml."';";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function gerarSpan4($boExecuta=false,$stArquivo="Form")
{
    $obFormulario = new Formulario;

    $obIBuscaInnerLotacao = new IBuscaInnerLotacao;
    $obIBuscaInnerLotacao->obBscLotacao->setTitle("Informe a lotação dos servidores para os quais serão gerados assentamentos.");
    $obIBuscaInnerLotacao->obBscLotacao->setRotulo("*Lotação");
    // $obIBuscaInnerLotacao->obBscLotacao->setNull(false);

    if ($stArquivo == "Form") {
        $obFormulario->addTitulo          ( "Gerar Assentamento por Lotação"          );
    } else {
        $obFormulario->addTitulo          ( "Filtrar por Lotação"                     );
    }

    $stOnBlur = $obIBuscaInnerLotacao->obBscLotacao->obCampoCod->obEvento->getOnBlur();
    $stOnBlur = str_replace('ajaxJavaScript', 'ajaxJavaScriptSincrono', $stOnBlur);
    $obIBuscaInnerLotacao->obBscLotacao->obCampoCod->obEvento->setOnBlur($stOnBlur." preencheClassificacao(document.frm.HdninCodLotacao.value, 'lotacao'); ");
    $stOnChange = $obIBuscaInnerLotacao->obBscLotacao->obCampoCod->obEvento->getOnChange();
    $stOnChange = str_replace('ajaxJavaScript', 'ajaxJavaScriptSincrono', $stOnChange);
    $obIBuscaInnerLotacao->obBscLotacao->obCampoCod->obEvento->setOnChange($stOnChange." preencheClassificacao(document.frm.HdninCodLotacao.value, 'lotacao'); ");

    $obIBuscaInnerLotacao->geraFormulario($obFormulario);

    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();

    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);

    $stHtml = $obFormulario->getHTML();

    $stJs  = "f.stEval.value = '".$stEval."'; \n";
    $stJs .= "d.getElementById('spnSpan1').innerHTML = '".$stHtml."';";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function calcularDataFinal($boExecuta=false)
{
//    if ($_REQUEST['inQuantidadeDias'] == 0) {
//        $inQuantidadeDias = 0;
//        $stJs .= "f.inQuantidadeDias.value = '".$inQuantidadeDias."';";
//    } else {
//        $inQuantidadeDias = $_REQUEST['inQuantidadeDias'];
//    }
    $inQuantidadeDias = $_REQUEST['inQuantidadeDias'];
    if ($_REQUEST['stDataInicial'] != "" and $_REQUEST['inQuantidadeDias'] != 0) {
        $arDataInicial  = explode("/", $_REQUEST['stDataInicial'] );
        $stDataFinal = date("d/m/Y", mktime(0, 0, 0, $arDataInicial[1], ($arDataInicial[0]+$inQuantidadeDias-1), $arDataInicial[2]));

        $stJs .= "f.stDataFinal.value = '".$stDataFinal."';";
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function ajustarQuantidadeDias($boExecuta=false,$boSpan=false)
{
    $stDataInicial = ( $_REQUEST['stDataInicial'] != "" ) ? $_REQUEST['stDataInicial'] : Sessao::read('stDataInicial');
    $stDataFinal   = ( $_REQUEST['stDataFinal']   != "" ) ? $_REQUEST['stDataFinal']   : Sessao::read('stDataFinal');

//    if ($stDataFinal == "") {
//        $arDataInicial  = explode("/", $stDataInicial );
//        $stDataFinal    = date("d/m/Y", mktime(0, 0, 0, $arDataInicial[1], ($arDataInicial[0]+$_REQUEST['inQuantidadeDias']-1), $arDataInicial[2]));
//        $stJs .= "f.stDataFinal.value = '".$stDataFinal."';";
//    }
    $arDataInicial = explode("/",$stDataInicial);
    $arDataFinal   = explode("/",$stDataFinal);
    $stDataInicial = $arDataInicial[2]."/".$arDataInicial[1]."/".$arDataInicial[0];
    $stDataFinal   = $arDataFinal[2]  ."/".$arDataFinal[1]  ."/".$arDataFinal[0];

    // Armazena nas variáveis $DataInicial e $DataFinal
    // os valores de $DataI e $DataF no formato 'timestamp'
    $DataInicial = getdate(strtotime($stDataInicial));
    $DataFinal   = getdate(strtotime($stDataFinal));

    // Calcula a Diferença
    $Dif = round (($DataFinal[0] - $DataInicial[0]) / 86400) + 1;
    if ($boSpan) {
        $stJs .= 'd.getElementById("inQuantidadeDias").innerHTML = "'.$Dif.'";';
    } else {
        $stJs .= "f.inQuantidadeDias.value = $Dif;\n";
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function preencherEspecialidade($boExecuta=false)
{
    $obRPessoalCargo = new RPessoalCargo;
    $obRPessoalCargo->setCodCargo( $_REQUEST['inCodCargo'] );
    $obRPessoalCargo->addEspecialidade();
    $obRPessoalCargo->roUltimoEspecialidade->listarEspecialidadesPorCargo( $rsEspecialidades );

    $stJs .= "limpaSelect(f.inCodEspecialidade,0);                                          \n";
    $stJs .= "f.inCodEspecialidade.options[0] = new Option('Selecione','', 'selected');     \n";
    $stJs .= "f.inCodExpecialidadeTxt.value='';                                             \n";
    $i = 1;
    while (!$rsEspecialidades->eof()) {
        $stJs .= "f.inCodEspecialidade.options[".$i++."] = new Option('".$rsEspecialidades->getCampo("descricao")."','".$rsEspecialidades->getCampo("cod_especialidade")."', '');\n";
        $rsEspecialidades->proximo();
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function preencherAssentamento($boExecuta=false)
{
    $rsAssentamentos = new RecordSet;
    $obRPessoalAssentamento = new RPessoalAssentamento( new RPessoalVantagem );
    $inCodClassificacao = ( $_REQUEST['inCodClassificacao'] ) ? $_REQUEST['inCodClassificacao'] : Sessao::read('inCodClassificacao');
    $obRPessoalAssentamento->obRPessoalClassificacaoAssentamento->setCodClassificacaoAssentamento( $inCodClassificacao );

    if ($_REQUEST['stModoGeracao'] == '') {
        $stModoGeracao = sessao::read('stModoGeracao');
    } else {
        $stModoGeracao = $_REQUEST['stModoGeracao'];
    }

    switch ($stModoGeracao) {
        case 'contrato':
        case 'cgm/contrato':
            $obRPessoalAssentamento->listarAssentamentoPorContrato( $rsAssentamentos, $_REQUEST['inContrato'], '', 'contrato' );                        
            break;
        case 'cargo':
            if ($_REQUEST['boCargoExercido']) {
                $obRPessoalAssentamento->listarAssentamentoPorContrato( $rsAssentamentos, $_REQUEST['inCodCargo'], '', 'cargo_exercido' );
            } else {
                $obRPessoalAssentamento->listarAssentamentoPorContrato( $rsAssentamentos, $_REQUEST['inCodCargo'], '', 'cargo' );
            }
            break;
        case 'lotacao':
            $obRPessoalAssentamento->listarAssentamentoPorContrato( $rsAssentamentos, $_REQUEST['HdninCodLotacao'], '', 'lotacao' );
            break;
    }

    $stJs .= "limpaSelect(f.inCodAssentamento,0); \n";
    $stJs .= "f.inCodAssentamento.options[0] = new Option('Selecione','', 'selected');\n";
    $stJs .= "f.inCodAssentamentoTxt.value='';\n";
    $i = 1;
    while (!$rsAssentamentos->eof()) {
        if ( $rsAssentamentos->getCampo('cod_assentamento') == Sessao::read('inCodAssentamento') ) {
            $stSelected = "selected";
            $stJs .= "f.inCodAssentamentoTxt.value='".$rsAssentamentos->getCampo('cod_assentamento')."';\n";
            $inAuxCodAssentamento = $rsAssentamentos->getCampo('cod_assentamento');
        } else {
            $stSelected = "";
        }
        $stJs .= "f.inCodAssentamento.options[".$i++."] = new Option('".$rsAssentamentos->getCampo('descricao')."','".$rsAssentamentos->getCampo('cod_assentamento')."', '$stSelected');\n";
        $rsAssentamentos->proximo();
    }

    $stJs .= "f.inCodAssentamento.value='".$inAuxCodAssentamento."';\n";    
    $stJs .= gerarSpanLicencaPremio();

    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function preencherLotacao($boExecuta=false)
{
    $obROrganogramaOrgao = new ROrganogramaOrgao;
    if ($_REQUEST['inCodLotacao']) {
        $obROrganogramaOrgao->setCodOrgaoEstruturado( $inCodLotacao );
        $obROrganogramaOrgao->setCodOrgaoEstruturado($_REQUEST['inCodLotacao']);
        $obROrganogramaOrgao->listarOrgaoReduzido( $rsOrgao, "", "" );
        $stNull = "";
        if ( $rsOrgao->getNumLinhas() <= 0) {
            $stJs .= 'f.inCodLotacaoTxt.value = "";';
            $stJs .= 'd.getElementById("stLotacao").innerHTML = "&nbsp;";';
        } else {
            $stJs .= 'd.getElementById("stLotacao").innerHTML = "'.($rsOrgao->getCampo('descricao')?$rsOrgao->getCampo('descricao'):$stNull ).'";';
        }
    } else {
        $stJs .= 'd.getElementById("stLotacao").innerHTML = "&nbsp;";';
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function validarAssentamento($stAcao="",&$stDescricaoClassificacao,&$stDescricaoAssentamento)
{
    $obErro = new erro;
    if ( !$obErro->ocorreu() ) {
        $stModoGeracao = ( $_REQUEST['stModoGeracao'] ) ? $_REQUEST['stModoGeracao'] : $_REQUEST['hdnModoGeracao'];
        switch ($stModoGeracao) {
            case "contrato":
            case "cgm/contrato":
                if ($_REQUEST['inContrato'] == "") {
                    $obErro->setDescricao("@Campo Matrícula inválido!()");
                }
            break;
            case "cargo":
                if ( !isset($_REQUEST['boCargoExercido']) and !isset($_REQUEST['boFuncaoExercida']) ) {
                    $obErro->setDescricao("@Campo Cargo Exercido ou Função Exercida inválidos!()");
                }
                if ( !$obErro->ocorreu() and $_REQUEST['inCodCargo'] == "" ) {
                    $obErro->setDescricao("@Campo Cargo inválido!()");
                }
            break;
            case "lotacao":
                if ($_REQUEST['inCodLotacao'] == "") {
                    $obErro->setDescricao("@Campo Lotação inválido!()");
                }
            break;
        }
    }
    if ($_REQUEST['inCodClassificacao'] == "") {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Classificação inválido!()");
    }
    if ($_REQUEST['inCodAssentamento'] == "") {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Assentamento inválido!()");
    }
    //if ( ($_REQUEST['stDataInicial'] == "" or $_REQUEST['stDataFinal'] == "") ) {
    //    $obErro->setDescricao($obErro->getDescricao()."@Campo Período inválido!()");
    //}
    if ( ($_REQUEST['stDataInicial'] == "") ) {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Período inválido!(Informe a data inicial)");
    }
    if ( ($_REQUEST['stDataInicial'] != "" and $_REQUEST['stDataFinal'] != "") and SistemaLegado::comparaDatas($_REQUEST['stDataInicial'],$_REQUEST['stDataFinal']) ) {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Período inválido!( Data Final (".$_REQUEST['stDataFinal'].") deve ser maior que Data Inicial (".$_REQUEST['stDataInicial']."))");
    }
    if (Sessao::read("boValidaLicencaPremio") == "true") {
        if ( ($_REQUEST['dtInicial'] == "" or $_REQUEST['dtFinal'] == "") ) {
            $obErro->setDescricao($obErro->getDescricao()."@Campo Período Aquisitivo Licença Prêmio inválido!()");
        }
        if ( ($_REQUEST['dtInicial'] != "" and $_REQUEST['dtFinal'] != "") and SistemaLegado::comparaDatas($_REQUEST['dtInicial'],$_REQUEST['dtFinal']) ) {
            $obErro->setDescricao($obErro->getDescricao()."@Campo Período Aquisitivo Licença Prêmio inválido!( Data Final (".$_REQUEST['dtFinal'].") deve ser maior que Data Inicial (".$_REQUEST['dtInicial']."))");
        }
    }
    if((isset($_REQUEST['inCodRegime'])&&$_REQUEST['inCodRegime']=='')||(isset($_REQUEST['inCodRegimeFuncao'])&&$_REQUEST['inCodRegimeFuncao']=='')){
        $obErro->setDescricao($obErro->getDescricao()."@Campo Regime inválido!()");
    }
    if((isset($_REQUEST['inCodSubDivisao'])&&$_REQUEST['inCodSubDivisao']=='')||(isset($_REQUEST['inCodSubDivisaoFuncao'])&&$_REQUEST['inCodSubDivisaoFuncao']=='')){
        $obErro->setDescricao($obErro->getDescricao()."@Campo Subdivisão inválido!()");
    }
    if((isset($_REQUEST['inCodCargo'])&&$_REQUEST['inCodCargo']=='')){
        $obErro->setDescricao($obErro->getDescricao()."@Campo Cargo inválido!()");
    }
    if((isset($_REQUEST['inCodFuncao'])&&$_REQUEST['inCodFuncao']=='')){
        $obErro->setDescricao($obErro->getDescricao()."@Campo Função inválido!()");
    }
    if((isset($_REQUEST['dtDataAlteracaoFuncao'])&&$_REQUEST['dtDataAlteracaoFuncao']=='')){
        $obErro->setDescricao($obErro->getDescricao()."@Campo Data da Alteração da Função inválido!()");
    }
    if((isset($_REQUEST['stHorasMensais'])&&$_REQUEST['stHorasMensais']=='')){
        $obErro->setDescricao($obErro->getDescricao()."@Campo Horas Mensais inválido!()");
    }
    if((isset($_REQUEST['stHorasSemanais'])&&$_REQUEST['stHorasSemanais']=='')){
        $obErro->setDescricao($obErro->getDescricao()."@Campo Horas Semanais inválido!()");
    }
    if((isset($_REQUEST['inSalario'])&&$_REQUEST['inSalario']=='')){
        $obErro->setDescricao($obErro->getDescricao()."@Campo Salário inválido!()");
    }
    if((isset($_REQUEST['dtVigenciaSalario'])&&$_REQUEST['dtVigenciaSalario']=='')){
        $obErro->setDescricao($obErro->getDescricao()."@Campo Vigência do Salário inválido!()");
    }
    if((isset($_REQUEST['stObservacao'])&&$_REQUEST['stObservacao']=='')){
        $obErro->setDescricao($obErro->getDescricao()."@Campo Observação inválido!()");
    }
    if ( !$obErro->ocorreu() ) {
        $obRPessoalAssentamento = new RPessoalAssentamento( new RPessoalVantagem );
        $obRPessoalClassificacaoAssentamento = new RPessoalClassificacaoAssentamento();

        $obRPessoalAssentamento->setCodAssentamento($_REQUEST['inCodAssentamento']);
        $obRPessoalAssentamento->listarAssentamento( $rsAssentamentos );
        $stDescricaoAssentamento = $rsAssentamentos->getCampo("descricao");

        $obRPessoalClassificacaoAssentamento->setCodClassificacaoAssentamento($_REQUEST['inCodClassificacao']);
        $obRPessoalClassificacaoAssentamento->listarClassificacao( $rsClassificacao );
        $stDescricaoClassificacao = $rsClassificacao->getCampo("descricao");

        $arAssentamentosGerados = ( is_array(Sessao::read('arAssentamentos')) ) ? Sessao::read('arAssentamentos') : array();
        foreach ($arAssentamentosGerados as $arAssentamento) {
            if ($arAssentamento['inCodClassificacao'] == $_REQUEST['inCodClassificacao']
            AND $arAssentamento['inCodAssentamento']  == $_REQUEST['inCodAssentamento']) {
                $boIgual = false;
                $arPeriodo1 = array($_REQUEST['stDataInicial'],$_REQUEST['stDataFinal']);
                $arPeriodo2 = array($arAssentamento['stDataInicial'],$arAssentamento['stDataFinal']);
                switch ($stModoGeracao) {
                    case "contrato":
                    case "cgm/contrato":
                        $stComplemento = "contrato(".$_REQUEST['inContrato'].")";
                        if ($arAssentamento['inRegistro'] == $_REQUEST['inContrato']) {
                            $boIgual = true;
                        }
                    break;
                    case "cargo":
                        if ($arAssentamento['stDescricaoEspecialidade'] != "") {
                            $stComplemento = "cargo/especialidade(".$arAssentamento['stDescricaoCargo']."/".$arAssentamento['stDescricaoEspecialidade'].")";
                        } else {
                            $stComplemento = "cargo(".$arAssentamento['stDescricaoCargo'].")";
                        }
                        if ($arAssentamento['inCodCargo']         == $_REQUEST['inCodCargo']
                        AND $arAssentamento['inCodEspecialidade'] == $_REQUEST['inCodEspecialidade']) {
                            $boIgual = true;
                        }
                    break;
                    case "lotacao":
                        $stComplemento = "lotação(".$_REQUEST['inCodLotacao'].")";
                        if ($arAssentamento['inCodLotacao'] == $_REQUEST['inCodLotacao']) {
                            $boIgual = true;
                        }
                    break;
                }
                $stMensagem = "@Este período(".$_REQUEST['stDataInicial']." até ".$_REQUEST['stDataFinal'].") já foi cadastrado para o $stComplemento, classifiação($stDescricaoClassificacao) e assentamento($stDescricaoAssentamento).";
                if ($stAcao == "incluir" and $boIgual) {
                    if ( verificarPeriodo($arPeriodo1,$arPeriodo2) ) {
                        $obErro->setDescricao($stMensagem);
                    }
                }
                if ($stAcao == "alterar" and $boIgual) {
                    if ( (int) $arAssentamento['inId'] !== (int) Sessao::read('inId') ) {
                        if ( verificarPeriodo($arPeriodo1,$arPeriodo2) ) {
                            $obErro->setDescricao($stMensagem);
                        }
                    }
                }
                break;
            }
        }
    }

    return $obErro;
}

function verificarPeriodo($arPeriodo1,$arPeriodo2)
{
    $boErro = false;
    list($dia,$mes,$ano) = explode("/",$arPeriodo1[0]);
    $stDataInicialP1 = $ano.$mes.$dia;
    list($dia,$mes,$ano) = explode("/",$arPeriodo1[1]);
    $stDataFinalP1 = $ano.$mes.$dia;
    list($dia,$mes,$ano) = explode("/",$arPeriodo2[0]);
    $stDataInicialP2 = $ano.$mes.$dia;
    list($dia,$mes,$ano) = explode("/",$arPeriodo2[1]);
    $stDataFinalP2 = $ano.$mes.$dia;

    if ($stDataInicialP1 >= $stDataInicialP2 and $stDataInicialP1 <= $stDataFinalP2) {
        $boErro = true;
    }
    if ($stDataFinalP1 >= $stDataInicialP2  and $stDataFinalP1 <= $stDataFinalP2) {
        $boErro = true;
    }
    if ($stDataInicialP1 < $stDataInicialP2 and $stDataFinalP1 >= $stDataInicialP2) {
        $boErro = true;
    }

    return $boErro;
}

function retornarArrayPost($stAcao,$stDescricaoClassificacao,$stDescricaoAssentamento)
{
    if ($stAcao == 'incluir') {
        $arTemp['inId'] = (is_array(Sessao::read('arAssentamentos'))) ? count(Sessao::read('arAssentamentos')) : 0;
    } else {
        $arTemp['inId'] = Sessao::read('inId');
    }
    $stModoGeracao = ( $_REQUEST['stModoGeracao'] ) ? $_REQUEST['stModoGeracao'] : $_REQUEST['hdnModoGeracao'];
    switch ($stModoGeracao) {
        case "contrato":
            $arTemp['inRegistro']         = $_REQUEST['inContrato'];
            $arTemp['stNomCgm']           = $_REQUEST['hdnCGM'];
        break;
        case "cgm/contrato":
            $arTemp['inNumCGM']           = $_REQUEST['inNumCGM'];
            $arTemp['inCampoInner']       = $_REQUEST['inCampoInner'];
            $arTemp['inRegistro']         = $_REQUEST['inContrato'];
        break;
        case "cargo":
            $obRPessoalCargo = new RPessoalCargo;
            $obRPessoalCargo->setCodCargo( $_REQUEST['inCodCargo'] );

            if ($_REQUEST['inCodEspecialidade'] != "") {
                $obRPessoalEspecialidade = new RPessoalEspecialidade( $obRPessoalCargo );
                $obRPessoalEspecialidade->setCodEspecialidade( $_REQUEST['inCodEspecialidade'] );
                $obRPessoalEspecialidade->consultaEspecialidadeCargo($rsCargoEspecialidade);
            } else {
                $obRPessoalCargo->listarCargo($rsCargoEspecialidade);
            }

            $arTemp['boCargoExercido']    = $_REQUEST['boCargoExercido'];
            $arTemp['boFuncaoExercida']   = $_REQUEST['boFuncaoExercida'];
            $arTemp['inCodCargo']         = $_REQUEST['inCodCargo'];
            $arTemp['stDescricaoCargo']   = $rsCargoEspecialidade->getCampo('descricao');
            $arTemp['inCodEspecialidade'] = $_REQUEST['inCodEspecialidade'];
            $arTemp['stDescricaoEspecialidade'] = $rsCargoEspecialidade->getCampo('descricao_especialidade');
            if ( $rsCargoEspecialidade->getCampo('descricao_especialidade') != "" ) {
                $arTemp['stDescricaoCargoEspecialidade']   = $rsCargoEspecialidade->getCampo('descricao')."/".$rsCargoEspecialidade->getCampo('descricao_especialidade');
            } else {
                $arTemp['stDescricaoCargoEspecialidade']   = $rsCargoEspecialidade->getCampo('descricao');
            }
        break;
        case "lotacao":
            $arTemp['inCodLotacao']       = $_REQUEST['inCodLotacao'];
        break;
    }
    $arTemp['inCodClassificacao']       = $_REQUEST['inCodClassificacao'];
    $arTemp['stClassificacao']          = TRIM($stDescricaoClassificacao);
    $arTemp['inCodAssentamento']        = $_REQUEST['inCodAssentamento'];
    $arTemp['stAssentamento']           = TRIM($stDescricaoAssentamento);
    $arTemp['inQuantidadeDias']         = $_REQUEST['inQuantidadeDias'];
    $arTemp['stDataInicial']            = $_REQUEST['stDataInicial'];
    $arTemp['stDataFinal']              = $_REQUEST['stDataFinal'];
    $arTemp["dtInicial"]                = $_REQUEST["dtInicial"];
    $arTemp["dtFinal"]                  = $_REQUEST["dtFinal"];
    $arTemp['stObservacao']             = TRIM($_REQUEST['stObservacao']);
    $arTemp['inCodNorma']               = $_REQUEST['inCodNorma'];
    $arTemp['inCodTipoNorma']           = $_REQUEST['inCodTipoNorma'];
    $arTemp['hdnDataAlteracaoFuncao']   = $_REQUEST['hdnDataAlteracaoFuncao'];
    $arTemp['inCodProgressao']          = $_REQUEST['inCodProgressao'];
    $arTemp['inCodRegime']              = $_REQUEST['inCodRegime'];
    $arTemp['stRegime']                 = $_REQUEST['stRegime'];
    $arTemp['inCodSubDivisao']          = $_REQUEST['inCodSubDivisao'];
    $arTemp['stSubDivisao']             = $_REQUEST['stSubDivisao'];
    $arTemp['stCargo']                  = $_REQUEST['stCargo'];
    $arTemp['inCodEspecialidadeCargo']  = $_REQUEST['inCodEspecialidadeCargo'];
    $arTemp['stEspecialidadeCargo']     = $_REQUEST['stEspecialidadeCargo'];
    $arTemp['inCodRegimeFuncao']        = $_REQUEST['inCodRegimeFuncao'];
    $arTemp['stRegimeFuncao']           = $_REQUEST['stRegimeFuncao'];
    $arTemp['inCodSubDivisaoFuncao']    = $_REQUEST['inCodSubDivisaoFuncao'];
    $arTemp['stSubDivisaoFuncao']       = $_REQUEST['stSubDivisaoFuncao'];
    $arTemp['inCodFuncao']              = $_REQUEST['inCodFuncao'];
    $arTemp['stFuncao']                 = $_REQUEST['stFuncao'];
    $arTemp['inCodEspecialidadeFuncao'] = $_REQUEST['inCodEspecialidadeFuncao'];
    $arTemp['stEspecialidadeFuncao']    = $_REQUEST['stEspecialidadeFuncao'];
    $arTemp['dtDataAlteracaoFuncao']    = $_REQUEST['dtDataAlteracaoFuncao'];
    $arTemp['stHorasMensais']           = $_REQUEST['stHorasMensais'];
    $arTemp['stHorasSemanais']          = $_REQUEST['stHorasSemanais'];
    $arTemp['inCodPadrao']              = $_REQUEST['inCodPadrao'];
    $arTemp['stPadrao']                 = $_REQUEST['stPadrao'];
    $arTemp['inSalario']                = $_REQUEST['inSalario'];
    $arTemp['dtVigenciaSalario']        = $_REQUEST['dtVigenciaSalario'];

    return $arTemp;
}

function incluirAssentamento($boExecuta=false)
{
    $obErro = new erro;
    $inId = Sessao::read('inId');
    if ( !$obErro->ocorreu() and isset($inId) ) {
        $obErro->setDescricao("Há um assentamento gerado em processo de alteração.");
    }
    if ( !$obErro->ocorreu() ) {
        $obErro = validarAssentamento("incluir",$stDescricaoClassificacao,$stDescricaoAssentamento);
    }
    if ( !$obErro->ocorreu() ) {
        Sessao::write('stModoGeracao', $_REQUEST['stModoGeracao']);
        $arAssentamentos = Sessao::read('arAssentamentos');
        $arAssentamentos[] = retornarArrayPost('incluir',$stDescricaoClassificacao,$stDescricaoAssentamento);
        $arAssentamentos[count($arAssentamentos)-1]['arNormas'] = Sessao::read('arNormas');
        Sessao::remove('arNormas');
        Sessao::write('arAssentamentos', $arAssentamentos);
        $stJs .= "f.stModoGeracao.disabled = true;  \n";
        $stJs .= montaListaNorma();
        $stJs .= montarListaAssentamento();
        $stJs .= limparAssentamento();
    } else {
        $stJs .= "alertaAviso('@".$obErro->getDescricao()."','form','erro','".Sessao::getId()."'); \n";
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function alterarAssentamento($boExecuta=false)
{
    $obErro = new erro;
    $inId = Sessao::read('inId');
    if ( !$obErro->ocorreu() and !isset($inId) ) {
        $obErro->setDescricao("Não há nenhum assentamento gerado em processo de alteração.");
    }
    if ( !$obErro->ocorreu() ) {
        $obErro = validarAssentamento("alterar",$stDescricaoClassificacao,$stDescricaoAssentamento);
    }
    if ( !$obErro->ocorreu() ) {
        $arAssentamentos = Sessao::read('arAssentamentos');
        $arAssentamentos[$inId] = retornarArrayPost('alterar',$stDescricaoClassificacao,$stDescricaoAssentamento);
        $arAssentamentos[$inId]['arNormas'] = Sessao::read('arNormas');
        Sessao::remove('arNormas');
        Sessao::write('arAssentamentos', $arAssentamentos);
        Sessao::remove("inCodNormas");
        $stJs .= montaListaNorma();
        $stJs .= montarListaAssentamento();
        $stJs .= limparAssentamento();
    } else {
        $stJs .= "alertaAviso('@".$obErro->getDescricao()."','form','erro','".Sessao::getId()."'); \n";
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function limparAssentamento($boExecuta=false)
{
    $stModoGeracao = ( $_REQUEST['stModoGeracao'] ) ? $_REQUEST['stModoGeracao'] : $_REQUEST['hdnModoGeracao'];
    switch ($stModoGeracao) {
        case "contrato":
            $stJs .= "f.inContrato.value = '';                                              \n";
            $stJs .= "d.getElementById('inNomCGM').innerHTML = '&nbsp;';                    \n";
        break;
        case "cgm/contrato":
            $stJs .= "f.inNumCGM.value = '';                                                \n";
            $stJs .= "f.inCampoInner.value = '';                                            \n";
            $stJs .= "d.getElementById('inCampoInner').innerHTML = '&nbsp;';                \n";
            $stJs .= "limpaSelect(f.inContrato,0);                                          \n";
            $stJs .= "f.inContrato.options[0] = new Option('Selecione','', 'selected');     \n";
        break;
        case "cargo":
            $stJs .= "f.boCargoExercido.ckecked = false;                                    \n";
            $stJs .= "f.boFuncaoExercida.checked = false;                                   \n";
            $stJs .= "f.inCodCargoTxt.value = '';                                           \n";
            $stJs .= "f.inCodCargo.value = '';                                              \n";
            $stJs .= "f.inCodExpecialidadeTxt.value = '';                                   \n";
            $stJs .= "limpaSelect(f.inCodEspecialidade,0);                                  \n";
            $stJs .= "f.inCodEspecialidade.options[0] = new Option('Selecione','', 'selected');     \n";
        break;
        case "lotacao":
            $stJs .= "f.inCodLotacao.value = '';                                            \n";
            $stJs .= "d.getElementById('stLotacao').innerHTML = '&nbsp;';                   \n";
        break;
    }
    $stJs .= "f.inCodContrato.value         = '';                                           \n";
    $stJs .= "f.inCodMatricula.value        = '';                                           \n";
    $stJs .= "f.inCodClassificacao.value    = '';                                           \n";
    $stJs .= "f.inCodClassificacaoTxt.value = '';                                           \n";
    $stJs .= "limpaSelect(f.inCodAssentamento,0);                                           \n";
    $stJs .= "f.inCodAssentamento.options[0] = new Option('Selecione','', 'selected');      \n";
    $stJs .= "f.inCodAssentamentoTxt.value  = '';                                           \n";
    $stJs .= "f.inQuantidadeDias.value      = '';                                           \n";
    $stJs .= "f.stDataInicial.value         = '';                        \n";
    $stJs .= "f.stDataFinal.value           = '';                                           \n";
    $stJs .= "d.getElementById('spnLicencaPremio').innerHTML = '';                          \n";
    $stJs .= "f.stObservacao.value          = '';                                           \n";
    $stJs .= "f.stCodNorma.value           = '';                                            \n";
    $stJs .= "d.getElementById('stNorma').innerHTML = '&nbsp;';                             \n";
    $stJs .= "d.getElementById('spnCargoFuncaoSalario').innerHTML = '';                     \n";
    Sessao::remove('arNormas');
    $stJs .= montaListaNorma();
    Sessao::remove('inId');
    Sessao::remove('inCodClassificacao');
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function excluirAssentamento($boExecuta=false)
{
    $arTemp = array();
    $arAssentamentos = Sessao::read('arAssentamentos');
    foreach ($arAssentamentos as $arAssentamento) {
        if ($arAssentamento['inId'] != $_REQUEST['inId']) {
            $arAssentamento['inId'] = count($arTemp);
            $arTemp[] = $arAssentamento;
        }
    }
    if ( count($arTemp) == 0 ) {
        $stJs .= "f.stModoGeracao.disabled = false;";
    }
    $arAssentamentos = $arTemp;
    Sessao::write('arAssentamentos', $arAssentamentos);
    $stJs .= montarListaAssentamento();
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function montaAlterarAssentamento($boExecuta=false)
{
    $arAssentamentos = Sessao::read('arAssentamentos');
    $arAssentamento  = $arAssentamentos[$_GET['inId']];
    $stModoGeracao = ( $_REQUEST['stModoGeracao'] ) ? $_REQUEST['stModoGeracao'] : $_REQUEST['hdnModoGeracao'];

    switch ($stModoGeracao) {
        case "contrato":
            $stJs .= "f.inContrato.value                     = '".$arAssentamento['inRegistro']."';         \n";
            $stJs .= "d.getElementById('inNomCGM').innerHTML = '".$arAssentamento['stNomCgm']  ."';         \n";
            $stJs .= "f.hdnCGM.value                         = '".$arAssentamento['stNomCgm']  ."';         \n";
        break;
        case "cgm/contrato":
            $stJs .= "f.inNumCGM.value                       = '".$arAssentamento['inNumCGM']  ."';         \n";
            $stJs .= "f.inCampoInner.value                   = '".$arAssentamento['inCampoInner']."';       \n";
            $stJs .= "d.getElementById('inCampoInner').innerHTML = '".$arAssentamento['inCampoInner']."';   \n";
            $stJs .= "limpaSelect(f.inContrato,0);                                                          \n";
            $stJs .= "f.inContrato.options[0] = new Option('Selecione','', 'selected');                     \n";
            $obRPessoalServidor = new RPessoalServidor;
            $obRPessoalServidor->obRCGMPessoaFisica->setNumCGM($arAssentamento['inNumCGM']);
            $obRPessoalServidor->consultaRegistrosServidor($rsRegistros);
            $inIndex = 1;
            while ( !$rsRegistros->eof() ) {
                $stJs .= "f.inContrato[".$inIndex."] = new Option('".$rsRegistros->getCampo('registro')."','".$rsRegistros->getCampo('registro')."','');\n";
                $inIndex++;
                $rsRegistros->proximo();
            }
            $stJs .= "f.inContrato.value                     = '".$arAssentamento['inRegistro']."';         \n";
        break;
        case "cargo":
            $stJs .= "f.boCargoExercido.ckecked         = ".$arAssentamento['boCargoExercido'].";           \n";
            if ($arAssentamento['boFuncaoExercida'] == "") {
                $stJs .= "f.boFuncaoExercida.checked        = false;                                        \n";
            } else {
                $stJs .= "f.boFuncaoExercida.checked        = ".$arAssentamento['boFuncaoExercida'].";          \n";
            }
            $stJs .= "f.inCodCargoTxt.value             = '".$arAssentamento['inCodCargo']."';              \n";
            $stJs .= "f.inCodCargo.value                = '".$arAssentamento['inCodCargo']."';              \n";
            $stJs .= "f.inCodExpecialidadeTxt.value     = '".$arAssentamento['inCodEspecialidade']."';      \n";
            $stJs .= "limpaSelect(f.inCodEspecialidade,0);                                                  \n";
            $obRPessoalCargo = new RPessoalCargo;
            $obRPessoalCargo->setCodCargo( $_REQUEST['inCodCargo'] );
            $obRPessoalEspecialidade = new RPessoalEspecialidade( $obRPessoalCargo );
            $obRPessoalEspecialidade->consultaEspecialidadeCargo($rsEspecialidades);
            $stJs .= "f.inCodEspecialidade.options[0] = new Option('Selecione','', 'selected');             \n";
            $inIndex = 1;
            while (!$rsEspecialidades->eof()) {
                $stJs .= "f.inCodEspecialidade.options[$inIndex] = new Option('".$rsEspecialidades->getCampo('descricao_especialidade')."','".$rsEspecialidades->getCampo('cod_especialidade')."', '');     \n";
                $inIndex++;
                $rsEspecialidades->proximo();
            }
            $stJs .= "f.inCodEspecialidade.value     = '".$arAssentamento['inCodEspecialidade']."';         \n";
        break;
        case "lotacao":
            $obROrganogramaOrgao = new ROrganogramaOrgao;
            $obROrganogramaOrgao->setCodOrgaoEstruturado( $arAssentamento['inCodLotacao'] );
            $obROrganogramaOrgao->listarOrgaoReduzido( $rsOrgaoReduzido );
            $stJs .= "f.inCodLotacao.value = '".$arAssentamento['inCodLotacao']."';                         \n";
            $stJs .= "d.getElementById('stLotacao').innerHTML = '".$rsOrgaoReduzido->getCampo('descricao')."';\n";
        break;
    }
    Sessao::write('inCodClassificacao', $arAssentamento['inCodClassificacao']);
    Sessao::write('inId', $_GET['inId']);
    $stJs .= "f.inCodClassificacaoTxt.value = ".$arAssentamento['inCodClassificacao'].";\n";
    $stJs .= "f.inCodClassificacao.value    = ".$arAssentamento['inCodClassificacao'].";\n";
    $stJs .= preencherAssentamento();
    $stJs .= "f.inCodAssentamentoTxt.value  = ".$arAssentamento['inCodAssentamento'] .";\n";
    $stJs .= "f.inCodAssentamento.value     = ".$arAssentamento['inCodAssentamento'] .";\n";
    $stJs .= "f.inQuantidadeDias.value      = '".$arAssentamento['inQuantidadeDias']  ."';\n";
    $stJs .= "f.stDataInicial.value         = '".$arAssentamento['stDataInicial']   ."';\n";
    $stJs .= "f.stDataFinal.value           = '".$arAssentamento['stDataFinal']     ."';\n";
    Sessao::write('arNormas', $arAssentamento['arNormas']);
    $stJs .= montaListaNorma();
//    $stJs .= "f.inCodTipoNorma.value           = '".$arAssentamento['inCodTipoNorma']."';\n";
//    $stJs .= "f.inCodTipoNormaTxt.value           = '".$arAssentamento['inCodTipoNorma']."';\n";
//    Sessao::write("inCodTipoNorma", $arAssentamento['inCodTipoNorma']);
//    $stJs .= MontaNorma();
//    $stJs .= "f.inCodNorma.value           = '".$arAssentamento['inCodNorma']."';\n";
//    $stJs .= "f.inCodNormaTxt.value           = '".$arAssentamento['inCodNorma']."';\n";
    $_REQUEST["inCodClassificacao"] = $arAssentamento['inCodClassificacao'];
    $_REQUEST["inCodClassificacaoTxt"] = $arAssentamento['inCodClassificacao'];
    $_REQUEST["inCodAssentamento"]  = $arAssentamento['inCodAssentamento'];
    $_REQUEST["dtInicial"]          = $arAssentamento["dtInicial"];
    $_REQUEST["dtFinal"]            = $arAssentamento["dtFinal"];
    $stJs .= gerarSpanLicencaPremio();
    $stJs .= buscaContrato($arAssentamento['inRegistro']);
    $stJs .= "f.stObservacao.value          = '".$arAssentamento['stObservacao']    ."';\n";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function montarListaAssentamento($boExecuta=false)
{
    $rsAssentamentosGerados = new recordset;
    $arAssentamentosGerados = ( is_array(Sessao::read('arAssentamentos')) ) ? Sessao::read('arAssentamentos') : array();
    $rsAssentamentosGerados->preenche($arAssentamentosGerados);
    $obLista = new Lista;
    $obLista->setRecordSet  ( $rsAssentamentosGerados );
    $obLista->setTitulo     ("Assentamentos Gerados");
    $obLista->setMostraPaginacao(false);

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 2 );
    $obLista->commitCabecalho();

    $stModoGeracao = ( $_REQUEST['stModoGeracao'] ) ? $_REQUEST['stModoGeracao'] : $_REQUEST['hdnModoGeracao'];

    switch ($stModoGeracao) {
        case "contrato":
        case "cgm/contrato":
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Matrícula");
            $obLista->ultimoCabecalho->setWidth( 20 );
            $obLista->commitCabecalho();
        break;
        case "cargo":
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Cargo/Especialidade");
            $obLista->ultimoCabecalho->setWidth( 20 );
            $obLista->commitCabecalho();
        break;
        case "lotacao":
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Lotação");
            $obLista->ultimoCabecalho->setWidth( 20 );
            $obLista->commitCabecalho();
        break;
    }

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Classificação");
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Assentamento");
    $obLista->ultimoCabecalho->setWidth( 24 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Período");
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Ação");
    $obLista->ultimoCabecalho->setWidth( 2 );
    $obLista->commitCabecalho();

    switch ($stModoGeracao) {
        case "contrato":
        case "cgm/contrato":
            $obLista->addDado();
            $obLista->ultimoDado->setAlinhamento("CENTRO");
            $obLista->ultimoDado->setCampo( "inRegistro" );
            $obLista->commitDado();
        break;
        case "cargo":
            $obLista->addDado();
            $obLista->ultimoDado->setAlinhamento("CENTRO");
            $obLista->ultimoDado->setCampo( "stDescricaoCargoEspecialidade" );
            $obLista->commitDado();
        break;
        case "lotacao":
            $obLista->addDado();
            $obLista->ultimoDado->setAlinhamento("CENTRO");
            $obLista->ultimoDado->setCampo( "inCodLotacao" );
            $obLista->commitDado();
        break;
    }

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "stClassificacao" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "stAssentamento" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "[stDataInicial] à [stDataFinal]" );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "alterar" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "javascript:modificaDado('montaAlterarAssentamento');" );
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "javascript:modificaDado('excluirAssentamento');" );
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $stJs = "d.getElementById('spnSpan2').innerHTML = '".$stHtml."';";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function processarForm($boExecuta = false, $stArquivo = "Form", $stAcao = "incluir")
{
    switch ($stAcao) {
        case "incluir":
            $stJs  = gerarSpan1($boExecuta,$stArquivo);
        break;

        case "alterar":

            $stJs  = preencherAssentamento($boExecuta);
            $stJs .= processarTriadi(3);
            $stJs .= buscaNormas();
            $stJs .= montaListaNorma();
            $stJs .= buscaContrato($_REQUEST['inRegistro'], false);
            
            $_REQUEST['inRegistro'] = $_REQUEST['inCodContrato'];
            $arDados = carregaDados();

            $inCodMotivo = SistemaLegado::pegaDado("cod_motivo"
                                            ,"pessoal.assentamento_assentamento"
                                            ,"WHERE cod_assentamento = ".Sessao::read('inCodAssentamento')." AND cod_classificacao = ".Sessao::read('inCodClassificacao')."");
            
            if ( ($inCodMotivo == 18) || ($inCodMotivo == 14) )
                $stJs .= gerarSpanCargoFuncaoSalario($arDados);

            $stJs .= preencheSubDivisaoAlterar();
            $stJs .= preencheCargoAlterar();
            $stJs .= preencheEspecialidadeAlterar();
            $stJs .= preencheSubDivisaoFuncaoAlterar();
            $stJs .= preencheFuncaoAlterar();
            $stJs .= preencheEspecialidadeFuncaoAlterar();
            
            if ( ($inCodMotivo == 18) || ($inCodMotivo == 14) )
                $stJs .= preencheInformacoesSalariais($arDados['inCodFuncao'], $arDados['inCodEspecialidadeFuncao']);
            
            $stJs .= preencheProgressaoAlterar();
        break;

        case "excluir":
        case "consultar":
            $stJs  = buscaNormas();
            $stJs .= montaListaNorma();
            $stJs .= processarTriadi(3,true);
        break;
    }

    if ($boExecuta) {
        sistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function submeter($boExecuta=false)
{
    if (is_array(Sessao::read('arAssentamentos'))) {
        $stJs .= "parent.frames[2].Salvar();    \n";
    } else {
        $stMensagem = "Lista de assentamento inválida!(Informe os assentamentos a serem gerados.)";
        $stJs .= "alertaAviso('@$stMensagem','form','erro','".Sessao::getId()."'); \n";
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function processarTriadi($inCampo,$boSpan=false)
{
    $rsContrato = new RecordSet();
    $stDataInicial = ( $_REQUEST['stDataInicial'] != "" ) ? $_REQUEST['stDataInicial'] : Sessao::read('stDataInicial');
    $stDataFinal   = ( $_REQUEST['stDataFinal']   != "" ) ? $_REQUEST['stDataFinal']   : Sessao::read('stDataFinal');
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
    $obTPessoalContrato = new TPessoalContrato();

    if ($_REQUEST["inContrato"]) {
        $inMatricula = $_REQUEST["inContrato"];
    } else {
        $inMatricula = $_REQUEST["inRegistro"];
    }

    $boErroGeracao=='false';
    if (($inMatricula=='' && $_REQUEST['stModoGeracao'] =='contrato') || ($inMatricula=='' && $_REQUEST['stModoGeracao'] =='cgm/contrato')) {
        $boErroGeracao ='true';
        $stMensagem    ="@Informe primeiro a Matrícula.";
    } elseif ($_REQUEST['inCodCargo']=='' && $_REQUEST['stModoGeracao'] =='cargo') {
        $boErroGeracao ='true';
        $stMensagem    ="@Informe primeiro o Cargo.";
    } elseif ($_REQUEST['inCodLotacao']=='' && $_REQUEST['stModoGeracao'] =='lotacao') {
        $boErroGeracao ='true';
        $stMensagem    ="@Informe primeiro a Lotação.";
    }

    if ($boErroGeracao=='true') {
        $stJs .= "f.btIncluir.disabled = true; \n";
        $stJs .= "f.stDataInicial.value = '';\n";
        $stJs .= "f.stDataFinal.value = '';\n";
        $stJs .= "f.inQuantidadeDias.value = '';\n";
        $stJs .= "alertaAviso('@$stMensagem','form','erro','".Sessao::getId()."'); \n";
    } else {
        switch ($_REQUEST['stModoGeracao']) {
            case 'contrato':
            case 'cgm/contrato':
                $stFiltro = " WHERE registro = ".$inMatricula;
                $obTPessoalContrato->recuperaTodos($rsContrato,$stFiltro);
                break;
            case 'cargo':
                $stFiltro = " WHERE cod_cargo = ".$_REQUEST['inCodCargo'];
                if ($_REQUEST['boFuncaoExercida']) {
                    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorFuncao.class.php" );
                    $obTPessoalContratoServidorFuncao = new TPessoalContratoServidorFuncao;
                    $obTPessoalContratoServidorFuncao->recuperaTodos( $rsContrato, $stFiltro );
                } else {
                    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidor.class.php" );
                    $obTPessoalContratoServidor = new TPessoalContratoServidor;
                    $obTPessoalContratoServidor->recuperaTodos( $rsContrato, $stFiltro );
                }
                break;
            case 'lotacao':
                include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidor.class.php" );
                $stFiltro = " WHERE pcso.cod_orgao = ".$_REQUEST['HdninCodLotacao'];
                $obTPessoalContratoServidor = new TPessoalContratoServidor;
                $obTPessoalContratoServidor->recuperaContratosLotacao( $rsContrato, $stFiltro );
                break;
        }

        if ($_REQUEST['inCodClassificacao'] != "") {
            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalClassificacaoAssentamento.class.php");
            $obTPessoalClassificacaoAssentamento = new TPessoalClassificacaoAssentamento();
            $stFiltro  = " AND ca.cod_classificacao = ".$_REQUEST["inCodClassificacao"];
            $obTPessoalClassificacaoAssentamento->recuperaRelacionamento($rsClassificacao,$stFiltro);
            $stTipoClassificacao = $rsClassificacao->getCampo('cod_tipo');
        } else {
            $stTipoClassificacao = '0';
        }

        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorCasoCausa.class.php");
        $rsContratoServidorCasoCausa = new RecordSet;
        $obTPessoalContratoServidorCasoCausa = new TPessoalContratoServidorCasoCausa();
        foreach ($rsContrato->getElementos() as $contrato) {
            $stFiltro = " AND contrato.cod_contrato = ".$contrato["cod_contrato"];
            if ($stDataInicial && !$stDataFinal) {
                $stFiltro .= " AND (dt_rescisao < to_date('".$stDataInicial."','dd/mm/yyyy'))";
            } elseif (!$stDataInicial && $stDataFinal) {
                $stFiltro .= " AND (dt_rescisao < to_date('".$stDataFinal."','dd/mm/yyyy'))";
            } elseif ($stDataInicial && $stDataFinal) {
                $stFiltro .= " AND (dt_rescisao < to_date('".$stDataInicial."','dd/mm/yyyy') OR dt_rescisao < to_date('".$stDataFinal."','dd/mm/yyyy'))";
            } else {
                $stFiltro = '';
            }

            if ($stFiltro) {
                $obTPessoalContratoServidorCasoCausa->recuperaCasoCausaRegistro($rsContratoServidorCasoCausa,$stFiltro);
            }

            if ($rsContratoServidorCasoCausa->getNumLinhas() > 0 && $stTipoClassificacao == '1') {
                $stMensagem = "@Data do afastamento não deve ser posterior a ".SistemaLegado::dataToBr($rsContratoServidorCasoCausa->getCampo("dt_rescisao"))." para o contrato ".$rsContratoServidorCasoCausa->getCampo("registro");
                break;
            }
        }

        if ($rsContratoServidorCasoCausa->getNumLinhas() < 0 || $stTipoClassificacao=='1') {
            if (Sessao::read("inQuantDiasAfastamentoTemporario") != "" and $_REQUEST['inQuantidadeDias'] > Sessao::read("inQuantDiasAfastamentoTemporario")) {
                $_REQUEST['inQuantidadeDias'] = Sessao::read("inQuantDiasAfastamentoTemporario");
                $stJs .= "f.inQuantidadeDias.value = '".Sessao::read("inQuantDiasAfastamentoTemporario")."';\n";
            }
            $inQuantDias   = $_REQUEST['inQuantidadeDias'];
            switch ($inCampo) {
                case 1:
                    switch (true) {
                        case $inQuantDias == "":
                            $stJs .= "f.stDataFinal.value = '';\n";
                            break;
                        case $inQuantDias != "" and $stDataInicial != "":
                            $stJs .= calcularDataFinal();
                            break;
                    }
                    break;
                case 2:
                    switch (true) {
                        case $inQuantDias != "" and $stDataFinal == "":
                            $stJs .= calcularDataFinal();
                            break;
                        case $inQuantDias == "" and $stDataFinal != "" OR
                            $inQuantDias != "" and $stDataFinal != "":
                            $stJs .=  ajustarQuantidadeDias(false,$boSpan);
                            break;
                    }
                    break;
                case 3:
                    switch (true) {
                        case $stDataFinal == "":
                            $stJs .= "f.inQuantidadeDias.value = '';\n";
                            break;
                        case $inQuantDias != "" and $stDataInicial != "":
                            $stJs .= ajustarQuantidadeDias(false,$boSpan);
                            break;
                        case $stDataFinal != "" and $stDataInicial != "":
                            $stJs .= ajustarQuantidadeDias(false,$boSpan);
                            break;
                    }
                    break;
            }
        } else {
            $stJs .= "f.stDataInicial.value = '';\n";
            $stJs .= "f.stDataFinal.value = '';\n";
            $stJs .= "f.inQuantidadeDias.value = '';\n";
            $stJs .= "alertaAviso('@Data do afastamento não deve ser posterior a ".SistemaLegado::dataToBr($rsContratoServidorCasoCausa->getCampo("dt_rescisao"))."','form','erro','".Sessao::getId()."'); \n";
        }
    }

    return $stJs;
}

function processarQuantDiasAssentamento()
{
    $inDias = "";
    if ($_REQUEST["inCodClassificacao"] != "") {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalClassificacaoAssentamento.class.php");
        $obTPessoalClassificacaoAssentamento = new TPessoalClassificacaoAssentamento();
        $stFiltro  = " AND ca.cod_classificacao = ".$_REQUEST["inCodClassificacao"];
        $stFiltro .= " AND ca.cod_tipo = 2";
        $obTPessoalClassificacaoAssentamento->recuperaRelacionamento($rsClassificacao,$stFiltro);
    }
    if (($_REQUEST["inCodAssentamento"] != "") && ($rsClassificacao->getNumLinhas() == 1) ) {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamento.class.php");
        $obTPessoalAssentamento = new TPessoalAssentamento();
        $stFiltro  = " AND A.cod_assentamento = ".$_REQUEST["inCodAssentamento"];
        $obTPessoalAssentamento->recuperaAssentamentos($rsAssentamento,$stFiltro);
        if ($rsAssentamento->getNumLinhas() == 1) {
            $inDias = $rsAssentamento->getCampo("dia");
        }
    }
    
    if ( ($_REQUEST["inCodAssentamento"] != "") && ($_REQUEST["inCodClassificacao"] != "") ) {
        $inCodMotivo = SistemaLegado::pegaDado("cod_motivo","pessoal.assentamento_assentamento","WHERE cod_assentamento = ".$_REQUEST['inCodAssentamento']." AND cod_classificacao = ".$_REQUEST["inCodClassificacao"]."");
        //Verifica se o cod_motivo é '18 - Readaptação' ou '14 - Alteração de Cargo'
        if ( ($inCodMotivo == 18) || ($inCodMotivo == 14) ){            
            $_REQUEST['inRegistro'] = $_REQUEST['inCodContrato'];
            $arDados = carregaDados();            
            $stJs .= gerarSpanCargoFuncaoSalario($arDados);
        }
    }else{
        $stJs .= "d.getElementById('spnCargoFuncaoSalario').innerHTML = '';\n";
    }

    Sessao::write("inQuantDiasAfastamentoTemporario", $inDias);
    $stJs .= "f.inQuantidadeDias.value = '$inDias';\n";
    $stJs .= gerarSpanLicencaPremio();

    return $stJs;
}

function gerarSpanCargoFuncaoSalario($arDados = "")
{

    $obRFolhaPagamentoPadrao = new RFolhaPagamentoPadrao();
    $obRPessoalRegime = new RPessoalRegime();
    $obRPessoalServidor = new RPessoalServidor();
    $obRPessoalServidor->addContratoServidor();

    $stHtml = "";
    $stEval = "";
    
    //-------------------------------------------
    //---------------CARGO-----------------------
    //-------------------------------------------
    //INFORMAÇÕES DO CARGO
    //Selecão da regime
    $obTxtCodRegime = new TextBox;
    $obTxtCodRegime->setRotulo                  ( "Regime"                              );
    $obTxtCodRegime->setName                    ( "inCodRegime"                         );
    $obTxtCodRegime->setTitle                   ( "Informe o regime de trabalho."       );
    $obTxtCodRegime->setSize                    ( 10                                    );
    $obTxtCodRegime->setMaxLength               ( 8                                     );
    $obTxtCodRegime->setInteiro                 ( true                                  );
    $obTxtCodRegime->setNull                    ( true                                  );
    $obTxtCodRegime->obEvento->setOnChange      ( "buscaValor('preencheSubDivisao');preencheCampo( this, document.frm.inCodRegimeFuncao);preencheCampo( this, document.frm.stRegimeFuncao);"    );
    
    $obRPessoalRegime->listarRegime( $rsRegime, "", $boTransacao );

    $obCmbCodRegime = new Select;
    $obCmbCodRegime->setName                    ( "stRegime"                            );
    $obCmbCodRegime->setRotulo                  ( "Regime"                              );
    $obCmbCodRegime->setTitle                   ( "Selecione o regime."                 );
    $obCmbCodRegime->setNull                    ( false                                 );
    $obCmbCodRegime->setCampoId                 ( "[cod_regime]"                        );
    $obCmbCodRegime->setCampoDesc               ( "descricao"                           );
    $obCmbCodRegime->addOption                  ( "", "Selecione"                       );
    $obCmbCodRegime->preencheCombo              ( $rsRegime                             );
    $obCmbCodRegime->obEvento->setOnChange      ( "buscaValor('preencheSubDivisao');preencheCampo( this, document.frm.inCodRegimeFuncao);preencheCampo( this, document.frm.stRegimeFuncao);"    );
    
    //Selecão da Sub-divisao
    $obTxtCodSubDivisao = new TextBox;
    $obTxtCodSubDivisao->setRotulo              ( "Subdivisão"                          );
    $obTxtCodSubDivisao->setName                ( "inCodSubDivisao"                     );
    $obTxtCodSubDivisao->setTitle               ( "Selecione a subdivisão do regime."   );
    $obTxtCodSubDivisao->setSize                ( 10                                    );
    $obTxtCodSubDivisao->setMaxLength           ( 8                                     );
    $obTxtCodSubDivisao->setInteiro             ( true                                  );
    $obTxtCodSubDivisao->setNull                ( true                                  );
    $obTxtCodSubDivisao->obEvento->setOnChange ( "buscaValor('preencheCargo');preencheCampo( this, document.frm.inCodSubDivisaoFuncao);preencheCampo( this, document.frm.stSubDivisaoFuncao);" );
    
    $obCmbCodSubDivisao = new Select;
    $obCmbCodSubDivisao->setName                ( "stSubDivisao"                        );
    $obCmbCodSubDivisao->setRotulo              ( "Subdivisão"                          );
    $obCmbCodSubDivisao->setTitle               ( "Selecione a subdivisão."             );
    $obCmbCodSubDivisao->setNull                ( false                                 );
    $obCmbCodSubDivisao->setCampoId             ( "[cod_sub_divisao]"                   );
    $obCmbCodSubDivisao->setCampoDesc           ( "descricao"                           );
    $obCmbCodSubDivisao->addOption              ( "", "Selecione"                       );
    $obCmbCodSubDivisao->obEvento->setOnChange ( "buscaValor('preencheCargo');preencheCampo( this, document.frm.inCodSubDivisaoFuncao);preencheCampo( this, document.frm.stSubDivisaoFuncao);"      );
    
    $obTxtCargo = new TextBox;
    $obTxtCargo->setRotulo                      ( "Cargo"                               );
    $obTxtCargo->setName                        ( "inCodCargo"                          );
    $obTxtCargo->setTitle                       ( "Selecione o cargo do servidor."      );
    $obTxtCargo->setSize                        ( 10                                    );
    $obTxtCargo->setMaxLength                   ( 10                                    );
    $obTxtCargo->setInteiro                     ( true                                  );
    $obTxtCargo->setNull                        ( true                                  );
    $obTxtCargo->obEvento->setOnChange          ( "buscaValor('preencheEspecialidade');preencheCampo( this, document.frm.inCodFuncao);preencheCampo( this, document.frm.stFuncao);" );
    
    $obCmbCargo = new Select;
    $obCmbCargo->setName                        ( "stCargo"                             );
    $obCmbCargo->setRotulo                      ( "Cargo"                               );
    $obCmbCargo->setTitle                       ( "Selecione o cargo do servidor."      );
    $obCmbCargo->setNull                        ( false                                 );
    $obCmbCargo->addOption                      ( "", "Selecione"                       );
    $obCmbCargo->setCampoId                     ( "[cod_cargo]"                         );
    $obCmbCargo->setCampoDesc                   ( "descricao"                           );
    $obCmbCargo->obEvento->setOnChange          ( "buscaValor('preencheEspecialidade');
                                                   preencheCampo( this, document.frm.inCodFuncao);
                                                   preencheCampo( this, document.frm.stFuncao);" );
   
    //Selecão da Especialidade Cargo
    $obTxtCodEspecialidadeCargo = new TextBox;
    $obTxtCodEspecialidadeCargo->setRotulo      ( "Especialidade"                          );
    $obTxtCodEspecialidadeCargo->setName        ( "inCodEspecialidadeCargo"                );
    $obTxtCodEspecialidadeCargo->setTitle       ( "Selecione a especialidade do servidor." );
    $obTxtCodEspecialidadeCargo->setSize        ( 10                                       );
    $obTxtCodEspecialidadeCargo->setMaxLength   ( 10                                       );
    $obTxtCodEspecialidadeCargo->setInteiro     ( true                                     );
    $obTxtCodEspecialidadeCargo->setNull        ( true                                     );
    $obTxtCodEspecialidadeCargo->obEvento->setOnChange( "buscaValor('preenchePreEspecialidadeFuncao');" );
    
    $obCmbCodEspecialidadeCargo = new Select;
    $obCmbCodEspecialidadeCargo->setName        ( "stEspecialidadeCargo"                   );
    $obCmbCodEspecialidadeCargo->setRotulo      ( "Função"                                 );
    $obCmbCodEspecialidadeCargo->setTitle       ( "Selecione a especialidade do servidor." );
    $obCmbCodEspecialidadeCargo->setNull        ( true                                     );
    $obCmbCodEspecialidadeCargo->setCampoId     ( "[cod_especialidade]"                    );
    $obCmbCodEspecialidadeCargo->setCampoDesc   ( "descricao_especialidade"                );
    $obCmbCodEspecialidadeCargo->addOption      ( "", "Selecione"                          );
    $obCmbCodEspecialidadeCargo->obEvento->setOnChange( "buscaValor('preenchePreEspecialidadeFuncao');" );
    //FIM INFORMAÇÕES DO CARGO
    
    //-------------------------------------------
    //---------------FUNÇÃO-----------------------
    //-------------------------------------------
    //INFORMAÇÕES DA FUNÇÃO
    $obTxtCodRegimeFuncao = new TextBox;
    $obTxtCodRegimeFuncao->setRotulo             ( "Regime"                             );
    $obTxtCodRegimeFuncao->setName               ( "inCodRegimeFuncao"                  );
    $obTxtCodRegimeFuncao->setTitle              ( "Informe o regime de trabalho."      );
    $obTxtCodRegimeFuncao->setSize               ( 10                                   );
    $obTxtCodRegimeFuncao->setMaxLength          ( 8                                    );
    $obTxtCodRegimeFuncao->setInteiro            ( true                                 );
    $obTxtCodRegimeFuncao->setNull               ( true                                 );
    $obTxtCodRegimeFuncao->obEvento->setOnChange ( "buscaValor('preencheSubDivisaoFuncao');" );
    
    $obRPessoalRegime->listarRegime( $rsRegime, "", $boTransacao );

    $obCmbCodRegimeFuncao = new Select;
    $obCmbCodRegimeFuncao->setName               ( "stRegimeFuncao"                     );
    $obCmbCodRegimeFuncao->setRotulo             ( "Regime"                             );
    $obCmbCodRegimeFuncao->setTitle              ( "Selecione o regime."                );
    $obCmbCodRegimeFuncao->setNull               ( false                                );
    $obCmbCodRegimeFuncao->setCampoId            ( "[cod_regime]"                       );
    $obCmbCodRegimeFuncao->setCampoDesc          ( "descricao"                          );
    $obCmbCodRegimeFuncao->addOption             ( "", "Selecione"                      );
    $obCmbCodRegimeFuncao->preencheCombo         ( $rsRegime                            );
    $obCmbCodRegimeFuncao->obEvento->setOnChange ( "buscaValor('preencheSubDivisaoFuncao');" );
    
    $obTxtCodSubDivisaoFuncao = new TextBox;
    $obTxtCodSubDivisaoFuncao->setRotulo        ( "Subdivisão"                          );
    $obTxtCodSubDivisaoFuncao->setName          ( "inCodSubDivisaoFuncao"               );
    $obTxtCodSubDivisaoFuncao->setTitle         ( "Selecione a subdivisão do regime."   );
    $obTxtCodSubDivisaoFuncao->setSize          ( 10                                    );
    $obTxtCodSubDivisaoFuncao->setMaxLength     ( 8                                     );
    $obTxtCodSubDivisaoFuncao->setInteiro       ( true                                  );
    $obTxtCodSubDivisaoFuncao->setNull          ( true                                  );
    $obTxtCodSubDivisaoFuncao->obEvento->setOnChange ( "buscaValor('preencheFuncao');"  );
    
    $obCmbCodSubDivisaoFuncao = new Select;
    $obCmbCodSubDivisaoFuncao->setName          ( "stSubDivisaoFuncao"                  );
    $obCmbCodSubDivisaoFuncao->setRotulo        ( "Subdivisão"                          );
    $obCmbCodSubDivisaoFuncao->setTitle         ( "Selecione a subdivisão."             );
    $obCmbCodSubDivisaoFuncao->setNull          ( false                                 );
    $obCmbCodSubDivisaoFuncao->setCampoId       ( "[cod_sub_divisao]"                   );
    $obCmbCodSubDivisaoFuncao->setCampoDesc     ( "descricao"                           );
    $obCmbCodSubDivisaoFuncao->addOption        ( "", "Selecione"                       );
    $obCmbCodSubDivisaoFuncao->obEvento->setOnChange ( "buscaValor('preencheFuncao');"  );
    
    //Selecão da funcao
    $obTxtCodFuncao = new TextBox;
    $obTxtCodFuncao->setRotulo                  ( "Função"                              );
    $obTxtCodFuncao->setName                    ( "inCodFuncao"                         );
    $obTxtCodFuncao->setTitle                   ( "Selecione a função do servidor."     );
    $obTxtCodFuncao->setSize                    ( 10                                    );
    $obTxtCodFuncao->setMaxLength               ( 10                                    );
    $obTxtCodFuncao->setInteiro                 ( true                                  );
    $obTxtCodFuncao->setNull                    ( false                                 );
    $obTxtCodFuncao->obEvento->setOnChange      ( " buscaValor('preencheEspecialidadeFuncao');" );
    
    $obCmbCodFuncao = new Select;
    $obCmbCodFuncao->setName                    ( "stFuncao"                            );
    $obCmbCodFuncao->setRotulo                  ( "Função"                              );
    $obCmbCodFuncao->setTitle                   ( "Selecione a função do servidor."     );
    $obCmbCodFuncao->setNull                    ( false                                 );
    $obCmbCodFuncao->setCampoId                 ( "[cod_cargo]"                         );
    $obCmbCodFuncao->setCampoDesc               ( "descricao"                           );
    $obCmbCodFuncao->addOption                  ( "", "Selecione"                       );
    $obCmbCodFuncao->obEvento->setOnChange      ( "buscaValor('preencheEspecialidadeFuncao');" );
    
    //Selecão da Especialidade Funcao
    $obTxtCodEspecialidadeFuncao = new TextBox;
    $obTxtCodEspecialidadeFuncao->setRotulo     ( "Especialidade"                          );
    $obTxtCodEspecialidadeFuncao->setName       ( "inCodEspecialidadeFuncao"               );
    $obTxtCodEspecialidadeFuncao->setTitle      ( "Selecione a especialidade do servidor." );
    $obTxtCodEspecialidadeFuncao->setSize       ( 10                                       );
    $obTxtCodEspecialidadeFuncao->setMaxLength  ( 10                                       );
    $obTxtCodEspecialidadeFuncao->setInteiro    ( true                                     );
    $obTxtCodEspecialidadeFuncao->setNull       ( true                                     );
    $obTxtCodEspecialidadeFuncao->obEvento->setOnChange( "buscaValor('preencheInformacoesSalariais');" );
    
    $obCmbCodEspecialidadeFuncao = new Select;
    $obCmbCodEspecialidadeFuncao->setName       ( "stEspecialidadeFuncao"                 );
    $obCmbCodEspecialidadeFuncao->setRotulo     ( "Especialidade"                         );
    $obCmbCodEspecialidadeFuncao->setTitle      ( "Selecione a especialidade do servidor." );
    $obCmbCodEspecialidadeFuncao->setNull       ( true                                    );
    $obCmbCodEspecialidadeFuncao->setCampoId    ( "[cod_especialidade]"                   );
    $obCmbCodEspecialidadeFuncao->setCampoDesc  ( "descricao_especialidade"               );
    $obCmbCodEspecialidadeFuncao->addOption     ( "", "Selecione"                         );
    $obCmbCodEspecialidadeFuncao->obEvento->setOnChange( "buscaValor('preencheInformacoesSalariais');" );
    
    $obDataAlteracaoFuncao = new Data;
    $obDataAlteracaoFuncao->setRotulo           ( "Data da Alteração da Função"         );
    $obDataAlteracaoFuncao->setTitle            ( "Data da alteração da função."         );
    $obDataAlteracaoFuncao->setName             ( "dtDataAlteracaoFuncao"               );
    $obDataAlteracaoFuncao->setId               ( 'dtDataAlteracaoFuncao'               );
    $obDataAlteracaoFuncao->setSize             ( 10                                    );
    $obDataAlteracaoFuncao->setMaxLength        ( 10                                    );
    $obDataAlteracaoFuncao->setNull             ( false                                 );
    $obDataAlteracaoFuncao->setInteiro          ( false                                 );
    $obDataAlteracaoFuncao->setReadOnly         ( true                                  );
    $obDataAlteracaoFuncao->setStyle            ( "color: #888888"                      );
    //$obDataAlteracaoFuncao->obEvento->setOnChange("buscaValor('validaDataAlteracaoFuncao');");
    
    $obHdnDataAlteracaoFuncao = new Hidden;
    $obHdnDataAlteracaoFuncao->setName          ( "hdnDataAlteracaoFuncao"              );
    
    //FIM INFORMAÇÕES DA FUNÇÃO

    //-------------------------------------------
    //---------------SALARIAIS-------------------
    //-------------------------------------------
    //seleção de padrao
    $obTxtCodPadrao = new TextBox;
    $obTxtCodPadrao->setRotulo             ( "Padrão"     );
    $obTxtCodPadrao->setName               ( "inCodPadrao" );
    
    $obTxtCodPadrao->setTitle              ( "Informe o padrão." );
    $obTxtCodPadrao->setSize               ( 10    );
    $obTxtCodPadrao->setMaxLength          ( 10    );
    $obTxtCodPadrao->setInteiro            ( true );
    $obTxtCodPadrao->setNull               ( true );
    $obTxtCodPadrao->obEvento->setOnChange    ( "buscaValor('preencheProgressao');" );
    
    $obRFolhaPagamentoPadrao->listarPadraoPorContratosInativos( $rsPadrao, $boTransacao,"");
    
    $obCmbCodPadrao = new Select;
    $obCmbCodPadrao->setName                  ( "stPadrao"            );
    
    $obCmbCodPadrao->setRotulo                ( "Padrao"              );
    $obCmbCodPadrao->setTitle                 ( "Selecione o padrão." );
    $obCmbCodPadrao->setNull                  ( true                  );
    $obCmbCodPadrao->setCampoId               ( "[cod_padrao]" );
    $obCmbCodPadrao->setCampoDesc             ( "[descricao] - [valor]" );
    $obCmbCodPadrao->addOption                ( "", "Selecione"       );
    $obCmbCodPadrao->preencheCombo            ( $rsPadrao             );
    $obCmbCodPadrao->obEvento->setOnChange    ( "buscaValor('preencheProgressao');" );
    
    $obHdnProgressao =  new Hidden;
    $obHdnProgressao->setName   ( "inCodProgressao" );
    
    
    //Label da progressao
    $obLblProgressao = new Label;
    $obLblProgressao->setRotulo ( 'Progressão'    );
    $obLblProgressao->setName   ( 'stlblProgressao' );
    $obLblProgressao->setId     ( 'stlblProgressao' );
    
    
    $obTxtHorasMensais = new TextBox;
    $obTxtHorasMensais->setRotulo           ( "Horas Mensais"                            );
    $obTxtHorasMensais->setName             ( "stHorasMensais"                           );
    
    $obTxtHorasMensais->setTitle            ( "Informe a quantidade de horas mensais."   );
    $obTxtHorasMensais->setNull             ( false                                      );
    $obTxtHorasMensais->setSize             ( 6                                          );
    $obTxtHorasMensais->setMaxLength        ( 6                                          );
    $obTxtHorasMensais->setFloat            ( true                                       );
    $obTxtHorasMensais->obEvento->setOnChange      ( "buscaValor('calculaSalario');"     );
    
    $obTxtHorasSemanais = new TextBox;
    $obTxtHorasSemanais->setRotulo           ( "Horas Semanais"                           );
    $obTxtHorasSemanais->setName             ( "stHorasSemanais"                          );
    $obTxtHorasSemanais->setTitle            ( "Informe a quantidade de horas semanais."  );
    $obTxtHorasSemanais->setNull             ( false                                      );
    $obTxtHorasSemanais->setSize             ( 6                                          );
    $obTxtHorasSemanais->setMaxLength        ( 6                                          );
    $obTxtHorasSemanais->setFloat            ( true                                       );
    
    //Valor do salario salarial
    $obTxtSalario = new Moeda;
    $obTxtSalario->setRotulo    ( "Salário");
    $obTxtSalario->setTitle     ( "Informe o salário do servidor.");
    $obTxtSalario->setName      ( "inSalario" );
    $obTxtSalario->setMaxLength ( 14  );
    $obTxtSalario->setSize      ( 15  );
    $obTxtSalario->setNull      ( false );
    
    //Vigência
    $obDtVigenciaSalario = new Data;
    $obDtVigenciaSalario->setName               ( "dtVigenciaSalario"            );
    $obDtVigenciaSalario->setTitle              ("Informe a vigência do salário.");
    $obDtVigenciaSalario->setNull               ( false                          );
    $obDtVigenciaSalario->setRotulo             ( "Vigência do Salário"          );
    $obDtVigenciaSalario->obEvento->setOnChange ( "buscaValor('validarVigenciaSalario');" );
    //FIM SALARIAIS

    if ( isset($_REQUEST["inCodContrato"]) )
        $inCodContrato = $_REQUEST["inCodContrato"];
    else
        $inCodContrato = $arDados["inCodContrato"];

    $obRPessoalServidor->roUltimoContratoServidor->setCodContrato( $inCodContrato );
    $obRPessoalServidor->roUltimoContratoServidor->listarDadosAbaContratoServidor( $rsContrato,$boTransacao );
    $dtDataProgressao = $rsContrato->getCampo("dt_inicio_progressao");
    
    $obTxtDataProgressao = new Hidden();
    $obTxtDataProgressao->setName   ( "dtDataProgressao" );
    $obTxtDataProgressao->obEvento->setOnChange( "buscaValor('preencheProgressao');");

    //Setando os valores caso for alteracao
    if (isset($arDados)) {
        $obTxtCodRegime->setValue              ( $arDados['inCodRegime']              );
        $obCmbCodRegime->setValue              ( $arDados['inCodRegime']              );
        $_REQUEST["inCodRegime"]                = $arDados['inCodRegime'];
        $_REQUEST["inCodSubDivisao"]            = $arDados['inCodSubDivisao'];
        $_REQUEST["inCodCargo"]                 = $arDados['inCodCargo'];
        $_REQUEST["inCodSubDivisaoFuncao"]      = $arDados['inCodSubDivisaoFuncao'];
        $_REQUEST["inCodFuncao"]                = $arDados['inCodFuncao'];
        $obTxtCodRegimeFuncao->setValue        ( $arDados['inCodRegimeFuncao']        );
        $obCmbCodRegimeFuncao->setValue        ( $arDados['inCodRegimeFuncao']        );
        $obTxtCodEspecialidadeFuncao->setValue ( $arDados['inCodEspecialidadeFuncao'] );
        $obCmbCodEspecialidadeFuncao->setValue ( $arDados['inCodEspecialidadeFuncao'] );
        $obDataAlteracaoFuncao->setValue       ( $arDados['dtDataAlteracaoFuncao']    );
        $obHdnDataAlteracaoFuncao->setValue    ( $arDados['dtDataAlteracaoFuncao']    );
        $obTxtCodPadrao->setValue              ( $arDados['inCodPadrao']              );
        $obCmbCodPadrao->setValue              ( $arDados['inCodPadrao']              );
        $obHdnProgressao->setValue             ( $arDados['inCodProgressao']          );
        $obLblProgressao->setValue             ( $arDados['stlblProgressao']          );
        $obTxtHorasMensais->setValue           ( $arDados['stHorasMensais']           );
        $obTxtHorasSemanais->setValue          ( $arDados['stHorasSemanais']          );
        $obTxtSalario->setValue                ( $arDados['inSalario']                );
        $obDtVigenciaSalario->setValue         ( $arDados['dtVigenciaSalario']        );
        $obTxtDataProgressao->setValue         ( $dtDataProgressao                    );

        Sessao::write('arDados',$arDados);
    }


    $obFormulario = new Formulario();
    $obFormulario->addTitulo            ( "Informações do Cargo"                                            );
    $obFormulario->addComponenteComposto( $obTxtCodRegime,$obCmbCodRegime                                   );
    $obFormulario->addComponenteComposto( $obTxtCodSubDivisao,$obCmbCodSubDivisao                           );
    $obFormulario->addComponenteComposto( $obTxtCargo,$obCmbCargo                                           );
    $obFormulario->addComponenteComposto( $obTxtCodEspecialidadeCargo, $obCmbCodEspecialidadeCargo          );
    $obFormulario->addTitulo            ( "Informações da Função"                                           );
    $obFormulario->addComponenteComposto( $obTxtCodRegimeFuncao,$obCmbCodRegimeFuncao                       );
    $obFormulario->addComponenteComposto( $obTxtCodSubDivisaoFuncao,$obCmbCodSubDivisaoFuncao               );
    $obFormulario->addComponenteComposto( $obTxtCodFuncao,$obCmbCodFuncao                                   );
    $obFormulario->addComponenteComposto( $obTxtCodEspecialidadeFuncao, $obCmbCodEspecialidadeFuncao        );
    $obFormulario->addComponente        ( $obDataAlteracaoFuncao                                            );
    $obFormulario->addHidden            ( $obHdnDataAlteracaoFuncao                                         );
    $obFormulario->addTitulo            ( "Informações Salariais"                                           );
    $obFormulario->addComponente        ( $obTxtHorasMensais                                                );
    $obFormulario->addComponente        ( $obTxtHorasSemanais                                               );
    $obFormulario->addComponenteComposto( $obTxtCodPadrao,$obCmbCodPadrao                                   );
    $obFormulario->addHidden            ( $obHdnProgressao                                                  );
    $obFormulario->addHidden            ( $obTxtDataProgressao                                              );
    $obFormulario->addComponente        ( $obLblProgressao                                                  );
    $obFormulario->addComponente        ( $obTxtSalario                                                     );
    $obFormulario->addComponente        ( $obDtVigenciaSalario                                              );

    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();

    $stJs = "d.getElementById('spnCargoFuncaoSalario').innerHTML = '".$stHtml."';\n";
    $stJs .= preencheSubDivisao();
    $stJs .= preencheCargo();
    $stJs .= preencheEspecialidade();

    return $stJs;
}

function carregaDados()
{       

    $obRPessoalServidor = new RPessoalServidor();
    $obRPessoalServidor->addContratoServidor();

    if ( isset($_REQUEST['inCodMatricula'])) 
        $inRegistro = $_REQUEST['inCodMatricula'];
    else
        $inRegistro = $_REQUEST["inRegistro"];

    $inCodContrato = SistemaLegado::pegaDado("cod_contrato","pessoal.contrato","WHERE registro = ".$inRegistro);

    $obRPessoalServidor->roUltimoContratoServidor->setCodContrato( $inCodContrato );
    $obRPessoalServidor->roUltimoContratoServidor->listarDadosAbaContratoServidor( $rsContrato,$boTransacao );
    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalOcorrencia->setCodOcorrencia($rsContrato->getCampo("cod_ocorrencia"));
    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalOcorrencia->listarOcorrencia( $rsOcorrencia,$boTransacao );
    $obRPessoalServidor->roUltimoContratoServidor->consultarContratoServidorSubDivisaoFuncao( $rsContratoServidorSubDivisaoFuncao, $boTransacao );
    $obRPessoalServidor->roUltimoContratoServidor->consultarContratoServidorRegimeFuncao( $rsContratoServidorRegimeFuncao, $boTransacao );

    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
    list($stDia, $stMes, $stAno) = explode("/", $rsPeriodoMovimentacao->getCampo("dt_final"));
    $stVigencia = $stAno."-".$stMes."-".$stDia;
        
    $rsContrato->addFormatacao('salario','NUMERIC_BR');
        
    $inContrato                            = $rsContrato->getCampo("cod_contrato");
    $arDados['inCodContrato']              = $rsContrato->getCampo("cod_contrato");
    $inRegistro                            = $rsContrato->getCampo("registro");
    $arDados['inRegistro']                 = $rsContrato->getCampo("registro");
    //Informações do cargo
    $arDados['inCodRegime']                = $rsContrato->getCampo("cod_regime");
    $inCodRegime                           = $rsContrato->getCampo("cod_regime");
    $arDados['inCodSubDivisao']            = $rsContrato->getCampo("cod_sub_divisao");
    $inCodSubDivisao                       = $rsContrato->getCampo("cod_sub_divisao");
    $arDados['inCodCargo']                 = $rsContrato->getCampo("cod_cargo");
    $inCodCargo                            = $rsContrato->getCampo("cod_cargo");
    $arDados['inCodEspecialidadeCargo']    = $rsContrato->getCampo("cod_especialidade_cargo");
    $inCodEspecialidadeCargo               = $rsContrato->getCampo("cod_especialidade_cargo");
    //Informações da função
    $arDados['inCodRegimeFuncao']          = $rsContratoServidorRegimeFuncao->getCampo("cod_regime");
    $inCodRegimeFuncao                     = $rsContratoServidorRegimeFuncao->getCampo("cod_regime");
    $arDados['inCodSubDivisaoFuncao']      = $rsContratoServidorSubDivisaoFuncao->getCampo("cod_sub_divisao");
    $inCodSubDivisaoFuncao                 = $rsContratoServidorSubDivisaoFuncao->getCampo("cod_sub_divisao");
    $arDados['inCodFuncao']                = $rsContrato->getCampo("cod_funcao");
    $arDados['inCodEspecialidadeFuncao']   = ($rsContrato->getCampo("cod_especialidade_funcao") != "") ? $rsContrato->getCampo("cod_especialidade_funcao") : $rsContrato->getCampo("cod_especialidade_cargo");
    $inCodEspecialidadeFuncao              = ($rsContrato->getCampo("cod_especialidade_funcao") != "") ? $rsContrato->getCampo("cod_especialidade_funcao") : $rsContrato->getCampo("cod_especialidade_cargo");
    $arDados['dtDataAlteracaoFuncao']      = $rsContrato->getCampo("ultima_vigencia");
        
    //Informações salariais
    $rsContrato->addFormatacao("horas_mensais", "NUMERIC_BR");
    $rsContrato->addFormatacao("horas_semanais", "NUMERIC_BR");
    $arDados['stHorasMensais']             = $rsContrato->getCampo("horas_mensais");
    $stHorasMensais                        = $rsContrato->getCampo("horas_mensais");
    $arDados['stHorasSemanais']            = $rsContrato->getCampo("horas_semanais");
    $arDados['dtVigenciaSalario']          = $rsContrato->getCampo("vigencia");
    Sessao::write('dtVigenciaSalario',$dtVigenciaSalario);
    $arDados['dtDataProgressao']           = $rsContrato->getCampo("dt_inicio_progressao");
    $dtDataProgressao                      = $rsContrato->getCampo("dt_inicio_progressao");
    $arDados['inCodPadrao']                = $rsContrato->getCampo("cod_padrao");
    $inCodPadrao                           = $rsContrato->getCampo("cod_padrao");
    $arDados['inCodProgressao']            = $rsContrato->getCampo("cod_nivel_padrao");
    $arDados['inSalario']                  = $rsContrato->getCampo("salario");
    //$stDataProgressao
    
    return $arDados;

}

function gerarSpanLicencaPremio()
{
    $stHtml = "";
    $stEval = "";
    $rsAssentamento  = new RecordSet();
    $rsClassificacao = new RecordSet();
    $inCodClassificacao = ( $_REQUEST["inCodClassificacao"] != '') ? $_REQUEST["inCodClassificacao"] : Sessao::read('inCodClassificacao');

    if ($_REQUEST["inCodAssentamento"] != "") {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoAssentamento.class.php");
        $obTPessoalAssentamentoAssentamento = new TPessoalAssentamentoAssentamento();
        $stFiltro  = " AND assentamento_assentamento.cod_assentamento = ".$_REQUEST["inCodAssentamento"];
        $stFiltro .= " AND assentamento_assentamento.cod_motivo = 9";
        $obTPessoalAssentamentoAssentamento->recuperaAssentamento($rsAssentamento,$stFiltro);
    }
    
    if ($inCodClassificacao != "" ) {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalClassificacaoAssentamento.class.php");
        $obTPessoalClassificacaoAssentamento = new TPessoalClassificacaoAssentamento();
        $stFiltro  = " AND ca.cod_classificacao = ".$inCodClassificacao;
        $stFiltro .= " AND ca.cod_tipo = 2";
        $obTPessoalClassificacaoAssentamento->recuperaRelacionamento($rsClassificacao,$stFiltro);        
    }

    if ($rsAssentamento->getNumLinhas() == 1 AND $rsClassificacao->getNumLinhas() == 1) {
        $obDtInicial = new Data();
        $obDtInicial->setRotulo("Período Aquisitivo Licença Prêmio");
        $obDtInicial->setName("dtInicial");
        $obDtInicial->setTitle("Informe o período aquisitivo da licença prêmio, ver relatório controle de licenças prêmio.");
        $obDtInicial->setNull(false);
        $obDtInicial->setValue($_REQUEST["dtInicial"]);

        $obLabelAte = new Label;
        $obLabelAte->setRotulo                          ( "Período Aquisitivo Licença Prêmio"                                                        );
        $obLabelAte->setValue                           ( "até"                                                             );
        $obLabelAte->setTitle                           ( "Informe o período aquisitivo da licença prêmio, ver relatório controle de licenças prêmio."                              );

        $obDtFinal = new Data();
        $obDtFinal->setRotulo("Período Aquisitivo Licença Prêmio");
        $obDtFinal->setName("dtFinal");
        $obDtFinal->setTitle("Informe o período aquisitivo da licença prêmio, ver relatório controle de licenças prêmio.");
        $obDtFinal->setNull(false);
        $obDtFinal->setValue($_REQUEST["dtFinal"]);;

        $obFormulario = new Formulario();
        $obFormulario->agrupaComponentes( array($obDtInicial, $obLabelAte, $obDtFinal)                  );
        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();
    }

    $stJs  = "d.getElementById('spnLicencaPremio').innerHTML = '".$stHtml."';\n";
    $stJs .= "LiberaFrames();\n";

    return $stJs;
}

function MontaNorma($stSelecionado = "")
{
    $stCombo  = "inCodNorma";
    $stFiltro = "inCodTipoNorma";
    $stJs .= "limpaSelect(f.$stCombo,0); \n";
    $stJs .= "f.$stCombo.options[0] = new Option('Selecione','', 'selected');\n";
    $stJs .= "f.".$stCombo."Txt.value='$stSelecionado';\n";
    $inCodTipoNorma = (trim($_REQUEST[$stFiltro]) != "") ? trim($_REQUEST[$stFiltro]) : Sessao::read("inCodTipoNorma");
    if ($inCodTipoNorma != "") {
        include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPadrao.class.php" );
        $obRFolhaPagamentoPadrao = new RFolhaPagamentoPadrao;
        $obRFolhaPagamentoPadrao->obRNorma->obRTipoNorma->setCodTipoNorma( $inCodTipoNorma );
        $obRFolhaPagamentoPadrao->obRNorma->listar( $rsCombo );
        $inCount = 0;

        while (!$rsCombo->eof()) {
            $inCount++;
            $inId   = $rsCombo->getCampo("cod_norma");
            $stDesc = $rsCombo->getCampo("nom_norma");
            $stNumNormaExercicio = $rsCombo->getCampo("num_norma_exercicio");
            if( $stSelecionado == $inId )
                $stSelected = 'selected';
            else
                $stSelected = '';
            $stJs .= "f.$stCombo.options[$inCount] = new Option('".$stNumNormaExercicio." - ".$stDesc."','".$inId."','".$stSelected."'); \n";
            $rsCombo->proximo();
        }
    }

    return $stJs;
}

function incluirNorma()
{
    include_once CAM_GA_NORMAS_MAPEAMENTO."TNorma.class.php";
    include_once CAM_GA_NORMAS_MAPEAMENTO."TTipoNorma.class.php";
    include_once CAM_GA_NORMAS_MAPEAMENTO."TNormaDataTermino.class.php";

    $obErro = new Erro();
    $obTNorma = new TNorma();
    $obTTipoNorma = new TTipoNorma();
    $obTNormaDataTermino = new TNormaDataTermino();

    if ($_REQUEST['hdnCodTipoNorma'] == "" || $_REQUEST['stCodNorma'] == "") {
        $obErro->setDescricao("Informe o Tipo de Norma e a Norma!");
    } else {

        $arCodNorma = explode("/",$_REQUEST["stCodNorma"]);
        $stFiltroTipoNorma = " WHERE cod_tipo_norma = ".$_REQUEST['hdnCodTipoNorma'];

        $stFiltroNorma  = " WHERE cod_tipo_norma = ".$_REQUEST['hdnCodTipoNorma'];
        $stFiltroNorma .= "   AND num_norma = '".(int) $arCodNorma[0]."'";

        $obTNorma->recuperaNormas($rsRecordSetNorma, $stFiltroNorma);
        $obTTipoNorma->recuperaTodos($rsRecordSetTipoNorma, $stFiltroTipoNorma);

        $stFiltroDataTermino = " WHERE cod_norma = ".$rsRecordSetNorma->getCampo('cod_norma');
        $obTNormaDataTermino->recuperaTodos($rsRecordSetDataTermino, $stFiltroDataTermino);
        $arNormas = Sessao::read('arNormas');

        $arNorma = array();
        $arNorma['stNomTipoNorma']          =   $rsRecordSetTipoNorma->getCampo('nom_tipo_norma');
        $arNorma['stNorma']                 =   $rsRecordSetNorma->getCampo('num_norma_exercicio')." - ".$rsRecordSetNorma->getCampo('nom_norma');
        $arNorma['dtAssinatura']            =   $rsRecordSetNorma->getCampo('dt_assinatura_formatado');
        $arNorma['dtTermino']               =   $rsRecordSetDataTermino->getCampo('dt_termino');
        $arNorma['dtPublicacao']            =   $rsRecordSetNorma->getCampo('dt_publicacao');
        $arNorma['inCodNorma']              =   $rsRecordSetNorma->getCampo('cod_norma');
        $arNorma['inCodTipoNorma']          =   $rsRecordSetNorma->getCampo('cod_tipo_norma');
        $arNorma['stNomNorma']              =   $rsRecordSetNorma->getCampo('nom_norma');
        $arNorma['stDescricao']             =   $rsRecordSetNorma->getCampo('descricao');
        $arNorma['stExercicio']             =   $rsRecordSetNorma->getCampo('exercicio');
        $arNorma['inNumNorma']              =   $rsRecordSetNorma->getCampo('num_norma');
        $arNorma['inId']                    =   count($arNormas);

        if ($arNormas != "") {
            foreach ($arNormas as $arrNorma) {
                if ($arrNorma['stTipoNorma'] == $arNorma['stTipoNorma'] && $arrNorma['stNorma'] == $arNorma['stNorma']) {
                    $obErro->setDescricao("Esta norma já está na lista!");
                }
            }
        }
    }

    if ($obErro->ocorreu()) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    } else {
        $arNormas[] = $arNorma;
        Sessao::write('arNormas',$arNormas);
        $stJs .= montaListaNorma();
    }

    $stJs .= "f.hdnCodTipoNorma.value               = '';\n";
    $stJs .= "f.stCodNorma.value                    = '';\n";
    $stJs .= "d.getElementById('stNorma').innerHTML = '&nbsp;';\n";

    return $stJs;
}

function buscaNormas()
{
    include_once CAM_GA_NORMAS_MAPEAMENTO."TNorma.class.php";
    include_once CAM_GA_NORMAS_MAPEAMENTO."TTipoNorma.class.php";
    include_once CAM_GA_NORMAS_MAPEAMENTO."TNormaDataTermino.class.php";

    $obErro = new Erro();
    $obTNorma = new TNorma();
    $obTTipoNorma = new TTipoNorma();
    $obTNormaDataTermino = new TNormaDataTermino();

    $arCodNormas = Sessao::read('arCodNorma');

    if ($arCodNormas != "") {
        foreach ($arCodNormas as $norma) {
            if ($norma['inCodTipoNorma'] == "" || $norma['inCodNorma'] == "") {
                $obErro->setDescricao("Informe o Tipo de Norma e a Norma!");
        }

        $stFiltroTipoNorma = " WHERE cod_tipo_norma = ".$norma['inCodTipoNorma'];

        $stFiltroNorma  = " WHERE cod_tipo_norma = ".$norma['inCodTipoNorma'];
        $stFiltroNorma .= "   AND cod_norma = ".$norma['inCodNorma'];

        $stFiltroDataTermino = " WHERE cod_norma = ".$norma['inCodNorma'];

        $obTNorma->recuperaNormas($rsRecordSetNorma, $stFiltroNorma);
        $obTTipoNorma->recuperaTodos($rsRecordSetTipoNorma, $stFiltroTipoNorma);
        $obTNormaDataTermino->recuperaTodos($rsRecordSetDataTermino, $stFiltroDataTermino);

        $arNorma = array();
        $arNorma['stNomTipoNorma']          =   $rsRecordSetTipoNorma->getCampo('nom_tipo_norma');
        $arNorma['stNorma']                 =   $rsRecordSetNorma->getCampo('num_norma_exercicio')." - ".$rsRecordSetNorma->getCampo('nom_norma');
        $arNorma['dtAssinatura']            =   $rsRecordSetNorma->getCampo('dt_assinatura_formatado');
        $arNorma['dtTermino']               =   $rsRecordSetDataTermino->getCampo('dt_termino');
        $arNorma['dtPublicacao']            =   $rsRecordSetNorma->getCampo('dt_publicacao');
        $arNorma['inCodNorma']              =   $rsRecordSetNorma->getCampo('cod_norma');
        $arNorma['inCodTipoNorma']          =   $rsRecordSetNorma->getCampo('cod_tipo_norma');
        $arNorma['stNomNorma']              =   $rsRecordSetNorma->getCampo('nom_norma');
        $arNorma['stDescricao']             =   $rsRecordSetNorma->getCampo('descricao');
        $arNorma['stExercicio']             =   $rsRecordSetNorma->getCampo('exercicio');
        $arNorma['inNumNorma']              =   $rsRecordSetNorma->getCampo('num_norma');
        $arNorma['inId']                    =   count($arNormas);

        $arNormas[] = $arNorma;
    }
        Sessao::write('arNormas',$arNormas);
    }
}

function montaListaNorma()
{
    $rsRecordSet = new RecordSet();
    if (Sessao::read('arNormas') != "") {
        $rsRecordSet->preenche(Sessao::read('arNormas'));
    }

    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo( "Normas/Fundamentação Legal" );

    $obLista->setRecordSet( $rsRecordSet );
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 3 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Tipo Norma" );
    $obLista->ultimoCabecalho->setWidth( 17 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Norma" );
    $obLista->ultimoCabecalho->setWidth( 37 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Assinatura" );
    $obLista->ultimoCabecalho->setWidth( 12 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Publicação" );
    $obLista->ultimoCabecalho->setWidth( 12 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Término" );
    $obLista->ultimoCabecalho->setWidth( 12 );
    $obLista->commitCabecalho();
    if ($_REQUEST['stAcao'] == 'alterar' || $_REQUEST['stAcao'] == 'incluir') {
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 4 );
        $obLista->commitCabecalho();
    }

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stNomTipoNorma" );
    $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stNorma" );
    $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "dtAssinatura" );
    $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "dtPublicacao" );
    $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "dtTermino" );
    $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
    $obLista->commitDado();

    if ($_REQUEST['stAcao'] == 'alterar' || $_REQUEST['stAcao'] == 'incluir') {
        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:modificaDado('excluirNorma');" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();
    }

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);
    $stJs .= "d.getElementById('spnFundamentacaoLegal').innerHTML = '".$stHtml."';";

    return $stJs;
}

function excluirNorma()
{
    $arTemp       = array();
    $arNormas     = Sessao::read('arNormas');

    foreach ($arNormas as $arNorma) {
        if ($arNorma['inId'] != $_GET['inId']) {
            $arTemp[] = $arNorma;
        }
    }
    $arNormas = $arTemp;
    Sessao::write('arNormas', $arNormas);
    $stJs .= montaListaNorma();

    return $stJs;
}

function preencheClassificacao($inCod, $comboType)
{
    //Para montar o combo Classificao
    $obRPessoalClassificacaoAssentamento = new RPessoalClassificacaoAssentamento();
    $obRPessoalClassificacaoAssentamento->listarClassificacaoGeracaoAssentamento( $rsClassificacao, $inCod, $comboType );

   /*for ($i=0; $i < count($rsClassificacao->arElementos); $i++ ) {
        $arr[$i]->cod_classificacao = $rsClassificacao->getCampo("cod_classificacao");
        $arr[$i]->descricao = utf8_encode($rsClassificacao->getCampo("descricao"));
        $arr[$i]->cod_tipo = $rsClassificacao->getCampo("cod_tipo");
        $arr[$i]->descricao_tipo = utf8_encode($rsClassificacao->getCampo("descricao_tipo"));

    }*/

    $dado    = null;
    for ($i=0; $i < count($rsClassificacao->arElementos); $i++ ) {
    //    for ($j=0; $j < count($rsClassificacao->arElementos[$i]); $j++ ) {
             $dado[$i]['cod_classificacao'] =  $rsClassificacao->arElementos[$i]['cod_classificacao'];
             $dado[$i]['descricao'] = $rsClassificacao->arElementos[$i]['descricao'];
             $dado[$i]['cod_tipo'] = $rsClassificacao->arElementos[$i]['cod_tipo'];
             $dado[$i]['descricao_tipo'] = $rsClassificacao->arElementos[$i]['descricao_tipo'];
      //  }
    }

    return json_encode($dado);
}

function preencheSubDivisao()
{
    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->setCodContrato( $_REQUEST["inCodContrato"] );
    $js .= "limpaSelect(f.stSubDivisao,0); \n";
    $js .= "f.stSubDivisao[0] = new Option('Selecione','', 'selected');\n";
    $js .= "f.inCodSubDivisao.value = ''; \n";

    $js .= "f.inCodCargo.value = ''; \n";
    $js .= "limpaSelect(f.stCargo,0); f.stCargo[0] = new Option('Selecione','', 'selected');\n";
    $js .= "f.inCodEspecialidadeCargo.value = ''; \n";
    $js .= "limpaSelect(f.stEspecialidadeCargo,0); f.stEspecialidadeCargo[0] = new Option('Selecione','', 'selected');\n";

    //Limpa componentes da função
    $js .= "limpaSelect(f.stSubDivisaoFuncao,0);\n";
    $js .= "f.stSubDivisaoFuncao[0] = new Option('Selecione','', 'selected');\n";
    $js .= "f.inCodSubDivisaoFuncao.value = ''; \n";

    $js .= "limpaSelect(f.stFuncao,0);\n";
    $js .= "f.stFuncao[0] = new Option('Selecione','', 'selected');\n";
    $js .= "f.inCodFuncao.value = ''; \n";

    $js .= "limpaSelect(f.stEspecialidadeFuncao,0);\n";
    $js .= "f.stEspecialidadeFuncao[0] = new Option('Selecione','', 'selected');\n";
    $js .= "f.inCodEspecialidadeFuncao.value = ''; \n";

    if ($_REQUEST["inCodRegime"]) {
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegime->addPessoalSubDivisao();
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegime->roUltimoPessoalSubDivisao->roPessoalRegime->setCodRegime( $_REQUEST['inCodRegime'] );
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegime->roUltimoPessoalSubDivisao->listarSubDivisao( $rsSubDivisao, $stFiltro,"", $boTransacao );
        $inContador = 1;
        while ( !$rsSubDivisao->eof() ) {
            $inCodSubDivisao  = $rsSubDivisao->getCampo( "cod_sub_divisao" );
            $stSubDivisao     = $rsSubDivisao->getCampo( "nom_sub_divisao" );
            $arAcao = explode("_",$_REQUEST['stAcao']);
            if ($inCodSubDivisao == $_REQUEST["inCodSubDivisao"]) {
                $stSelected = "selected";
                $js .= "f.inCodSubDivisao.value = '".$_REQUEST["inCodSubDivisao"]."'; \n";
            } else {
                $stSelected = "";
            }
            $js .= "f.stSubDivisao.options[$inContador] = new Option('".$stSubDivisao."','".$inCodSubDivisao."','".$stSelected."'); \n";
            $inContador++;
            $rsSubDivisao->proximo();
        }
        $_REQUEST["inCodRegimeFuncao"] = $_REQUEST['inCodRegime'];
        $js .= preencheSubDivisaoFuncao();
    }
    $stJs .= $js;

    return $stJs;
}

function preencheSubDivisaoFuncao()
{
    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->setCodContrato( $_REQUEST["inCodContrato"] );

    $js .= "f.inCodSubDivisaoFuncao.value = '';                                                 \n";
    $js .= "limpaSelect(f.stSubDivisaoFuncao,0);                                                \n";
    $js .= "f.stSubDivisaoFuncao[0] = new Option('Selecione','', 'selected');                   \n";
    $js .= "f.inCodFuncao.value = '';                                                           \n";
    $js .= "limpaSelect(f.stFuncao,0);                                                          \n";
    $js .= "f.stFuncao[0] = new Option('Selecione','', 'selected');                             \n";
    $js .= "f.inCodEspecialidadeFuncao.value = '';                                              \n";
    $js .= "limpaSelect(f.stEspecialidadeFuncao,0);                                             \n";
    $js .= "f.stEspecialidadeFuncao[0] = new Option('Selecione','', 'selected');                \n";

    $stFiltro = " AND pr.cod_regime = ".$_REQUEST["inCodRegimeFuncao"];
    if ($_REQUEST["inCodRegimeFuncao"]) {
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegime->addPessoalSubDivisao();
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegime->roUltimoPessoalSubDivisao->listarSubDivisao( $rsSubDivisao, $stFiltro,"", $boTransacao );
        $inContador = 1;
        while ( !$rsSubDivisao->eof() ) {
            $stSelected       = "";
            $inCodSubDivisao  = $rsSubDivisao->getCampo( "cod_sub_divisao" );
            $stSubDivisao     = $rsSubDivisao->getCampo( "nom_sub_divisao" );
            $arAcao = explode("_",$_REQUEST['stAcao']);
            if ($inCodSubDivisao == $_REQUEST["inCodSubDivisaoFuncao"]) {
                $stSelected = "selected";
                $js .= "f.inCodSubDivisaoFuncao.value = '".$_REQUEST["inCodSubDivisaoFuncao"]."'; \n";
            }
            $js .= "f.stSubDivisaoFuncao.options[$inContador] = new Option('".$stSubDivisao."','".$inCodSubDivisao."','".$stSelected."'); \n";
            $inContador++;
            $rsSubDivisao->proximo();
        }
    }
    $stJs .= limpaInformacoesSalariais();
    $stJs .= $js;
       
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php" );
    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
    $dtCompetenciaFinal = $rsPeriodoMovimentacao->getCampo("dt_final");
    $stJs .= "f.dtDataAlteracaoFuncao.readOnly = false;                                 \n";
    $stJs .= "f.dtDataAlteracaoFuncao.style.color = '#000000';                          \n";
    $stJs .= "f.dtDataAlteracaoFuncao.value = '$dtCompetenciaFinal';  \n";

    return $stJs;
}

function preencheCargo()
{
    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->setCodContrato( $_REQUEST["inCodContrato"] );
    $js .= "f.inCodCargo.value = ''; \n";
    $js .= "limpaSelect(f.stCargo,0); f.stCargo[0] = new Option('Selecione','', 'selected');\n";
    $js .= "f.inCodEspecialidadeCargo.value = ''; \n";
    $js .= "limpaSelect(f.stEspecialidadeCargo,0); f.stEspecialidadeCargo[0] = new Option('Selecione','', 'selected');\n";

    //Limpa componentes da função
    $js .= "limpaSelect(f.stFuncao,0);\n";
    $js .= "f.stFuncao[0] = new Option('Selecione','', 'selected');\n";
    $js .= "f.inCodFuncao.value = ''; \n";

    $js .= "limpaSelect(f.stEspecialidadeFuncao,0);\n";
    $js .= "f.stEspecialidadeFuncao[0] = new Option('Selecione','', 'selected');\n";
    $js .= "f.inCodEspecialidadeFuncao.value = ''; \n";
    if ($_REQUEST["inCodSubDivisao"]) {
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addCargoSubDivisao();
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao($_REQUEST['inCodSubDivisao']);

        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->listarCargosPorSubDivisaoServidor($rsCargo);

        $arAcao = explode("_",$_REQUEST['stAcao']);
        if ($arAcao[0] != "alterar") {
            $js .= "f.inCodCargo.value = ''; \n";
            $js .= "limpaSelect(f.stCargo,0); f.stCargo[0] = new Option('Selecione','', 'selected');\n";
            $js .= "f.stCargo[0] = new Option('Selecione','','selected');\n";
        }
        $js .= "f.inCodEspecialidadeCargo.value = '';\n";
        $js .= "limpaSelect(f.stEspecialidadeCargo,0); f.stEspecialidadeCargo[0] = new Option('Selecione','', 'selected');\n";
        $inContador = 1;
        while ( !$rsCargo->eof() ) {
            $inCodCargo = $rsCargo->getCampo( "cod_cargo" );
            $stCargo    = $rsCargo->getCampo( "descricao" );
            $arAcao = explode("_",$_REQUEST['stAcao']);
            if ($inCodCargo == $_REQUEST["inCodCargo"]) {
                $stSelected = "selected";
                $js .= "f.inCodCargo.value = '".$_REQUEST["inCodCargo"]."'; \n";
            } else {
                $stSelected = "";
            }
            $js .= "f.stCargo.options[$inContador] = new Option('".$stCargo."','".$inCodCargo."','".$stSelected."'); \n";
            $inContador++;
            $rsCargo->proximo();
        }
        $_REQUEST["inCodSubDivisaoFuncao"] = $_REQUEST['inCodSubDivisao'];
        $js .= preencheFuncao();
    }
    $stJs .= $js;

    return $stJs;
}

function preencheFuncao()
{
    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->setCodContrato( $_REQUEST["inRegistro"] );
    $js .= "f.inCodFuncao.value = '';\n";
    $js .= "limpaSelect(f.stFuncao,0);\n";
    $js .= "f.stFuncao[0] = new Option('Selecione','','selected');\n";
    $js .= "f.inCodEspecialidadeFuncao.value = ''; \n";
    $js .= "limpaSelect(f.stEspecialidadeFuncao,0); \n";
    $js .= "f.stEspecialidadeFuncao[0] = new Option('Selecione','', 'selected');\n";

    if ($_REQUEST["inCodSubDivisaoFuncao"]) {
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addCargoSubDivisao();
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao($_REQUEST['inCodSubDivisaoFuncao']);
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->listarCargosPorSubDivisaoServidor($rsFuncao);        
        $js .= "f.inCodFuncao.value = '';\n";
        $js .= "limpaSelect(f.stFuncao,0);\n";
        $js .= "f.stFuncao[0] = new Option('Selecione','','selected');\n";
        $js .= "f.inCodEspecialidadeFuncao.value = ''; \n";
        $js .= "limpaSelect(f.stEspecialidadeFuncao,0); f.stEspecialidadeFuncao[0] = new Option('Selecione','', 'selected');\n";
        $inContador = 1;
        while ( !$rsFuncao->eof() ) {
            $inCodFuncao = $rsFuncao->getCampo( "cod_cargo" );
            $stFuncao    = $rsFuncao->getCampo( "descricao" );
            if ($inCodFuncao == $_REQUEST["inCodFuncao"]) {
                $stSelected = "selected";
                $js .= "f.inCodFuncao.value = '".$_REQUEST["inCodFuncao"]."'; \n";
            } else {
                $stSelected = "";
            }
            $js .= "f.stFuncao.options[$inContador] = new Option('".$stFuncao."','".$inCodFuncao."','".$stSelected."'); \n";
            $inContador++;
            $rsFuncao->proximo();
        }
    }
    $js .= limpaInformacoesSalariais();
    $js .= preencheEspecialidadeFuncao();
    $stJs .= $js;
    if ($_REQUEST['stAcao'] == "alterar") {
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php" );
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
        $dtCompetenciaFinal = $rsPeriodoMovimentacao->getCampo("dt_final");
        $stJs .= "f.dtDataAlteracaoFuncao.value = '$dtCompetenciaFinal';  \n";
    }

    return $stJs;
}

function preencheEspecialidade()
{
    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addEspecialidade();
    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addCargoSubDivisao();

    $js .= "f.inCodEspecialidadeCargo.value = ''; \n";
    $js .= "limpaSelect(f.stEspecialidadeCargo,0); f.stEspecialidadeCargo[0] = new Option('Selecione','', 'selected');\n";

    //Limpa componentes da função

    $js .= "limpaSelect(f.stEspecialidadeFuncao,0);\n";
    $js .= "f.stEspecialidadeFuncao[0] = new Option('Selecione','', 'selected');\n";
    $js .= "f.inCodEspecialidadeFuncao.value = ''; \n";

    $js = limpaInformacoesSalariais();

    if ($_REQUEST["inCodCargo"]) {

        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao( $_REQUEST["inCodSubDivisao"] );
        $inCodCargo = ($_REQUEST["inCodCargo"]<>'') ? $_REQUEST["inCodCargo"] : $_REQUEST["inHdnCodCargo"];
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setCodCargo( $inCodCargo );
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->consultaCargoPadrao( $rsPadrao );
        $rsPadrao->addFormatacao( "valor", NUMERIC_BR );
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoEspecialidade->consultaEspecialidadeCargo( $rsEspecialidade );

        $js .= "f.inCodPadrao.value = '".$rsPadrao->getCampo('cod_padrao')."'; \n";
        $js .= "f.stPadrao.value = '".$rsPadrao->getCampo('cod_padrao')."'; \n";
        $js .= "f.stHorasMensais.value = '".$rsPadrao->getCampo('horas_mensais')."'; \n";
        $js .= "f.stHorasSemanais.value = '".$rsPadrao->getCampo('horas_semanais')."'; \n";
        $js .= "limpaSelect(f.stEspecialidadeCargo,0); \n";
        $js .= "f.inCodEspecialidadeCargo.value = ''; \n";
        $js .= "f.stEspecialidadeCargo[0] = new Option('Selecione','','selected');\n";
        $js .= "limpaSelect(f.stEspecialidadeFuncao,0); \n";
        $js .= "f.inCodEspecialidadeFuncao.value = ''; \n";
        $js .= "f.stEspecialidadeFuncao[0] = new Option('Selecione','','selected');\n";
        $inContador = 1;
        while ( !$rsEspecialidade->eof() ) {
            $inCodEspecialidade = $rsEspecialidade->getCampo( "cod_especialidade" );
            $stEspecialidade    = $rsEspecialidade->getCampo( "descricao_especialidade" );
            $js .= "f.stEspecialidadeCargo.options[$inContador] = new Option('".$stEspecialidade."','".$inCodEspecialidade."'); \n";
            $js .= "f.stEspecialidadeFuncao.options[$inContador] = new Option('".$stEspecialidade."','".$inCodEspecialidade."'); \n";
            $inContador++;
            $rsEspecialidade->proximo();
        }
        $js .= preencheInformacoesSalariais( $_REQUEST["inCodCargo"] );
    }
    if ($_REQUEST['stAcao'] == "alterar") {
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php" );
    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
        $dtCompetenciaFinal = $rsPeriodoMovimentacao->getCampo("dt_final");
        $stJs .= "f.dtDataAlteracaoFuncao.value = '".$dtCompetenciaFinal."';\n";
    }

    return $js;
}

function preencheEspecialidadeFuncao()
{
    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $js .= "limpaSelect(f.stEspecialidadeFuncao,0);                                             \n";
    $js .= "f.stEspecialidadeFuncao[0] = new Option('Selecione','', 'selected');                \n";
    $js .= "f.inCodEspecialidadeFuncao.value = '';                                              \n";

    if ($_REQUEST["inCodFuncao"]) {
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addEspecialidade();
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addCargoSubDivisao();
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao( $_REQUEST["inCodSubDivisaoFuncao"] );
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setCodCargo( $_REQUEST["inCodFuncao"] );
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoEspecialidade->consultaEspecialidadeCargo( $rsEspecialidade );
        $inContador = 1;
        while ( !$rsEspecialidade->eof() ) {
            $inCodEspecialidade = $rsEspecialidade->getCampo( "cod_especialidade" );
            $stEspecialidade    = $rsEspecialidade->getCampo( "descricao_especialidade" );
            $js .= "f.stEspecialidadeFuncao.options[$inContador] = new Option('".$stEspecialidade."','".$inCodEspecialidade."'); \n";
            $inContador++;
            $rsEspecialidade->proximo();
        }
    }
    if ($_REQUEST['stAcao'] == "alterar") {
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php" );
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
        $dtCompetenciaFinal = $rsPeriodoMovimentacao->getCampo("dt_final");
        $stJs .= "f.dtDataAlteracaoFuncao.value = '".$dtCompetenciaFinal."';\n";
    }

    $js .= preencheInformacoesSalariais();

    if ($_REQUEST['stAcao'] == "alterar") {
        $js .= "f.dtDataAlteracaoFuncao.value = '$dtCompetenciaFinal';  \n";
    }

    return $js;
}

function preenchePreEspecialidadeFuncao()
{
    if ($_REQUEST['inCodFuncao'] == $_REQUEST['inCodCargo']) {
        $stJs .= "f.inCodEspecialidadeFuncao.value = f.inCodEspecialidadeCargo.value; \n";
        $stJs .= "f.stEspecialidadeFuncao.value    = f.inCodEspecialidadeCargo.value; \n";
    }
    $stJs .= preencheInformacoesSalariais("", $_REQUEST['inCodEspecialidadeCargo'], "");

    return $stJs;
}

function preencheInformacoesSalariais($inCodFuncao = "", $inCodEspecialidadeFuncao = "", $stDataProgressao = "")
{
    include_once ( CAM_GRH_PES_NEGOCIO."RPessoalCargo.class.php" );
    $inCodFuncao              = $inCodFuncao              ? $inCodFuncao              : $_REQUEST["stFuncao"];
    $inCodEspecialidadeFuncao = $inCodEspecialidadeFuncao ? $inCodEspecialidadeFuncao : $_REQUEST["stEspecialidadeFuncao"];
    $stDataProgressao         = $stDataProgressao         ? $stDataProgressao         : $_REQUEST["dtDataProgressao"];
    $stHorasMensais           = "";
    $stHorasSemanais          = "";
    $inCodPadrao              = "";
    $inCodProgressao          = "";
    $nuSalario                = "";

    $js = limpaInformacoesSalariais();

    //Se posssui CodFuncao pode-se buscar pelo padrão e calcular o salário.
    if ($inCodFuncao) {
        $obRPessoalCargo = new RPessoalCargo;

        // Para ver se o cargo possui especialidades
        $obRPessoalCargo->addCargoSubDivisao();
        $obRPessoalCargo->setCodCargo( $inCodFuncao );
        $obRPessoalCargo->addEspecialidade();
        $obRPessoalCargo->roUltimoEspecialidade->consultaEspecialidadeCargo( $rsEspecialidades );

        //Se Cargo da função tem especialidade
        if ( $rsEspecialidades->getNumLinhas() > 0 ) {
            //Se está setado o cod da especialidade
            if ($inCodEspecialidadeFuncao) {
                $obRPessoalCargo->roUltimoEspecialidade->setCodEspecialidade( $inCodEspecialidadeFuncao );
                $obRPessoalCargo->roUltimoEspecialidade->consultaEspecialidadeCargo( $rsPadraoEspecialidade );
                $rsPadraoEspecialidade->addFormatacao( "horas_mensais" , NUMERIC_BR );
                $rsPadraoEspecialidade->addFormatacao( "horas_semanais", NUMERIC_BR );
                $stHorasMensais           = $rsPadraoEspecialidade->getCampo("horas_mensais");
                $stHorasSemanais          = $rsPadraoEspecialidade->getCampo("horas_semanais");
                $inCodPadrao              = $rsPadraoEspecialidade->getCampo("cod_padrao");
            }
        } else {
            //Cargo da função não tem especialidade
            $obRPessoalCargo->consultaCargoPadrao( $rsPadraoCargo, $boTransacao );                                    
            $rsPadraoCargo->addFormatacao( "horas_mensais" , NUMERIC_BR );
            $rsPadraoCargo->addFormatacao( "horas_semanais", NUMERIC_BR );
            $stHorasMensais           = $rsPadraoCargo->getCampo("horas_mensais");
            $stHorasSemanais          = $rsPadraoCargo->getCampo("horas_semanais");
            $inCodPadrao              = $rsPadraoCargo->getCampo("cod_padrao");
        }

        //Este if garante que se o cargo tem especialidade o código dele esteja setado ou não tenha especialidade
        if ( !( $rsEspecialidades->getNumLinhas() > 0) || ( ($rsEspecialidades->getNumLinhas() > 0) && $inCodEspecialidadeFuncao ) ) {
            //O valor aqui setado será usado na função calculaSalario,
            // quando não for passado nenhum parametro de horas mensais a ela            
            $_REQUEST["stHorasMensais"] = $stHorasMensais;
            $js .= preencheProgressao($inCodPadrao);

            $js .= "f.stHorasMensais.value = '".$stHorasMensais."'; \n";
            $js .= "f.stHorasSemanais.value = '".$stHorasSemanais."'; \n";
            $js .= "f.inCodPadrao.value = '".$inCodPadrao."'; \n";
            $js .= "f.stPadrao.value = '".$inCodPadrao."'; \n";
        }
    }
    if ($_REQUEST['stAcao'] == "alterar") {
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php" );
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
        $dtCompetenciaFinal = $rsPeriodoMovimentacao->getCampo("dt_final");
        $js .= "f.dtDataAlteracaoFuncao.value = '".$dtCompetenciaFinal."';  \n";
    }

    return $js;
}

// function comparaComDataNascimento($stCampo,$stRotulo)
// {
//     $obRPessoalServidor = new RPessoalServidor();
    
//     $inNumCGM = explode("-", $_REQUEST['hdnCGM']);
//     $obRPessoalServidor->obRCGMPessoaFisica->setNumCGM( $inNumCGM[0] );
//     $obRPessoalServidor->obRCGMPessoaFisica->consultarCGM($rsCGM);

//     if ( $rsCGM->getCampo("dt_nascimento") != "" ) {
//         $arDataNascimento = explode("-",$rsCGM->getCampo("dt_nascimento"));
//         $stDataNascimento = $arDataNascimento[2]."/".$arDataNascimento[1]."/".$arDataNascimento[0];
//     }

//     $dtComparacao = $_REQUEST[$stCampo];
//     $dtNascimento = $stDataNascimento;
//     $stJs = "";
//     if ($dtNascimento == "") {
//         $stMensagem = "campo Data de Nascimento da Guia Identificação inválido()!";
//         $stJs .= "f.".$stCampo.".value = '';\n";
//         $stJs .= "alertaAviso('$stMensagem','form','erro','".Sessao::getId()."');       \n";
//     } else {
//         if ( $dtComparacao != "" and sistemaLegado::comparaDatas($dtNascimento,$dtComparacao) ) {
//             $stMensagem = $stRotulo." (".$dtComparacao.") não pode ser anterior à Data de Nascimento(".$dtNascimento.")!";
//             $stJs .= "f.".$stCampo.".value = '';\n";
//             $stJs .= "alertaAviso('$stMensagem','form','erro','".Sessao::getId()."');       \n";
//         }
//     }

//     return $stJs;
// }

function preencheProgressao($inCodPadrao)
{
     include_once ( CAM_GRH_PES_MAPEAMENTO.'FRecuperaQuantidadeMesesProgressaoAfastamento.class.php');
    // $stValida = comparaComDataNascimento("dtDataProgressao","Data Início para Progressão");
    // if ($stValida != "") {
    //     $stJs .= $stValida;
    // } else {
        include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPadrao.class.php" );
        $inCodProgressao  = "";
        $stLblProgressao  = "&nbsp;";
        //$inCodPadrao      = $_REQUEST['inCodPadrao'];
        $stDataProgressao = $_REQUEST['dtDataProgressao'];
        if ($inCodPadrao != "" and $stDataProgressao != "") {
            //calcula diferença de meses entre datas
            $stDataProgressao    = explode('/',$stDataProgressao);

            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
            $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
            $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);

            $dtDataAtual = explode('/',$rsPeriodoMovimentacao->getCampo("dt_final"));

            if (isset($_REQUEST['inCodContrato'])) {
                $inCodContrato = $_REQUEST['inCodContrato'];
            } else {
               $inCodContrato = 0;
            }

            $rsQtdMeses = new RecordSet;
            $obFPessoalRecuperaQuantidadeMesesProgressaoAfastamento = new FPessoalRecuperaQuantidadeMesesProgressaoAfastamento;
            $obFPessoalRecuperaQuantidadeMesesProgressaoAfastamento->setDado('cod_contrato', $inCodContrato);
            $obFPessoalRecuperaQuantidadeMesesProgressaoAfastamento->setDado('dt_inicial', $stDataProgressao[2]."-".$stDataProgressao[1]."-".$stDataProgressao[0]);
            $obFPessoalRecuperaQuantidadeMesesProgressaoAfastamento->setDado('dt_final', $dtDataAtual[2]."-".$dtDataAtual[1]."-".$dtDataAtual[0]);
            $obFPessoalRecuperaQuantidadeMesesProgressaoAfastamento->recuperaMesesProgressaoAfastamento($rsQtdMeses);

            $arQtdMeses = $rsQtdMeses->arElementos;
            //Lista as progressões, a última progressão do rsProgressao é a progressão do padrão para esta data de início de progressão
            $obRFolhaPagamentoPadrao = new RFolhaPagamentoPadrao;
            $obRFolhaPagamentoPadrao->setCodPadrao( $inCodPadrao );
            $obRFolhaPagamentoPadrao->addNivelPadrao();
            $obRFolhaPagamentoPadrao->roUltimoNivelPadrao->setQtdMeses( $arQtdMeses[0]['retorno'] );
            $obRFolhaPagamentoPadrao->roUltimoNivelPadrao->listarNivelPadrao( $rsProgressao );
            $rsProgressao->setUltimoElemento();
            if ( $rsProgressao->getNumLinhas() > 0 ) {
                $stLblProgressao = $rsProgressao->getCampo('cod_nivel_padrao')." - ".$rsProgressao->getCampo('descricao');
                $inCodProgressao = $rsProgressao->getCampo('cod_nivel_padrao');
            }
        }
        $stJs .= calculaSalario( $inCodPadrao, $inCodProgressao );

        $stJs .= "d.getElementById('stlblProgressao').innerHTML = '".$stLblProgressao."'; \n";
        $stJs .= "f.inCodProgressao.value = '".$inCodProgressao."'; \n";
    //}

    return $stJs;
}

function calculaSalario($inCodPadrao = "", $inCodProgressao = "", $inHorasMensais = "")
{
    include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPadrao.class.php" );
    //O valor do $_REQUEST["stHorasMensais"] é setado na função preencheInformacoesSalariais
    $inHorasMensais = $inHorasMensais != ""   ? $inHorasMensais   : $_REQUEST["stHorasMensais"];
    //Para quando o calculaSalario é chamado sem ter passado pelo preencheInformacoesSalariais
    $inHorasMensais = $inHorasMensais   ? $inHorasMensais   : $_REQUEST["stHorasMensais"];
    $nuSalario = "";

    if ($inCodPadrao != "") {
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPadrao.class.php");
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodo);

        $obTFolhaPagamentoPadrao = new TFolhaPagamentoPadrao();
        $stFiltro = " AND FPP.cod_padrao = ".$inCodPadrao;
        $stFiltro .= " AND FPP.vigencia <= to_date('".$rsPeriodo->getCampo("dt_final")."','dd/mm/yyyy')";
        $obTFolhaPagamentoPadrao->recuperaRelacionamento($rsPadrao,$stFiltro);

        $inHorasMensaisPadrao = ($rsPadrao->getCampo('horas_mensais') > 0.00) ? $rsPadrao->getCampo('horas_mensais') : 1;
        if ($inCodProgressao != "") {
            $obRFolhaPagamentoPadrao = new RFolhaPagamentoPadrao;
            $obRFolhaPagamentoPadrao->setCodPadrao( $inCodPadrao );
            $obRFolhaPagamentoPadrao->addNivelPadrao();
            $obRFolhaPagamentoPadrao->roUltimoNivelPadrao->setCodNivelPadrao( $inCodProgressao);
            $obRFolhaPagamentoPadrao->roUltimoNivelPadrao->listarNivelPadrao( $rsProgressao );
            $nuSalarioPadrao      = $rsProgressao->getCampo('valor');
            $nuSalarioHoraPadrao  = $nuSalarioPadrao / $inHorasMensaisPadrao;
            $nuSalario            = $nuSalarioHoraPadrao * $inHorasMensais;
        } else {
            $nuSalarioPadrao      = $rsPadrao->getCampo('valor');
            $nuSalarioHoraPadrao  = $nuSalarioPadrao / $inHorasMensaisPadrao;
            $nuSalario            = $nuSalarioHoraPadrao * $inHorasMensais;
        }
    }

    if ($nuSalario != '') {
        $nuSalario = number_format($nuSalario, 2, ',','.');
        $stJs .= "f.inSalario.value = '".$nuSalario."'; \n";

        return $stJs;
    }
}

function validarVigenciaSalario()
{
    $stValida = comparaComDataNascimento("dtVigenciaSalario","Vigência do Salário");
    if ($stValida != "") {
        $stJs .= $stValida;
    } else {
        if ( sistemaLegado::comparaDatas(Sessao::read('dtVigenciaSalario'),$_REQUEST['dtVigenciaSalario']) ) {
            $stMensagem = "A vigência deve ser posterior a ".Sessao::read('dtVigenciaSalario');
            $stJs .= "alertaAviso('$stMensagem','form','erro','".Sessao::getId()."');       \n";
            $stJs .= "f.dtVigenciaSalario.value = '".Sessao::read('dtVigenciaSalario')."';";
        }
    }

    return $stJs;
}

function limpaInformacoesSalariais()
{
    $js .= "f.inCodPadrao.value = '';      \n";
    $js .= "f.stPadrao.value = '';         \n";
    $js .= "f.stHorasMensais.value = '';   \n";
    $js .= "f.stHorasSemanais.value = '';  \n";
    $js .= "f.inSalario.value = '';        \n";
    $js .= "d.getElementById('stlblProgressao').innerHTML = '&nbsp;'; \n";
    $js .= "f.inCodProgressao.value = ''; \n";

    return $js;
}


function preencheSubDivisaoAlterar()
{
    $arDados = Sessao::read('arDados');
    $inCodRegime = $arDados['inCodRegime'];
    $inCodSubDivisao = $arDados['inCodSubDivisao'];
    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->setCodContrato( $_REQUEST['inContrato'] );
    $stFiltro = " AND pr.cod_regime = ".$inCodRegime;        
    
    if ($inCodRegime) {
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegime->addPessoalSubDivisao();
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegime->roUltimoPessoalSubDivisao->listarSubDivisao( $rsSubDivisao, $stFiltro,"", $boTransacao );
        $inContador = 1;
        while ( !$rsSubDivisao->eof() ) {
            if ($inCodSubDivisao == $rsSubDivisao->getCampo( "cod_sub_divisao" )) {
                $stSelected = "selected";
                $stJs .= "f.inCodSubDivisao.value = '".$inCodSubDivisao."'; \n";
            } else {
                $stSelected = "";
            }
            $stJs .= "f.stSubDivisao.options[$inContador] = new Option('".$rsSubDivisao->getCampo( "nom_sub_divisao" )."','".$rsSubDivisao->getCampo( "cod_sub_divisao" )."','".$stSelected."'); \n";
            $inContador++;
            $rsSubDivisao->proximo();
        }
        $stJs .= "f.stSubDivisao.value = '".$inCodSubDivisao."'; \n";
    }

    return $stJs;
}

function preencheCargoAlterar()
{
    $arDados = Sessao::read('arDados');
    $inCodSubDivisao = $arDados['inCodSubDivisao'];
    $inCodCargo = $arDados['inCodCargo'];

    $obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->setCodContrato( $_REQUEST["inContrato"] );
    if ($inCodSubDivisao) {
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addCargoSubDivisao();
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao($inCodSubDivisao);
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setCodCargo($inCodCargo);
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setBuscarCargosNormasVencidas(true);
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->listarCargosPorSubDivisaoServidor($rsCargo);
        $inContador = 1;
        $boDesbloqueia = false;
        while ( !$rsCargo->eof() ) {
            if ($inCodCargo == $rsCargo->getCampo( "cod_cargo" )) {
                $obRFolhaPagamentoPeriodoMovimentacao->listarUltimaMovimentacao($rsUltimaMovimentacao);
                $boComparaDatas = SistemaLegado::comparaDatas($rsUltimaMovimentacao->getCampo('dt_final'), $rsCargo->getCampo('dt_publicacao'), true);

                if ($boComparaDatas === false || ($rsCargo->getCampo('dt_termino') !== null && $rsCargo->getCampo('dt_termino') >= $rsUltimaMovimentacao->getCampo('dt_inicial'))) {
                    $boDesbloqueia = true;
                }

                $stSelected = "selected";
            } else {
                $stSelected = "";
            }
            $stJs .= "f.stCargo.options[$inContador] = new Option('".$rsCargo->getCampo( "descricao" )."','".$rsCargo->getCampo( "cod_cargo" )."','".$stSelected."'); \n";
            $inContador++;
            $rsCargo->proximo();
        }
        $stJs .= "f.inCodCargo.value = '".$inCodCargo."'; \n";
        $stJs .= "f.stCargo.value = '".$inCodCargo."'; \n";

    }

    return $stJs;
}

function preencheEspecialidadeAlterar()
{
    $arDados = Sessao::read('arDados');
    $inCodSubDivisao = $arDados['inCodSubDivisao'];
    $inCodCargo = $arDados['inCodCargo'];
    $inCodEspecialidadeCargo = $arDados['inCodEspecialidadeCargo'];

    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addEspecialidade();
    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addCargoSubDivisao();
    if ($inCodSubDivisao) {
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao( $inCodSubDivisao );
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setCodCargo( $inCodCargo );
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoEspecialidade->consultaEspecialidadeCargo( $rsEspecialidade );
        $inContador = 1;
        while ( !$rsEspecialidade->eof() ) {
            $inCodEspecialidade = $rsEspecialidade->getCampo( "cod_especialidade" );
            $stEspecialidade    = $rsEspecialidade->getCampo( "descricao_especialidade" );
            if ($inCodEspecialidade == $inCodEspecialidadeCargo) {
                $stSelected = "selected";
            } else {
                $stSelected = "";
            }
            $js .= "f.stEspecialidadeCargo.options[$inContador] = new Option('".$stEspecialidade."','".$inCodEspecialidade."','".$stSelected."'); \n";
            $inContador++;
            $rsEspecialidade->proximo();
        }
    } else {
        sistemaLegado::exibeAviso("Deve ser selecionada uma subdivisão."," "," ");
    }
    $stJs .= $js;

    return $stJs;
}

function preencheSubDivisaoFuncaoAlterar()
{
    $arDados = Sessao::read('arDados');
    $inCodRegimeFuncao = $arDados['inCodRegimeFuncao'];
    $inCodSubDivisaoFuncao = $arDados['inCodSubDivisaoFuncao'];
    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->setCodContrato( $_REQUEST["inContrato"] );

    $js .= "f.inCodSubDivisaoFuncao.value = '';                                                 \n";
    $js .= "limpaSelect(f.stSubDivisaoFuncao,0);                                                \n";
    $js .= "f.stSubDivisaoFuncao[0] = new Option('Selecione','', 'selected');                   \n";
    $js .= "f.inCodFuncao.value = '';                                                           \n";
    $js .= "limpaSelect(f.stFuncao,0);                                                          \n";
    $js .= "f.stFuncao[0] = new Option('Selecione','', 'selected');                             \n";
    $js .= "f.inCodEspecialidadeFuncao.value = '';                                              \n";
    $js .= "limpaSelect(f.stEspecialidadeFuncao,0);                                             \n";
    $js .= "f.stEspecialidadeFuncao[0] = new Option('Selecione','', 'selected');                \n";
    $stFiltro = " AND pr.cod_regime = ".$inCodRegimeFuncao;
    if ($inCodRegimeFuncao) {
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegime->addPessoalSubDivisao();
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegime->roUltimoPessoalSubDivisao->listarSubDivisao( $rsSubDivisao, $stFiltro,"", $boTransacao );
        $inContador = 1;
        while ( !$rsSubDivisao->eof() ) {
            $inCodSubDivisao  = $rsSubDivisao->getCampo( "cod_sub_divisao" );
            $stSubDivisao     = $rsSubDivisao->getCampo( "nom_sub_divisao" );
            if ($inCodSubDivisao == $inCodSubDivisaoFuncao) {
                $stSelected = "selected";
                $js .= "f.inCodSubDivisaoFuncao.value = '".$inCodSubDivisaoFuncao."'; \n";

            } else {
                $stSelected = "";
            }
            $js .= "f.stSubDivisaoFuncao.options[$inContador] = new Option('".$stSubDivisao."','".$inCodSubDivisao."','".$stSelected."'); \n";
            $inContador++;
            $rsSubDivisao->proximo();
        }
        $js .= "f.stSubDivisaoFuncao.value = '".$inCodSubDivisaoFuncao."'; \n";
    }
    $stJs .= $js;

    return $stJs;
}

function preencheFuncaoAlterar()
{
    $arDados = Sessao::read('arDados');    
    $inCodSubDivisaoFuncao = $arDados['inCodSubDivisaoFuncao'];
    $inCodFuncao  = $arDados['inCodFuncao'];

    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->setCodContrato( $_REQUEST["inContrato"] );
    if ($inCodSubDivisaoFuncao) {
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addCargoSubDivisao();
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao($inCodSubDivisaoFuncao);
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setCodCargo($inCodFuncao);
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setBuscarCargosNormasVencidas(true);
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->listarCargosPorSubDivisaoServidor($rsFuncao);
        $inContador = 1;
        while ( !$rsFuncao->eof() ) {
            if ($inCodFuncao == $rsFuncao->getCampo( "cod_cargo" )) {
                $stSelectedFuncao = "selected";
            } else {
                $stSelectedFuncao = "";
            }
            $stJs .= "f.stFuncao.options[$inContador] = new Option('".$rsFuncao->getCampo( "descricao" )."','".$rsFuncao->getCampo( "cod_cargo" )."','".$stSelectedFuncao."'); \n";
            $inContador++;
            $rsFuncao->proximo();
        }
        $stJs .= "f.inCodFuncao.value = '".$inCodFuncao."'; \n";
        $stJs .= "f.stFuncao.value = '".$inCodFuncao."'; \n";
    }

    return $stJs;
}

function preencheEspecialidadeFuncaoAlterar()
{
    $arDados = Sessao::read('arDados');
    $inCodFuncao  = $arDados['inCodFuncao'];
    $inCodSubDivisaoFuncao = $arDados['inCodSubDivisaoFuncao'];
    $inCodEspecialidadeFuncao = $arDados['inCodEspecialidadeFuncao'];

    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addEspecialidade();
    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addCargoSubDivisao();
    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao( $inCodSubDivisaoFuncao );
    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setCodCargo( $inCodFuncao );
    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoEspecialidade->consultaEspecialidadeCargo( $rsEspecialidade );
    $inContador = 1;
    if ( $rsEspecialidade->getNumLinhas() > 0 ) {
        while ( !$rsEspecialidade->eof() ) {
            $inCodEspecialidade = $rsEspecialidade->getCampo( "cod_especialidade" );
            $stEspecialidade    = $rsEspecialidade->getCampo( "descricao_especialidade" );
            if ($inCodEspecialidade == $inCodEspecialidadeFuncao) {
                $stSelected = "selected";
            } else {
                $stSelected = "";
            }
            $js .= "f.stEspecialidadeFuncao.options[$inContador] = new Option('".$stEspecialidade."','".$inCodEspecialidade."','".$stSelected."'); \n";
            $inContador++;
            $rsEspecialidade->proximo();
        }
        $js .= "f.inCodEspecialidadeFuncao.value = '".$inCodEspecialidadeFuncao."'; \n";
    } else {
        $js .= "f.inCodEspecialidadeFuncao.value = ''; \n";
    }
    $stJs .= $js;

    return $stJs;
}

function preencheProgressaoAlterar()
{
    include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPadrao.class.php" );

    $arDados = Sessao::read('arDados');
    $dtDataProgressao = $arDados['dtDataProgressao'];
    $inCodPadrao      = $arDados['inCodPadrao'];
    
    $inCodProgressao  = "";
    $stLblProgressao  = "&nbsp;";
    $stDataProgressao = ( $stDataProgressao != "" ) ? $stDataProgressao : $dtDataProgressao;
    if ($inCodPadrao && $stDataProgressao) {
        //calcula diferença de meses entre datas -- INICIO
        $stDataProgressao    = explode('/',$stDataProgressao);

        include_once (CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
        include_once ( CAM_GRH_PES_MAPEAMENTO.'FRecuperaQuantidadeMesesProgressaoAfastamento.class.php');        
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);

        $dtDataAtual = explode('/',$rsPeriodoMovimentacao->getCampo("dt_final"));

        //calcula diferença de meses entre datas -- FIM
        $rsQtdMeses = new RecordSet;
        $obFPessoalRecuperaQuantidadeMesesProgressaoAfastamento = new FPessoalRecuperaQuantidadeMesesProgressaoAfastamento;
        $obFPessoalRecuperaQuantidadeMesesProgressaoAfastamento->setDado('cod_contrato', $_REQUEST['inContrato']);
        $obFPessoalRecuperaQuantidadeMesesProgressaoAfastamento->setDado('dt_inicial', $stDataProgressao[2]."-".$stDataProgressao[1]."-".$stDataProgressao[0]);
        $obFPessoalRecuperaQuantidadeMesesProgressaoAfastamento->setDado('dt_final', $dtDataAtual[2]."-".$dtDataAtual[1]."-".$dtDataAtual[0]);
        $obFPessoalRecuperaQuantidadeMesesProgressaoAfastamento->recuperaMesesProgressaoAfastamento($rsQtdMeses);

        $arQtdMeses = $rsQtdMeses->arElementos;
        //Lista as progressões, a última progressão do rsProgressao é a progressão do padrão para esta data de início de progressão
        $obRFolhaPagamentoPadrao = new RFolhaPagamentoPadrao;
        $obRFolhaPagamentoPadrao->setCodPadrao( $inCodPadrao );
        $obRFolhaPagamentoPadrao->addNivelPadrao();

        $obRFolhaPagamentoPadrao->roUltimoNivelPadrao->setQtdMeses( $arQtdMeses[0]['retorno'] );
        $obRFolhaPagamentoPadrao->roUltimoNivelPadrao->listarNivelPadrao( $rsProgressao );
        $rsProgressao->setUltimoElemento();
        if ( $rsProgressao->getNumLinhas() > 0 ) {
            $stLblProgressao = $rsProgressao->getCampo('cod_nivel_padrao')." - ".$rsProgressao->getCampo('descricao');
            $inCodProgressao = $rsProgressao->getCampo('cod_nivel_padrao');
        }
    }
    
    $stJs .= "d.getElementById('stlblProgressao').innerHTML = '".$stLblProgressao."'; \n";
    $stJs .= "f.inCodProgressao.value = '".$inCodProgressao."'; \n";

    return $stJs;

}


function buscaContrato($codMatricula = '', $boCarregaAssentamento = true){
    $codContrato = "";
    $inRegistro = "";
    $stJs = "";
    
    if($codMatricula!=''){
        $obRPessoalServidor = new RPessoalServidor();
        $obRPessoalServidor->addContratoServidor();
        $obRPessoalServidor->roUltimoContratoServidor->setRegistro( $codMatricula );
        $obRPessoalServidor->roUltimoContratoServidor->listarContratos($rsContrato);
        while (!$rsContrato->eof()) {
            $codContrato = $rsContrato->getCampo("cod_contrato");
            $inRegistro = $rsContrato->getCampo("registro");
            $_REQUEST['inCodContrato'] = $codContrato;
            $_REQUEST['inCodMatricula'] = $inRegistro;
            $_REQUEST['inContrato'] = $codMatricula;
            $rsContrato->proximo();
        }
    }
    
    if($boCarregaAssentamento){
        $stJs .= "f.inCodContrato.value = '".$codContrato."';";
        $stJs .= "f.inCodMatricula.value = '".$inRegistro."';";
        $stJs .= processarQuantDiasAssentamento();
    }
    
    return $stJs;
}

switch ($request->get('stCtrl')) {
    case "incluirNorma":
        $stJs .= incluirNorma();
    break;
    case "excluirNorma":
        $stJs .= excluirNorma();
    break;
    case "gerarAssentamento":
        $stJs .= gerarAssentamento();
    break;
    case "gerarAssentamentoFiltro":
        $stJs .= gerarAssentamento(false,"Filtro");
    break;
    case "preencherEspecialidade":
        $stJs .= preencherEspecialidade();
    break;
    case "preencherAssentamento":
        $stJs .= preencherAssentamento();
    break;
    case "preencherLotacao":
        $stJs .= preencherLotacao();
    break;
    case "incluirAssentamento":
        $stJs .= incluirAssentamento();
    break;
    case "alterarAssentamento":
        $stJs .= alterarAssentamento();
    break;
    case "excluirAssentamento":
        $stJs .= excluirAssentamento();
    break;
    case "limparAssentamento":
        $stJs .= limparAssentamento();
    break;
    case "montaAlterarAssentamento":
        $stJs .= montaAlterarAssentamento();
    break;
    case "calcularDataFinal":
        $stJs .= calcularDataFinal();
    break;
    case "submeter":
        $stJs .= submeter();
    break;
    case "ajustarQuantidadeDias":
        $stJs .= ajustarQuantidadeDias();
    break;
    case "processarTriadi1":
        Sessao::remove('stDataInicial');
        Sessao::remove('stDataFinal');
        $stJs .= processarTriadi(1);
        break;
    case "processarTriadi2":
        Sessao::remove('stDataInicial');
        Sessao::remove('stDataFinal');
        $stJs .= processarTriadi(2);
        break;
    case "processarTriadi3":
        Sessao::remove('stDataInicial');
        Sessao::remove('stDataFinal');
        $stJs .= processarTriadi(3);
        break;
    case "processarQuantDiasAssentamento":
        $stJs = processarQuantDiasAssentamento();
        break;
    case "MontaNorma":
        $stJs .= MontaNorma();
        break;
    case "preencheClassificacao":
        $stAjaxReturn = preencheClassificacao($_REQUEST['inCod'], $_REQUEST['combo_type']);
        break;
    case "buscaContrato":
        $stJs .= buscaContrato($_REQUEST['inContrato']);
    break;
    case "preencheSubDivisao":
        $stJs .= preencheSubDivisao();
        break;
    case "preencheSubDivisaoFuncao":
        $stJs .= preencheSubDivisaoFuncao();
        break;
    case "preencheCargo":
        $stJs .= preencheCargo();
        break;
    case "preencheFuncao":
        $stJs .= preencheFuncao();
        break;
    case "preencheEspecialidade":
        $stJs .= preencheEspecialidade();
        break;
    case "preencheEspecialidadeFuncao":
        $stJs .= preencheEspecialidadeFuncao();
        break;
    case "preenchePreEspecialidadeFuncao":
        $stJs .= preenchePreEspecialidadeFuncao();
        break;
    case "preencheInformacoesSalariais":
        $stJs .= preencheInformacoesSalariais();
        break;
    // case "validaDataAlteracaoFuncao":
    //     $stJs .= comparaComDataNascimento("dtDataAlteracaoFuncao","Data da Alteração da Função");
    //     break;
    case "preencheProgressao":
        $stJs .= preencheProgressao($_REQUEST['inCodPadrao']);
        break;
    case "calculaSalario":
        $stJs .= calculaSalario($_REQUEST['inCodPadrao'],$_REQUEST['inCodProgressao']);
        break;
    case "validarVigenciaSalario":
        $stJs .= validarVigenciaSalario();
        break;
}

if (isset($stJs)) {
    sistemaLegado::executaFrameOculto($stJs);
}

if (isset($stAjaxReturn)) {
    echo $stAjaxReturn; exit;
}

?>
