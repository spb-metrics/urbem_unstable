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
    * Página de Oculto do Exportação Remessa CaixaEconomicaFederal
    * Data de Criação: 09/10/2007

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: <Alex Cardoso>

    * @ignore

    $Id: OCExportarRemessaCaixaEconomicaFederal.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-04.08.11
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GRH_PES_COMPONENTES."IFiltroContrato.class.php"                                       );
include_once( CAM_GRH_PES_COMPONENTES."IFiltroPensionista.class.php"                                    );
include_once( CAM_GRH_PES_COMPONENTES."IFiltroCGMContrato.class.php"                                    );
include_once( CAM_GRH_PES_COMPONENTES."IFiltroCGMPensionista.class.php"                                    );
include_once( CAM_GRH_PES_COMPONENTES."ISelectMultiploLotacao.class.php"                                );
include_once( CAM_GRH_PES_COMPONENTES."ISelectMultiploLocal.class.php"                                  );

//Define o nome dos arquivos PHP
$stPrograma = "ExportarRemessaCaixaEconomicaFederal";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

###########################LIMPA SPANS#####################################

function limparSpans()
{
    #Cadastro
    $stJs .= "d.getElementById('spnCadastro').innerHTML = '';\n";

    #Ativos / Aposentados / Pensionistas
    $stJs .= gerarSpanAtivosAposentadosPensionistas(false);

    return $stJs;
}

###########################ATIVOS / APOSENTADOS#####################################

