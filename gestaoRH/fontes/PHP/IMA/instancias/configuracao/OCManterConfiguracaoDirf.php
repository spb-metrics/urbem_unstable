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
    * Página de Oculto do Configuração Dirf
    * Data de Criação: 22/11/2007

    * @author Diego Lemos de Souza

    * Casos de uso: uc-04.08.14

    $Id: OCManterConfiguracaoDirf.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_IMA_MAPEAMENTO."TIMACodigoDirf.class.php"                                        );
include_once ( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoClassificacaoDespesa.class.php"                      );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoDirf";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function limpar()
{
    $stJs .= "f.reset();\n";

    return $stJs;
}

function incluirPrestadoresDeServico()
{
    $arPrestadoresServico = Sessao::read("arPrestadoresServico");

    $obErro = validarPrestadoresDeServico("incluir");

    if (!$obErro->ocorreu()) {
        $stFiltro  = " WHERE cod_dirf = ".$_GET["inCodDIRF"];
        $stFiltro .= "   AND tipo = '".trim($_GET["stTipoPrestador"])."'";
        $stFiltro .= "   AND exercicio = '".$_GET["inExercicio"]."'";

        $obTIMACodigoDirf = new TIMACodigoDirf();
        $obTIMACodigoDirf->recuperaCodigosDIRF($rsCodigoDirf, $stFiltro);

        $stFiltro  = "  AND trim(mascara_classificacao) = '".trim($_GET["inCodDespesa"])."' ";
        $stFiltro .= "  AND exercicio = '".$_GET["inExercicio"]."'";
        $obTOrcamentoClassificacaoDespesa = new TOrcamentoClassificacaoDespesa;
        $obTOrcamentoClassificacaoDespesa->recuperaRelacionamento($rsClassificacaoDespesa, $stFiltro);

        $arElementos = array();
        $arElementos["inId"]                       = count($arPrestadoresServico) + 1;
        $arElementos["tipo"]                       = $rsCodigoDirf->getCampo("tipo");
        $arElementos["tipo_formatado"]             = $rsCodigoDirf->getCampo("tipo_formatado");
        $arElementos["codigo_retencao"]            = $rsCodigoDirf->getCampo("cod_dirf");
        $arElementos["descricao_retencao"]         = $rsCodigoDirf->getCampo("descricao");
        $arElementos["desdobramento"]              = $_GET["inCodDespesa"];
        $arElementos["descricao_elemento_despesa"] = $rsClassificacaoDespesa->getCampo("descricao");
        $arElementos["cod_conta_despesa"]          = $rsClassificacaoDespesa->getCampo("cod_conta");
        $arElementos["cod_prestador"]              = "";

        $arPrestadoresServico[] = $arElementos;
        Sessao::write("arPrestadoresServico", $arPrestadoresServico);

        $stJs .= montaListaPrestadoresServico();
    } else {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function montaListaPlanoSaude()
{
    $arRecordSet = Sessao::read("arPlanoSaude");
    $rsRecordSet = new RecordSet;
    $rsRecordSet->preenche( $arRecordSet );

    $stLink .= "&stAcao=".$_REQUEST['stAcao'];

    if ($rsRecordSet->getNumLinhas() != 0 ) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo( "Lista Plano Privado de Assistência à Saúde" );
        $obLista->setRecordSet( $rsRecordSet );

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Registro ANS" );
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "CGM Plano Saúde" );
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Evento" );
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Ação" );
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "[inRegistro]");
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "[inCGMPlanoSaude] - [stNomCGMPlanoSaude]");
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "[inCodigoEventoPlanoSaude] - [stNomEventoPlanoSaude]");
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncaoAjax( true );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('excluirPlanoSaude');");
        $obLista->commitAcao();

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
    }

    $stJs = "jQuery('#spnListaPlanoSaude').html('".$stHtml."');";

    return $stJs;
}

