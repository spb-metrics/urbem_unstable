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

    * Página de Formulario de Manter Homologacao
    * Data de Criação: 23/10/2006

    * @author Analista: Anelise Schwengber
    * @author Desenvolvedor: Andre Almeida

    * @ignore

    * Casos de uso: uc-03.05.21

    $Id: OCManterHomologacao.php 45605 2011-07-14 20:04:27Z davi.aroldi $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';
include_once ( CAM_GP_COM_MAPEAMENTO."TComprasCompraDireta.class.php" );
include_once ( CAM_GP_COM_MAPEAMENTO."TComprasCompraDiretaHomologacao.class.php" );
include_once ( CAM_GP_COM_MAPEAMENTO."TComprasJulgamentoItem.class.php" );
include_once ( CAM_GP_COM_MAPEAMENTO."TComprasJustificativaRazao.class.php" );

$stCtrl = $_REQUEST['stCtrl'];

function configuracoesIniciais()
{
    Sessao::write('itensHomologacao', array());
}

function buscaDataHomologacacao()
{
    $stFiltro = " WHERE cod_compra_direta = ".$_REQUEST["inCodCompraDireta"]."
                AND cod_modalidade    = ".$_REQUEST["inCodModalidade"]."
                AND cod_entidade      = ".$_REQUEST["inCodEntidade"]."
                AND exercicio         = '".$_REQUEST["stExercicioCompraDireta"]."' ";

    $obTCompraDiretaHomologacao = new TComprasCompraDiretaHomologacao();
    $obTCompraDiretaHomologacao->recuperaTodos($rsHomologacao, $stFiltro);

    $arTimestamp = explode(" ", $rsHomologacao->getCampo("timestamp"));

    $dtDataHomologacao = date('d/m/Y', strtotime($rsHomologacao->getCampo("timestamp")));
    $dtHoraHomologacao = date('H:i', strtotime($rsHomologacao->getCampo("timestamp")));

    if ( $rsHomologacao->getNumLinhas() > 0 ) {
        $stJs .= "jQuery('#stDtHomologacao').val('$dtDataHomologacao');\n";
        $stJs .= "jQuery('#stHoraHomologacao').val('$dtHoraHomologacao');\n";
    } else {
        $stJs .= "jQuery('#stDtHomologacao').val('');\n";
        $stJs .= "jQuery('#stHoraHomologacao').val('');\n";
    }

    echo $stJs;
}

function buscaJustificativaRazao()
{
    $obTComprasJustificativaRazao = new TComprasJustificativaRazao();
    $obTComprasJustificativaRazao->setDado('exercicio_entidade', $_REQUEST["stExercicioCompraDireta"]   );
    $obTComprasJustificativaRazao->setDado('cod_entidade'      , $_REQUEST["inCodEntidade"]   );
    $obTComprasJustificativaRazao->setDado('cod_modalidade'    , $_REQUEST["inCodModalidade"]   );
    $obTComprasJustificativaRazao->setDado('cod_compra_direta' , $_REQUEST["inCodCompraDireta"] );
    $obTComprasJustificativaRazao->recuperaPorChave($rsJustificativaRazao);
    
    $stJustificativa = preg_replace('/[\r\n]+/', "\\n", addslashes($rsJustificativaRazao->getCampo('justificativa')));
    $stRazao         = preg_replace('/[\r\n]+/', "\\n", addslashes($rsJustificativaRazao->getCampo('razao')));
    $stFundamentacao = preg_replace('/[\r\n]+/', "\\n", addslashes($rsJustificativaRazao->getCampo('fundamentacao_legal')));

    if ( $rsJustificativaRazao->getNumLinhas() > 0 ) {
        $stJs .= "jQuery('#stJustificativa').val('$stJustificativa');\n";
        $stJs .= "jQuery('#stRazao').val('$stRazao');\n";
        $stJs .= "jQuery('#stFundamentacao').val('$stFundamentacao');\n";
    } else {
        $stJs .= "jQuery('#stJustificativa').val('');\n";
        $stJs .= "jQuery('#stRazao').val('');\n";
        $stJs .= "jQuery('#stFundamentacao').val('');\n";
    }
    echo $stJs;
}