function gerarSpanAtivosAposentados()
{
    $stSituacao = $_GET["stSituacao"];

    $stJs .= limparSpans();

    include_once(CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php");
    $obIFiltroComponentes = new IFiltroComponentes();

    if ($_GET["stSituacao"] != 'todos') {
       $obIFiltroComponentes->setMatricula();
       $obIFiltroComponentes->setCGMMatricula();
       $obIFiltroComponentes->setLocal();
       $obIFiltroComponentes->setAtributoServidor();
    }

    $obIFiltroComponentes->setLotacao();
    $obIFiltroComponentes->setFiltroPadrao('geral');

    $obFormulario = new Formulario();

    switch ($stSituacao) {
        case 'ativos':
                $obFormulario->addTitulo("Ativos");
                $obIFiltroComponentes->setAtivos();
            break;
        case 'aposentados':
                $obFormulario->addTitulo("Aposentados");
                $obIFiltroComponentes->setAposentados();
            break;
        case 'rescindidos':
                $obFormulario->addTitulo("Rescindidos");
                $obIFiltroComponentes->setRescisao();
            break;
        case 'todos':
                $obFormulario->addTitulo("Todos");
                $obIFiltroComponentes->setTodos();
            break;
    }

    $obIFiltroComponentes->geraFormulario($obFormulario);
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $stJs .= "d.getElementById('spnCadastro').innerHTML = '$stHtml';\n";
    $stJs .= gerarSpanAtivosAposentadosPensionistas(true);

    return $stJs;
}

###########################PENSIONISTAS#####################################

function gerarSpanPensionistas()
{
    $stSituacao = $_GET["stSituacao"];

    $stJs .= limparSpans();

    include_once(CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php");
    $obIFiltroComponentes = new IFiltroComponentes();
    $obIFiltroComponentes->setMatriculaPensionista();
    $obIFiltroComponentes->setCGMMatriculaPensionista();
    $obIFiltroComponentes->setLocal();
    $obIFiltroComponentes->setLotacao();
    $obIFiltroComponentes->setAtributoPensionista();
    $obIFiltroComponentes->setFiltroPadrao('geral');

    $obFormulario = new Formulario();
    $obFormulario->addTitulo("Pensionistas");
    $obIFiltroComponentes->geraFormulario($obFormulario);
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $stJs .= "d.getElementById('spnCadastro').innerHTML = '$stHtml';\n";
    $stJs .= gerarSpanAtivosAposentadosPensionistas(true);

    return $stJs;
}

###########################ESTAGIARIOS#####################################

function gerarSpanEstagiarios()
{
    $stSituacao = $_GET["stSituacao"];

    $stJs .= limparSpans();

    include_once(CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php");
    $obIFiltroComponentes = new IFiltroComponentes();
    $obIFiltroComponentes->setCGMCodigoEstagio();
    $obIFiltroComponentes->setLocal();
    $obIFiltroComponentes->setLotacao();
    $obIFiltroComponentes->setAtributoEstagiario();
    $obIFiltroComponentes->setFiltroPadrao('geral');

    $obFormulario = new Formulario();
    $obFormulario->addTitulo("Estagiários");
    $obIFiltroComponentes->geraFormulario($obFormulario);
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $stJs .= "d.getElementById('spnCadastro').innerHTML = '$stHtml';\n";

    return $stJs;
}

###########################ATIVOS E APOSENTADOS E PENSIONISTAS #####################################

function gerarSpanAtivosAposentadosPensionistas($boGerar = true)
{
    if ($boGerar) {
        include_once(CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php");
        $obIFiltroCompetencia = new IFiltroCompetencia(true,"",true);

        include_once(CAM_GRH_PES_COMPONENTES."IFiltroTipoFolha.class.php");
        $obIFiltroTipoFolha = new IFiltroTipoFolha();
        $obIFiltroTipoFolha->setMostraDesdobramento(true,"D");
        $obIFiltroTipoFolha->setValorPadrao(1);

        $obFormulario = new Formulario();
        $obIFiltroCompetencia->geraFormulario($obFormulario);
        $obIFiltroTipoFolha->geraFormulario($obFormulario);
        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();
    }
    $stJs .= "d.getElementById('spnAtivosAposentadosPensionistas').innerHTML = '$stHtml';\n";

    return $stJs;
}

###########################UTILS##########################

function submeter()
{
    $obErro = new Erro();

    if ($_GET["stSituacao"] == "") {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Cadastro inválido!()");
    }

    if ($_GET["inTipoMovimento"] == "") {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Tipo de movimento inválido!()");
    }

    if ($_GET['stSituacao'] == 'ativos' or
        $_GET['stSituacao'] == 'aposentados' or
        $_GET["stSituacao"] == 'pensionistas' or
        $_GET["stSituacao"] == 'todos' or
        $_GET["stSituacao"] == 'pensao_judicial' or
        $_GET['stSituacao'] == 'rescindidos') {
        if ( empty($_GET["inCodMes"]) ) {
              $obErro->setDescricao($obErro->getDescricao()."@Campo Mês da Competência inválido!()");
        } else {
            if ( empty($_GET["inAno"]) ) {
                $obErro->setDescricao($obErro->getDescricao()."@Campo Ano da Competência inválido!()");
            }
        }

        //Tipo Folha
        if ($_GET["inCodConfiguracao"] == '') {
            $obErro->setDescricao($obErro->getDescricao()."@Campo Tipo de Cálculo inválido!()");
        } else {
        if ($_GET["inCodConfiguracao"] == 0) {
            if ( empty($_GET["inCodComplementar"]) ) {
            $obErro->setDescricao($obErro->getDescricao()."@Campo Folha Complementar inválido!()");
            }
        } elseif ($_GET["inCodConfiguracao"] == 3) {
            if ( empty($_GET["stDesdobramento"]) ) {
            $obErro->setDescricao($obErro->getDescricao()."@Campo Desdobramento inválido!()");
            }
        }
        }
    }

    if ($_GET["stSituacao"] == "pensao_judicial" or $_GET["stSituacao"] == "todos") {
    if (trim($_GET["stTipoFiltro"])=="") {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Tipo de Filtro inválido!()");
    }
    if ( trim($_GET["inCodConfiguracao"]) == "" ) {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Tipo de Cálculo inválido!()");
    }
    }

    if ($_GET["stSituacao"] == 'ativos' or
        $_GET["stSituacao"] == 'aposentados' or
        $_GET["stSituacao"] == 'pensionistas' or
        $_GET["stSituacao"] == 'rescindidos') {
        switch ($_GET["stTipoFiltro"]) {
            case "":
                $obErro->setDescricao($obErro->getDescricao()."@Campo Tipo de Filtro do Ativos/Aposentados inválido!()");
                break;
            case "contrato":
            case "contrato_rescisao":
            case "cgm_contrato":
            case "cgm_contrato_rescisao":
                if ( count(Sessao::read("arContratos")) == 0 ) {
                    $obErro->setDescricao($obErro->getDescricao()."@A lista de contratos deve possuir pelo menos um contrato!()");
                }
                break;
            case "contrato_pensionista":
            case "cgm_contrato_pensionista":
                if ( count(Sessao::read("arPensionistas")) == 0 ) {
                    $obErro->setDescricao($obErro->getDescricao()."@A lista de contratos deve possuir pelo menos um contrato!()");
                }
                break;
            case "atributo_servidor":
                if ($_GET["inCodAtributo"] == "") {
                    $obErro->setDescricao($obErro->getDescricao()."@Campo Atributo Dinâmico do Ativos/Aposentados inválido!()");
                }
                break;
            case "atributo_pensionista":
                if ($_GET["inCodAtributo"] == "") {
                    $obErro->setDescricao($obErro->getDescricao()."@Campo Atributo Dinâmico do Pensionista inválido!()");
                }
                break;
        }
    }

    if ($_GET["stSituacao"] == 'estagiarios') {
        switch ($_GET["stTipoFiltro"]) {
            case "":
                $obErro->setDescricao($obErro->getDescricao()."@Campo Tipo de Filtro do Estagiário inválido!()");
                break;
            case "cgm_codigo_estagio":
                if ( count(Sessao::read("arEstagios")) == 0 ) {
                    $obErro->setDescricao($obErro->getDescricao()."@A lista de estagiários deve possuir pelo menos um estágio!()");
                }
                break;
            case "atributo_estagiario":
                if ($_GET["inCodAtributo"] == "") {
                    $obErro->setDescricao($obErro->getDescricao()."@Campo Atributo Dinâmico do Estagiário inválido!()");
                }
                break;
        }
    }

    $rsConfiguracaoConvenio = Sessao::read("rsConfiguracaoConvenio");
    if ($rsConfiguracaoConvenio->getNumLinhas() == -1) {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Código do Convênio no Banco inválido!()");
        $obErro->setDescricao($obErro->getDescricao()."@Campo Agência do Convênio inválido!()");
        $obErro->setDescricao($obErro->getDescricao()."@Campo Conta do Convênio inválido!()");
    }

    if ( $obErro->ocorreu() ) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','aviso','".Sessao::getId()."');";
    } else {
        $stJs .= "parent.frames[2].Salvar(); BloqueiaFrames(true,false);\n";
    }

    return $stJs;
}

function limparForm()
{
    $stJs .= "montaParametrosGET('gerarSpanAtivosAposentados','stSituacao');";

    return $stJs;
}

function validarValores()
{
    if ( !($_GET['nuValorLiquidoInicial'] === 0 and $_GET['nuValorLiquidoFinal'] === 0) ) {
        if ($_GET['nuValorLiquidoFinal'] === 0) {
            $_GET['nuValorLiquidoFinal'] = "";
            $stJs .= "f.nuValorLiquidoFinal.value = '';\n";
            if ($_GET['nuValorLiquidoInicial'] >= 0) {
                $stJs .= "alertaAviso('@O valor líquido final deve ser maior que o valor líquido inicial!()','form','aviso','".Sessao::getId()."');";
            }
        }
        if ($_GET['nuValorLiquidoInicial'] != "" and $_GET['nuValorLiquidoFinal'] != "") {
            $nuValorLiquidoInicial = str_replace('.','',$_GET['nuValorLiquidoInicial']);
            $nuValorLiquidoInicial = str_replace(',','.',$nuValorLiquidoInicial);

            $nuValorLiquidoFinal = str_replace('.','',$_GET['nuValorLiquidoFinal']);
            $nuValorLiquidoFinal = str_replace(',','.',$nuValorLiquidoFinal);
            if ($nuValorLiquidoInicial > $nuValorLiquidoFinal) {
                $stJs .= "f.nuValorLiquidoInicial.value = '';\n";
                $stJs .= "f.nuValorLiquidoFinal.value = '';\n";
                $stJs .= "alertaAviso('@O valor líquido inicial deve ser menos que o valor líquido final!()','form','aviso','".Sessao::getId()."');";
            }
        }
    }

    return $stJs;
}

function validarDataPagamento()
{
    $obErro = new Erro();
    $arDtPagamento = explode("/",$_GET["dtPagamento"]);
    $arDtGeracaoArquivo = explode("/",$_GET["dtGeracaoArquivo"]);
    $dtPagamento = $arDtPagamento[2]."-".$arDtPagamento[1]."-".$arDtPagamento[0];
    $dtGeracaoArquivo = $arDtGeracaoArquivo[2]."-".$arDtGeracaoArquivo[1]."-".$arDtGeracaoArquivo[0];
    if ($dtPagamento<$dtGeracaoArquivo) {
        $obErro->setDescricao("O campo Data do Pagamento deve ser superior a Data da Geração Arquivo");
    }
    if ($obErro->ocorreu()) {
        $stJs  = "alertaAviso('@".$obErro->getDescricao()."!()','form','aviso','".Sessao::getId()."');";
        $stJs .= "f.dtPagamento.value = '';\n";
    }

    return $stJs;
}

function gerarSpanPensaoJudicial()
{
    $stSituacao = $_GET["stSituacao"];
    $stJs .= limparSpans();

    include_once(CAM_GRH_PES_COMPONENTES."IFiltroComponentesDependentes.class.php");
    $obIFiltroComponentesDependentes = new IFiltroComponentesDependentes();
    $obIFiltroComponentesDependentes->setCGMDependente();
    $obIFiltroComponentesDependentes->setCGMMatriculaServidorDependente();
    $obIFiltroComponentesDependentes->setFiltroPadrao('geral');

    $obFormulario = new Formulario();
    $obFormulario->addTitulo("Pensão Judicial");

    $obIFiltroComponentesDependentes->geraFormulario($obFormulario);
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $stJs .= "d.getElementById('spnCadastro').innerHTML = '$stHtml';\n";
    $stJs .= gerarSpanComplementar(true);

    return $stJs;
}

function gerarSpanComplementar($boGerar = true)
{
    if ($boGerar) {
    include_once(CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php");
    $obIFiltroCompetencia = new IFiltroCompetencia(true,"",true);

    include_once(CAM_GRH_PES_COMPONENTES."IFiltroTipoFolha.class.php");
    $obIFiltroTipoFolha = new IFiltroTipoFolha();
    $obIFiltroTipoFolha->setMostraDesdobramento(true,"D");
    $obIFiltroTipoFolha->setValorPadrao(1);

        $obFormulario = new Formulario();
    $obIFiltroCompetencia->geraFormulario($obFormulario);
    $obIFiltroTipoFolha->geraFormulario($obFormulario);
    $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();
    }
    $stJs .= "d.getElementById('spnAtivosAposentadosPensionistas').innerHTML = '$stHtml';\n";

    return $stJs;
}

function gerarSpan()
{
    switch ($_REQUEST['stSituacao']) {
    case "ativos":
    case "aposentados":
    case "rescindidos":
    case "todos":
        $stJs .= gerarSpanAtivosAposentados();
        break;
    case "pensionistas":
        $stJs .= gerarSpanPensionistas();
            break;
    case "estagiarios":
        $stJs .= gerarSpanEstagiarios();
            break;
    case "pensao_judicial":
        $stJs .= gerarSpanPensaoJudicial();
        break;
    }

    return $stJs;
}

switch ($_GET['stCtrl']) {
    case "gerarSpan":
    $stJs .= gerarSpan();
    break;
    case "gerarSpanAtivosAposentadosPensionistas":
        $stJs .= gerarSpanAtivosAposentadosPensionistas();
        break;
    case "limparSpans":
        $stJs .= limparSpans();
        break;
    case "limparForm":
        $stJs .= limparForm();
        break;
    case "submeter":
        $stJs .= submeter();
        break;
    case "validarValores":
        $stJs .= validarValores();
        break;
    case "validarDataPagamento":
        $stJs .= validarDataPagamento();
        break;
}

if ($stJs) {
    echo $stJs;
}

?>