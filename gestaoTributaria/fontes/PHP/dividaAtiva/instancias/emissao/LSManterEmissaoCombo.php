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
  * Página de Lista de Emissao
  * Data de criação : 16/08/2007

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

    * $Id: LSManterEmissaoCombo.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.04.03
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaDocumento.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterEmissao";
$pgFilt = "FL".$stPrograma.".php";
$pgForm = "FM".$stPrograma."Combo.php";
$pgList = "LS".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao') ? $request->get('stAcao') : 'incluir';
$stLink = '';

//MANTEM FILTRO E PAGINACAO
//USADO QUANDO EXISTIR FILTRO
$link = Sessao::read( 'link' );
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array($link) ) {
    $link = $link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
}

Sessao::write( 'link', $link );

$boTransacao = new Transacao();
//MONTAGEM DO FILTRO
$stFiltro = "";
/* PARAMETROS VINDOS DA EMISSAO DA INSCRICAO DE DIVIDA */
if ($_REQUEST["inNumeroParcelamento"]) {
    $stFiltro .= " \n AND dp.num_parcelamento = ".$_REQUEST['inNumeroParcelamento'];
    $stLink .= "&inNumeroParcelamento=".$_REQUEST['inNumeroParcelamento'];
}

if ( is_numeric($_REQUEST['inCodTipoDocumento']) ) {
    $stFiltro .= " \n AND ddd.cod_tipo_documento = ".$_REQUEST['inCodTipoDocumento'];
    $stLink .= "&inCodTipoDocumento=".$_REQUEST['inCodTipoDocumento'];

    if ($_REQUEST['stCodDocumento']) {
        $stFiltro2 .= " \n AND ded.num_documento = ".$_REQUEST['stCodDocumento'];
        $stLink .= "&stCodDocumento=".$_REQUEST['stCodDocumento'];
    }

}

if ($_REQUEST['inExercicio']) {
    $stFiltro .= " \n AND ddp.exercicio = ".$_REQUEST['inExercicio'];
    $stLink .= "&inExercicio=".$_REQUEST['inExercicio'];
}

$inCodInscricaoInicial = ($_REQUEST['inCodInscricaoInicial']!='') ? $_REQUEST['inCodInscricaoInicial'] : $_SESSION['inCodInscricaoInicial'];
$inCodInscricaoFinal = (isset($_REQUEST['inCodInscricaoFinal']))? $_REQUEST['inCodInscricaoFinal'] : $_SESSION['inCodInscricaoFinal'];
if ($inCodInscricaoInicial!='' && $inCodInscricaoFinal!='') {
    $arDados = explode( "/", $inCodInscricaoInicial);
    $arDados2 = explode( "/", $inCodInscricaoFinal );
    $stFiltro .= "AND ddp.exercicio 	BETWEEN '".$arDados[1]."' AND '".$arDados2[1]."'
          AND ddp.cod_inscricao BETWEEN ".(INT)$arDados[0]." AND ".(INT)$arDados2[0];

    $stLink .= "&inCodInscricaoInicial=".$inCodInscricaoInicial;
    $stLink .= "&inCodInscricaoFinal=".$inCodInscricaoFinal;
}else
if ($inCodInscricaoInicial!='') {
    $arDados = explode( "/", $inCodInscricaoInicial );
    $stFiltro .= " \n AND ddp.cod_inscricao = ".$arDados[0];
    $stFiltro .= " \n AND ddp.exercicio = '".$arDados[1]."'";
    $stLink .= "&inCodInscricaoInicial=".$inCodInscricaoInicial;
}else
if ($inCodInscricaoFinal!='') {
    $arDados = explode( "/", $inCodInscricaoFinal );
    $stFiltro .= " \n AND ddp.cod_inscricao = ".$arDados[0];
    $stFiltro .= " \n AND ddp.exercicio = '".$arDados[1]."'";
    $stLink .= "&inCodInscricaoFinal=".$inCodInscricaoFinal;
}

$stLink .= "&stTipoModalidade=".$_REQUEST['stTipoModalidade'];
$stLink .= "&stNumDocumento=".$_REQUEST['stNumDocumento'];