function carregaItensBanco()
{
    $boTodosAutorizados = false;
    $boTodosHomologados = false;

    $obTCompraDiretaHomologacao = new TComprasCompraDiretaHomologacao;
    $obTCompraDiretaHomologacao->setDado( "cod_compra_direta" , $_REQUEST["inCodCompraDireta"]   );
    $obTCompraDiretaHomologacao->setDado( "cod_modalidade", $_REQUEST["inCodModalidade"]      );
    $obTCompraDiretaHomologacao->setDado( "cod_entidade"  , $_REQUEST["inCodEntidade"]        );
    $obTCompraDiretaHomologacao->setDado( "exercicio", $_REQUEST["stExercicioCompraDireta"] );

    $stFiltro = "and julgamento_item.ordem = 1 \n";
    $obTCompraDiretaHomologacao->recuperaItensComStatus( $rsItens, $stFiltro );

    $inId = $inHomologados = $inAutorizados = 0;
    $itensHomologacao = array();

    if ( $rsItens->getNumLinhas() > 0 ) {
        foreach ($rsItens->arElementos as $item) {
            $itensHomologacao[$inId]['inId'] = $inId;
            $itensHomologacao[$inId]['numHomologacao']           = $item["num_homologacao"];
            $itensHomologacao[$inId]['codTipoDocumento']         = $item["cod_tipo_documento"];
            $itensHomologacao[$inId]['codDocumento']             = $item["cod_documento"];
            $itensHomologacao[$inId]['exercicioJulgamentoItem']  = $item["julgamento_item_exercicio"];
            //$itensHomologacao[$inId]['autorizadoEmpenho']      = $item["autorizado_empenho"];
            $itensHomologacao[$inId]['codCompraDireta']          = $item["cod_compra_direta"];
            $itensHomologacao[$inId]['codModalidade']            = $item["cod_modalidade"];
            $itensHomologacao[$inId]['codEntidade']              = $item["cod_entidade"];
            //$itensHomologacao[$inId]['numAdjudicacao']         = $item["num_adjudicacao"];
            $itensHomologacao[$inId]['CompraDiretaExercicio']    = $item["exercicio_entidade"];
            $itensHomologacao[$inId]['lote']                     = $item["lote"];
            $itensHomologacao[$inId]['codCotacao']               = $item["cod_cotacao"];
            $itensHomologacao[$inId]['cotacaoExercicio']         = $item["exercicio_cotacao"];
            $itensHomologacao[$inId]['cgmFornecedor']            = $item["cgm_fornecedor"];
            $itensHomologacao[$inId]['nomFornecedor']            = trim( $item["nom_cgm"] );
            $itensHomologacao[$inId]['quantidade']               = $item["quantidade"];
            $itensHomologacao[$inId]['valorCotacao']             =  number_format($item["vl_cotacao"],2,',','.');
            $itensHomologacao[$inId]['valorTotal']               = number_format( $item["vl_total"] , 2, ',', '.');
            $itensHomologacao[$inId]['codItem']                  = $item["cod_item"];
            $itensHomologacao[$inId]['descricaoItem']            = trim( $item["descricao"] );
            //$itensHomologacao[$inId]['justificativa_anulacao'] = $item["justificativa_anulacao"];
            $itensHomologacao[$inId]['status']                   = $item["status"];

            include_once ( CAM_GP_COM_MAPEAMENTO . 'TComprasMapaItem.class.php' );
            $obTComprasMapaItem = new TComprasMapaItem();
            $itemComplemento = "";
            $obTComprasMapaItem->setDado('cod_mapa'     , $item["cod_mapa"]);
            $obTComprasMapaItem->setDado('cod_item'     , $item["cod_item"]);
            $obTComprasMapaItem->setDado('exercicio'    , $item["exercicio_mapa"]);
            $obTComprasMapaItem->setDado('cod_entidade' , $item["cod_entidade"]);
            $obTComprasMapaItem->recuperaComplementoItemMapa( $rsItemComplemento );

            $rsItemComplemento->setPrimeiroElemento();
            While (!$rsItemComplemento->eof()) {
                if ($itemComplemento == "") {
                    $itemComplemento= $rsItemComplemento->getCampo('complemento');
                } else {
                    $itemComplemento= $itemComplemento ." \n <br>".$rsItemComplemento->getCampo('complemento');
                }

                $rsItemComplemento->proximo();
            }

            $itensHomologacao[$inId]['complemento']             = $itemComplemento;
            $itensHomologacao[$inId]['boAlterado']              = "false";
            //$itensHomologacao[$inId]['boAnuladoBanco']        = ( $item["num_adjudicacao_anulada"] != "" ) ? true : false;
            $itensHomologacao[$inId]['valorUnitarioReferencia'] = $item["vl_unitario_referencia"];

            // se ja estiver todos autorizados, nao pode alterar nada
            if ($item['status'] == "Homologado e Autorizado") {
                $inAutorizados++;
            }
            if ($item['status'] == "Homologado") {
                $inHomologados++;
            }

            $inId = $inId + 1;
        }

        if ($inId == $inAutorizados) {
            $boTodosAutorizados = true;
        }
        if ($inId == $inHomologados) {
            $boTodosHomologados = true;
        }
    }
    
    Sessao::write('boTodosHomologados', $boTodosHomologados);
    Sessao::write('boTodosAutorizados', $boTodosAutorizados);
    Sessao::write('itensHomologacao', $itensHomologacao);
    Sessao::write('rsItens', $rsItens);
    $boRetorno = true;

    return $boRetorno;
}