function montaListaPrestadoresServico()
{
    $arRecordSet = Sessao::read("arPrestadoresServico");
    $rsRecordSet = new Recordset;
    $rsRecordSet->preenche( is_array($arRecordSet) ? $arRecordSet : array() );
    $rsRecordSet->ordena("desdobramento");

    $stLink .= "&stAcao=".$_REQUEST["stAcao"];

    if ($rsRecordSet->getNumLinhas() != 0) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo( "Lista de Configuração - Prestadores de Serviço" );
        $obLista->setRecordSet( $rsRecordSet );

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Desdobramento" );
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Descrição" );
        $obLista->ultimoCabecalho->setWidth( 40 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Tipo de Prestador" );
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Código Retenção" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Ação" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "[desdobramento]");
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "[descricao_elemento_despesa]");
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "[tipo_formatado]");
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "[codigo_retencao]");
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "ALTERAR" );
        $obLista->ultimaAcao->setLinkId("alterar");
        $obLista->ultimaAcao->setFuncaoAjax( true );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('carregarPrestadoresDeServico');");
        $obLista->commitAcao();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncaoAjax( true );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('excluirPrestadoresDeServico');");
        $obLista->commitAcao();

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
    }
    $stJs = "jQuery('#spnListaPrestadoresServico').html('".$stHtml."');";
    $stJs.= limparPlanoSaude();

    return $stJs;
}

function carregarPrestadoresDeServico()
{
    $arPrestadoresServico = Sessao::read("arPrestadoresServico");
    $inId = $_REQUEST["inId"];

    foreach ($arPrestadoresServico as $chave => $dados) {
        if (trim($inId) == trim($dados["inId"])) {
            $stJs .= " jQuery('#inCodDIRF').val('".$dados["codigo_retencao"]."');                       \n";
            $stJs .= " jQuery('#inCodDespesa').val('".$dados["desdobramento"]."');                      \n";
            $stJs .= " jQuery('#stTipoPrestador').val('".$dados["tipo"]."');                            \n";
            $stJs .= " jQuery('#stDescricaoDespesa').html('".$dados["descricao_elemento_despesa"]."');  \n";
            $stJs .= " jQuery('#stCodDIRF').html('".$dados["descricao_retencao"]."');                   \n";
            $stJs .= " jQuery('#inId').val('".$dados["inId"]."');                                       \n";
        }
    }
    $stJs .= " jQuery('#btIncluirPrestadoresDeServico').attr('disabled', 'disabled');        \n";
    $stJs .= " jQuery('#btAlterarPrestadoresDeServico').attr('disabled', '');                \n";
    $stJs .= " jQuery('#btAlterarPrestadoresDeServico').attr('onClick', 'if ( ValidaPrestadoresDeServico() ) { montaParametrosGET(\'alterarPrestadoresDeServico\'); }' );";

    return $stJs;
}