//INSCRIÇÃO ECONÔMICA
$stLink .= "&inNumInscricaoEconomicaInicial=".$_REQUEST['inNumInscricaoEconomicaInicial'];
$stLink .= "&inNumInscricaoEconomicaFinal=".$_REQUEST['inNumInscricaoEconomicaFinal'];
if ($_REQUEST['inNumInscricaoEconomicaInicial'] AND $_REQUEST['inNumInscricaoEconomicaFinal']) {
    $stFiltro .= " \n AND dde.inscricao_economica >= ".$_REQUEST['inNumInscricaoEconomicaInicial'];
    $stFiltro .= " \n AND dde.inscricao_economica <= ".$_REQUEST['inNumInscricaoEconomicaFinal'];
}

if ($_REQUEST['inNumInscricaoEconomicaInicial'] AND !$_REQUEST['inNumInscricaoEconomicaFinal']) {
    $stFiltro .= " \n AND dde.inscricao_economica = ".$_REQUEST['inNumInscricaoEconomicaInicial'];
}

//INSCRIÇÃO IMOBILIARIA
$stLink .= "&inCodImovelInicial=".$_REQUEST['inCodImovelInicial'];
$stLink .= "&inCodImovelFinal=".$_REQUEST['inCodImovelFinal'];
if ($_REQUEST['inCodImovelInicial'] AND $_REQUEST['inCodImovelFinal']) {
    $stFiltro .= " \n AND ddi.inscricao_municipal >= ".$_REQUEST['inCodImovelInicial'];
    $stFiltro .= " \n AND ddi.inscricao_municipal <= ".$_REQUEST['inCodImovelFinal'];
}

if ($_REQUEST['inCodImovelInicial'] AND !$_REQUEST['inCodImovelFinal']) {
    $stFiltro .= " \n AND ddi.inscricao_municipal = ".$_REQUEST['inCodImovelInicial'];
}

//CONTRIBUINTES
$stLink .= "&inCodContribuinteInicial=".$_REQUEST['inCodContribuinteInicial'];
$stLink .= "&inCodContribuinteFinal=".$_REQUEST['inCodContribuinteFinal'];
if ($_REQUEST['inCodContribuinteInicial'] AND $_REQUEST['inCodContribuinteFinal']) {
    $stFiltro .= " \n AND ddc.numcgm >= ".$_REQUEST['inCodContribuinteInicial'];
    $stFiltro .= " \n AND ddc.numcgm <= ".$_REQUEST['inCodContribuinteFinal'];
}

if ($_REQUEST['inCodContribuinteInicial'] AND !$_REQUEST['inCodContribuinteFinal']) {
    $stFiltro .= " \n AND ddc.numcgm = ".$_REQUEST['inCodContribuinteInicial'];
}

if ($_REQUEST['stTipoModalidade'] == "emissao") {
    if ($_REQUEST['stCodDocumento']) {
        $stFiltro .= " \n AND tmp_ded.num_documento = ".$_REQUEST['stCodDocumento'];
    }

    if ($_REQUEST['stNumDocumento']) {
        $arTMPd = explode( "/", $_REQUEST['stNumDocumento'] );
        $stFiltro .= " \n AND tmp_ded.num_documento = ".$arTMPd[0]." AND ded.exercicio = '".$arTMPd[1]."'";
    }

    $stFiltro2 = "";
} else {
    $stFiltro2 = "";
    if ($_REQUEST['stCodDocumento']) {
        $stFiltro2 .= " ded.num_documento = ".$_REQUEST['stCodDocumento']." AND ";
    }

    if ($_REQUEST['stNumDocumento']) {
        $arTMPd = explode( "/", $_REQUEST['stNumDocumento'] );
        $stFiltro2 .= " ded.num_documento = ".$arTMPd[0]." AND ded.exercicio = '".$arTMPd[1]."' AND ";
    }

    if ($stFiltro2) {
        if ($_REQUEST['stTipoModalidade'] == "emissao") {
            $stFiltro2 = " WHERE ".$stFiltro2;
        }ELSE{
            $stFiltro2 = " AND ".$stFiltro2;
        }

        $stFiltro2 = substr( $stFiltro2, 0, strlen($stFiltro2) - 4 );
    }
}

Sessao::write('stLink', $stLink);
Sessao::remove( 'dados_emissao' );

