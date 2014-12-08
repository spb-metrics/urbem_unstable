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
    * Pagina de Formulário de Manter Ordem de Compra
    * Data de Criação   : 06/07/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * $Id: FMManterOrdemCompra.php 59612 2014-09-02 12:00:51Z gelson $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once(TCOM."TComprasOrdem.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterOrdemCompra";
$pgFilt       = "FL".$stPrograma.".php";
$pgList       = "LS".$stPrograma.".php";
$pgForm       = "FM".$stPrograma.".php";
$pgProc       = "PR".$stPrograma.".php";
$pgOcul       = "OC".$stPrograma.".php";
$pgJS         = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');
$stTipoOrdem = ( strpos($stAcao,'OS')===false ) ? 'C' : 'S';
$stDesc = ($stTipoOrdem=='C') ? 'Compra' : 'Serviço';

$obCompraOrdemCompra = new TComprasOrdem();
$obCompraOrdemCompra->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
$obCompraOrdemCompra->setDado('exercicio', $_REQUEST['stExercicioOrdemCompra']);
$obCompraOrdemCompra->setDado('cod_ordem', $_REQUEST['inCodOrdemCompra']);
$obCompraOrdemCompra->setDado('tipo'     , $stTipoOrdem );

include_once ($pgJS);
$stEmpenho = $_REQUEST['inCodEmpenho']."/".$_REQUEST['stExercicioEmpenho'];

if ( strpos($stAcao,'incluir') !== false ) {
    $jsOnLoad = "executaFuncaoAjax('BuscaEmpenhoItens','&inCodEmpenho=".$_REQUEST["inCodEmpenho"]."&inCodEntidade=".$_REQUEST['inCodEntidade']."&stTipoOrdem=".$stTipoOrdem."&stAcao=".$_REQUEST['stAcao']."');";
} else {
    $jsOnLoad = "executaFuncaoAjax('BuscaOrdemCompraItens','&inCodEmpenho=".$_REQUEST["inCodEmpenho"]."&inCodEntidade=".$_REQUEST['inCodEntidade']."&stExercicioOrdemCompra=".$_REQUEST['stExercicioOrdemCompra']."&inCodOrdemCompra=".$_REQUEST['inCodOrdemCompra']."&stTipoOrdem=".$stTipoOrdem."&stAcao=".$_REQUEST['stAcao']."');";
}

$arFiltros = Sessao::read('arFiltros');

if ( !empty($arFiltros) ) {
    $stFiltro = '';
    foreach ($arFiltros as $stCampo => $stValor) {
        if (is_array($stValor)) {
            foreach ($stValor as $stCampo2 => $stValor2) {
                if (is_array($stValor2)) {
                    foreach ($stValor2 as $stCampo3 => $stValor3) {
                        $stFiltro .= "&".$stCampo3."=".urlencode( $stValor3 );
                    }
                } else {
                    $stFiltro .= "&".$stCampo2."=".urlencode( $stValor2 );
                }
            }
        } else {
            $stFiltro .= "&".$stCampo."=".urlencode( $stValor );
        }
    }
}

$stLocation = $pgList."?".Sessao::getId()."&stAcao=".$_REQUEST['stAcao'].$stFiltro;

//DEFINICAO DOS COMPONENTES DO FORMULARIO
$obForm = new Form();
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( 'oculto' );

$obHdnCtrl = new Hidden();
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl  );

$obHdnAcao = new Hidden();
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

// SPAN para a listagem dos itens, que é montada pelo Método BuscaEmpenho (Oculto).
$obSpnListaItens = new Span;
$obSpnListaItens->setId('spnListaItens');