function limpaSessao()
{
    Sessao::write('itensHomologacao', $itensHomologacao);
}

function verificaAutorizacao()
{
    $itensHomologacao = Sessao::read('itensHomologacao');

    // verificar 1 a 1
    $inId = $inHomologados = $inAutorizados = 0;
    foreach ($itensHomologacao  as $item) {

        if ( ($item['status'] == "Homologado") or ($item['status'] == "Anulado") ) {
            $inHomologados++;
        }
        $inId++;
    }

    if ($inId == $inHomologados) {
        Sessao::write('boTodosHomologados ', true);
    }

    $stJs = "";

    return $stJs;
}

function preencheObjeto()
{
    $obTComprasCompraDireta = new TComprasCompraDireta;

    $obTComprasCompraDireta->setDado( "cod_compra_direta"  , $_GET["inCodCompraDireta"]       );
    $obTComprasCompraDireta->setDado( "cod_modalidade"     , $_GET["inCodModalidade"]      );
    $obTComprasCompraDireta->setDado( "cod_entidade"       , $_GET["inCodEntidade"]        );
    $obTComprasCompraDireta->setDado( "exercicio_entidade" , $_GET["stExercicioCompraDireta"] );
    $obTComprasCompraDireta->recuperaObjetoCompraDireta( $rsObjeto );

    $stJs = "d.getElementById('objeto').innerHTML = '".str_replace('\r\n', '\n', preg_replace('/(\r\n|\n|\r)/', ' ',$rsObjeto->getCampo('descricao')))."';\n";

    return $stJs;
}

function limpaObjeto()
{
    $stJs = "d.getElementById('objeto').innerHTML = '&nbsp;';\n";

    return $stJs;
}

