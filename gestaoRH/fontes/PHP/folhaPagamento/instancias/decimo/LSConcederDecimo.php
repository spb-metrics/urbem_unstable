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
    * Página de Lista do Conceder de 13 Salário
    * Data de Criação: 15/09/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30711 $
    $Name$
    $Author: souzadl $
    $Date: 2008-03-26 17:16:54 -0300 (Qua, 26 Mar 2008) $

    * Casos de uso: uc-04.05.24
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoDecimo.class.php"                   );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConcessaoDecimo.class.php"                        );

//Define o nome dos arquivos PHP
$stPrograma = "ConcederDecimo";
$pgForm = "FM".$stPrograma.".php";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

if ($stAcao == "cancelar" or $stAcao == "excluir") {
    $stCaminho = CAM_GRH_FOL_INSTANCIAS."decimo/";

    //Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
    $stAcao = "excluir";
    switch ($stAcao) {
        case 'cancelar':
        case 'excluir': $pgProx = $pgProc; break;
        DEFAULT       : $pgProx = $pgForm;
    }
    $link = Sessao::read("link");
    if ($_GET["pg"] and  $_GET["pos"]) {
        $link["pg"]  = $_GET["pg"];
        $link["pos"] = $_GET["pos"];
        Sessao::write("link",$link);
    } elseif ( is_array($link) ) {
        $_GET = $link;
        $_REQUEST = $link;
    } else {
        foreach ($_REQUEST as $key => $valor) {
            $link[$key] = $valor;
        }
        Sessao::write("link",$link);
    }
    $stLink  = "&stAcao=$stAcao";
    $stLink .= "&stTipoFiltro=".$_REQUEST['stTipoFiltro'];

    $stValoresFiltro = "";
    switch ($_REQUEST['stTipoFiltro']) {
        case "contrato":
        case "cgm_contrato":
            $arContratos = Sessao::read("arContratos");
            foreach ($arContratos as $arContrato) {
                $stValoresFiltro .= $arContrato["cod_contrato"].",";
            }
            $stValoresFiltro = substr($stValoresFiltro,0,strlen($stValoresFiltro)-1);
        break;
        case "lotacao":
            $stValoresFiltro = implode(",",$_REQUEST["inCodLotacaoSelecionados"]);
        break;
        case "local":
            $stValoresFiltro = implode(",",$_REQUEST["inCodLocalSelecionados"]);
        break;
        case "reg_sub_fun_esp":
            $stValoresFiltro  = implode(",",$_REQUEST["inCodRegimeSelecionadosFunc"])."#";
            $stValoresFiltro .= implode(",",$_REQUEST["inCodSubDivisaoSelecionadosFunc"])."#";
            $stValoresFiltro .= implode(",",$_REQUEST["inCodFuncaoSelecionados"])."#";
            if (is_array($_REQUEST["inCodEspecialidadeSelecionadosFunc"])) {
                $stValoresFiltro .= implode(",",$_REQUEST["inCodEspecialidadeSelecionadosFunc"]);
            }
            break;
    }

    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoUltimoRegistroEventoDecimo.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao;
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsUltimaMovimentacao);
    $obTFolhaPagamentoUltimoRegistroEventoDecimo = new TFolhaPagamentoUltimoRegistroEventoDecimo();
    $stFiltro  = " WHERE concessao_decimo.cod_periodo_movimentacao = ".$rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao");
    $stFiltro .= "   AND concessao_decimo.folha_salario IS ".$_REQUEST["boPagEmFolhaSalario"];

    $rsLista = new RecordSet();

    $obTFolhaPagamentoConcessaoDecimo = new TFolhaPagamentoConcessaoDecimo();
    $obTFolhaPagamentoConcessaoDecimo->setDado("stConfiguracao","cgm,oo,f,ef,l");
    $obTFolhaPagamentoConcessaoDecimo->setDado("stTipoFiltro",$_REQUEST["stTipoFiltro"]);
    $obTFolhaPagamentoConcessaoDecimo->setDado("inCodPeriodoMovimentacao",$rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao"));
    $obTFolhaPagamentoConcessaoDecimo->setDado("stValoresFiltro",$stValoresFiltro);
    $obTFolhaPagamentoConcessaoDecimo->recuperaContratosParaCancelar($rsLista1,$stFiltro);
    $obTFolhaPagamentoConcessaoDecimo->recuperaContratosParaCancelarPensionista($rsLista2,$stFiltro);

    $arLista1 = $rsLista1->getElementos();
    $arLista2 = $rsLista2->getElementos();

    switch (true) {
        case $arLista1 != 0 and $arLista2 != 0 :
            $arLista  = array_merge($arLista1,$arLista2);
            break;
        case $arLista1 != 0 and $arLista2 == 0 :
            $arLista  = array_merge($arLista1);
            break;
        case $arLista1 == 0 and $arLista2 != 0 :
            $arLista  = array_merge($arLista2);
            break;
    }

    if ($arLista == '') {
        $arLista = array();
    }

    $rsLista->preenche($arLista);

    $obLista = new Lista;
    $obLista->obPaginacao->setFiltro("&stLink=".$stLink );
    $obLista->setRecordSet( $rsLista );
    $obLista->setTitulo("Matrículas");

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 2 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Matrícula" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Servidor" );
    $obLista->ultimoCabecalho->setWidth( 25 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Lotação" );
    $obLista->ultimoCabecalho->setWidth( 25 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    if ($_REQUEST['stTipoFiltro'] == "local") {
        $obLista->ultimoCabecalho->addConteudo( "Local" );
    } else {
        $obLista->ultimoCabecalho->addConteudo( "Função" );
    }
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Desdobramento" );
    $obLista->ultimoCabecalho->setWidth( 10 );
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
    $obLista->ultimoDado->setCampo( "desc_orgao" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    if ($_REQUEST['stTipoFiltro'] == "local") {
        $obLista->ultimoDado->setCampo( "desc_local" );
    } else {
        $obLista->ultimoDado->setCampo( "desc_funcao" );
    }
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "desdobramento_texto" );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao ( $stAcao );
    $obLista->ultimaAcao->addCampo( "&inCodContrato"          , "cod_contrato" );
    $obLista->ultimaAcao->addCampo( "&inRegistro"             , "registro" );
    $obLista->ultimaAcao->addCampo( "&stDescQuestao"          , "mensagem" );
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
    $obLista->commitAcao();
    $obLista->show();
} else {
    $jsOnload   = "executaFuncaoAjax('preencherSpanLista','&stOpcao=".$_GET['stOpcao']."');";

    $obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

    //DEFINICAO DOS COMPONENTES
    $obHdnAcao =  new Hidden;
    $obHdnAcao->setName                             ( "stAcao"                                              );
    $obHdnAcao->setValue                            ( $stAcao                                               );

    $obHdnCtrl =  new Hidden;
    $obHdnCtrl->setName                             ( "stCtrl"                                              );
    $obHdnCtrl->setValue                            ( $stStrl                                               );

    //Define objeto SPAN
    $obSpnSpan1 = new Span;
    $obSpnSpan1->setId                              ( "spnSpan1"                                            );

    $obSpnMensagem = new Span;
    $obSpnMensagem->setId                           ( "spnMensagem"                                         );
    $obSpnMensagem->setValue                        ( "<b>ATENÇÃO:</b> Existem matrículas nesta competência com registro de décimo terceiro, porém sem cálculos. Corrigir esta inconsistência." );

    $obBtnOk = new ok();
    $obBtnOk->setValue                              ( "Imprimir"                                            );

    $obRdoRegistradosSucesso = new Radio();
    $obRdoRegistradosSucesso->setRotulo             ( "Matrículas"                                          );
    $obRdoRegistradosSucesso->setLabel              ( "Registrados com Sucesso"                             );
    $obRdoRegistradosSucesso->setName               ( "stRegistrados"                                       );
    $obRdoRegistradosSucesso->setValue              ( "sim"                                                 );
    $obRdoRegistradosSucesso->obEvento->setOnChange ( "montaParametrosGET('montaListaConcessao','stRegistrados');");
    $obRdoRegistradosSucesso->setChecked            ( true                                                  );

    $obRdoNaoRegistrados = new Radio();
    $obRdoNaoRegistrados->setRotulo                 ( "Matrículas"                                          );
    $obRdoNaoRegistrados->setLabel                  ( "Não Registrados"                                     );
    $obRdoNaoRegistrados->setName                   ( "stRegistrados"                                       );
    $obRdoNaoRegistrados->obEvento->setOnChange     ( "montaParametrosGET('montaListaConcessao','stRegistrados');");
    $obRdoNaoRegistrados->setValue                  ( "nao"                                                 );

    //DEFINICAO DO FORM
    $obForm = new Form;
    $obForm->setAction                              ( CAM_FW_POPUPS."relatorio/OCRelatorio.php"             );
    $obForm->setTarget                              ( "oculto"                                              );

    $obHdnCaminho = new Hidden;
    $obHdnCaminho->setName                          ( "stCaminho"                                           );
    $obHdnCaminho->setValue                         ( CAM_GRH_FOL_INSTANCIAS."decimo/PRRelatorioConcederDecimo.php" );

    //DEFINICAO DO FORMULARIO
    $obFormulario = new Formulario;
    $obFormulario->addForm                          ( $obForm                                               );
    $obFormulario->addTitulo                        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right");
    $obFormulario->addHidden                        ( $obHdnAcao                                            );
    $obFormulario->addHidden                        ( $obHdnCaminho                                         );
    $obFormulario->addHidden                        ( $obHdnCtrl                                            );
    if ($_GET['stOpcao'] == "T4") {
        $obFormulario->addSpan                      ( $obSpnMensagem                                        );
        $obFormulario->addSpan                      ( $obSpnSpan1                                           );
        $obFormulario->defineBarra                  ( array($obBtnOk),"",""                                 );
    }
    if ($_GET['stOpcao'] == "T3") {
        $obFormulario->addTitulo                    ( "Competência"                                         );
        $obFormulario->agrupaComponentes            ( array($obRdoRegistradosSucesso,$obRdoNaoRegistrados)  );
        $obFormulario->addSpan                      ( $obSpnSpan1                                           );
    }
    $obFormulario->show();
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