if ( strpos($stAcao,'incluir') === false ) {
//if ($stAcao != "incluir") {
    // adiciona o campo de número de ordem de compra ao formulário
    $obLblNumOrdemCompra = new Label();
    $obLblNumOrdemCompra->setRotulo("Ordem de $stDesc");
    $obLblNumOrdemCompra->setValue($_REQUEST['inCodOrdemCompra']."/".$_REQUEST['stExercicioOrdemCompra']);

    // adiciona o campo de data de ordem de compra ao formulário
    $obLblDtOrdemCompra = new Label();
    $obLblDtOrdemCompra->setRotulo("Data da Ordem de $stDesc");
    $obLblDtOrdemCompra->setValue($_REQUEST['dtOrdemCompra']);

    /***********************
    *    monta os hiddens  *
    ***********************/

    // adiciona o hidden de número de ordem de compra ao formulário
    $obHdnNumOrdemCompra = new Hidden();
    $obHdnNumOrdemCompra->setName  ( "inCodOrdemCompra" );
    $obHdnNumOrdemCompra->setValue ( $_REQUEST['inCodOrdemCompra']  );

    // adiciona o hidden de exercicio de compra ao formulário
    $obHdnExercicioOrdemCompra = new Hidden();
    $obHdnExercicioOrdemCompra->setName  ( "stExercicioOrdemCompra" );
    $obHdnExercicioOrdemCompra->setValue ( $_REQUEST['stExercicioOrdemCompra']  );

    // adiciona o hidden de data de compra ao formulário
    $obHdnDtOrdemCompra = new Hidden();
    $obHdnDtOrdemCompra->setName  ( "dtOrdemCompra" );
    $obHdnDtOrdemCompra->setValue ( $_REQUEST['dtOrdemCompra']  );
}

$obLblEntidade = new Label();
$obLblEntidade->setRotulo("Entidade");
$obLblEntidade->setValue($_REQUEST['inCodEntidade']." - ".$_REQUEST['stEntidade']);

$obLblNumEmpenho = new Label();
$obLblNumEmpenho->setRotulo("Número do Empenho");
$obLblNumEmpenho->setValue($_REQUEST['inCodEmpenho']."/".$_REQUEST['stExercicioEmpenho']);

// Número da Licitação / Compra Direta
// é feita a verificação do tipo para saber o que colocar no campo
$obLblCodigo = new Label();
if ($_REQUEST["stTipo"] == "licitacao") {
    $obLblCodigo->setRotulo("Número da Licitação");
    $obLblCodigo->setValue($_REQUEST['inCodigo']."/".$_REQUEST['stExercicio']);
} else {
    $obLblCodigo->setRotulo("Compra Direta");
    $obLblCodigo->setValue($_REQUEST['inCodigo'] );
}

$obLblModalidade = new Label();
$obLblModalidade->setRotulo("Modalidade");
$obLblModalidade->setValue($_REQUEST['inCodModalidade']." - ".$_REQUEST['stModalidade']);

$obLblObjeto = new Label();
$obLblObjeto->setRotulo("Objeto");
$obLblObjeto->setValue($_REQUEST['inCodObjeto']." - ".$_REQUEST['stObjeto']);

$obLblFornecedor = new Label();
$obLblFornecedor->setRotulo("Fornecedor");
$obLblFornecedor->setValue($_REQUEST['inCodFornecedor']." - ".$_REQUEST['stFornecedor']);

$obLblCondicoesPagamento = new Label();
$obLblCondicoesPagamento->setRotulo("Condições de Pagamento");
$obLblCondicoesPagamento->setValue($_REQUEST['stCondicoesPagamento']);

if ($stTipo == 'licitacao') {
    $obLblLocalEntrega = new Label();
    $obLblLocalEntrega->setRotulo("Local de Entrega do Material");
    $obLblLocalEntrega->setValue($_REQUEST['stLocalEntregaMaterial']);
}