function montaSpnItens()
{
    $itensHomologacao = Sessao::read('itensHomologacao');
    if (!is_array($itensHomologacao)) {
        $itensHomologacao = array();
    }

    $rsItens = new RecordSet;
    $rsItens->preenche( $itensHomologacao );
    //$rsItens->addFormatacao('quantidade', 'NUMERIC_BR_4');
    //$rsItens->addFormatacao('valorCotacao', 'NUMERIC_BR');
    //$rsItens->addFormatacao('valorUnitarioReferencia', 'NUMERIC_BR');

    $table = new Table;
    $table->setRecordset   ( $rsItens );
    $table->setSummary     ( 'Itens'  );
    //$table->setConditional ( true , "#E4E4E4" );

    if ( $rsItens->getNumLinhas() >= 5 ) {
        $table->setHeadFixed  (true);
        $table->setBodyHeight (150);
    }

    $table->Head->addCabecalho ( 'Item'             , 30 );
    $table->Head->addCabecalho ( 'Qtde'             , 10 );
    $table->Head->addCabecalho ( 'Valor Ref.'       , 10 );
    $table->Head->addCabecalho ( 'Valor'            , 10 );
    $table->Head->addCabecalho ( 'Fornecedor'       , 20 );
    $table->Head->addCabecalho ( 'Status'           , 15 );

    $boTodosAutorizados = Sessao::read('boTodosAutorizados');
    if (!$boTodosAutorizados) {
        $table->Head->addCabecalho( 'Selecione'        , 1 );
   }

    $table->Body->addCampo ( "[codItem] - [descricaoItem]<br>[complemento]" );
    $table->Body->addCampo ( "quantidade"              , "D" );
    $table->Body->addCampo ( "valorUnitarioReferencia" , "D" );
    $table->Body->addCampo ( "valorCotacao"            , "D" );
    $table->Body->addCampo ( "nomFornecedor"                 );
    $table->Body->addCampo ( "status"                  , "C" );

    $obRdnHomologarItem = new Radio();
    $obRdnHomologarItem->setValue( "inId" );
    $obRdnHomologarItem->setName( "boHomologarItemInId" );
    $obRdnHomologarItem->setNull( false );
    $obRdnHomologarItem->obEvento->setOnClick("montaParametrosGET( 'montaHomologacao', 'boHomologarItemInId' );");

    if (!$boTodosAutorizados) {
        $table->Body->addComponente( $obRdnHomologarItem, false );
    }

    $table->montaHTML(true);
    $stHtml = $table->getHTML();

    $obFormulario = new Formulario;

    if (!$boTodosAutorizados) {
        $obBtnHomologar = new Button;
        $obBtnHomologar->setName ( "btnHomologarTodos" );
        $obBtnHomologar->setValue( "Homologar Todos" );
        $obBtnHomologar->setTipo ( "button" );
        $obBtnHomologar->obEvento->setOnClick ( "montaParametrosGET( 'homologarTodos', 'btnHomologarTodos' );" );

        $obBtnCancelarHomologacao = new Button;
        $obBtnCancelarHomologacao->setName ( "btnCancelarHomologacaoTodos" );
        $obBtnCancelarHomologacao->setValue( "Cancelar Homologação" );
        $obBtnCancelarHomologacao->setTipo ( "button" );
        $obBtnCancelarHomologacao->obEvento->setOnClick ( "montaParametrosGET( 'cancelarHomologacaoTodos', 'btnCancelarHomologacaoTodos' );" );

        $obFormulario->defineBarra( array ( $obBtnHomologar, new label(), $obBtnCancelarHomologacao ),"right","" );
    }

    $obFormulario->montaInnerHTML();
    $stHtml .= $obFormulario->getHTML();

    $stJs .= "jq('#spnItensHomologacao').html('".$stHtml."');\n";

    return $stJs;
}