function alterarPrestadoresDeServico()
{
    $inId                 = $_REQUEST["inId"];
    $arPrestadoresServico = Sessao::read('arPrestadoresServico');

    $obErro = validarPrestadoresDeServico("alterar");

    if (!$obErro->ocorreu()) {
        foreach ($arPrestadoresServico as $campo => $valor) {
            if (trim($valor["inId"]) == trim($inId)) {

                $stFiltro  = " WHERE cod_dirf = ".$_GET["inCodDIRF"];
                $stFiltro .= "   AND tipo = '".trim($_GET["stTipoPrestador"])."'";
                $stFiltro .= "   AND exercicio = ".$_GET["inExercicio"];

                $obTIMACodigoDirf = new TIMACodigoDirf();
                $obTIMACodigoDirf->recuperaCodigosDIRF($rsCodigoDirf, $stFiltro);

                $stFiltro  = "  AND trim(mascara_classificacao) = trim('".trim($_GET["inCodDespesa"])."') ";
                $stFiltro .= "  AND exercicio = ".$_GET["inExercicio"];
                $obTOrcamentoClassificacaoDespesa = new TOrcamentoClassificacaoDespesa;
                $obTOrcamentoClassificacaoDespesa->recuperaRelacionamento($rsClassificacaoDespesa, $stFiltro);

                $arPrestadoresServico[$campo]["tipo"]                       = $rsCodigoDirf->getCampo("tipo");
                $arPrestadoresServico[$campo]["tipo_formatado"]             = $rsCodigoDirf->getCampo("tipo_formatado");
                $arPrestadoresServico[$campo]["codigo_retencao"]            = $rsCodigoDirf->getCampo("cod_dirf");
                $arPrestadoresServico[$campo]["descricao_retencao"]         = $rsCodigoDirf->getCampo("descricao");
                $arPrestadoresServico[$campo]["desdobramento"]              = $_GET["inCodDespesa"];
                $arPrestadoresServico[$campo]["descricao_elemento_despesa"] = $rsClassificacaoDespesa->getCampo("descricao");
                $arPrestadoresServico[$campo]["cod_conta_despesa"]          = $rsClassificacaoDespesa->getCampo("cod_conta");
            }
        }
        Sessao::write('arPrestadoresServico', $arPrestadoresServico);
        $stJs .= montaListaPrestadoresServico();
        $stJs .= " limpaFormularioPrestadoresDeServico(); \n";
    } else {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function excluirPrestadoresDeServico()
{
    $arTMP = array ();
    $id = $_GET["inId"];

    $arPrestadoresServico = Sessao::read("arPrestadoresServico");
    Sessao::remove("arPrestadoresServico");

    foreach ($arPrestadoresServico as $campo => $valor) {
        if ($valor["inId"] != $id) {
            $arTMP[] = $valor;
        }
    }
    Sessao::write("arPrestadoresServico", $arTMP);
    $stJs = montaListaPrestadoresServico();

    return $stJs;
}

function excluirPlanoSaude()
{
    $arTMP = array();
    $id = $_GET['inId'];

    $arPlanoSaude = Sessao::read("arPlanoSaude");
    Sessao::remove('arPlanoSaude');

    foreach ($arPlanoSaude as $campo => $valor) {
        if ($valor['inId'] != $id) {
            $arTMP[] = $valor;
        }
    }
    Sessao::write('arPlanoSaude', $arTMP);
    $stJs = montaListaPlanoSaude();

    return $stJs;
}

function validarPrestadoresDeServico($origem)
{
    $obErro               = new Erro();
    $arPrestadoresServico = Sessao::read('arPrestadoresServico');
    $inTotal              = 0;

    if (is_array($arPrestadoresServico) && count($arPrestadoresServico)>0) {
        foreach ($arPrestadoresServico as $campo => $valor) {
            if (trim($origem)=="alterar") {
                if (trim($_GET["inCodDIRF"])==trim($valor["codigo_retencao"])
                    && trim($_GET["inCodDespesa"])==trim($valor["desdobramento"])
                    && trim($_GET["inId"]) != trim($valor["inId"])) {
                        $obErro->setDescricao($obErro->getDescricao()."@O filtro informado já está na lista de prestadores de serviço.");
                }
           } else {
                if (trim($_GET["inCodDIRF"])==trim($valor["codigo_retencao"])
                    && trim($_GET["inCodDespesa"])==trim($valor["desdobramento"])) {
                    $obErro->setDescricao($obErro->getDescricao()."@O filtro informado já está na lista de prestadores de serviço.");
               }
            }
        }
    }

    return $obErro;
}

function validarPopUpCodigoRetencao()
{
    $obErro = new Erro();

    if ($_GET["inExercicio"] == "") {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Exercício inválido!");
    }

    if ($_GET["stTipoPrestador"] == "") {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Tipo de Prestador inválido!");
    }

    if (!$obErro->ocorreu()) {
        $stJs .= "abrePopUp('".CAM_GRH_IMA_POPUPS."configuracao/FLProcurarRetencaoDIRF.php','frm', 'inCodDIRF','stCodDIRF','&inExercicio='+document.getElementById('inExercicio').value+'&stTipoPrestador='+document.getElementById('stTipoPrestador').value,'".Sessao::getId()."','800','550');";
    } else {
        $stJs .= "d.getElementById('stCodDIRF').innerHTML = '&nbsp;';\n";
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function limpaCodigoRetencao()
{
    $stJs  = " jQuery('#inCodDIRF').val('');         \n";
    $stJs .= " jQuery('#stCodDIRF').html('&nbsp;');  \n";

    return $stJs;
}

function limpaCodigosExercicio()
{
    if (trim($_GET["stAcao"])=="incluir") {
        Sessao::remove("arPrestadoresServico");
        $stJs .= montaListaPrestadoresServico();
    }
    $stJs .= limpaCodigoRetencao();
    $stJs .= " jQuery('#inCodDespesa').val('');                     \n";
    $stJs .= " jQuery('#stDescricaoDespesa').html('&nbsp;');        \n";
    $stJs .= " jQuery('#inCodClassificacaoIRRF').val('');           \n";
    $stJs .= " jQuery('#stCodClassificacaoIRRF').html('&nbsp;');    \n";

    return $stJs;
}

function mascaraClassificacaoElementoDespesa()
{
    include_once CAM_GF_EMP_NEGOCIO."REmpenhoAutorizacaoEmpenho.class.php";

    $inExercicio = (trim($_GET["inExercicio"])==""?Sessao::getExercicio():$_GET["inExercicio"]);

    $obREmpenhoPreEmpenho = new REmpenhoPreEmpenho;
    $obREmpenhoPreEmpenho->setExercicio( $inExercicio  );

    //Monta mascara da RUBRICA(ELEMENTO) DE DESPESA
    $arMascClassificacao = Mascara::validaMascaraDinamica( $_GET['stMascClassificacao'] , $_GET['inCodDespesa'] );

    //busca descrição do elemento de despesa
    $obREmpenhoPreEmpenho->obROrcamentoClassificacaoDespesa->setMascara              ( $_GET['stMascClassificacao'] );
    $obREmpenhoPreEmpenho->obROrcamentoClassificacaoDespesa->setMascClassificacao    ( $arMascClassificacao[1]      );
    $obREmpenhoPreEmpenho->obROrcamentoClassificacaoDespesa->setExercicio            ( $inExercicio                 );
    $obREmpenhoPreEmpenho->obROrcamentoClassificacaoDespesa->recuperaDescricaoDespesa( $stDescricao                 );

    $stJs .= "jQuery('#inCodDespesa').val('".$arMascClassificacao[1]."'); \n";

    if ($stDescricao != "") {
        $stJs .= "jQuery('#stDescricaoDespesa').html('".$stDescricao."');     \n";
    } else {
        $null = "&nbsp;";
        $stJs .= "jQuery('#inCodDespesa').val('');                      \n";
        $stJs .= "jQuery('#stDescricaoDespesa').html('".$null."');      \n";
        $stJs .= "alertaAviso('@Elemento de despesa é inválido para o exercício de ".$inExercicio.". (".$arMascClassificacao[1].")','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function mascaraClassificacaoIRRF()
{
    include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoReceita.class.php" );
    $obROrcamentoReceita = new ROrcamentoReceita;

    $inExercicio = (trim($_GET["inExercicio"])==""?Sessao::getExercicio():$_GET["inExercicio"]);

    //Monta mascara da RUBRICA(ELEMENTO) DE DESPESA
    $arMascClassificacao = Mascara::validaMascaraDinamica( $_GET['stMascClassificacao'] , $_GET['inCodClassificacaoIRRF'] );

    $obROrcamentoReceita->obROrcamentoClassificacaoReceita->setMascara              ( $_GET['stMascClassificacao'] );
    $obROrcamentoReceita->obROrcamentoClassificacaoReceita->setMascClassificacao    ( $arMascClassificacao[1]       );
    $obROrcamentoReceita->obROrcamentoClassificacaoReceita->setExercicio            ( $inExercicio                 );
    $obROrcamentoReceita->obROrcamentoClassificacaoReceita->recuperaDescricaoReceita( $stDescricao );

    $stJs .= "jQuery('#inCodClassificacaoIRRF').val('".$arMascClassificacao[1]."'); \n";

    if ($stDescricao != "") {
        $stJs .= "jQuery('#stCodClassificacaoIRRF').html('".$stDescricao."');     \n";
    } else {
        $null = "&nbsp;";
        $stJs .= "jQuery('#inCodClassificacaoIRRF').val('');                \n";
        $stJs .= "jQuery('#stCodClassificacaoIRRF').html('".$null."');      \n";
        $stJs .= "alertaAviso('@Código Classificação IRRF é inválido para o exercício de ".$inExercicio.". (".$arMascClassificacao[1].")','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function carregaPlanoSaude()
{
    $arPlanoSaude = Sessao::read('arPlanoSaude');
    $stChave = $_REQUEST['inExercicio'].'-'.$_REQUEST['inCGMPlanoSaude'].'-'.$_REQUEST['inRegistro'];
    foreach ($arPlanoSaude as $key => $arDados) {
        $stComp = $_REQUEST['inExercicio'].'-'.$arDados['inCGMPlanoSaude'].'-'.$arDados['inRegistro'];
        if ($stChave == $stComp) {
            return false;
        }
    }
    $arPlanoSaude[] = array( 'inCGMPlanoSaude' => $_REQUEST['inCGMPlanoSaude'],
                             'stNomCGMPlanoSaude' => $_REQUEST['stNomCGMPlanoSaude'],
                             'inRegistro' => $_REQUEST['inRegistro'],
                             'inCodigoEventoPlanoSaude' => $_REQUEST['inCodigoEventoPlanoSaude'],
                             'stNomEventoPlanoSaude' => $_REQUEST['HdninCodigoEventoPlanoSaude']
                        );
    Sessao::write('arPlanoSaude', $arPlanoSaude);

    return true;
}

function limparPlanoSaude()
{
    $stJs  = "jQuery('#inCGMPlanoSaude').val('');\n";
    $stJs .= "jQuery('#stNomCGMPlanoSaude').html('&nbsp;');\n";
    $stJs .= "jQuery('#inRegistro').val('');\n";
    $stJs .= "jQuery('#inCodigoEventoPlanoSaude').val('');\n";
    $stJs .= "jQuery('#HdninCodigoEventoPlanoSaude').val('');\n";
    $stJs .= "jQuery('#stEventoPlanoSaude').html('&nbsp;');\n";

    return $stJs;
}

switch ($_GET['stCtrl']) {
    case "incluirPrestadoresDeServico":
        $stJs .= incluirPrestadoresDeServico();
        break;
    case "alterarPrestadoresDeServico":
        $stJs .= alterarPrestadoresDeServico();
        break;
    case "excluirPrestadoresDeServico":
        $stJs .= excluirPrestadoresDeServico();
        break;
    case "carregarPrestadoresDeServico":
        $stJs .= carregarPrestadoresDeServico();
        break;
    case "validarPopUpCodigoRetencao":
        $stJs .= validarPopUpCodigoRetencao();
        break;
    case "limpaCodigoRetencao":
        $stJs .= limpaCodigoRetencao();
        break;
    case "montaListaPrestadoresServico":
        $stJs .= montaListaPrestadoresServico();
        break;
    case "mascaraClassificacaoElementoDespesa":
        $stJs .= mascaraClassificacaoElementoDespesa();
        break;
    case "mascaraClassificacaoIRRF":
        $stJs .= mascaraClassificacaoIRRF();
        break;
    case "limpaCodigosExercicio":
        $stJs .= limpaCodigosExercicio();
        break;
    case "incluirPlanoSaude":
        if ( carregaPlanoSaude() ) {
            $stJs .= montaListaPlanoSaude();
            $stJs .= limparPlanoSaude();
        } else {
            echo "alertaAviso('Já existe as informações na lista','','erro','".Sessao::getId()."');";
        }
        break;
    case "montaListaPlanoSaude":
        $stJs .= montaListaPlanoSaude();
        break;
    case "limparPlanoSaude":
        $stJs .= limparPlanoSaude();
        break;
    case "excluirPlanoSaude":
        $stJs .= excluirPlanoSaude();
        break;
}

if ($stJs) {
    echo $stJs;
}

?>