if ( strpos($stAcao,'incluir') !== false || strpos($stAcao,'alterar') !== false ) {
    $obTxtObservacao = new TextBox;
    $obTxtObservacao->setName      ( "stObservacao" );
    $obTxtObservacao->setRotulo    ( "Observação" );
    $obTxtObservacao->setTitle     ( "Informe a observação.");
    $obTxtObservacao->setNull      ( true );
    $obTxtObservacao->setMaxLength ( 200 );
    $obTxtObservacao->setSize      ( 100 );

    // se a ação for alterar, faz a busca do campo observacao para alteracao
    if ( strpos($stAcao,'alterar') !== false ) {

        $obCompraOrdemCompra->recuperaPorChave( $rsOrdemCompra );
        $obTxtObservacao->setValue($rsOrdemCompra->getCampo('observacao'));
    }

} elseif ( strpos($stAcao,'anular') !== false ) {
    $obTxtMotivo = new TextBox;
    $obTxtMotivo->setName      ( "stMotivo" );
    $obTxtMotivo->setRotulo    ( "Motivo" );
    $obTxtMotivo->setTitle     ( "Informe o motivo.");
    $obTxtMotivo->setNull      ( false );
    $obTxtMotivo->setMaxLength ( 200 );
    $obTxtMotivo->setSize      ( 100 );
} elseif ( strpos($stAcao,'consultar') !== false ) {

    $obCompraOrdemCompra->recuperaTotaisOrdem( $rsTotais );
    $flTotalOC = $rsTotais->getCampo('total_oc');
    $flTotalOCAnulado = $rsTotais->getCampo('total_oc_anulado');
    $flTotalAtendido = $rsTotais->getCampo('total_atendido');

    if ($stTipoOrdem=='C') {
        if($flTotalOC > 0 && $flTotalOCAnulado == 0 && empty($flTotalAtendido))
            $stStatusOC = "Emitida";
        elseif(empty($flTotalOCAnulado) && empty($flTotalAtendido))
            $stStatusOC = "Não Atendida";
        elseif( ($flTotalOC-$flTotalOCAnulado)>0 && empty($flTotalAtendido) )
            $stStatusOC = "Parcialmente Anulada";
        elseif( ($flTotalOC-$flTotalOCAnulado)<=0 )
            $stStatusOC = "Anulada";
        elseif( ($flTotalOC-$flTotalOCAnulado-$flTotalAtendido)<=0 )
            $stStatusOC = "Atendida";
        else
            $stStatusOC = "Parcialmente Atendida";
    } else {

        if( ($flTotalOC-$flTotalOCAnulado)<=0 )
            $stStatusOC = "Anulada";
        else
            $stStatusOC = "Emitida";
    }

    $obLblStatus = new Label();
    $obLblStatus->setRotulo("Status da Ordem de $stDesc");
    $obLblStatus->setValue($stStatusOC);

    if ($stStatusOC == "Anulada") {
        $stFiltro  =    " exercicio = '". $_REQUEST['stExercicioOrdemCompra']."' ";
        $stFiltro .=    "AND cod_entidade = ". $_REQUEST['inCodEntidade']." ";
        $stFiltro .=    "AND cod_ordem = ". $_REQUEST['inCodOrdemCompra']." ";
        $stFiltro .=    "AND tipo = '".$stTipoOrdem."' ";

        $obCompraOrdemCompra->recuperaMotivo( $rsMotivo, $stFiltro );
        $stMotivo = $rsMotivo->getCampo('motivo');

        $obLblMotivo = new Label();
        $obLblMotivo->setRotulo("Motivo da Anulação");
        $obLblMotivo->setValue($stMotivo);
    }
}

    //Radios de Inclusão de Assinatura do Usuário Logado
    $obRadioAssinaturaUsuarioSim = new Radio;
    $obRadioAssinaturaUsuarioSim->setRotulo ( "Incluir Assinatura do Usuário" );
    $obRadioAssinaturaUsuarioSim->setName   ( "stIncluirAssinaturaUsuario" );
    $obRadioAssinaturaUsuarioSim->setId     ( "stIncluirAssinaturaUsuarioSim" );
    $obRadioAssinaturaUsuarioSim->setChecked ( false );
    $obRadioAssinaturaUsuarioSim->setValue   ( "sim" );
    $obRadioAssinaturaUsuarioSim->setLabel   ( "Sim" );
    $obRadioAssinaturaUsuarioSim->setNull    ( false );

    $obRadioAssinaturaUsuarioNao = new Radio;
    $obRadioAssinaturaUsuarioNao->setName ( "stIncluirAssinaturaUsuario" );
    $obRadioAssinaturaUsuarioNao->setId   ( "stIncluirAssinaturaUsuarioNao" );
    $obRadioAssinaturaUsuarioNao->setChecked ( true );
    $obRadioAssinaturaUsuarioNao->setValue   ( "nao" );
    $obRadioAssinaturaUsuarioNao->setLabel   ( "Não" );
    $obRadioAssinaturaUsuarioNao->setNull    ( false );