function montaspnHomologacao($inId = "")
{
    $itensHomologacao = Sessao::read('itensHomologacao');

    if ($inId != "") {
        $obTxtAreaJustificativaAnulacao = new TextArea;
        $obTxtAreaJustificativaAnulacao->setRotulo( "Justificativa da Anulação" );
        $obTxtAreaJustificativaAnulacao->setName( "stJustificativaAnulacao" );
        $obTxtAreaJustificativaAnulacao->setId( "stJustificativaAnulacao" );
        $obTxtAreaJustificativaAnulacao->setReadOnly( true );
        $obTxtAreaJustificativaAnulacao->setValue( $itensHomologacao[$inId]['justificativa_anulacao'] );

        $obSpnAnularItem = new Span;
        $obSpnAnularItem->setId ( "spnAnularItem" );
        $obSpnAnularItem->setValue( "" );

        $obChkIrProximo = new CheckBox;
        $obChkIrProximo->setRotulo('Ir para o próximo item');
        $obChkIrProximo->setName('boProximoItem');
        $obChkIrProximo->setChecked( false );
        if ( count( $itensHomologacao ) <= $inId+1 ) {
            $obChkIrProximo->setDisabled( true );
            $obChkIrProximo->setChecked( false );
        } else {
            $obChkIrProximo->setChecked( false );
        }

        $obBtnHomologar = new Button;
        $obBtnHomologar->setName ( "btnHomologar" );
        $obBtnHomologar->setValue( "Homologar" );
        $obBtnHomologar->setTipo ( "button" );
        $obBtnHomologar->obEvento->setOnClick ( "montaParametrosGET( 'homologarItem', 'boHomologarItemInId, boProximoItem' );" );
        if ($itensHomologacao[$inId]['status'] != "A Homologar") {
            $obBtnHomologar->setDisabled( true );
        }

        $obBtnCancelar = new Button;
        $obBtnCancelar->setName ( "btnCancelarHomologacao" );
        $obBtnCancelar->setValue( "Cancelar a Homologação" );
        $obBtnCancelar->setTipo ( "button" );
        $obBtnCancelar->obEvento->setOnClick ( "montaParametrosGET( 'cancelarHomologacao', 'boHomologarItemInId, boProximoItem' );" );
        if ($itensHomologacao[$inId]['status'] != "Homologado") {
            $obBtnCancelar->setDisabled( true );
        }

        $obBtnAnular = new Button;
        $obBtnAnular->setName ( "btnAnularItem" );
        $obBtnAnular->setValue( "Anular Item" );
        $obBtnAnular->setTipo ( "button" );
        $obBtnAnular->obEvento->setOnClick ( "montaParametrosGET( 'montaAnularItem', 'boAdjudicarItemInId, boProximoItem' );" );
        if ($itensHomologacao[$inId]['status'] != "A Homologar") {
            $obBtnAnular->setDisabled( true );
        }

        $obFormulario = new Formulario;
        $obFormulario->addTitulo( "Operação de Homologação" );
        if ($itensHomologacao[$inId]['status'] == "Anulado" || $itensHomologacao[$inId]['status'] == "Revogado") {
            $obFormulario->addComponente( $obTxtAreaJustificativaAnulacao );
        }
        $obFormulario->addSpan( $obSpnAnularItem );
        $obFormulario->addComponente( $obChkIrProximo );
        $obFormulario->defineBarra( array ( $obBtnHomologar , $obBtnCancelar, $obBtnAnular ),"","" );
        $obFormulario->montaInnerHTML();
        $stHtml .= $obFormulario->getHTML();
    } else {
        $stHtml = "";
    }

    $stJs .= "document.getElementById('spnHomologacao').innerHTML = '".$stHtml."';\n";

    return $stJs;
}

function homologarItem($inId)
{
    $itensHomologacao = Sessao::read('itensHomologacao');
    $itensHomologacao[$inId]['status']        = "Homologado";
    $itensHomologacao[$inId]['boAlterado']    = "true";
    Sessao::write('itensHomologacao', $itensHomologacao);
}

function proximoItem()
{
    $stJs .= montaSpnItens();
    $inId = "";
    $itensHomologacao = Sessao::read('itensHomologacao');

    $inId = $_GET['boHomologarItemInId'] + 1;

    if ($itensHomologacao[$inId] != "") {
        if ($_GET['boProximoItem'] == "on") {
            $stJs .= "document.frm.boHomologarItemInId[$inId].checked = true;\n";
        }
        $stJs .= montaspnHomologacao( $inId );
    } else {
        $stJs .= "document.getElementById('spnHomologacao').innerHTML = '';\n";
    }

    return $stJs;
}

function cancelarHomologacao($inId)
{
    $itensHomologacao = Sessao::read('itensHomologacao');
    $itensHomologacao[$inId]['status']     = "A Homologar";
    $itensHomologacao[$inId]['boAlterado'] = "true";
    Sessao::write('itensHomologacao', $itensHomologacao);
}