$obTDATDividaDocumento = new TDATDividaDocumento;
if ($_REQUEST['stTipoModalidade'] == "emissao") {

    $obTDATDividaDocumento->criaTabelasDocumentos($boTransacao);         
    $obTDATDividaDocumento->recuperaListaDocumentoLScombo( $rsDocumentos, $stFiltro, $stOrdem, $boTransacao );
    $obTDATDividaDocumento->deletaTabelaDocumentos($boTransacao);
    $arTMPdados = $rsDocumentos->getElementos();

    $arTMP2dados = array();
    $arTMPnomes = array();
    $inNomes = 0;
    $inContador = 0;

    for ( $inX=0; $inX<count($arTMPdados); $inX++ ) {
        $boNaLista = false;
        for ($inY=0; $inY<$inNomes; $inY++) {
            if ($arTMPnomes[$inY]["nome_documento"] == $arTMPdados[$inX]["nome_documento"]) {
                $boNaLista = true;
            }
        }

        if (!$boNaLista) {
            $arTMPnomes[$inNomes]["nome_documento"] = $arTMPdados[$inX]["nome_documento"];
            $inNomes++;
        }

        $boNaLista = false;
        for ($inY=0; $inY<$inContador; $inY++) {
            if ($arTMP2dados[$inY]["num_parcelamento"] == $arTMPdados[$inX]["num_parcelamento"]) {
                $arTMP2dados[$inY]["nome_arquivo_agt"] .= "$$".$arTMPdados[$inX]["nome_arquivo_agt"];
                $arTMP2dados[$inY]["nome_arquivo_swx"] .= "$$".$arTMPdados[$inX]["nome_arquivo_swx"];
                $arTMP2dados[$inY]["nome_documento"] .= "$$".$arTMPdados[$inX]["nome_documento"];
                $arTMP2dados[$inY]["cod_documento"] .= "$$".$arTMPdados[$inX]["cod_documento"];
                $arTMP2dados[$inY]["num_emissao"] .= "$$".$arTMPdados[$inX]["num_emissao"];
                $arTMP2dados[$inY]["cod_tipo_documento"] .= "$$".$arTMPdados[$inX]["cod_tipo_documento"];
                $arTMP2dados[$inY]["num_documento"] .= "$$".$arTMPdados[$inX]["num_documento"];

                $boNaLista = true;
                break;
            }
        }

        if (!$boNaLista) {
            $arTMP2dados[$inContador] = $arTMPdados[$inX];
            $inContador++;
        }
    }

    $rsDocumentos->preenche( $arTMP2dados );
} else {
    $stFiltro = str_replace('ddd.','DOCUMENTO.', $stFiltro);
    $stFiltro = str_replace('ddp.','DIVIDA_PARCELAMENTO.', $stFiltro);
    $stFiltro = str_replace('dp.' ,'PARCELAMENTO.', $stFiltro);
    $stFiltro = str_replace('dde.','DIVIDA_EMPRESA.', $stFiltro);
    $stFiltro = str_replace('ddi.','DIVIDA_IMOVEL.', $stFiltro);
    $stFiltro = str_replace('ddc.','DIVIDA_CGM.', $stFiltro);
    $stFiltro = str_replace('ded.','EMISSAO_DOCUMENTO.', $stFiltro);

    $stFiltro2 = str_replace('ded.','EMISSAO_DOCUMENTO.', $stFiltro2);

    $obTDATDividaDocumento->recuperaListaDocumentoReemissaoLScombo( $rsDocumentos, $stFiltro, $stFiltro2 );
    $arTMPdados = $rsDocumentos->getElementos();
    $arTMPnomes = array();
    $inNomes = 0;
    for ( $inX=0; $inX<count($arTMPdados); $inX++ ) {
        $boNaLista = false;
        for ($inY=0; $inY<$inNomes; $inY++) {
            if ($arTMPnomes[$inY]["nome_documento"] == $arTMPdados[$inX]["nome_documento"]) {
                $boNaLista = true;
            }
        }

        if (!$boNaLista) {
            $arTMPnomes[$inNomes]["nome_documento"] = $arTMPdados[$inX]["nome_documento"];
            $inNomes++;
        }
    }
}

$rsModelos = new RecordSet;
$rsModelos->preenche( $arTMPnomes );

$obForm = new Form;
$obForm->setAction ( $pgForm );
$obFormulario = new FormularioAbas;
$obFormulario->addForm  ( $obForm );