/*******************************
 monta os hiddens do formulário
*******************************/

$obHdnCodEntidade = new Hidden();
$obHdnCodEntidade->setName  ( "inCodEntidade" );
$obHdnCodEntidade->setId    ( "inCodEntidade" );
$obHdnCodEntidade->setValue ( $_REQUEST['inCodEntidade']  );

$obHdnExercicioEmpenho = new Hidden();
$obHdnExercicioEmpenho->setName  ( "stExercicioEmpenho" );
$obHdnExercicioEmpenho->setValue ( $_REQUEST['stExercicioEmpenho']  );

$obHdnCodEmpenho = new Hidden();
$obHdnCodEmpenho->setName  ( "inCodEmpenho" );
$obHdnCodEmpenho->setValue ( $_REQUEST['inCodEmpenho']  );

$obHdnTipo = new Hidden();
$obHdnTipo->setName  ( "stTipo" );
$obHdnTipo->setValue ( $_REQUEST['stTipo']  );

/*******************************
        fim dos hiddens
*******************************/

/*******************************
        MONTA O FORMULARIO
********************************/

$obFormulario = new Formulario();
$obFormulario->addForm( $obForm );
$title = strpos($stAcao,'incluir') !== false ? "Dados para Registro de Ordem de $stDesc" : "Ordem de $stDesc";
$obFormulario->addTitulo( $title );

$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnCodEntidade);
$obFormulario->addHidden($obHdnExercicioEmpenho);
$obFormulario->addHidden($obHdnCodEmpenho);
$obFormulario->addHidden($obHdnTipo);

if ( strpos($stAcao,'incluir') === false ) {
    $obFormulario->addHidden($obHdnNumOrdemCompra);
    $obFormulario->addHidden($obHdnExercicioOrdemCompra);
    $obFormulario->addHidden($obHdnDtOrdemCompra);
    $obFormulario->addComponente($obLblNumOrdemCompra);
    $obFormulario->addComponente($obLblDtOrdemCompra);
}
$obFormulario->addComponente($obLblEntidade);
$obFormulario->addComponente($obLblNumEmpenho);
$obFormulario->addComponente($obLblCodigo);
$obFormulario->addComponente($obLblModalidade);
$obFormulario->addComponente($obLblObjeto);
$obFormulario->addComponente($obLblCondicoesPagamento);
if ($stTipo == 'licitacao') {
    $obFormulario->addComponente($obLblLocalEntrega);
}
$obFormulario->addComponente($obLblFornecedor);
if(is_object($obLblStatus))
    $obFormulario->addComponente($obLblStatus);
if(is_object($obLblMotivo))
    $obFormulario->addComponente($obLblMotivo);
if ( strpos($stAcao,'incluir') !== false || strpos($stAcao,'alterar') !== false ) {
    $obFormulario->addComponente($obTxtObservacao);
} elseif ( strpos($stAcao,'anular') !== false ) {
    $obFormulario->addComponente($obTxtMotivo);
}

$obFormulario->addSpan($obSpnListaItens);

if ((strpos($stAcao,'anular') === false) and (strpos($stAcao,'consultar') === false)) {
    include_once( CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php");
    $obMontaAssinaturas = new IMontaAssinaturas;
    $obMontaAssinaturas->geraFormulario( $obFormulario );
    $obFormulario->agrupaComponentes( array( $obRadioAssinaturaUsuarioSim, $obRadioAssinaturaUsuarioNao ) );
}

if ( strpos($stAcao,'consultar') === false ) {
    $obFormulario->Cancelar($stLocation);
} else {
    $obBtnClean = new Button;
    $obBtnClean->setName             ( "btnVoltar" );
    $obBtnClean->setValue            ( "Voltar" );
    $obBtnClean->setTipo             ( "button" );
    $obBtnClean->obEvento->setOnClick( "Cancelar('".$stLocation."');" );
    $obBtnClean->setDisabled         ( false );

    $obFormulario->defineBarra(array($obBtnClean));
}
$obFormulario->Show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