function montaAnularItem()
{
    $obTxtAreaJustificativa = new TextArea;
    $obTxtAreaJustificativa->setRotulo( "*Justificativa da Anulação" );
    $obTxtAreaJustificativa->setName( "stJustificativa da Anulação" );
    $obTxtAreaJustificativa->setId( "stJustificativa" );

    $obChkRevogado = new CheckBox;
    $obChkRevogado->setRotulo('Anulação por Revogação');
    $obChkRevogado->setName('boRevogado');

    $obBtnOkAnular = new Button;
    $obBtnOkAnular->setName ( "btnOkAnular" );
    $obBtnOkAnular->setValue( "Ok" );
    $obBtnOkAnular->setTipo ( "button" );
    $obBtnOkAnular->setStyle( "width: 60px" );
    $obBtnOkAnular->obEvento->setOnClick ( "if( document.getElementById('stJustificativa').value != '' ) montaParametrosGET( 'anularItem', 'boHomologarItemInId, boProximoItem, stJustificativa, boRevogado' ); else alertaAviso('Campo Justificativa da Anulação inválido.','form','erro','".Sessao::getId()."', '../');" );

    $obBtnCancelarAnular = new Button;
    $obBtnCancelarAnular->setName ( "btnCancelarAnular" );
    $obBtnCancelarAnular->setValue( "Cancelar" );
    $obBtnCancelarAnular->setTipo ( "button" );
    $obBtnCancelarAnular->obEvento->setOnClick ( "montaParametrosGET( 'cancelarAnularItem', 'boHomologarItemInId, boProximoItem' );" );

    $obFormulario = new Formulario;
    $obFormulario->addComponente( $obTxtAreaJustificativa );
    $obFormulario->addComponente( $obChkRevogado          );
    $obFormulario->defineBarra( array ( $obBtnOkAnular , $obBtnCancelarAnular ),"","" );
    $obFormulario->montaInnerHTML();
    $stHtml .= $obFormulario->getHTML();

    $stJs .= "document.getElementById('spnAnularItem').innerHTML = '".$stHtml."';\n";

    return $stJs;
}

function anularItem($inId, $stJustificativaAnulacao)
{
    $itensHomologacao = Sessao::read('itensHomologacao');

    if ($_GET['boRevogado']) {
        $itensHomologacao[$inId]['status']                 = "Revogado";
    } else {
        $itensHomologacao[$inId]['status']                 = "Anulado";
    }
    $itensHomologacao[$inId]['boAlterado']             = "true";
    $itensHomologacao[$inId]['justificativa_anulacao'] =  $stJustificativaAnulacao;

    Sessao::write('itensHomologacao', $itensHomologacao);
}

function cancelarAnularItem()
{
    $stJs .= "document.getElementById('spnAnularItem').innerHTML = '';\n";

    return $stJs;
}

function alterarStatusItens($stStatus)
{
    $itensHomologacao = Sessao::read('itensHomologacao');
    for ($inCount=0; $inCount<count($itensHomologacao); $inCount++) {
        $itensHomologacao[$inCount]['status']     = $stStatus;
        $itensHomologacao[$inCount]['boAlterado'] = "true";
    }
    Sessao::write('itensHomologacao', $itensHomologacao);
    // Após a aplicação do status de Homologação ou A Homologar, esconde a div de homologação manual.
    $stJs .= "document.getElementById('spnHomologacao').innerHTML = '&nbsp;';\n";
    $stJs .= montaSpnItens();

    return $stJs;
}