$obLista = new Lista;
$obLista->setMostraPaginacao (false );
$obLista->setRecordSet( $rsDocumentos );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Contribuinte");
$obLista->ultimoCabecalho->setWidth( 35 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Inscrição/Ano" );
$obLista->ultimoCabecalho->setWidth( 12 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Cobrança/Ano" );
$obLista->ultimoCabecalho->setWidth( 12 );
$obLista->commitCabecalho();

if ($_REQUEST['stTipoModalidade'] == "reemissao") {
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Documento" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
}

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("");
$obLista->ultimoCabecalho->setWidth( 3 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[numcgm] - [nom_cgm]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "inscricoes" );
$obLista->commitDado();

$obLista->addDado();
if ($_REQUEST['stTipoModalidade'] == "emissao") {
    $obLista->ultimoDado->setCampo( "[numero_parcelamento]/[exercicio_cobranca]" );
} else {
    $obLista->ultimoDado->setCampo( "cobranca" );
}

$obLista->commitDado();

if ($_REQUEST['stTipoModalidade'] == "reemissao") {
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[num_documento]/[exercicio]" );
    $obLista->commitDado();
}

$obChkEmitir = new Checkbox;
$obChkEmitir->setName ( "nboEmitir" );
if ($_REQUEST['stTipoModalidade'] == "emissao") {
    $obChkEmitir->setValue ( "[nome_arquivo_agt]&[nome_arquivo_swx]&[nome_documento]&[numcgm]&[numero_parcelamento]&[exercicio]&[num_parcelamento]&[cod_documento]&[num_emissao]&[cod_tipo_documento]&[nao_usar_num_documento]&[inscricoes]" );
} else {
    $obChkEmitir->setValue ( "[nome_arquivo_agt]&[nome_arquivo_swx]&[nome_documento]&[numcgm]&[cobranca]&[exercicio]&[num_documento]&[cod_documento]&[num_emissao]&[cod_tipo_documento]&[num_documento]&[inscricoes]" );
}

$obLista->addDadoComponente ( $obChkEmitir );
$obLista->ultimoDado->setAlinhamento ( 'CENTRO' );
$obLista->ultimoDado->setCampo ( "emitir" );
$obLista->commitDadoComponente ();

$obChkTodosN = new Checkbox;
$obChkTodosN->setName                        ( "boTodos" );
$obChkTodosN->setId                          ( "boTodos" );
$obChkTodosN->setRotulo                      ( "Selecionar Todas" );
$obChkTodosN->obEvento->setOnChange          ( "selecionarTodos('n');" );
$obChkTodosN->montaHTML();

$obTabelaCheckboxN = new Tabela;
$obTabelaCheckboxN->addLinha();
$obTabelaCheckboxN->ultimaLinha->addCelula();
$obTabelaCheckboxN->ultimaLinha->ultimaCelula->setColSpan ( 2 );
$obTabelaCheckboxN->ultimaLinha->ultimaCelula->setClass   ( $obLista->getClassPaginacao() );
$obTabelaCheckboxN->ultimaLinha->ultimaCelula->addConteudo( "<div align='right'>Selecionar Todos".$obChkTodosN->getHTML()."&nbsp;</div>");
$obTabelaCheckboxN->ultimaLinha->commitCelula();
$obTabelaCheckboxN->commitLinha();

$obTabelaCheckboxN->montaHTML();
$obLista->montaHTML();

$stHtmlTmp  = $obLista->getHTML();
$stHtmlTmp .= $obTabelaCheckboxN->getHTML();

$obSpanLista = new Span;
$obSpanLista->setId       ( 'spnListaNormais' );
$obSpanLista->setValue    ( $stHtmlTmp );

$obCmbModelo = new Select;
$obCmbModelo->setRotulo       ( "Modelo"    );
$obCmbModelo->setTitle        ( "Modelo de documento"    );
$obCmbModelo->setName         ( "cmbModelo" );
$obCmbModelo->addOption       ( "", "Selecione" );
$obCmbModelo->setCampoId      ( "nome_documento" );
$obCmbModelo->setCampoDesc    ( "nome_documento" );
$obCmbModelo->preencheCombo    ( $rsModelos );
$obCmbModelo->setNull         ( false );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $_REQUEST['stCtrl']  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $_REQUEST['stAcao']  );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GT_DAT_INSTANCIAS."emissao/OCGeraRelatorio.php" );

if ($_REQUEST['stOrigemFormulario'] == 'inscricao_divida') {
    $js = "	selecionarTodos('y'); ";
    sistemaLegado::executaFrameOculto( $js );
}

$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCaminho );
$obFormulario->addSpan  ( $obSpanLista );
$obFormulario->addComponente ( $obCmbModelo );
$obFormulario->Cancelar( $pgFilt );

$obFormulario->show();
SistemaLegado::LiberaFrames(true,false);