function validaLicitacao($inCodCompraDireta, $inCodModalidade, $inCodEntidade, $stExercicio)
{
    include_once(CAM_GP_LIC_MAPEAMENTO."TLicitacaoEdital.class.php");
    $obTLicitacaoEdital = new TLicitacaoEdital;
    $stFiltro  = " WHERE cod_licitacao       =  ".$inCodCompraDireta;
    $stFiltro .= "   AND cod_modalidade      =  ".$inCodModalidade;
    $stFiltro .= "   AND cod_entidade        =  ".$inCodEntidade;
    $stFiltro .= "   AND exercicio           = '".Sessao::getExercicio()."'";
    $stFiltro .= "   AND exercicio_licitacao = '".$stExercicio."'";
    $obTLicitacaoEdital->recuperaTodos($rsEdital, $stFiltro);

    if ($rsEdital->getNumLinhas() > 0) {
        include_once(CAM_GP_LIC_MAPEAMENTO."TLicitacaoEditalSuspenso.class.php");
        $obTLicitacaoEditalSuspenso = new TLicitacaoEditalSuspenso;
        $obTLicitacaoEditalSuspenso->setDado('num_edital' , $rsEdital->getCampo('num_edital') );
        $obTLicitacaoEditalSuspenso->setDado('exercicio'  , $rsEdital->getCampo('exercicio')  );
        $obTLicitacaoEditalSuspenso->recuperaPorChave($rsEditalSuspenso);

        if ($rsEditalSuspenso->getNumLinhas() <= 0) {
            include_once(CAM_GP_LIC_MAPEAMENTO."TLicitacaoEditalImpugnado.class.php");
            $obTLicitacaoEditalImpugnado = new TLicitacaoEditalImpugnado;
            $stFiltro  = " WHERE num_edital =  ".$rsEdital->getCampo('num_edital');
            $stFiltro .= "   AND exercicio  = '".$rsEdital->getCampo('exercicio')."'";
            $obTLicitacaoEditalImpugnado->recuperaTodos($rsEditalImpugnado, $stFiltro);

            if ($rsEditalImpugnado->getNumLinhas() > 0) {
                $boErro = true;
                echo "alertaAviso('Edital ".$rsEdital->getCampo('num_edital')."/".$rsEdital->getCampo('exercicio')." está impugnado!', 'form','erro','".Sessao::getId()."');";
            }
        } else {
            $boErro = true;
            echo "alertaAviso('Edital ".$rsEdital->getCampo('num_edital')."/".$rsEdital->getCampo('exercicio')." está suspenso!', 'form','erro','".Sessao::getId()."');";
        }
    }
    if ($boErro == true) {
            $js  = limpaSessao();
            $js .= '$("spnAutorizacaoEmpenho").innerHTML = "";';
    } else {
        $js .= preencheObjeto();
        $js .= verificaAutorizacao();
    }
}

switch ($stCtrl) {
    case "configuracoesIniciais":
        configuracoesIniciais();
        $js = montaSpnItens();
    break;
    case "carregaItensBanco":
        if ($_GET["inCodCompraDireta"]) {
            if (carregaItensBanco()) {
                $inCodCompraDireta  = $_REQUEST['inCodCompraDireta'];
                $inCodModalidade    = $_REQUEST['inCodModalidade'];
                $inCodEntidade      = $_REQUEST['inCodEntidade'];
                $stExercicio        = $_REQUEST['stExercicioCompraDireta'];
                $js .= validaLicitacao($inCodCompraDireta, $inCodModalidade, $inCodEntidade, $stExercicio);
            }
        } else {
            $js  = limpaSessao();
            $js .= limpaObjeto();
            $js .= '$("spnAutorizacaoEmpenho").innerHTML = "";';
        }
        $js .= montaspnHomologacao();
        $js .= montaSpnItens();
        $js .= preencheObjeto();
        $js .= buscaDataHomologacacao();
        $js .= buscaJustificativaRazao();
    break;
    case "montaSpnItens":
        $js = montaSpnItens();
    break;
    case "montaHomologacao":
        $js = montaspnHomologacao( $_GET['boHomologarItemInId'] );
    break;
    case "limpaSpans":
        $js  = limpaSessao();
        $js .= limpaObjeto();
        $js .= montaspnHomologacao();
        $js .= montaSpnItens();
        $js .= '$("spnAutorizacaoEmpenho").innerHTML = "";';
    break;
    case "homologarItem":
        $js  = homologarItem( $_GET['boHomologarItemInId'] );
        $js .= verificaAutorizacao();
        $js .= proximoItem();
    break;
    case "cancelarHomologacao":
        $js  = cancelarHomologacao( $_GET['boHomologarItemInId'] );
        $js .= proximoItem();
    break;
    case "montaAnularItem":
        $js = montaAnularItem();
    break;
    case "anularItem":
        $js  = anularItem( $_GET['boHomologarItemInId'], $_GET['stJustificativa'], $_GET['boRevogado'] );
        $js .= proximoItem();
    break;
    case "cancelarAnularItem":
        $js = cancelarAnularItem();
    break;
    case "limparTela":
        $js  = limpaSessao();
        $js .= limpaObjeto();
        $js .= montaspnHomologacao();
        $js .= montaSpnItens();
        $js .= '$("spnAutorizacaoEmpenho").innerHTML = "";';
    break;
    case "homologarTodos":
        $js = alterarStatusItens( 'Homologado' );
    break;
    case "cancelarHomologacaoTodos":
        $js = alterarStatusItens( 'A Homologar' );
    break;

}

if ($js) {
    echo $js;
}

?>
